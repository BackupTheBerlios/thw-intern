<?php

			$NewsEditor = new Page('Beitrag editieren', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$NewsEditor->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=report_overview";
			$menu[1][text] = 'Berichte';
			$menu[2][link] = "$PHP_SELF?area=photo_editcatalog&id=$id";
			$menu[2][text] = 'Photokatalog editieren';
			$menu[3][link] = "$PHP_SELF?area=photo_editcatalog&id=$id&add_photo=1";
			$menu[3][text] = 'Photo hinzufügen';

			if ($submit)
			{

				if ($heading)
				{
					$output .= '
									<tr>
										<td colspan=2><input type=hidden name=message value="' . $heading . '"></td>
									</tr>
									';
				}
				else
				{
					$output .= '
									<tr>
										<td align=right>
											Titel :
										</td>
										<td><input type=text size=20 name=heading></td>
									</tr>';

					$title = 'Fehlende Eingabe';
					$error++;
				}

				if ($message)
				{
					$output .= '
									<tr>
										<td colspan=2><input type=hidden name=message value="' . $message . '"></td>
									</tr>
									';
				}
				else
				{
					$output .= '
									<tr>
										<td align=right>
											Nachricht :
										</td>
										<td><textarea cols=40 rows=5 name=message></textarea></td>
									</tr>';

					$title = 'Fehlende Eingabe';
					$error++;

				}

				if ($error)
				{
					$output .=  '
									<tr>
										<td align=center colspan=2>
											<input type=hidden name=area value=news_add>
											<input type=submit name=submit value="Los">
											<input type=reset value="Zurücksetzten">
										</td>
									</tr>
								</table>
							</form>';
				}
				else
				{
					$output .=

					$heading = $NewsEditor->html_filter($heading);
					$message = $NewsEditor->html_filter($message);

					// $sql = "insert into " . DB_NEWS . " (heading, message, creator, unfinished, date) values ('$heading', '$message', $user_id, 0, NULL)";
					$sql = "update " . DB_NEWS . " set ... where id=$id";

					// $db->query($sql);
					echo $sql;

					$title = 'Eingetragen!';
					$output = 'Die Nachricht wurde erfolgreich eingetragen!';
					$menu[right][text] = 'Fertig';
					$menu[right][link] = "$PHP_SELF";
				}

			}
			else
			{
					$sql = "select id, heading, message, creator, unix_timestamp(date) as date from " . DB_NEWS . " where unfinished=0 and id=$id";
					$current = $db->fetch_array($db->query($sql));

					$output = '
							<form action=' . $PHP_SELF . ' method=post>
								<table width=100% align=center border=0>
									<tr>
										<td align=right>
											Titel :
										</td>
										<td><input type=text size=20 name=heading value="' . $current[heading] . '"></td>
									</tr>
									<tr>
										<td align=right>
											Nachricht :
										</td>
										<td><textarea cols=40 rows=5 name=message>' . $current[message] . '</textarea></td>
									</tr>
									<tr>
										<td align=center colspan=2>
											<input type=hidden name=area value=news_add>
											<input type=submit name=submit value="Los">
											<input type=reset value="Zurücksetzten">
										</td>
									</tr>
								</table>
							</form>';
					$title = 'News editieren';
					$menu = 0;
			}
			$Box = new InfoBox($title , $output, $menu);

			$NewsEditor->html_footer();
?>
