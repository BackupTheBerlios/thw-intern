<?php

			$ReportCreator = new Page('Bericht hinzufügen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$ReportCreator->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=report_overview";
			$menu[1][text] = 'Berichte';

			$ReportCreator->pagetitle('Bericht erstellen', $menu);

			echo '<br>';

			if ($submit)
			{

					$error = 0;

					$output = '<form action=' . $PHP_SELF . ' method=post>
									<table width=100% align=center border=0>';

						if ($heading)
						{
							$output .= '<input type=hidden name=heading value=' . $heading . '>';
							$heading = $ReportCreator->html_filter($heading);
						}
						else
						{
							$error++;
							$headline = 'Fehlende Eingabe!';
							$output .= '<tr class=bar><td>Überschrift</tD></tr>';
							$output .= '<tr><td>Es wurde keine Überschrift eingegeben!</td></tr>';
							$output .= '<tr><td align=center><input type=text name=heading size=20></td></tr>';
						}

						if ($report)
						{
							$output .= '<input type=hidden name=report value=' . $report . '>';
							$report = $ReportCreator->html_filter($report);
						}
						else
						{
							$error++;
							$headline = 'Fehlende Eingabe!';

							$output .= '<tr class=bar><td>Bericht</tD></tr>';
								$output .= '<tr><td>Es wurde kein Text eingegeben!</td></tr>';
								$output .= '<tr><td align=center><textarea name=report cols=50 rows=15></textarea>';
						}

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


						if ($error)
						{
							$output .= '<tr><td><b>Bitte die fehlenden Eingaben nachholen!</b></td></tr>';
							$output .= '<tr class=bar><td>Aktionen</td></tr>';

								$output .= '	<tr><td align=center>
												<input type=hidden name=area value=report_add>
												<input type=hidden name=reserved_id value=' . $reserved_id . '>
												<input type=submit name=submit value="Eintragen">
												<input type=reset value="Zurücksetzen">
												</td></tR>';

							$output .= '</table></form>';

						}
						else
						{
							$headline = 'Bericht wird eingetragen!';
							$sql = "update " . DB_REPORTS . " set heading='" . $heading . "', report='$report', creator=$user_id, type=$type, public=$public, unfinished=0, begin=$db_begin, end=$db_end where id=$reserved_id";
							$output .= 'sql : ' . $sql;

							$db->query($sql);
							$output .= '</table></form>';
							$menu = array();
							$menu[left][text] = 'Bilder hinzufügen';
							$menu[left][link] = "$PHP_SELF?area=photo_editcatalog&id=$reserved_id&add_photo=1";
							$menu[right][text] = 'Fertig';
							$menu[right][link] = "$PHP_SELF?area=report_overview";
						}

						$InputBox = new InfoBox($headline, $output, $menu, '95%');
			}
			else
			{
						$reserved_id = reserve_id_reports($db);

						$output = '<form action="' . $PHP_SELF . '" method=post>
										<table width=100% align=center border=0>';
							$output .= '<tr class=bar><td>Termin</tD></tr>';
								$output .= '<tr><td align=center>Begin : <input type=text name=begin_date size=10> <input type=text name=begin_time size=5></td></tr>';
								$output .= '<tr><td align=center>Ende : <input type=text name=end_date size=10> <input type=text name=end_time size=5></td></tr>';

							$output .= '<tr class=bar><td>Typ</tD></tr>';

								$output .= '<tr><td align=center>Typ : <select size=1 name=type>';

									for ($i = 0; $i < count($report_types); $i++)
									{
										$output .= '<option value=' . ($i + 1) . '>' . $report_types[$i + 1][name] . '</option>';
									}

								$output .= '</select> Öffentlich : <select size=1 name=public><option value=1>Ja</option><option value=0>Nein</option></select></td></tr>';

							$output .= '<tr class=bar><td>Überschrift</tD></tr>';
								$output .= '<tr><td align=center><input type=text name=heading size=20></td></tr>';

							$output .= '<tr class=bar><td>Bericht</tD></tr>';
								$output .= '<tr><td align=center><textarea name=report cols=50 rows=15></textarea>';


							$output .= '<tr class=bar><td>Aktionen</tD></tr>';

								$output .= '	<tr><td align=center>
												<input type=hidden name=area value=report_add>
												<input type=hidden name=reserved_id value=' . $reserved_id . '>
												<input type=submit name=submit value="Eintragen">
												<input type=reset value="Zurücksetzen">
												</td></tR>';

						$output .= '</table>
									</form>';
						$InputBox = new InfoBox('Bericht eingeben', $output, 0, '95%');
			}

			$ReportCreator->html_footer();
?>
