<?php

			if($user_id == $id)		// editieren wir unseren eigenen Account?
			{
				// echo 'Das ist unser Account! ';
				$owner = 1;
			}
			else
			{
				$owner = 0;
				// echo 'Das ist NICHT unser Account!';
			}

			$UserEditor = new Page('User editieren', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1, $owner);
			$UserEditor->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=user";
			$menu[1][text] = 'Userverwaltung';

			$UserEditor->pagetitle('User editieren', $menu);
			echo '<br>';

			// Userdaten holen
			$sql = "select *, unix_timestamp(geburtstag) as geburtstag from " . DB_USERS . " where id=$id";
			$raw = $db->fetch_array($db->query($sql));

			$output = '';

			if ($submit)
			{
				$error = 0;
				if ($v_name and $n_name)
				{
					$v_name = trim($v_name);
					$n_name = trim($n_name);
				}
				else
				{
					$output .= '<p class=red>Name nicht vollständig eingegeben!</p>';
					$error++;
				}

				if ($geburtstag)
				{
					$geburtstag = trim($geburtstag);
				}

				if ($email)
				{
					$email = trim($email);
				}

				if ($homepage)
				{
					$homepage = trim($homepage);
				}

				if ($icq)
				{
					$icq = trim($icq);
				}

				if ($neues_passwort)
				{
					if ($neues_passwort_best)
					{
						if ($neues_passwort == $neues_passwort_best)
						{
							if ($altes_passwort == $raw[password])
							{
								$output .=  '<p class=green>Das eingegebene Passwort stimmt! Neue Passwörter stimmen überein! => Passwort wurde geändert!</p>';
								$password = $neues_passwort;
							}
							else
							{
								$output .=  '<p class=red>Das eingegebene Passwort ist falsch!</p>';
								$error++;
							}
						}
						else
						{
							$output .=  '<p class=red>Es wurden zwei neue Passwörter eingegeben, sie stimmen aber nicht überein!</p>';
							$error++;
						}

					}
					else
					{
						$output .=  '<p class=red>Es wurde ein neues Passwort eingegeben, aber nicht bestätigt!</p>';
						$error++;
					}
				}
				else
				{
					$password = $raw[password];
				}

				if ($telefon)
				{
					$telefon = trim($telefon);
				}

				if ($handy)
				{
					$handy = trim($handy);
				}

				if ($error)
				{
					$output .= '<p class=red>Es sind Fehler aufgetreten!</p>';
				}
				else
				{

					$geburtstag = date_string2unix ($geburtstag, '9:00');

					$geburtstag = strftime('%Y%m%e', $geburtstag);

					$telefon = $telefon . '##' . $handy;

					$login_name = substr ($v_name, 0 , 1);
					$login_name = $n_name . $login_name;

					$sql = "update " . DB_USERS . " set
							n_name = '$n_name',
							v_name = '$v_name',
							einheit = $einheit,
							rang = $rang_new,
							email = '$email',
							homepage = '$homepage',
							icq = '$icq',
							adresse = '',
							telefon = '$telefon',
							online = $raw[online],
							last_action = $raw[last_action],
							geburtstag = '$geburtstag',
							last_login = $raw[last_login],
							password = '$password',
							login_name = '$login_name',
							funktion = ''
							where id=$id";

					// echo $sql;

					if ($db->query($sql))
					{
						$output = '<p class=green>User <B>' . $v_name . ' ' . $n_name . '</b> erfolgreich eingetragen!</p>';
					}
					else
					{
						$output = '<p class=red>Beim Eintragen des Users ist ein Fehler aufgetreten... )o:</p>';
					}

					$v_name = '';
					$n_name = '';
					$geburtstag = '';
					$email = '';
					$rang_new = '';
					$telefon = '';
					$handy = '';
					$icq = '';
					$homepage = '';
					$einheit = '';
				}
			}

					$v_name = $raw[v_name];
					$n_name = $raw[n_name];
					$geburtstag = strftime('%d.%m.%Y', $raw[geburtstag]);
					$email = $raw[email];
					$rang_new = $raw[rang];

					$temp = explode('##', $raw[telefon]);

					$telefon = $temp[0];
					$handy = $temp[1];
					$icq = $raw[icq];
					$homepage = $raw[homepage];
					$einheit = $raw[einheit];

					if ($UserEditor->admin())
					{
						// echo 'We`re Admin';
						$altes_passwort = $raw[password];
						$disable_rank = '';
						$disable_name = '';
						$pre_disable_rank = '';
					}
					else
					{
						// echo 'We`re not Admin';
						$disable_rank = ' disabled ';
						$pre_disable_rank = '<input type=hidden name=rang_new value=' . $raw[rang] . '>';
						$disable_name = ' disabled><input type=hidden name=n_name value=' . $raw[n_name] . '><input type=hidden name=v_name value=' . $raw[v_name];
					}

				$output .= '
					<form action=' . $PHP_SELF . ' method=get>
						<table width=100% align=center border=0>
							<tr>
								<td colspan=2><b>Daten</b></td>
							</tr>

							<tr>
								<td align=right width=50%>
									Name
								</td>
								<td>
									<input type=text name=n_name value="' . $n_name . '" size=15 ' . $disable_name . '>
								</td>
							</tr>
							<tr>
								<td align=right width=50%>
									Vorname
								</td>
								<td>
									<input type=text name=v_name value="' . $v_name . '" size=15 ' . $disable_name . '>
								</td>
							</tr>
							<tr>
								<td align=right width=50%>
									Geburtstag
								</td>
								<td>
									<input type=text name=geburtstag value="' . $geburtstag . '" size=10>
								</td>
							</tr>

							<tr>
								<td colspan=2>&nbsp;</td>
							</tr>
							<tr>
								<td colspan=2><b>Erreichbarkeit</b></td>
							</tr>

							<tr>
								<td align=right width=50%>
									E-Mail
								</td>
								<td>
									<input type=text name=email  value="' . $email . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50%>
									Homepage
								</td>
								<td>
									<input type=text name=homepage  value="' . $homepage . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50%>
									ICQ
								</td>
								<td>
									<input type=text name=icq  value="' . $icq . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50%>
									Telefon
								</td>
								<td>
									<input type=text name=telefon  value="' . $telefon . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50%>
									Handy
								</td>
								<td>
									<input type=text name=handy value="' . $handy . '" size=15>
								</td>
							</tr>

							<tr>
								<td colspan=2>&nbsp;</td>
							</tr>
							<tr>
								<td colspan=2><b>Passwort</b></td>
							</tr>
							<tr>
								<td align=right width=50%>
									altes Passwort
								</td>
								<td>
									<input type=password name=altes_passwort  value="' . $altes_passwort . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50%>
									neues Passwort
								</td>
								<td>
									<input type=password name=neues_passwort size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50%>
									neues Passwort bestätigen
								</td>
								<td>
									<input type=password name=neues_passwort_best size=15>
								</td>
							</tr>

							<tr>
								<td colspan=2>&nbsp;</td>
							</tr>
							<tr>
								<td colspan=2><b>Funktion</b></td>
							</tr>

							<tr>
								<td align=right width=50%>
									Rang
								</td>
								<td>
									' . $pre_disable_rank . '<select size=1 name=rang_new ' . $disable_rank . '>';

								for ($i = 0; $i < ZUFU; $i++)
								{
									if (($i + 1) == $rang_new)
									{
										$input_string = 'selected';
									}
									else
									{
										$input_string = '';
									}

									$output .= '
										<option
											value=' . ($i + 1) . ' ' . $input_string . '>
											' . $rang[($i + 1)][name] . '
										</option>';
								}

								$output .= '
									<option>------------------------------------</option>
									';
								for ($i = ZUFU; $i < OB; $i++)
								{
									if ( ($i + 1) == $rang_new)
									{
										$input_string = 'selected';
									}
									else
									{
										$input_string = '';
									}

									$output .= '
										<option
											value=' . ($i + 1) . ' ' . $input_string . '>
											' . $rang[($i + 1)][name] . '
										</option>';
								}


					$output .= '
									</select>
								</td>
							</tr>

							<tr>
								<td align=right colspan=2 align=right>

									<input type=hidden name=einheit value=' . $einheit . '>
									<input type=hidden name=area value=user_edit>
									<input type=hidden name=id value=' . $id . '>
									<input type=submit name=submit value="Los">
									<input type=reset value="Zurücksetzen">
								</td>
							</tr>

						</table>
					</form>


							';

				$Box = new InfoBox('User editieren', $output, 0);


			$UserEditor->html_footer();
?>
