<?php

			$DateView = new Page('Termin', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$DateView->html_header();

			$sql = "select *, unix_timestamp(created) as new_date from " . DB_DATES . " where id = $id";
			$current = $db->fetch_array($db->query($sql));

			$owner = 0;

			if ($user_id == $current[creator] )
			{
				$owner = 1;
			}
			if ( $owner or $DateView->admin() )
			{
				$admin = 1;
			}


			if ($user_add)
			{
					// Zuerst überprüfen ob der User bereits eingetragen ist!
					$sql = "select user_id from " . DB_USERLISTS . " where ref_id = $id and user_id = $user_add";
					// echo 'prüfe ob user schon eingetragen ist : ' . $sql . '<br>';
					if ($db->num_rows($db->query($sql)))
					{		// User ist bereits eingetragen
						echo '<p class=red>Whoups, der User ist bereits eingetragen!</p>';
					}
					else	// User ist noch nicht eingetragen, also weiter
					{
						$sql = "insert into " . DB_USERLISTS . " (ref_id, user_id, comment) values ($id, $user_add, '$comment')";
						// echo 'user wird eingetragen : ' . $sql . '<br>';
						$db->query($sql);
					}
			}

			if ($remove_user)
			{
					$sql = "delete from " . DB_USERLISTS . " where ref_id = $id and user_id = $remove_user";
					$db->query($sql);
			}

			switch($search)
			{
				case 'date_prev':
					$sql = "select id from " . DB_DATES . " where begin < $time and unfinished = 0 order by begin desc limit 1";

					$tmp = $db->query($sql);

					if ($db->num_rows($tmp))
					{
						$tmp = $db->fetch_array($tmp);
						$id = $tmp[id];
					}
					break;

				case 'date_next':
					$sql = "select id from " . DB_DATES . " where begin > $time and unfinished = 0 order by begin desc limit 1";

					$tmp = $db->query($sql);

					if ($db->num_rows($tmp))
					{
						$tmp = $db->fetch_array($tmp);
						$id = $tmp[id];
					}
					break;

				case 'type_next':
					$sql = "select id from " . DB_DATES . " where begin > $time and unfinished = 0 and type = $type order by begin desc limit 1";

					$tmp = $db->query($sql);

					if ($db->num_rows($tmp))
					{
						$tmp = $db->fetch_array($tmp);
						$id = $tmp[id];
					}
					break;
				case 'type_prev':
					$sql = "select id from " . DB_DATES . " where begin < $time and unfinished = 0 and type = $type order by begin desc limit 1";

					$tmp = $db->query($sql);

					if ($db->num_rows($tmp))
					{
						$tmp = $db->fetch_array($tmp);
						$id = $tmp[id];
					}
					break;

			}

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=date_overview";
			$menu[1][text] = 'Terminübersicht';
			$menu[2][link] = "$PHP_SELF?area=date_create";
			$menu[2][text] = 'Neuen Termin anlegen';
			$menu[3][link] = "$PHP_SELF?area=date_edit&id=$id&post_news=1";
			$menu[3][text] = 'Diesen Termin editieren';
			$menu[4][link] = "$PHP_SELF?area=date_delete&id=$id&post_news=1";
			$menu[4][text] = 'Diesen Termin löschen';

			$DateView->pagetitle('Termin', $menu);
			// $viewer = new DateViewer($id, $db);

			$output = '

				<table width=100% align=center border=0 cellspacing=2 cellpadding=1>
					<tr>
						<td width=17% style="font-size: 9pt;">
							Info :
						</td>
						<td bgcolor=#F2F5FF  style="font-weight: bold;">
							' . $current[info] . '
						</td>
					</tr>
					<tr>
						<td width=10% style="font-size: 9pt;">
							Typ :
						</td>
						<td bgcolor=#F2F5FF  style="font-weight: bold;">
							' . $date_types[$current[type]][name] . '
						</td>
					</tr>
					<tr>
						<td style="font-size: 9pt;">
							Termin :
						</td>
						<td bgcolor=#F2F5FF  style="font-weight: bold;">
							' . strftime('%e.%m.%Y %H:%M', $current[begin]) . ' - ' . strftime('%e.%m.%Y %H:%M', $current[end]) . '
						</td>
					</tr>
					<tr>
						<td style="font-size: 9pt;">
							Kommentar :
						</td>
						<td bgcolor=#F2F5FF  style="font-weight: bold;">
							' . nl2br($current[comment]) . '
						</td>
					</tr>
				';

			if ( $current[anzahl_helfer] > 0 )
			{
				// Wie viele Helfer sind überhaupt eingetragen?
				$sql = "select count(*) as count from " . DB_USERLISTS . " where ref_id = $current[id]";
				$eingetragene_helfer = $db->fetch_array($db->query($sql));

				if ($eingetragene_helfer[count] > 0 )
				{
					// Namen und evtl Zusatzfeld der eingetragenen User holen
					$sql = "
							select
									user_id,
									comment,
									v_name,
									n_name
								from
									" . DB_USERLISTS . ",
									" . DB_USERS . "
								where
									ref_id = $id and
									" . DB_USERS . ".id = " . DB_USERLISTS .  ".user_id
								order
									by n_name";
					$helfer = $db->query($sql);

					$temp_string = '
						<table width=100% align=left>
						';
					if ($current[userlist_has_comment])
					{
						while ( $current_helfer = $db->fetch_array($helfer) )
						{
							if ( $admin )
							{
								$temp_string .= '
									<tr>
										<td>
											<a href="' . $PHP_SELF . '?area=date_view&id=' . $id . '&remove_user=' . $current_helfer[user_id] . '" class=blue>' .
											$current_helfer[n_name] . ' ' . $current_helfer[v_name] . '</a>
										</td>
										<td>
											' . $current_helfer[comment] . '
										</td>
									</tr>
									';
							}
							else
							{
								if ($current_helfer[user_id] == $user_id )
								{
									$temp_string .= '
										<tr>
											<td class=green>
												'.
												$current_helfer[n_name] . ' ' . $current_helfer[v_name] . '
											</td>
											<td class=green>
												' . $current_helfer[comment] . '
											</td>
										</tr>
										';
								}
								else
								{
									$temp_string .= '
										<tr>
											<td>
												'.
												$current_helfer[n_name] . ' ' . $current_helfer[v_name] . '
											</td>
											<td>
												' . $current_helfer[comment] . '
											</td>
										</tr>
										';
								}
							}
						}

					}
					else
					{
						$temp_string .= '
							<tr>
								<td>
							';
						while ( $current_helfer = $db->fetch_array($helfer) )
						{
							if ( $admin )
							{
								$temp_string .= '
									<a href="' . $PHP_SELF . '?area=date_view&id=' . $id . '&remove_user=' . $current_helfer[user_id] . '" class=blue>' .
									$current_helfer[n_name] . ' ' . $current_helfer[v_name] . '</a>,
								';
							}
							else
							{
								if ($current_helfer[user_id] == $user_id )
								{
									$temp_string .= '
										<b class=green>' .
										$current_helfer[n_name] . ' ' . $current_helfer[v_name] . ',
										</b>
									';
								}
								else
								{
									$temp_string .= '
										' .
										$current_helfer[n_name] . ' ' . $current_helfer[v_name] . ',
									';
								}
							}
						}
						$temp_string .= '
								</td>
							</tr>
							';
					}
					$temp_string .= '
						</table>
						';
				}

				$output .= '
					<tr>
						<td style="font-size: 9pt;" >
							Helfer :
						</td>
						<td bgcolor=#F2F5FF style="font-weight: bold;">
							' . $eingetragene_helfer[count] . ' / ' . $current[anzahl_helfer] . ' : <br> ' . $temp_string . '
						</td>
					</tr>
					';




				if ( ( $current[anzahl_helfer]  - $eingetragene_helfer[count] ) > 0  )
				{
					if ( $admin )
					{
						$output .= '
							<tr>
								<td style="font-size: 9pt;" >
									Helfer eintragen :
								</td>
								<td align=left bgcolor=#F2F5FF style="font-weight: bold;">
									<form action="' . $PHP_SELF . '" method=get>
										<select size=1 name=user_add>

									';
								$sql = "select id, n_name, v_name from " . DB_USERS . " order by n_name";
								$users_raw = $db->query($sql);


								while ($current_helfer = $db->fetch_array($users_raw))
								{
									$output .= '<option value=' . $current_helfer[id] . '>' . $current_helfer[n_name] . ' ' . $current_helfer[v_name] . '</option>';
								}

								$output .= '
										</select>
									';
								if ( $current[userlist_has_comment] )
								{
									$output .= '
										<input type=text size=20 name=comment maxlength=255>
										';
								}
								$output .= '
										<input type=hidden name=area value=date_view>
										<input type=hidden name=id value=' . $id . '>
										<input type=submit value=Eintragen>
									</form>
								</td>
							</tr>
							';
					}
					else
					{
						$output .= '
							<tr>
								<td style="font-size: 9pt;" >
									Mich eintragen :
								</td>
								<td align=left bgcolor=#F2F5FF style="font-weight: bold;">
									<form action="' . $PHP_SELF . '" method=post>
									
									<b>' . $user_v_name . ' ' . $user_n_name . ' :
									<input type=hidden name=user_add value=' . $user_id . '>
									';

								if ( $current[userlist_has_comment] )
								{
									$output .= '
										<input type=text size=20 name=comment maxlength=255>
										';
								}
								$output .= '
										<input type=hidden name=area value=date_view>
										<input type=hidden name=id value=' . $id . '>
										<input type=submit value=Eintragen>
									</form>
								</td>
							</tr>
							';
					}
				}

			}

			if ( $current[ort] )
			{
				$output .= '
					<tr>
						<td style="font-size: 9pt;">
							Ort :
						</td>
						<td bgcolor=#F2F5FF style="font-weight: bold;">
							' . $current[ort] . '
						</td>
					</tr>
					';
			}

			if ( $current[kleidung] )
			{
				$output .= '
					<tr>
						<td style="font-size: 9pt;">
							Kleidung :
						</td>
						<td bgcolor=#F2F5FF style="font-weight: bold;">
							' . $current[kleidung] . '
						</td>
					</tr>
					';
			}
			
			$sql = "select v_name, n_name from " . DB_USERS . " where id=$current[creator]";
			$creator = $db->fetch_array($db->query($sql));
			$output .= '
				<tr>
					<td style="font-size: 9pt;">
						Erstellt :
					</td>
					<td bgcolor=#F2F5FF style="font-weight: bold;">
						von <a href="' . $PHP_SELF . '?area=user_view&id=' . $current[creator]. '" class=blue>
							' . $creator[v_name] . ' ' . $creator[n_name] . '
						</a>, am ' . strftime('%e.%m.%Y %H:%M', $current[new_date]) . '
					</td>
				</tr>
				';

			$output .= '
				</table>
				';


			echo '<br>';
			$box = new Column('Termin', $output, 0, '90%');

			$DateView->html_footer();
?>
