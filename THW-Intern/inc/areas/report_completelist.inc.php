<?php

	$show_all = 1;

	$page->title_bar();

	// Wieviele Einträge sollen pro Seite angezeigt werden??
	define('MAX_ENTRIES_PER_PAGE', 20);

	if (($offset  < 0) or (!$offset))
	{
		$offset = 0;
	}


	$sql = '
			select
				' . TB_BERICHT . '.id,
				' . TB_BERICHT . '.berichtart,
				' . TB_BERICHT . '.ref_object_id,
				' . TB_BERICHT . '.flag_freigegeben,
				' . TB_BERICHT . '.titel,
				date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y %H:%i") as date_create_readable,
				date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y %H:%i") as date_begin_readable,
				date_format(' . TB_OBJECT . '.date_end, "%d.%m.%Y %H:%i") as date_end_readable,
				unix_timestamp(' . TB_OBJECT . '.date_begin) as date_begin,
				' . TB_OBJECT . '.date_create,
				' . TB_OBJECT . '.date_lastchange
			from
				' . TB_BERICHT . ',
				' . TB_OBJECT . '
			where
				' . TB_OBJECT . '.id = ' . TB_BERICHT . '.ref_object_id
		';

// 	echo $sql;

	$filterbar = '
			<table width=100% align=center border=0>
				<tr>
					<td>
						<form action=' . $PHP_SELF . ' method=get>
							<select size=1 name=sort_by_type>
								<option value=0>Alles</option>
			';


		while ($current = each($REPORT_TYPES))
		{
			$filterbar .= '
								<option value=' . $current[0] . '> Nur ' . $current[value][name] . ' </option>
				';
		}

	$filterbar .= '
							</select>
							<input type=hidden name=action value=' . $action . '>
							<input type=hidden name=offset value=0>
							<input type=hidden name=show_all value=' . $show_all . '>
							<input type=submit value="Los">
						</form>
					</td>
			';

	if ($sort_by_type)
	{
		$sql .= '
						and
					' . TB_BERICHT . '.berichtart = ' . $sort_by_type . '
				';
		$title = 'Filter: nur <i>' . $REPORT_TYPES[$sort_by_type][name] . '</i>';
	}
	else
	{
		$title = 'Alle Berichte';
	}

	$filterbar .= '
				</tr>
			</table>
		';

	$sql .= '
			order by
				' . TB_OBJECT . '.date_create desc
			limit
				' . $offset . ', ' . (MAX_ENTRIES_PER_PAGE + 1) . '
		';

// 	echo $sql;


	$raw = $db->query($sql);

	$bottom_menu = array();
	$tmp = 0;
	if ($offset)
	{
		$bottom_menu[$tmp][text] = '&lt;&lt; Neuere ';
		$bottom_menu[$tmp++][link] = $PHP_SELF . '?action=' . $action . '&offset=' . ($offset - MAX_ENTRIES_PER_PAGE) . '&sort_by_type=' . $sort_by_type . '&show_all=' . $show_all;
	}

	if ($db->num_rows($raw) > MAX_ENTRIES_PER_PAGE)
	{
		$bottom_menu[$tmp][text] = 'Ältere &gt;&gt; ';
		$bottom_menu[$tmp++][link] = $PHP_SELF . '?action=' . $action . '&offset=' . ($offset + MAX_ENTRIES_PER_PAGE) . '&sort_by_type=' . $sort_by_type . '&show_all=' . $show_all;
	}

	if ($db->num_rows($raw))
	{
		$dates = '
				' . $page->dialog_box(0, $filterbar, 0, 0, '100%') . '
					<table width=100% align=center border=0 class=list_table>
						<th>Erschienen</th><th>Typ</th><th>Titel</th>
			';

		$counter = 0;
		while ($current = $db->fetch_array($raw))
		{
			if ($counter == MAX_ENTRIES_PER_PAGE)
			{
				break;
			}

			// Prüfen wann der Termin angelegt worden ist...
			if($current[date_lastchange] > $session->user_info('last_login'))
			{
				// Ok, dieser Dienst ist seit dem letzten Login veränder worden
				// Dann prüfen wir jetzt ob der Dienst angelegt oder nur editiert worden ist

				if ($current[date_create] == $current[date_lastchange])
				{
					// Ok, sieht so aus als wäre er neu erstellt worden!
					$class = 'list_table_less_important';
				}
				else
				{
					// Dieser Dienst wurde nur editiert...
					$class = 'list_table_important';
				}
			}
			else
			{
				// Und hier haben wir die normalen Dienste...
				$class = 'list_table_active';
			}

			$dates .= '
					<tr class=' . $class . '>
						<td width=20%>
							' . $current[date_create_readable] . '
						</td>
						<td width=20%>
							' . $REPORT_TYPES[$current[berichtart]][name] . '
						</td>
						<td>
							<a href="' . $PHP_SELF . '?action=report_read&id=' . $current[ref_object_id] . '">' . $current[titel] . '</a>
						</td>
					</tr>
					';

			$counter++;
		}
		$dates .= '
				</table>
			';
	}
	else
	{
		$dates = '
				' . $page->dialog_box(0, $filterbar, 0, 0, '100%') . '
				Whoups, keine Termine gefunden... )o: (evtl. Sortierkriterien ändern)
			';
		$bottom_menu = 0;
	}

	echo $page->dialog_box($title, $dates , 0, $bottom_menu, '96%');

?>
