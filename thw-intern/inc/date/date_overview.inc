<?php

			$DateOverview = new Page('Terminverwaltung', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$DateOverview->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=date_create";
			$menu[1][text] = 'Neuen Termin anlegen';
			$menu[2][link] = "$PHP_SELF?area=date_training";
			$menu[2][text] = 'Ausbildung';

			$DateOverview->pagetitle('Terminverwaltung', $menu);
			echo '<br>';

			echo '
					<table width=95% align=center>
						<tr>';

					echo '
						<tr>
							<td width=50% valign=top>';

					$output = ' ... ist <b> ' . strftime('%A</b> der <b>%e.%m.%Y</b>') . '.';
					$Box = new Column('Heute...', $output, 0, '100%');


					if ($offset < 0)
					{
						$offset = 0;
					}
					else if (!$offset)
					{
						$offset = 0;

						$sql = "select id
										from " . DB_DATES . "
										where unfinished=0 and begin < " . time() . "
										order by begin desc ";

						$offset = $db->num_rows( $db->query( $sql ) );
					}

					$sql = "select id, type, begin, end, info from " . DB_DATES . " where unfinished = 0 order by begin limit $offset, 10";
					// echo $sql . '<br>';
					$dates_raw = $db->query($sql);

					$output = 'Dies sind die nächsten Dienste : ';

					$output .= '
							<table width=100% align=center border=0 cellspacing=3>
							';
					while ($current = $db->fetch_array($dates_raw))
					{
						$output .= '
								<tr bgcolor=#F2F5FF>
									<td style="font-size: 9pt;" width=10%>' . strftime('%e.%m.%Y', $current[begin]) . '</td>
									<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[begin]) . '</td>
									<td style="font-size: 9pt;" width=2%>-</td>
									<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[end]) . '</td>
									<td style="font-size: 9pt;" width=5%>' . $date_types[$current[type]][name] . '</td>
									<td style="font-size: 9pt;"><a href="' . $PHP_SELF . '?area=date_view&id=' . $current[id] . '" class=blue>' . $current[info] . '</a></td>
								</tr>';
					}

					$sql = "select id
									from " . DB_DATES . "
									where unfinished=0
									order by begin desc limit " . ($offset + 10) . ", 10";

					if ( $db->num_rows( $db->query($sql) ) > 0 )
					{
						$insert1 = '[<a href="' . $PHP_SELF . '?area=date_overview&offset=' . ( $offset + 10 ) . '" class=blue>>></a>]';
					}

					$temp = ($offset - 10);
					if ($temp < 0 ) $temp = 0;
					$sql = "select id
									from " . DB_DATES . "
									where unfinished=0
									order by begin desc limit " .  $temp . ", 10";

					if ( $db->num_rows( $db->query($sql) ) > 0 )
					{
						$insert2 = '[<a href="' . $PHP_SELF . '?area=date_overview&offset=' . ( ($offset - 10) ) . '" class=blue>&lt;&lt;</a>]';
					}

					$output .= '
								<tr>
									<td colspan=4 align=left>
										' . $insert2 . '
									</td>
									<td colspan=3 align=right>
										' . $insert1 . '
									</td>
								</tr>
						';

					$output .= '</table>';
					$Box = new Column('Die nächsten Dienste', $output, 0, '99%');


/*					$output = 'Dies sind die nächsten 4 Dienste deiner Einheit (Diese Auflistung geht nach TZ) : <br><b>noch nicht fertig</b>';
					$Box = new Column('Die nächsten 4 Dienste meiner Einheit', $output, 0, '99%');
*/

