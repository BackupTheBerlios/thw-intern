<?php

			$NewsCreator = new Page('News-hinzufügen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$NewsCreator->html_header();

			$news = $db->query($sql);

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=news_add";
			$menu[1][text] = 'News hinzufügen';

			$NewsCreator->pagetitle('News hinzufügen', $menu);

			echo '<br>';


			if ($submit)
			{
				$error = 0;

				if ($heading)
				{
					$output = '
							<form action=' . $PHP_SELF . ' method=post>
								<table width=100% align=center border=0>
									<tr>
										<td colspan=2><input type=hidden name=heading value="' . $heading . '"></td>
									</tr>
									';
				}
				else
				{
					$title = 'Fehlende Eingabe';
					$error++;
					$output = '
							<form action=' . $PHP_SELF . ' method=post>
								<table width=100% align=center border=0>
									<tr>
										<td align=right>
											Titel :
										</td>
										<td><input type=text size=20 name=heading></td>
									</tr>';

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

					$heading = $NewsCreator->html_filter($heading);
					$message = $NewsCreator->html_filter($message);

					$sql = "insert into " . DB_NEWS . " (heading, message, creator, unfinished, date) values ('$heading', '$message', $user_id, 0, NULL)";

					$db->query($sql);
					// echo $sql;

					$title = 'Eingetragen!';
					$output = 'Die Nachricht wurde erfolgreich eingetragen!';
					$menu[right][text] = 'Fertig';
					$menu[right][link] = "$PHP_SELF";
				}

			}
			else
			{
					$output = '
							<form action=' . $PHP_SELF . ' method=post>
								<table width=100% align=center border=0>
									<tr>
										<td align=right>
											Titel :
										</td>
										<td><input type=text size=20 name=heading></td>
									</tr>
									<tr>
										<td align=right>
											Nachricht :
										</td>
										<td><textarea cols=40 rows=5 name=message></textarea></td>
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
					$title = 'News hinzufügen';
					$menu = 0;
			}
			$Box = new InfoBox($title , $output, $menu);

			$NewsCreator->html_footer();
?>
