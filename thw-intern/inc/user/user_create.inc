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

				if (!$rang_neu)
				{
					$output .= '<p class=red>Kein Rang ausgewählt</p>';
					$error++;
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
								funktion
							)
							values
							(
								'$n_name',
								'$v_name',
								$einheit,
								$rang_neu,
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
					$rang_neu = '';
					$telefon = '';
					$handy = '';
					$icq = '';
					$homepage = '';

				}
			}

				function parse_einheiten ($current)
				{

					for ($i = 0; $i < count($current); $i++)
					{
						if ( $current[$i][assignable] )
						{
							$inputstring = '<input type=radio name=einheit value=' . $current[$i][id] . '>';
						}
						else
						{
							$inputstring = '';
						}
						$output .= '
								<tr>
									<td>
										' . str_repeat ('&nbsp;&nbsp;&nbsp;&nbsp;', $current[$i][indent]) . '
										' . $inputstring . '
										' .
										$current[$i][name]
										. '
									</td>
								</tr>
							';

							if ( is_array($current[$i][einheiten] )  )
							{
								$output .= parse_einheiten ( $current[$i][einheiten] );
							}
					}

					return ( $output );
				}


				$output .= '
					<form action=' . $PHP_SELF . ' method=get>
						<table width=100% align=center border=0>
							<tr bgcolor=#F2F5FF>
								<td colspan=2><b>Daten</b></td>
							</tr>

							<tr>
								<td align=right width=50% bgcolor=#F2F5FF>
									Name
								</td>
								<td>
									<input type=text name=n_name value="' . $n_name . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50% bgcolor=#F2F5FF>
									Vorname
								</td>
								<td>
									<input type=text name=v_name value="' . $v_name . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50% bgcolor=#F2F5FF>
									Geburtstag
								</td>
								<td>
									<input type=text name=geburtstag value="' . $geburtstag . '" size=10>
								</td>
							</tr>

							<tr>
								<td colspan=2>&nbsp;</td>
							</tr>
							<tr bgcolor=#F2F5FF>
								<td colspan=2><b>Erreichbarkeit</b></td>
							</tr>

							<tr>
								<td align=right width=50% bgcolor=#F2F5FF>
									E-Mail
								</td>
								<td>
									<input type=text name=email  value="' . $email . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50% bgcolor=#F2F5FF>
									Homepage
								</td>
								<td>
									<input type=text name=homepage  value="' . $homepage . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50% bgcolor=#F2F5FF>
									ICQ
								</td>
								<td>
									<input type=text name=icq  value="' . $icq . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50% bgcolor=#F2F5FF>
									Telefon
								</td>
								<td>
									<input type=text name=telefon  value="' . $telefon . '" size=15>
								</td>
							</tr>
							<tr>
								<td align=right width=50% bgcolor=#F2F5FF>
									Handy
								</td>
								<td>
									<input type=text name=handy value="' . $handy . '" size=15>
								</td>
							</tr>

							<tr>
								<td colspan=2>&nbsp;</td>
							</tr>
							<tr bgcolor=#F2F5FF>
								<td colspan=2><b>Rang & Einheit</b></td>
							</tr>

							<tr>
								<td align=right bgcolor=#F2F5FF>
									Rang
								</td>
								<td>
									<select size=1 name=rang_neu>';

								for ($i = 0; $i < ZUFU; $i++)
								{
									if ( ( $i + 1 ) == $rang_neu)
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
									<option value=0>------------------------------------</option>
									';
								for ($i = ZUFU; $i < OB; $i++)
								{
									if ( ( $i + 1 ) == $rang_neu)
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
								<td align=right width=50% bgcolor=#F2F5FF>
									Einheit
								</td>
								<td align=right>
									<table width=100% align=center>
												' . parse_einheiten($einheiten) . '
									</table>
								</td>
							</tr>
							<tr bgcolor=#F2F5FF>
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
