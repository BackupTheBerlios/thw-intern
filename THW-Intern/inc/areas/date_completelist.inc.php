<?php

	$page->title_bar();



	// Wieviele Einträge sollen pro Seite angezeigt werden??
	define('MAX_ENTRIES_PER_PAGE', 20);

	if (($offset  < 0) or (!$offset))
	{
		$offset = 0;
	}


	$sql = '
			select
				' . TB_TERMIN . '.id,
				' . TB_TERMIN . '.terminart,
				' . TB_TERMIN . '.ref_object_id,
				' . TB_TERMIN . '.flag_kueche,
				MID(' . TB_TERMIN . '.kommentar, 1, 100) as short_description,
				date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y %H:%i") as date_begin_readable,
				date_format(' . TB_OBJECT . '.date_end, "%d.%m.%Y %H:%i") as date_end_readable,
				unix_timestamp(' . TB_OBJECT . '.date_begin) as date_begin,
				' . TB_OBJECT . '.date_create,
				' . TB_OBJECT . '.date_lastchange
			from
				' . TB_TERMIN . ',
				' . TB_OBJECT . '
			where
				' . TB_OBJECT . '.id = ' . TB_TERMIN . '.ref_object_id
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

		for ($i = 1; $i <= count($DATE_TYPES); $i++)
		{
			$filterbar .= '
								<option value=' . $i . '>Nur ' . $DATE_TYPES[$i][name] . '</option>
				';
		}

	$filterbar .= '
							</select>
							<input type=hidden name=action value=' . $action . '>
							<input type=hidden name=offset value=' . $offset . '>
							<input type=hidden name=show_all value=' . $show_all . '>
							<input type=submit value="Los">
						</form>
					</td>
			';

	if ($show_all)
	{
		$sort_date = '';
		$filterbar .= '
					<td>
						<a href="' . $PHP_SELF . '?action=' . $action . '&offset=' . $offset . '&sort_by_type=' . $sort_by_type . '&show_all=0 ">vergangene Termine ausblenden</a>
					</td>
			';
	}
	else
	{
		$sql .= '
						and
					' . TB_OBJECT . '.date_begin > now()
				';
		$filterbar .= '
					<td>
						<a href="' . $PHP_SELF . '?action=' . $action . '&offset=' . $offset . '&sort_by_type=' . $sort_by_type . '&show_all=1 ">vergangene Termine einblenden</a>
					</td>
			';

	}

	if ($sort_by_type)
	{
		$sql .= '
						and
					' . TB_TERMIN . '.terminart = ' . $sort_by_type . '
				';
		$title = 'Filter: nur <i>' . $DATE_TYPES[$sort_by_type][name] . '</i>';
	}
	else
	{
		$title = 'Alle Dienste';
	}

	$filterbar .= '
				</tr>
			</table>
		';

	$sql .= '
			order by
				' . TB_OBJECT . '.date_begin desc
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
		$bottom_menu[$tmp++][link] = $PHP_SELF . '?action=date_completelist&offset=' . ($offset - MAX_ENTRIES_PER_PAGE) . '&sort_by_type=' . $sort_by_type . '&show_all=' . $show_all;
	}

	if ($db->num_rows($raw) > MAX_ENTRIES_PER_PAGE)
	{
		$bottom_menu[$tmp][text] = 'Ältere &gt;&gt; ';
		$bottom_menu[$tmp++][link] = $PHP_SELF . '?action=date_completelist&offset=' . ($offset + MAX_ENTRIES_PER_PAGE) . '&sort_by_type=' . $sort_by_type . '&show_all=' . $show_all;
	}

	if ($db->num_rows($raw))
	{
		$dates = '
				' . $page->dialog_box(0, $filterbar, 0, 0, '100%') . '
					<table width=100% align=center border=0 class=list_table>
						<th colspan=3>Datum</th><th>Typ</th><th>K&uuml;che</th>
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


			// Jetzt prüfen wir noch ob der Dienst nicht evtl. schon vorbei ist...
			if ($current[date_begin] < time())
			{
				$class = 'list_table_inactive';
			}


			if ($current[flag_kueche])
			{
				$current[flag_kueche] = 'Ja';
			}
			else
			{
				$current[flag_kueche] = '';
			}


			$dates .= '
					<tr class=' . $class . '>
						<td width=20%>
							' . $current[date_begin_readable] . '
						</td>
						<td width="5">
							-
						</td>
						<td width=20%>
							' . $current[date_end_readable] . '
						</td>
						<td>
							<a href="' . $PHP_SELF . '?action=date_view&id=' . $current[ref_object_id] . '" title="' . $current[short_description] . '...">' . $DATE_TYPES[$current[terminart]][name] . '</a>
						</td>
						<td width=5% align=center>
							' . $current[flag_kueche] . '
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
