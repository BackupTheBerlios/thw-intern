<?php

	$page->title_bar();



	// Prüfen ob wir evtl. Koch oder Küchenpersonal sind!
	if ( ($session->user_info('rang') == FUNKTION_KO) or ($session->user_info('rang') == FUNKTION_KUHE))
	{

		// Die Küchendienste holen!
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
					' . TB_TERMIN . '.flag_kueche = 1
				order by
					' . TB_OBJECT . '.date_begin
				limit
					' . MAX_DATES_PER_COLUMN . '

			';

		// echo $sql . '<bR>';

	$raw = $db->query($sql);
	if ($db->num_rows($raw))
	{
		$temp = '
					Bei folgenden Diensten wurde bei der Erstellung von der Führungskraft Verpflegung durch die OV-Küche angefordert
					<table width=100% align=center border=0 class=list_table>
						<th colspan=3>Datum</th><th>Typ</th>
			';

			$raw = $db->query($sql);
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


				$temp .= '
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
			$temp .= '
					</table>
				';
		}
		else
		{
			$my_dates = '
					Momentan keine!
				';
		}

		$kitchen_dates = '
					<br>
					' . $page->dialog_box('Küchendienste', $temp, 0, 0, '100%') . '
			';
	}



	// Die Termine holen wo der User eingetragen ist... Natürlich mit Status-Infos (o:
	// Das KILLER-QUERY!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
				' . TB_OBJECT . ',
				' . TB_HELFERLISTE . ',
				' . TB_HELFERLISTENEINTRAG . '
			where
				' . TB_OBJECT . '.date_begin > now()
				and
				' . TB_OBJECT . '.id = ' . TB_TERMIN . '.ref_object_id
				and
				' . TB_HELFERLISTE . '.ref_object_id = ' . TB_TERMIN . '.ref_object_id
				and
				' . TB_HELFERLISTENEINTRAG . '.ref_object_id = ' . TB_HELFERLISTE . '.ref_object_id
				and
				' . TB_HELFERLISTENEINTRAG . '.ref_user_id = ' . $session->user_info('id') . '
			order by
				' . TB_OBJECT . '.date_begin
			limit
				' . MAX_DATES_PER_COLUMN . '
		';

	$raw = $db->query($sql);
	if ($db->num_rows($raw))
	{
		$my_dates = '
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


			$my_dates .= '
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
		$my_dates .= '
				</table>
			';
	}
	else
	{
		$my_dates = '
				Momentan keine!
			';
	}

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
				' . TB_OBJECT . '.id = ' . TB_TERMIN . '.ref_object_id
				and
				' . TB_OBJECT . '.date_begin > now()
			order by
				' . TB_OBJECT . '.date_begin
			limit
				' . MAX_DATES_PER_COLUMN . '
		';

	// echo $sql;


	$raw = $db->query($sql);
	if ($db->num_rows($raw))
	{
		$all_dates = '
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


			$all_dates .= '
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
		$all_dates .= '
				</table>
			';
	}
	else
	{
		$all_dates = '
				Whoups, noch keine Termine gefunden... )o:
			';
	}

	$legende = '
		<p class=small>
			<b class=list_table_less_important>Grün</b> hinterlegte Berichte sind seit deinem letzten
			Login angelegt worden. <b class=list_table_important>Rot</b> hinterlegte wurden seit
			deinem letzten Login editiert. Alle anderen Dienste sind mit
			<b class=list_table_active>blau</b> markiert!
		</p>
		';

	$overviews = '
			<ul>
				<li><a href="' . $PHP_SELF . '?action=date_completelist&show_all=1&sort_by_type=' . DATETYPE_THV . '">THV-Dienstplan</a></li>
				<li><a href="' . $PHP_SELF . '?action=date_completelist&show_all=1&sort_by_type=' . DATETYPE_SP . '">Straßensperren</a></li>
			</ul>
		';



	// Zuerst speichern wir in "temp" alle noch kommenden Termine mit der
	// Anzahl der darin eingetragenen Usern:
	$sql = '
			create temporary table temp
			select
				count(*) as count,
				' . TB_HELFERLISTENEINTRAG . '.ref_object_id
			from
				' . TB_HELFERLISTENEINTRAG . ',
				' . TB_HELFERLISTE . ',
				' . TB_OBJECT . '
			where
					flag_drin > 0
				and
					' . TB_HELFERLISTE . '.ref_object_id = ' . TB_HELFERLISTENEINTRAG . '.ref_object_id
				and
					' . TB_OBJECT . '.id = ' . TB_HELFERLISTENEINTRAG . '.ref_object_id
				and
					' . TB_OBJECT . '.date_begin > now()
				and
					' . TB_HELFERLISTE . '.flag_open > 0
				and
					' . TB_HELFERLISTE . '.flag_hidden = 0
			group by ref_object_id
		';
	//echo $sql . '<br><bR>';
	$db->query($sql);

	// Ok, jetzt haben wir in Temp die Anzahl der User;

	$sql = '
			select
				' . TB_TERMIN . '.id,
				' . TB_TERMIN . '.terminart,
				' . TB_TERMIN . '.ref_object_id,
				date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y %H:%i") as date_begin_readable,
				date_format(' . TB_OBJECT . '.date_end, "%d.%m.%Y %H:%i") as date_end_readable,
				' . TB_OBJECT . '.date_create,
				' . TB_OBJECT . '.date_lastchange,
				' . TB_HELFERLISTE . '.slots,
				temp.count
			from
				' . TB_TERMIN . ',
				' . TB_OBJECT . ',
				' . TB_HELFERLISTE . ',
				temp
			where
					' . TB_OBJECT . '.id = ' . TB_TERMIN . '.ref_object_id
				and
					' . TB_OBJECT . '.date_begin > now()
				and
					temp.count < ' . TB_HELFERLISTE . '.slots
				and
					' . TB_HELFERLISTE . '.ref_object_id = ' . TB_TERMIN . '.ref_object_id
				and
					temp.ref_object_id = ' . TB_TERMIN . '.ref_object_id
			order by
				' . TB_OBJECT . '.date_begin
			limit
				' . MAX_DATES_PER_COLUMN . '
		';

	// echo $sql;

	$raw = $db->query($sql);

	$free_places = 'Bei folgenden Terminen werden noch Helfer gesucht:';

	if ($db->num_rows($raw))
	{
		$free_places  .= '
					<table width=100% align=center border=0 class=list_table>
						<th colspan=3>Datum</th><th>Typ</th><th>Plätze</th>
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


			$free_places .= '
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
						<td>
							<b>' . ($current[slots] - $current[count]) . '</b>/<b>' . $current[slots] . '</b>
						</td>
					</tr>
					';
		}
		$free_places .= '
				</table>
			';
	}
	else
	{
		$free_places = '
				Momentan gibt es nur leere und/oder volle Listen!
			';
	}

	// Jetzt müssen wir temp wieder rauswerfen:
	$sql = '
			drop table temp';
	$db->query($sql);




	echo '
		<table width=96% align=center border=0 cellpadding=3>
			<tr>
				<td width=50% valign=top>
					' . $page->dialog_box('Meine nächsten Dienste', $my_dates , 0, 0, '100%') . '
					<br>
					' . $page->dialog_box('Die nächsten Dienste', $all_dates, 0, 0, '100%') . '
				</td>
				<td valign=top>
					' . $page->dialog_box('Heute...', ('... ist <b>' . strftime('%A, der %d.%m.%Y') . '</b>.'), 0, 0, '100%') . '
					<br>
					' . $page->dialog_box('Übersichten', $overviews, 0, 0, '100%') . '
					<br>
					' . $page->dialog_box('Freie Plätze', $free_places, 0, 0, '100%') . '
					' . $kitchen_dates . '
					<br>
					' . $page->dialog_box('Legende', $legende, 0, 0, '100%') . '
				</td>
			</tr>
		</table>
		';

?>
