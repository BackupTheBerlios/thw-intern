<?php

			$User = new Page('User betrachten', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$User->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=user";
			$menu[1][text] = 'Userverwaltung';
			$menu[2][link] = "$PHP_SELF?area=user_edit&id=$id";
			$menu[2][text] = 'Diesen Account editieren';
			$menu[3][link] = "$PHP_SELF?area=user_delete&id=$id";
			$menu[3][text] = 'User löschen';

			$User->pagetitle('User betrachten', $menu);
			echo '<br>';

			$sql = "select *, unix_timestamp(geburtstag) as birth from users where id=$id";

			$tmp = $db->query($sql);

			if ($db->num_rows($tmp))
			{
				$result = $db->fetch_array($tmp);

				if (!$result[icq])
				{
					$icq = '-';
				}
				else
				{
					$icq = $result[icq];
				}





				function einheit_string ( $unit_id, $units, &$found,  $depth = 0)
				{
					if ($found)
					{
						break;
					}

					$depth++;
					$indent = str_repeat ('&nbsp;&nbsp;&nbsp;&nbsp;', $depth);

					for ( $i = 0; $i < count($units); $i++ )
					{

						echo $indent . '[' . $depth . '] pass # ' . $i . ' - processing : ' . $units[$i][name] . '<br>';
						$return = $units[$i][name];

						if ( $units[$i][id] == $unit_id )
						{
							echo '<b class=red>found the right entry!! <br></b>';
							$fount++;
							break;
						}
						else
						{
							if ( is_array($units[$i][einheiten]) )
							{
								$return .= ', ' . einheit_string($unit_id, $units[$i][einheiten], $depth, $found);
							}
						}
					}

					return ($return);
				}




				echo '
					<table width=98% align=center>
						<tr>
							<td width=30% align=right>
								Loginname :
							</td>
							<td>
								<b>
									' . $result[login_name] . '
								</b>
							</td>
						</tr>
						<tr>
							<td align=right>
								Vorname :
							</td>
							<td>
								<b>
									' . $result[v_name] . '
								</b>
							</td>
						</tr>
						<tr>
							<td align=right>
								Nachname :
							</td>
							<td>
								<b>
									' . $result[n_name] . '
								</b>
							</td>
						</tr>
						<tr>
							<td align=right>
								Geburtstag :
							</td>
							<td>
								<b>';
					if ($result[birth] > 0)
					{
						echo strftime("%e.%m.%Y" ,$result[birth]);
					}
					echo  '
								</b>
							</td>
						</tr>
						<tr>
							<td>
								<br>
							</td>
						</tr>
						<tr>
							<td align=right>
								Email :
							</td>
							<td>
								<a href="mailto:' . $result[email]  . '" class=blue>
									' . $result[email] . '
								</a>
							</td>
						</tr>
						<tr>
							<td align=right>
								Homepage :
							</td>
							<td>
								<a href="' . $result[homepage] . '" class=blue>
									' . $result[homepage] . '
								</a>
							</td>
						</tr>
						<tr>
							<td align=right>
								ICQ : 
							</td>
						<td>
							<b>
								' . $icq . '
							</b>
						</td>
					</tr>
					<tr>
						<td>
							<br>
						</td>
					</tr>
					<tr>
						<td align=right>
							Rang :
						</td>
						<td>
							<b>
								' . $rang[$result[rang]][name] . '
							</b>
						</td>
					</tr>
					<tr>
						<td align=right>
							Funktion :
						</td>
						<td>
							<b>
							</b>
						</td>
					</tr>
					<tr>
						<td align=right>
							Einheit :
						</td>
						<td>
							<b>
								' . $result[einheit] . '

							</b>
						</td>
					</tr>
					<tr>
						<td>
							<br>
						</td>
					</tr>
					<tr>
						<td align=right>
							Last action :
						</td>
						<td>
							<b>
								' . strftime('%H:%M:%S', $result[last_action]) . '
							</b>
						</td>
					</tr>
				</table>
				';
			}
			else
			{
				$options = array();
				$options[right][text] = 'Zur Userverwaltung';
				$options[right][link] = "$PHP_SELF?area=user";
				$options[left][text] = 'Zurück zur Startseite';
				$options[left][link] = "$PHP_SELF";
				$error = new InfoBox('User nicht gefunden', 'Whoups, der User mit dieser ID wurde nicht gefunden... Datenbankfehler?', $options);
			}

			$User->html_footer();
?>