// 					$sql = "select id, begin, end, info from " . DB_DATES . " where unfinished = 0 and begin > " . time() . " and type = 8 order by begin limit 10";
// 					$dates_raw = $db->query($sql);
// 
// 					if ($db->num_rows($dates_raw))
// 					{
// 						$output = 'Dies sind die nächsten Lehrgänge :';
// 
// 						$output .= '
// 								<table width=100% align=center border=0 cellspacing=3>
// 								';
// 						while ($current = $db->fetch_array($dates_raw))
// 						{
// 							$output .= '
// 									<tr bgcolor=#F2F5FF>
// 										<td style="font-size: 9pt;" width=10%>' . strftime('%e.%m.%Y', $current[begin]) . '</td>
// 										<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[begin]) . '</td>
// 										<td style="font-size: 9pt;" width=2%>-</td>
// 										<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[end]) . '</td>
// 										<td style="font-size: 9pt;"><a href="' . $PHP_SELF . '?area=date_view&id=' . $current[id] . '" class=blue>' . $current[info] . '</a></td>
// 									</tr>';
// 						}
// 
// 						$output .= '</table>';
// 						$Box = new Column('Lehrgänge', $output, 0, '99%');
// 					}

					echo '
							</td>
							<td valign=top>';


					////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


					$output = 'Die nächsten Dienste wo noch Teilnehmer gesucht werden :
					<table width=100% align=center border=0 cellspacing=3>
							';

					// Zuerst suchen wir Dienste raus die noch stattfinden und überhaupt helfer beinhalten!
					$sql = "select id, anzahl_helfer, begin, end, type, info from " . DB_DATES . " where anzahl_helfer > 0 and begin > unix_timestamp(now()) order by begin";
					$dates_raw = $db->query($sql);

					if ($db->num_rows($dates_raw)) 	// Überhaupt Dienste mit Listen??
					{
						// Jetzt müssen wir die Dienste raussuchen wo noch Plätze frei sind

						$counter = 0;

						// Dazu gehen wir jedes Ergebnis einzeln durch und überprüfen es
						while ($current = $db->fetch_array($dates_raw))
						{
							// Jetzt schauen wieviele eingetragen sind
							$sql = "select count(*) as count from " . DB_USERLISTS . " where ref_id = $current[id]";
							// echo $sql . '<br>';
							$temp = $db->query($sql);
							$temp = $db->fetch_array($temp);

							if ($temp[count] < $current[anzahl_helfer])
							{
								$counter ++;
								// Wie viele haben wir schon bearbeitet?
								if ($counter > 4)
								{
									break;
								}

									$output .= '
											<tr bgcolor=#F2F5FF>
												<td style="font-size: 9pt;" width=10%>' . strftime('%e.%m.%Y', $current[begin]) . '</td>
												<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[begin]) . '</td>
												<td style="font-size: 9pt;" width=2%>-</td>
												<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[end]) . '</td>
												<td style="font-size: 9pt;" width=5%>' . $date_types[$current[type]][name] . '</td>
												<td style="font-size: 9pt;"><a href="' . $PHP_SELF . '?area=date_view&id=' . $current[id] . '" class=blue>' . $current[info] . '</a></td>
												<td style="font-size: 9pt;">' . $temp[count] . '/' . $current[anzahl_helfer] . '</td>
											</tr>';

							}
						}
					}


					$output .= '</table>';

					// $output = 'Die nächsten 4 Dienste wo noch Helfer benötigt werden :<br><b>noch nicht fertig</b>';
					$Box = new Column('Freie Plätze', $output, 0, '99%');

					$sql = "select id, type, begin, end, info, eingetragene_helfer, anzahl_helfer from " . DB_DATES . " where unfinished = 0 and begin > " . time() . " and type=2 order by begin limit 4";
					$dates_raw = $db->query($sql);

					$output = 'Die nächsten THV-Dienste :
					<table width=100% align=center border=0 cellspacing=3>
							';
					if ($db->num_rows($dates_raw))
					{
						while ($current = $db->fetch_array($dates_raw))
						{
							$output .= '
									<tr bgcolor=#F2F5FF>
										<td style="font-size: 9pt;" width=10%>' . strftime('%e.%m.%Y', $current[begin]) . '</td>
										<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[begin]) . '</td>
										<td style="font-size: 9pt;" width=2%>-</td>
										<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[end]) . '</td>
										<td style="font-size: 9pt;"><a href="' . $PHP_SELF . '?area=date_view&id=' . $current[id] . '" class=blue>' . $current[info] . '</a></td>
										<td style="font-size: 9pt;">' . $current[eingetragene_helfer] . '/' . $current[anzahl_helfer] . '</td>
									</tr>';
						}

						$output .= '</table>';
						$Box = new Column('THV-Dienste', $output, 0, '100%');
					}


					$sql = "select
								" . DB_USERLISTS . ".ref_id,
								" . DB_DATES . ".begin,
								" . DB_DATES . ".end,
								" . DB_DATES . ".type,
								" . DB_DATES . ".info
							from " . DB_DATES . ", " . DB_USERLISTS . "
								where " . DB_USERLISTS . ".user_id = $user_id
								and " . DB_DATES . ".id = " . DB_USERLISTS . ".ref_id
								limit 6";
					// echo $sql;

					$dates_raw = $db->query($sql);

					$output = 'Dies sind deine nächsten Dienste :
						<table width=100% align=center border=0 cellspacing=3>
							';


					if ($db->num_rows($dates_raw))
					{
						while ($current = $db->fetch_array($dates_raw))
						{
							$output .= '
									<tr bgcolor=#F2F5FF>
										<td style="font-size: 9pt;" width=10%>' . strftime('%e.%m.%Y', $current[begin]) . '</td>
										<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[begin]) . '</td>
										<td style="font-size: 9pt;" width=2%>-</td>
										<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[end]) . '</td>
										<td style="font-size: 9pt;" width=5%><a href="' . $PHP_SELF . '?area=date_view&id=' . $current[ref_id] . '" class=blue>' . $date_types[$current[type]][name] . '</a></td>
										<td style="font-size: 9pt;"><a href="' . $PHP_SELF . '?area=date_view&id=' . $current[ref_id] . '" class=blue>' . $current[info] . '</a></td>
									</tr>';
						}
					}

					$output .= '
						</table>
						';

					$Box = new Column('Deine nächsten Dienste', $output, 0, '99%');



// 					echo '	</td>
// 						</tr>
// 						<tr>
// 							<td colspan=2>';
//
			echo '			</td>
						</tr>
					</table>';

			$DateOverview->html_footer();
?>
