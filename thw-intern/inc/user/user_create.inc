<?php


			$UserCreator = new Page('User erstellen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$UserCreator->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=user";
			$menu[1][text] = 'Userverwaltung';

			$UserCreator->pagetitle('User erstellen', $menu);
			echo '<br>';


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

					$new_pass = md5(date("D,G,s,l", time()));
					$password = substr($new_pass, 1, 8);

					$geburtstag = strftime('%Y%m%e', $geburtstag);

					$telefon = $telefon . '##' . $handy;

					$login_name = substr ($v_name, 0 , 1);
					$login_name = $n_name . $login_name;

					$sql = "insert into " . DB_USERS . " (
							n_name,
							v_name,
							einheit,
							rang,
							email,
							homepage,
							icq,
							adresse,
							telefon,
							online,
							last_action,
							geburtstag,
							last_login,
							password,
							login_name,
							funktion)
							values
							(
							'$n_name',
							'$v_name',
							'',
							$rang,
							'$email',
							'$homepage',
							'$icq',
							'',
							'$telefon',
							0,
							0,
							'$geburtstag',
							0,
							'$password',
							'$login_name',
							''
							)";

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
					$rang = '';
					$telefon = '';
					$handy = '';
					$icq = '';
					$homepage = '';

				}
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
									<input type=text name=n_name value="' . $n_name . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50%>
									Vorname
								</td>
								<td>
									<input type=text name=v_name value="' . $v_name . '" size=15>
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
								<td colspan=2><b>Funktion</b></td>
							</tr>

							<tr>
								<td align=right width=50%>
									Rang
								</td>
								<td>
									<select size=1 name=rang>';

								$sql = "select name, id from " . DB_RANG . " order by rang";
								$tmp = $db->query($sql);

								while ($current = $db->fetch_array($tmp))
								{
									if ($current[id] == $rang)
									{
										$input_string = 'selected';
									}
									else
									{
										$input_string = '';
									}
									$output .= '
										<option value=' . $current[id] . ' ' . $input_string . '>' . $current[name] . '</option>';
								}

					$output .= '
									</select>
								</td>
							</tr>

							<tr>
								<td align=right colspan=2 align=right>

									<input type=hidden name=area value=user_create>
									<input type=submit name=submit value="Los">
									<input type=reset value="Zurücksetzen">
								</td>
							</tr>

						</table>
					</form>


							';

				$Box = new InfoBox('User erstellen', $output, 0);


			$UserCreator->html_footer();
?>
