<?php

			$DateCreator = new Page('Neuen Termin erstellen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$DateCreator->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=date_overview";
			$menu[1][text] = 'Terminübersicht';

			$DateCreator->pagetitle('Neuen Termin erstellen', $menu);
			// document.forms[0].elements["hoge[]"][0].value
			echo '
				<br>

				<script language="JavaScript">
					function selectAllCheckboxes()
					{

					var msg = "";
						for (i=0; i < ' . 9 . '; i++)
						{
							if (document.all.elements["einheit" + i][i].checked == true)
							{
								document.all.elements["einheit" + i][i].checked=false;
							}
							else
							{
								document.all.elements["einheit" + i][i].checked=true;
							}
						}
					}
				</script>
				';

			if ($submit == 'Weiter')
			{

				// Laden der Einstellungen aus den Definitionen
				$has_unit = $date_types[$type][has_unit];
/*				if (!$has_userlist)
				{
					$has_userlist = $date_types[$type][has_userlist];
				}*/
				$helfer_preset = $date_types[$type][helfer_preset];
				if (!$helfer_preset) { $helfer_preset = 10; }
				$userlist_has_comment = $date_types[$type][userlist_has_comment];

				$output = '
					<form action=' . $PHP_SELF . ' method=post>
						<table width=100% align=center border=0 name=all>';

						$output .= '
							<tr class=bar>
								<td>
									Termin
								</tD>
							</tr>
							<tr>
								<td align=center>
									Begin : 
										<input type=text name=begin_date size=10> 
										<input type=text name=begin_time size=5>
								</td>
							</tr>
							<tr>
								<td align=center>
									Ende : 
										<input type=text name=end_date size=10> 
										<input type=text name=end_time size=5>
								</td>
							</tr>
							<tr class=bar>
								<td>
									Kurzinfo
								</tD>
							</tr>
							<tr>
								<td align=center>
									<input type=text name=heading size=20>
								</td>
							</tr>
							<tr class=bar>
								<td>
									Kommentar
								</tD>
							</tr>
							<tr>
								<td align=center>
									<textarea name=comment cols=50 rows=5>
									</textarea>
								</td>
							</tr>';

						if ($has_userlist)
						{
							$output .= '
							<tr class=bar>
								<td>
									Helfer
								</tD>
							</tr>
							<tr>
								<td align=center>
									Anzahl der benötigten Helfer :
										<input type=text size=3 name=anzahl_helfer value=' . $helfer_preset . '>
										<input type=hidden name=has_userlist value=1>
										<input type=hidden name=userlist_has_comment value=' . $userlist_has_comment . '>
									</td>
								</tr>
								';
						}
						else
						{
							$anzahl_helfer = 0;
							$output .= '
								<tr>
									<td>
										<input type=hidden name=anzahl_helfer value=0>
									</td>
								</tr>
								';
						}

						if ($has_units)
						{
							$output .= '
								<tr class=bar>
									<td>
										Einheit
									</tD>
								</tr>
								<tr>
									<td align=center>
										<table align=center>
											<tr>
												<td colspan=2>
													<input type=checkbox onClick="selectAllCheckboxes()"> Alle auswählen
												</td>
											</tr>
											<tr>
								';


								function parse_einheiten ($current)
								{

									for ($i = 0; $i < count($current); $i++)
									{
// 										echo 'processing : ' . $current[$i][name] . ' id : ' . $current[$i][id] . ' <br>';

										if ( $current[$i][selectable] )
										{
											$inputstring = '<input type=checkbox name="einheit[]" value=' . $current[$i][id] .'>';
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

								$output .= parse_einheiten($einheiten);
								$output .= '
											</tr>
										</table>
										<input type=hidden name=has_units value=1>
									</td>
								</tr>
								';
						}
						else
						{
							$output .= '<input type=hidden name=einheit value=0>';
						}

						if ($has_ort)
						{
							$output .= '<tr class=bar><td>Ort</tD></tr>';
								$output .= '<tr><td align=center>Ort : <input type=text size=10 name=ort value="Unterkunft"></td></tr>';
								$output .= '<input type=hidden name=has_ort value=1>';
						}
						else
						{
							$output .= '<input type=hidden name=ort value=0>';
						}

						if ($has_kleidung)
						{
							$output .= '<tr class=bar><td>Kleidung</tD></tr>';
								$output .= '<tr><td align=center>Vorgeschriebene Kleidung : <input type=text size=20 name=kleidung value="Arbeitsanzug"></td></tr>';
								$output .= '<input type=hidden name=has_kleidung value=1>';
						}
						else
						{
							$output .= '<input type=hidden name=kleidung value=0>';
						}


						$output .= '<tr class=bar><td>Aktionen</tD></tr>';
							$output .= '	<tr><td align=center>
											<input type=hidden name=area value=date_create>
											<input type=hidden name=type value=' . $type . '>
											<input type=hidden name=reserved_id value=' . $reserved_id . '>
											<input type=submit name=submit value="Eintragen">
											<input type=reset value="Zurücksetzen">
											</td></tR>';

				$output .= '</table></form>';

				$box = new InfoBox('Neuen Dienst erstellen', $output, 0, '93%');

			}
			else if ($submit == 'Eintragen')
			{

				$error = 0;

				$output = '<form action=' . $PHP_SELF . ' method=post>
								<table width=100% align=center border=0>';

					if ($begin_time AND $begin_date AND $end_time AND $end_date)
					{
						$output .= '<input type=hidden name=begin_date value="' . $begin_date . '">';
						$output .= '<input type=hidden name=begin_time value="' . $begin_time . '">';
						$output .= '<input type=hidden name=end_date value="' . $end_date . '">';
						$output .= '<input type=hidden name=end_time value="' . $end_time . '">';

						$db_begin = date_string2unix($begin_date, $begin_time);
						$db_end = date_string2unix($end_date, $end_time);
					}
					else
					{
						$error++;
						$headline = 'Fehlende Eingabe!';
						$output .= '<tr class=bar><td>Termin</td></tr>';
						$output .= '<tr><td>Das Datum wurde nicht richtig eingegeben!</td></tr>';
							$output .= '	<tr>
												<td align=center>Begin : <input type=text name=begin_date size=10> <input type=text name=begin_time size=5></td>
											</tr>';
							$output .= '	<tr>
												<td align=center>Ende : <input type=text name=end_date size=10> <input type=text name=end_time size=5></td>
											</tr>';
					}

					if ($heading)
					{
						$heading = $DateCreator->html_filter($heading);
						$output .= '<input type=hidden name=heading value=' . $heading . '>';
					}
					else
					{
						$error++;
						$headline = 'Fehlende Eingabe!';
						$output .= '<tr class=bar><td>Kurzinfo</tD></tr>';
						$output .= '<tr><td>Es wurde kein Kurzinfo eingegeben!</td></tr>';
						$output .= '<tr><td align=center><input type=text name=heading size=20></td></tr>';
					}

					if ($comment)
					{
						$output .= '<input type=hidden name=report value=' . $comment . '>';
						$comment = $DateCreator->html_filter($comment);
					}
					else
					{
						$error++;
						$headline = 'Fehlende Eingabe!';

						$output .= '<tr class=bar><td>Kommentar</tD></tr>';
							$output .= '<tr><td>Es wurde kein Text eingegeben!</td></tr>';
							$output .= '<tr><td align=center><textarea name=comment cols=50 rows=5></textarea>';
					}

					if ($has_userlist)
					{
						// $userlist_has_comment für die Datenbank auf 1 setzen (DB nimmt char nicht an)
						if ($userlist_has_comment)
						{
							$userlist_has_comment = 1;
						}

						$output .= '<input type=hidden name=has_userlist value=1>';
						$output .= '<input type=hidden name=userlist_has_comment value=' . $userlist_has_comment . '>';
							if ($anzahl_helfer)
							{
								$output .= '<input type=hidden name=anzahl_helfer value=' . $anzahl_helfer . '>';
							}
							else
							{
								$error++;
								$headline = 'Fehlende Eingabe!';
								$output .= '<tr class=bar><td>Helfer</tD></tr>';
								$output .= '<tr><td>Es wurde keine Helferanzahl eingegeben!</td></tr>';
								$output .= '<tr><td align=center><input type=text name=anzahl_helfer size=3></td></tr>';
							}
					}

					if ($has_ort)
					{
						$output .= '<input type=hidden name=has_ort value=1>';
							if ($ort)
							{
								$output .= '<input type=hidden name=ort value=' . $ort . '>';
							}
							else
							{
								$error++;
								$headline = 'Fehlende Eingabe!';
								$output .= '<tr class=bar><td>Ort</tD></tr>';
								$output .= '<tr><td>Es wurde kein Ort eingegeben!</td></tr>';
								$output .= '<tr><td align=center><input type=text name=ort size=10></td></tr>';
							}
					}

					if ($has_kleidung)
					{
						$output .= '<input type=hidden name=has_kleidung value=1>';
							if ($kleidung)
							{
								$output .= '<input type=hidden name=ort value=' . $ort . '>';
							}
							else
							{
								$error++;
								$headline = 'Fehlende Eingabe!';
								$output .= '<tr class=bar><td>Kleidung</tD></tr>';
								$output .= '<tr><td>Es wurde keine Kleidung eingegeben!</td></tr>';
								$output .= '<tr><td align=center><input type=text name=Kleidung size=20></td></tr>';
							}
					}

					if ($has_units)
					{
						$output .= '<input type=hidden name=has_units value=1>';
							if ($einheit)
							{
								// Zuerst einmal alle Einheiten ausfindig machen die dabei sind!

								$entered_units = array();

								// echo 'Folgende Einheiten wurden gewählt : <br>';


								$counter = 0;
								for ( $i = 0; $i < count( $einheit ); $i++)
								{
									$sql = "select id from " . DB_USERS . " where einheit=" . $einheit[$i];
									// echo ' <b> ' . $sql . '</b> <br>';
									$raw = $db->query($sql);
									if ($db->num_rows($raw))
									{
										while ( $current = $db->fetch_array($raw) )
										{
											$sql = "insert into " . DB_USERLISTS . "
												(ref_id, user_id) values
												($reserved_id, $current[id])
												";
											// echo $sql . '<br>';

											$db->query($sql);
											$counter++;
										}
									}

								}

/*								$db_units = implode('#', $einheit);
								$output .= '<input type=hidden name=einheiten value=' . $einheit . '>';*/
							}
							else
							{
								$error++;
								$headline = 'Fehlende Eingabe!';
								$output .= '<tr class=bar><td>Einheit</tD></tr>';
								$output .= '<tr><td>Es wurde keine Einheit eingegeben!</td></tr>';
								$output .= '<tr><td align=center>asdf</td></tr>';
							}
					}
					if ($error)
					{
						$output .= '<tr><td><b>Bitte die fehlenden Eingaben nachholen!</b></td></tr>';
						$output .= '<tr class=bar><td>Aktionen</td></tr>';

							$output .= '	<tr><td align=center>
											<input type=hidden name=area value=date_add>
											<input type=hidden name=type value=' . $type . '>
											<input type=hidden name=reserved_id value=' . $reserved_id . '>
											<input type=submit name=submit value="Eintragen">
											<input type=reset value="Zurücksetzen">
											</td></tR>';

						$output .= '</table></form>';

					}
					else
					{
						$headline = 'Termin wird eingetragen!';
						$sql = "update " . DB_DATES . " set
													info='" . $heading . "',
													comment='$comment',
													creator=$user_id,
													type=$type,
													einheit='$db_units',
													unfinished=0,
													anzahl_helfer=$anzahl_helfer,
													userlist_has_comment='$userlist_has_comment',
													ort='$ort',
													kleidung='$kleidung',
													begin=$db_begin,
													end=$db_end
													where id=$reserved_id";
						// $output .= 'sql : ' . $sql;

						$db->query($sql);

						$message = 'Ein neuer <a href="index.php?area=date_view&id=' . $reserved_id . '" class=blue>Dienst</a> (' . $date_types[$type][name] . ') wurde von ' . $user_v_name . ' ' . $user_n_name . ' am ' . strftime('%e.%m.%Y um %H:%M') . ' angelegt! Details gibt`s <a href="' . $PHP_SELF . '?area=date_view&id=' . $reserved_id . '" class=blue>hier</a>!';
						$sql = "insert into " . DB_NEWS . " (
											creator,
											date,
											heading,
											message,
											unfinished) values (
											$user_id,
											NULL,
											'Neuer Dienst',
											'$message',
											0)";

						$db->query($sql);
						$output .= '</table></form>';
						$menu = array();
						$menu[right][text] = 'Fertig';
						$menu[right][link] = "$PHP_SELF?area=date_overview";
						$menu[left][text] = 'weiteren Termin erstellen';
						$menu[left][link] = "$PHP_SELF?area=date_create";
						
						$output = 'Es wurden insgesamt <b>' . $counter . '</b> Helfer automatisch eingetragen!';
					}

					$InputBox = new InfoBox($headline, $output, $menu, '90%');

			}
			else
			{
					$reserved_id = reserve_id($db);

					$output = '<b>Bitte den Typ des neuen Termins wählen : </b><br>';

					$output .= '
								<form action=' . $PHP_SELF . ' method=get>
									<center><select size=1 name=type>
								';
					for ($i = 0; $i < count($date_types); $i++)
					{
						$output .= '<option value=' . ($i) . '> ' . $date_types[$i][name] . '</option>';
					}

					$output .= '	</select></center><br>';

					$output .= '<b>Bitte Zusätzliche Felder auswählen : </b>
									<table border=0 align=center>
										<tr>
											<td><input type=checkbox name=has_userlist></td>
											<td>Userliste</td>
										</tr>
										<tr>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name=userlist_has_comment></td>
											<td>mit Extra-Feldern</td>
										</tr>
										<tr>
											<td><input type=checkbox name=has_units></td>
											<td>Einheit (deaktiviert weil nich fertig!)</td>
										</tr>
										<tr>
											<td><input type=checkbox name=has_ort></td>
											<td>Ort</td>
										</tr>
										<tr>
											<td><input type=checkbox name=has_kleidung></td>
											<td>Kleidung</td>
										</tr>
									</table>
										<input type=hidden name=area value=date_create>
										<input type=hidden name=reserved_id value=' . $reserved_id . '>
										<table align=right><tr><td><input type=submit name=submit value="Weiter"></td>
								</tr>
									<td>
										<b>Hinweis : </b><br>
											Manche Dienste setzen automatisch Userlisten o.ä. .
										</ul>
									</td>
								<tr>
								</tr>
									<td>
										<b>Extra-Felder : </b><br>
											Jeder der sich einträgt kann noch einen kurzen <br>
											Kommentar dazu geben, beispielsweise weitere <br>Personen die er mitbringt o.ä. .
										</ul>
									</td>
								<tr>
								</tr>
						</table>
								';

					$output .= '
								</form>';

					$box = new InfoBox('Typ des Termins', $output, 0);

			}

			$DateCreator->html_footer();
?>
