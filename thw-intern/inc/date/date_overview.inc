<?php

			$DateOverview = new Page('Terminverwaltung', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$DateOverview->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=date_create";
			$menu[1][text] = 'Neuen Termin anlegen';

			$DateOverview->pagetitle('Terminverwaltung', $menu);
			echo '<br>';

			echo '
					<table width=95% align=center>
						<tr>';

					echo '
						<tr>
							<td width=50% valign=top>';

					$sql = "select id, type, begin, end, info from " . DB_DATES . " where unfinished = 0 and begin > " . time() . " order by begin limit 4";
					$dates_raw = $db->query($sql);

					$output = 'Dies sind die nächsten 4 Dienste : ';

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

					$output .= '</table>';
					$Box = new Column('Die nächsten 4 Dienste', $output, 0, '99%');


/*					$output = 'Dies sind die nächsten 4 Dienste deiner Einheit (Diese Auflistung geht nach TZ) : <br><b>noch nicht fertig</b>';
					$Box = new Column('Die nächsten 4 Dienste meiner Einheit', $output, 0, '99%');
*/

					$sql = "select id, begin, end, info from " . DB_DATES . " where unfinished = 0 and begin > " . time() . " and type = 8 order by begin limit 4";
					$dates_raw = $db->query($sql);

					if ($db->num_rows($dates_raw))
					{
						$output = 'Dies sind die nächsten Lehrgänge :';

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
										<td style="font-size: 9pt;"><a href="' . $PHP_SELF . '?area=date_view&id=' . $current[id] . '" class=blue>' . $current[info] . '</a></td>
									</tr>';
						}

						$output .= '</table>';
						$Box = new Column('Lehrgänge', $output, 0, '99%');
					}

					echo '
							</td>
							<td valign=top>';

					$output = '...ist<b> ' . strftime('%A</b> der <b>%e.%m.%Y</b>') . '.';
					$Box = new Column('Heute...', $output, 0, '100%');

					
					////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

					
					$output = 'Die nächsten Dienste wo noch Teilnehmer gesucht werden :
					<table width=100% align=center border=0 cellspacing=3>
							';

					// Zuerst suchen wir Dienste raus die noch stattfinden und überhaupt helfer beinhalten!
					$sql = "select id, anzahl_helfer, begin, end, type, info from " . DB_DATES . " where anzahl_helfer > 0 and begin > unix_timestamp(now())";
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
							$temp = $db->query($sql);

							if ($db->num_rows($temp))		// Überhaupt Einträge für den aktuellen Dienst gefunden?
							{
								$counter ++;
								// Wie viele haben wir schon bearbeitet?
								if ($counter > 4)
								{
									break;
								}

								$temp = $db->fetch_array($temp);
								// Überprüfen ob der Dienst nicht schon voll ist
								if ($temp[count] < $current[anzahl_helfer])
								{
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

					echo '	</td>
						</tr>
						<tr>
							<td colspan=2>';

					$output = '
							<form action=' . $PHP_SELF . ' method=get>
								<table width=100% align=center>
									<tr>
										<td valign=top align=center>
											<select size=9 name=id>';
					$sql = "select * from " . DB_DATES . " where unfinished=0 and begin > " . mktime(0, 1, 1, 1, 1, strftime('%Y')) .  " order by begin";
					// echo $sql;
					$dates_raw = $db->query($sql);

					while ($current = $db->fetch_array($dates_raw))
					{

						if (strlen($current[info]) > 25)
						{
									$current[info] = substr($current[info], 0, 25);
									$current[info] .= '...';
						}
						$output .= '<option value=' . $current[id] . ' ' . $insert_string . '>[' . strftime('%e.%m.%Y', $current[begin]) . '] [' . $date_types[$current[type]][name] . '] ' . $current[info] . '</option>';
					}

					$output .= '
											</select>
										</td>
										<td>
													<button type=submit name=area value=date_view>betrachten</button>
													<button type=submit name=area value=date_edit>editieren</button>
													<button type=submit name=area value=date_delete>löschen</button>

													<input type=checkbox name=post_news value=1 checked> Automatisch in den News melden!

													<table width=100% align=center bgcolor=#F2F5FF>
														<tr>
															<td>
																Benutzer des Internet Explorers bitte diese Buttons benutzen :
															</td>
														</tr>
														<tr>
															<td>
																<input type=radio name=area value=date_view default> Termin betrachten <br>
																<input type=radio name=area value=date_edit> Termin editieren <br>
																<input type=radio name=area value=date_delete> Termin löschen <br>
															</td>
														</tr>
														<tr>
															<td align=center>
																<input type=submit value="Los">
															</td>
														</tr>
													</table>
											</td>
										</tr>
									</table>


							</form>';

					$Box = new Column('Terminliste - dieses Jahr (' . strftime('%Y') . ')', $output, 0, '99%');


			echo '			</td>
						</tr>
					</table>';

			$DateOverview->html_footer();
?>
