<?php

			$DateEditor = new Page('Termin editieren', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$DateEditor->html_header();

			if (!$reserved_id)
			{
				$reserved_id = $id;
			}

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=date_overview";
			$menu[1][text] = 'Terminübersicht';
			$menu[2][link] = "$PHP_SELF?area=date_view&id=$reserved_id";
			$menu[2][text] = 'Zurück zum Termin';

			$sql = "select *, unix_timestamp(begin), unix_timestamp(end) from " . DB_DATES . " where id = $id";
			$date_array = $db->fetch_array($db->query($sql));

			$sql = "select id, name from " . DB_DATETYPES . " order by id";
			$date_types_raw = $db->query($sql);

			$i = 0;
			while ($current = $db->fetch_array($date_types_raw ))
			{
				$i++;
				$date_types[$i] = $current[name];
			}

			$DateEditor->pagetitle('Termin editieren', $menu);
			echo '<br>';

			if ($submit == 'Eintragen')
			{
				$error = 0;

				$output = '	
							<form action=' . $PHP_SELF . ' method=post>
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
						$heading = $DateEditor->html_filter($heading);
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
						$comment = $DateEditor->html_filter($comment);
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
						$output .= '<input type=hidden name=has_userlist value=1>';
							if ($anzahl_helfer)
							{
								$output .= '<input type=hidden name=anzahl_helfer value=' . $anzahl_helfer . '>
											<input type=hidden name=helfer value=' . $helfer . '>';
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
								$db_units = implode('#', $einheit);
								$output .= '<input type=hidden name=einheiten value=' . $einheit . '>';
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
						$headline = 'Termin wird geändert!';
						$sql = "update " . DB_DATES . " set
													info='" . $heading . "',
													comment='$comment',
													creator=$user_id,
													type=$type,
													einheit='$db_units',
													unfinished=0,
													anzahl_helfer=$anzahl_helfer,
													ort='$ort',
													kleidung='$kleidung',
													begin=$db_begin,
													end=$db_end
													where id=$reserved_id";
						$output .= 'sql : ' . $sql;

						$db->query($sql);

						$message = 'Der <a href="index.php?area=date_view&id=' . $reserved_id . '" class=blue>Termin</a> (' . $date_types[$type] . ') wurde von ' . $user_v_name . ' ' . $user_n_name . ' am ' . strftime('%e.%m.%Y um %H:%M') . ' geändert! Details gibt`s <a href="' . $PHP_SELF . '?area=date_view&id=' . $reserved_id . '" class=blue>hier</a>!';
						$sql = "insert into " . DB_NEWS . " (
											creator,
											date,
											heading,
											message,
											unfinished) values (
											$user_id,
											NULL,
											'Geänderter Termin',
											'$message',
											0)";

						$db->query($sql);
						$output .= '</table></form>';
						$menu = array();
						$menu[right][text] = 'Fertig';
						$menu[right][link] = "$PHP_SELF?area=date_view&id=$reserved_id";
						$menu[left][text] = 'Zurück zum Termin';
						$menu[left][link] = "$PHP_SELF?area=date_view&id=$reserved_id";
					}

				$box = new InfoBox('Termin editieren', $output, $menu, '93%');
			}
			else
			{

				$output = '<form action=' . $PHP_SELF . ' method=post>
					<table width=100% align=center border=0>';

						$output .= '<tr class=bar><td>Termin</tD></tr>';
							$output .= '<tr><td align=center>Begin : <input type=text name=begin_date size=10 value="' . strftime('%e.%m.%Y', $date_array[begin]) . '"> <input type=text name=begin_time size=5 value="' . strftime('%H:%M', $date_array[begin]) . '"></td></tr>';
							$output .= '<tr><td align=center>Ende : <input type=text name=end_date size=10 value="' . strftime('%e.%m.%Y', $date_array[end]) . '"> <input type=text name=end_time size=5 value="' . strftime('%H:%M', $date_array[end]) . '"></td></tr>';

						$output .= '<tr class=bar><td>Kurzinfo</tD></tr>';
							$output .= '<tr><td align=center><input type=text name=heading size=20 value="' . $date_array[info] . '"></td></tr>';

						$output .= '<tr class=bar><td>Kommentar</tD></tr>';
							$output .= '<tr><td align=center><textarea name=comment cols=50 rows=5>' . $date_array[comment] . '</textarea></td></tr>';

						if ($date_array[anzahl_helfer])
						{
							$output .= '<tr class=bar><td>Helfer</tD></tr>';
							switch($date_array[type])
							{
								case 3:
								case 9:
									$output .= '<tr><td align=center>Dieser Dienst beinhaltet zwar eine Userliste, diese kann aber nicht geändert werden!</td></tr>';
									$output .= '<input type=hidden name=has_userlist value=1>';
									$output .= '<input type=hidden name=anzahl_helfer value=' . $date_array[anzahl_helfer] . '>';
									$output .= '<input type=hidden name=helfer value=' . $date_array[helfer] . '>';
									break;
								default:
										$output .= '<tr><td align=center>Anzahl der benötigten Helfer : <input type=text size=3 name=anzahl_helfer value=' . $date_array[anzahl_helfer] . '></td></tr>';
										$output .= '<input type=hidden name=has_userlist value=1>';
							}
						}
						else
						{
							$anzahl_helfer = 0;
							$output .= '<input type=hidden name=anzahl_helfer value=0>
										<input type=hidden name=helfer value=0>';
						}

						if ($date_array[einheit])
						{
/*							$output .= '<tr class=bar><td>Einheit</tD></tr>';
								$output .= '<tr><td align=center><table width=100% align=center><tr><td width=50% valign=top><table width=100% align=center>';

								$output .= print_unit_checkbox($db, 1, 'einheit[]');

								$output .= '</table></td><td valign=top><table widtH=100% align=center>';

								$output .= print_unit_checkbox($db, 2, 'einheit[]');

								$output .= '</table></td></tr></table></td></tr>';
								$output .= '<input type=hidden name=has_units value=1>';
*/
						}
						else
						{
							$output .= '<input type=hidden name=einheit value=0>';
						}

						if ($date_array[ort])
						{
							$output .= '<tr class=bar><td>Ort</tD></tr>';
								$output .= '<tr><td align=center>Ort : <input type=text size=10 name=ort value="' . $date_array[ort] . '"></td></tr>';
								$output .= '<input type=hidden name=has_ort value=1>';
						}
						else
						{
							$output .= '<input type=hidden name=ort value=0>';
						}

						if ($date_array[kleidung])
						{
							$output .= '<tr class=bar><td>Kleidung</tD></tr>';
								$output .= '<tr><td align=center>Vorgeschriebene Kleidung : <input type=text size=20 name=kleidung value="' . $date_array[kleidung] . '"></td></tr>';
								$output .= '<input type=hidden name=has_kleidung value=1>';
						}
						else
						{
							$output .= '<input type=hidden name=kleidung value=0>';
						}


						$output .= '<tr class=bar><td>Aktionen</tD></tr>';
							$output .= '	<tr><td align=center>
											<input type=hidden name=area value=date_edit>
											<input type=hidden name=type value=' . $date_array[type] . '>
											<input type=hidden name=reserved_id value=' . $id . '>
											<input type=submit name=submit value="Eintragen">
											<input type=reset value="Zurücksetzen">
											</td></tR>';

				$output .= '</table></form>';

				$box = new InfoBox('Termin editieren', $output, 0, '93%');

			}


			$DateEditor->html_footer();
?>
