<?php

			$ForumCreateForum = new Page('Forum erstellen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$ForumCreateForum->html_header();

			$news = $db->query($sql);

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=forum_overview";
			$menu[1][text] = 'Forenübersicht';

			$ForumCreateForum->pagetitle('Forum erstellen', $menu);

			echo '<br>';

			if ($submit)
			{
				$error = 0;

				if ($name)
				{
					$output = '
							<form action=' . $PHP_SELF . ' method=post>
								<table width=100% align=center border=0>
									<tr>
										<td align=right>
											Titel :
										</td>
										<td><input type=hidden name=name value="' . $name . '">' . $name . '</td>
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
										<td><input type=text size=20 name=name></td>
									</tr>';


				}

				if ($description)
				{
					$output .= '
									<tr>
										<td colspan=2><input type=hidden name=description value="' . $description . '"></td>
									</tr>
									';
				}
				else
				{
					$output .= '
									<tr>
										<td align=right>
											Beschreibung :
										</td>
										<td><input type=text size=20 name=description></td>
									</tr>';

					$title = 'Fehlende Eingabe';
					$error++;

				}

				if ($error)
				{
					$sql = "select id, v_name, n_name from " . DB_USERS . " order by n_name";
					$users_raw = $db->query($sql);

					$output .=  '
									<tr>
										<td align=right>
											Admin :
										</td>
										<td>
											<select size=1 name=admin>';

											while ($current_user = $db->fetch_array($users_raw))
											{
												if ($current_user[id] == $admin)
												{
													$insert = ' selected ';
												}
												$output .= "<option value=$current_user[id] $insert>$current_user[n_name] $current_user[v_name]</option>";
												$insert = '';
											}

										$output .= '
											</select>
										</td>
									</tr>
									<tr>
										<td align=center colspan=2>
											<input type=hidden name=area value=forum_createforum>
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

					$name = $ForumCreateForum->html_filter($name);
					$description = $ForumCreateForum->html_filter($description);

					// $sql = "insert into " . DB_NEWS . " (heading, message, creator, unfinished, date) values ('$heading', '$message', $user_id, 0, NULL)";
					// $sql = "update " . DB_FOREN . " set forum_id=$id, heading='$heading', date=" . time() . ", refid=$ref_id, message='$message', owner=$user_id, counter=0, unfinished=0 where id=$reserved_id";
					$sql = "insert into " . DB_FOREN . " (forum_name, forum_description, forum_admin) values ('$name', '$description', $admin)";
					

					$db->query($sql);

					$title = 'Eingetragen!';
					$output = 'Das Forum <b>' . $name . '</b> wurde erstellt!';
					$menu[right][text] = 'Zur Forenübersicht';
					$menu[right][link] = "$PHP_SELF?area=forum_overview";
				}

			}
			else
			{
				$title = 'Neues Forum';
					$sql = "select id, v_name, n_name from " . DB_USERS . " order by n_name";
					$users_raw = $db->query($sql);

					$output .= '
							<form action=' . $PHP_SELF . ' method=post>
								<table width=100% align=center border=0>
									<tr>
										<td align=right>
											Titel :
										</td>
										<td><input type=text size=20 name=name></td>
									</tr>
									<tr>
										<td align=right>
											Beschreibung :
										</td>
										<td><input type=text size=20 name=description></td>
										</td>
									</tr>
									<tr>
										<td align=right>
											Admin :
										</td>
										<td>
											<select size=1 name=admin>';

											while ($current_user = $db->fetch_array($users_raw))
											{
												$output .= "<option value=$current_user[id]>$current_user[n_name] $current_user[v_name]</option>";
											}

										$output .= '
											</select>
										</td>
									</tr>
									<tr>
										<td align=center colspan=2>
											<input type=hidden name=area value=forum_createforum>
											<input type=submit name=submit value="Los">
											<input type=reset value="Zurücksetzten">
										</td>
									</tr>
								</table>
							</form>';

					$menu = 0;

			}
			$Box = new InfoBox($title , $output, $menu, $width);


			$ForumCreateForum->html_footer();
?>
