<?php

	$menu = array();
	$menu[0][link] = $PHP_SELF;
	$menu[0][text] = 'Neuen Termin anlegen';
	$menu[1][link] = $PHP_SELF;
	$menu[1][text] = 'Neuen Bericht anlegen';
	$page->title_bar($menu);


	// Alle Ausbildungsbeschreibungen holen!
	$sql = '
			select
				' . TB_BERICHT . '.id,
				' . TB_BERICHT . '.berichtart,
				' . TB_BERICHT . '.ref_object_id,
				' . TB_BERICHT . '.titel,
				date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y %H:%i") as date_create_readable,
				' . TB_OBJECT . '.date_create,
				' . TB_OBJECT . '.date_lastchange
			from
				' . TB_BERICHT . ',
				' . TB_OBJECT . '
			where
				' . TB_OBJECT . '.id = ' . TB_BERICHT . '.ref_object_id
				and
				' . TB_BERICHT . '.berichtart = ' . REPORTTYPE_AB . '
			order by
				' . TB_OBJECT . '.date_create
			limit
				' . MAX_DATES_PER_COLUMN . '
		';

	$raw = $db->query($sql);
	if ($db->num_rows($raw))
	{
		$reports = '
					<table width=100% align=center border=0 class=list_table>
						<th>Datum</th><th>Titel</th>
			';

		while ($current = $db->fetch_array($raw))
		{
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


			$reports .= '
					<tr class=' . $class . '>
						<td>
							' . $current[date_create_readable] . '
						</td>
						<td>
							<a href="' . $PHP_SELF . '?action=report_read&id=' . $current[ref_object_id] . '">' . $current[titel] . '</a>
						</td>
					</tr>
					';
		}
		$reports .= '
				</table>
			';
	}
	else
	{
		$reports = '
				Momentan keine vorhanden!
			';
	}

	// Alle Lehrgänge und Bereichsausbildungen holen!
	$sql = '
			select
				' . TB_TERMIN . '.id,
				' . TB_TERMIN . '.terminart,
				' . TB_TERMIN . '.ref_object_id,
				date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y %H:%i") as date_begin_readable,
				date_format(' . TB_OBJECT . '.date_end, "%d.%m.%Y %H:%i") as date_end_readable,
				' . TB_OBJECT . '.date_create,
				' . TB_OBJECT . '.date_lastchange
			from
				' . TB_TERMIN . ',
				' . TB_OBJECT . '
			where
				' . TB_OBJECT . '.date_begin > now()
				and
				' . TB_OBJECT . '.id = ' . TB_TERMIN . '.ref_object_id
				and
				' . TB_TERMIN . '.terminart <= ' . DATETYPE_LG . '
				and
				' . TB_TERMIN . '.terminart >= ' . DATETYPE_BA . '
			order by
				' . TB_OBJECT . '.date_begin
			limit
				' . MAX_DATES_PER_COLUMN . '
		';

	// echo $sql;
	$raw = $db->query($sql);
	if ($db->num_rows($raw))
	{
		$dates = '
					<table width=100% align=center border=0 class=list_table>
						<th colspan=3>Datum</th><th>Typ</th>
			';

		while ($current = $db->fetch_array($raw))
		{
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
						<td>
							' . $current[date_begin_readable] . '
						</td>
						<td>
							-
						</td>
						<td>
							' . $current[date_end_readable] . '
						</td>
						<td>
							<a href="' . $PHP_SELF . '?action=date_view&id=' . $current[ref_object_id] . '">' . $DATE_TYPES[$current[terminart]][name] . '</a>
						</td>
					</tr>
					';
		}
		$dates .= '
				</table>
			';
	}
	else
	{
		$dates = '
				Momentan stehen keine Ausbildungen an!
			';
	}


	echo '
		<table width=96% align=center border=0 cellpadding=3>
			<tr>
				<td width=50% valign=top>
					' . $page->dialog_box('Die nächsten Ausbildungen', $dates, 0, 0, '100%') . '
				</td>
				<td valign=top>
					' . $page->dialog_box('Ausbildungsbeschreibungen', $reports, 0, 0, '100%') . '
				</td>
			</tr>
		</table>
		';

?>
