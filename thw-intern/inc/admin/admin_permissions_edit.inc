<?php

			$AdminPermissionsEdit = new Page('Administration - Berechtigung editieren', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$AdminPermissionsEdit->html_header();

			$news = $db->query($sql);

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=admin_overview";
			$menu[1][text] = 'Administration';
			$menu[2][link] = "$PHP_SELF?area=admin_permissions";
			$menu[2][text] = 'Zugangsrechte';

			$AdminPermissionsEdit->pagetitle('Berechtigung editieren', $menu);
			echo '<br>';

			echo '	<form action=' . $PHP_SELF . ' method=get><input type=hidden name=area value=admin_permissions_add>';

			if ($id)
			{

				$output = '<b>Achtung : Hier gemachte Änderungen wirken sich sofort aus, also bitte nicht selbst ausschließen!';
				switch($submit)
				{
				
					case 'remove_user':
							if ($remove_user_id)
							{
								$sql = "select * from " . DB_PERMISSIONS . " where id=$id";
								$tmp = $db->fetch_array($db->query($sql));

								if ($tmp[users])
								{
									$old_users = explode('#', $tmp[users]);
									$temp = '';

									for ($i = 0; $i < count($old_users); $i++)
									{
										if ($old_users[$i] == $remove_user_id)
										{
											$output .= '<br><b class=green>Der User wurde entfernt!</b><br>';
										}
										else
										{
											if ($temp)
											{
												$temp .= '#' . $old_users[$i];
											}
											else
											{
												$temp = "$old_users[$i]";
											}

										}
									}
									$sql = "update " . DB_PERMISSIONS . " set users='$temp' where id=$id";

									$db->query($sql);

								}

							}

					case 'dazu':
							if ($new_user)
							{
								$sql = "select * from " . DB_PERMISSIONS . " where id=$id";
								$current = $db->fetch_array($db->query($sql));

								$temp = $current[users];
								if ($current[users])
								{
									$temp .= '#' . $new_user;
								}
								else
								{
									$temp = "$new_user";
								}


								$sql = "update " . DB_PERMISSIONS . " set users='$temp' where id=$id";

								$db->query($sql);

								$output .= '<br><b class=green>Der User wurde hinzugefügt!</b><br>';

							}

					default:

						$sql = "select * from " . DB_PERMISSIONS . " where id=$id";
						$current = $db->fetch_array($db->query($sql));

						if ($current[users])
						{
							$users = explode('#', $current[users]);

							$users_count = count($users);
						}
						else
						{
							$users_count = 0;
						}

						$output .= '<br>Eingetragene User (Durch anklicken entfernen!) : ' . $users_count;

						$output .= '<br>';

						if ($users_count)
						{
							$sql = "select v_name, n_name, id from " . DB_USERS . " where ";

							for ($i = 0; $i < $users_count; $i++)
							{
								if ($i > 0)
								{
									$sql .= ' or id = ' . $users[$i];
								}
								else
								{
									$sql .= ' id = ' . $users[$i];
								}
							}
						}

						$users_registered = $db->query($sql);

						while ($tmp = $db->fetch_array($users_registered))
						{
							$output .= '<a href="' . $PHP_SELF . '?area=admin_permissions_edit&id=' . $id . '&submit=remove_user&remove_user_id=' . $tmp[id] . '" class=blue>' . $tmp[v_name] . ' ' . $tmp[n_name] . '</a>, ';
						}

						$output .= '<br><br>';

						$sql = 'select v_name, n_name, id from ' . DB_USERS;
						
						$sql .= ' order by n_name';

						$users_all = $db->query($sql);

						$output .= '	<form action="' . $PHP_SELF . '" method=get>
											Den User <select size=1 name=new_user>';

											while ($perm = $db->fetch_array($users_all))
											{
												$output .= '<option value=' . $perm[id] . '>' . $perm[n_name] . ' ' . $perm[v_name] . '</option>';
											}

									$output .= '</select>  hinzufügen! <input type=hidden name=area value=admin_permissions_edit>
										<input type=hidden name=id value=' . $id . '>
										<input type=submit name=submit value="dazu">

										</form>';

						$menu = array();
						$menu[right][text] = 'Fertig';
						$menu[right][link] = "$PHP_SELF?area=admin_permissions";

						$Box = new Infobox('Berechtigung editieren : ' . $current[description], $output, $menu, '95%');
				}

			}
			else
			{
						$sql = "select * from " . DB_PERMISSIONS;

						$permissions_raw = $db->query($sql);

						$output = 'Bitte eine Berechtigung auswählen :';
						$output .= '<form action=' . $PHP_SELF . ' method=get>
										<select size=10 name=id>

												';
						while ($current = $db->fetch_array($permissions_raw))
						{
							if ($current[users])
							{
								$insert = count(explode('#', $current[users]));
							}
							else
							{
								$insert = 0;
							}
							$output .= '<option value=' . $current[id] . '>[' . $insert . ']' . $current[description] . '</option>';
						}

						$output .= '
										</select>
										<input type=hidden name=area value="admin_permissions_edit">
										<input type=submit value="Los">
									</form>
								';

						$Box = new Infobox('Liste der Berechtigungen', $output, 0);
			}

			echo '	</form>';


			$AdminPermissionsEdit->html_footer();
?>
