<?php

			// Zuerst alle User herausfiltern die noch als online vermerkt sind, aber sich seit dem Timeout nicht mehr gerührt haben!
			$sql = "update " . DB_USERS . " set online = 0, last_login = last_action where online=1 and (last_action + " . TIMEOUT . ") < " . time() . "";
 			$db->query($sql);


			$UserOverview = new Page('Userverwaltung', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$UserOverview->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=user_create";
			$menu[1][text] = 'Neuen User anlegen';

			$UserOverview->pagetitle('Userverwaltung', $menu);
			echo '<br>';

			echo '
					<table width=95% align=center>
						<tr>
							<td width=50% valign=top>';

					$output = '<form action=' . $PHP_SELF . ' method=get><input type=hidden name=id value=' . $user_id . '><button type=submit name=area value=user_edit>Mein Account</button></form> Hier kannst du deinen Account editieren.';
					$UserListe = new Column('Mein Account', $output, 0, '99%');

					$output = '';
					$sql = "select id, n_name, v_name from " . DB_USERS . " where online=1";
					$users_raw = $db->query($sql);

					if ($db->num_rows($users_raw))
					{
						$output .= 'Folgende User sind momentan online :<br>';
						while ($current = $db->fetch_array($users_raw))
						{
							$output .= '<a href=' . $PHP_SELF . '?area=user_view&id=' . $current[id] . ' class=blue>' . $current[v_name] . ' ' . $current[n_name] . '</a>, ';
						}
					}
					else
					{
						$output = 'Es sind zur Zeit keine User online... Datenbankfehler??';
					}

					$UserListe->Column('User online', $output, 0, '99%');

					$sql = "select id, v_name, n_name, unix_timestamp(geburtstag) as geburtstag, (year(current_date) - year(geburtstag)) - (right(current_date, 5)<right(geburtstag, 5)) as 'Alter' from users where month(geburtstag) = month(date_add(now(), interval 1 month)) or MONTH(geburtstag) = MONTH(DATE_ADD(NOW(), INTERVAL 0 MONTH))";

					$output = 'Folgende Personen haben in diesem oder im nächsten Monat Geburtstag : ';
					$users_raw = $db->query($sql);

					$output .= '<table width=100% align=center border=0 cellspacing=3>';
					while ($current = $db->fetch_array($users_raw))
					{
						$output .= '
								<tr bgcolor=#F2F5FF>
									<td style="font-size: 9pt;"><a href="' . $PHP_SELF . '?area=user_view&id=' . $current[id] . '" class=blue>' . $current[v_name] . '</a></td>
									<td style="font-size: 9pt;"><a href="' . $PHP_SELF . '?area=user_view&id=' . $current[id] . '" class=blue>' . $current[n_name] . '</a></td>
									<td style="font-size: 9pt;">' . ($current[Alter] + 1) . '</td>
									<td style="font-size: 9pt;">' . strftime('%e.%m.%Y', $current[geburtstag]) . '</td>
								</tr>';
					}

					$output .= '</table>';

					$UserListe->Column('Geburtstage', $output, 0, '99%');

					echo '
							</td>
							<td>';

							$sql = "select id, v_name, n_name, online from " . DB_USERS . " order by n_name";

							$users_raw = $db->query($sql);

							if ($db->num_rows($users_raw))
							{
								$output = '
										<form action=' . $PHP_SELF . ' method=get>
										<table width=100% align=center border=0>
											<tr>
												<td valign=right width=12>
														<select size=15 name=id>';

										while ($current = $db->fetch_array($users_raw))
										{
											$output .= '
														<option value=' . $current[id] . '>' . $current[n_name]  . ' ' . $current[v_name] . '</option>
													';
										}

										$output .= '
														</select>
												</td>
												<td valign=top>
													Hier können User je nach Rechten betrachtet, editiert und gelöscht werden!<br>
													<button type=submit name=area value=user_view>betrachten</button>
													<button type=submit name=area value=user_edit>editieren</button>
													<button type=submit name=area value=user_delete>löschen</button>


												</td>
											</tr>
										</table>
									</form>';

									$UserListe->Column('Userliste', $output, 0, '99%');

							}
							else
							{
								$options = array();
								$options[right][text] = 'Zum News-Archiv';
								$options[right][link] = "$PHP_SELF?area=news_archive";
								$options[left][text] = 'Zurück zur Startseite';
								$options[left][link] = "$PHP_SELF";
								$error = new InfoBox('Keine User gefunden', 'Whoups, es wurden keine User in der Datenbank gefunden... Datenbankfehler?', $options);
							}

					echo '	</td>
						</tr>';

			echo '</table>';

			$UserOverview->html_footer();
?>
