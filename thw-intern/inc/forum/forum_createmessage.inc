<?php

			$ForumCreateMessage = new Page('Forum : Nachricht erstellen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$ForumCreateMessage->html_header();

			$news = $db->query($sql);

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=forum_overview";
			$menu[1][text] = 'Forenübersicht';
			$menu[2][link] = "$PHP_SELF?area=forum_view&id=";
			$menu[2][text] = 'Zurück zum Forum';

			$ForumCreateMessage->pagetitle('Forum : Nachricht erstellen', $menu);

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

					$heading = $ForumCreateMessage->html_filter($heading);
					$message = $ForumCreateMessage->html_filter($message);

					// $sql = "insert into " . DB_NEWS . " (heading, message, creator, unfinished, date) values ('$heading', '$message', $user_id, 0, NULL)";
					$sql = "update " . DB_FORUM . " set forum_id=$id, heading='$heading', date='" . strftime('%Y-%m-%e %H:%M:%S', time()) . "', ref_id=$ref_id, message='$message', owner=$user_id, counter=0, unfinished=0 where id=$reserved_id";
					// echo $sql;

					$db->query($sql);


					$title = 'Eingetragen!';
					$output = 'Die Nachricht wurde erfolgreich eingetragen!';
					$menu[right][text] = 'Fertig';
					$menu[right][link] = "$PHP_SELF?area=forum_view&id=$id";
				}

			}
			else
			{
				$title = 'Neuer Beitrag';
				$heading = '';
				if ($ref_id)			// Wir erstellen eine Antwort!
				{
					$sql = "select *, unix_timestamp(date) as unix_stamp from " . DB_FORUM . " where id=$ref_id";
					$parent = $db->fetch_array($db->query($sql));
					$title = 'Antwort auf <i>' . $parent[heading] . '</i>';

					$sql = "select v_name, n_name from " . DB_USERS . " where id=$parent[owner]";
					$parent_owner = $db->fetch_array($db->query($sql));
				}

				$sql = "insert into " . DB_FORUM . " (unfinished, date, last_viewed) values (1, '" . strftime('%Y-%m-%e %H:%M:%S'). "', NULL)";

				$db->query($sql);
				$sql = "select last_insert_id() as reserved_id";

				$tmp = $db->fetch_array($db->query($sql));
				$reserved_id = $tmp[reserved_id];

				$output = '';
					if ($ref_id)
					{
						$output .= '
								<table width=100% align=center border=0>
									<tr class=bar>
										<td>
											von ' . $parent_owner[v_name] . ' ' . $parent_owner[n_name] . ' am ' . strftime('%e.%m.%Y %H:%M', $parent[unix_stamp]) . '
										</td>
									</tr>
									<tr>
										<td bgcolor=#F2F5FF>
											<i>
												' . nl2br($parent[message]) . '
											</i>
										</td>
									</tr>
								</table><br>';
								$heading = 'Re : ' . $parent[heading];
					}

					$output .= '
							<form action=' . $PHP_SELF . ' method=post>
								<table width=100% align=center border=0>
									<tr>
										<td align=right>
											Titel :
										</td>
										<td><input type=text size=20 name=heading value="' . $heading . '"></td>
									</tr>
									<tr>
										<td align=right>
											Nachricht :
										</td>
										<td><textarea cols=50 rows=6 name=message>
										--
' . $user_v_name . '</textarea></td>
									</tr>
									<tr>
										<td align=center colspan=2>
											<input type=hidden name=area value=forum_createmessage>
											<input type=hidden name=reserved_id value=' . $reserved_id . '>
											<input type=hidden name=id value=' . $id . '>
											<input type=hidden name=ref_id value=' . $ref_id . '>
											<input type=submit name=submit value="Los">
											<input type=reset value="Zurücksetzten">
										</td>
									</tr>
								</table>
							</form>';

					$menu = 0;
					$width = '90%';
			}
			$Box = new InfoBox($title , $output, $menu, $width);


			$ForumCreateMessage->html_footer();
?>
