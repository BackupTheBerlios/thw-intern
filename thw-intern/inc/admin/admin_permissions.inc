<?php

			$AdminPermissions = new Page('Administration - Zugangsrechte', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$AdminPermissions->html_header();

			$news = $db->query($sql);

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=admin_overview";
			$menu[1][text] = 'Administration';

			$AdminPermissions->pagetitle('Zugangsrechte', $menu);
			echo '<br>';

			$sql = "select * from " . DB_PERMISSIONS . " order by description";

			$permissions_raw = $db->query($sql);

			echo '
					<table width=95% align=center>
						<tr>
							<td width=50% valign=top>';

						$menu = array();
						$menu[0][link] = "$PHP_SELF?area=admin_permissions_edit";
						$menu[0][text] = 'Zugangsrechte editieren';

						$output = 'Hier können Zugangsrechte editiert werden, also User hinzugefügt und entfernt werden.';
						$Box = new Column('Berechtigung editieren', $output, $menu, '99%');

						$menu[0][link] = "$PHP_SELF?area=admin_permissions_add";
						$menu[0][text] = 'Neue Berechtigung';
						$output = 'Hier können neue Berechtigung angelegt werden.';
						$Box->Column('Neue Berechtigung', $output, $menu, '99%');

						$output = 'Hier können Zugangsberechtigungen komplett gelöscht werden.';
						$Box->Column('Berechtigung löschen', $output, 0, '99%');

					echo '
							</td>
							<td valign=top>';

						$output = 'Alle Berechtigungen : ';

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
							$output .= '<option value=' . $current[id] . '>[' . $insert . '] ' . $current[description] . '</option>';
						}

						$output .= '
											
										</select>
										<button type=submit name=area value=admin_permissions_delete>löschen</button>
									</form>';

						$Box->Column('Liste der Berechtigungen', $output, 0, '99%');

					echo '	</td>
						</tr>';

			echo '</table>';

			$AdminPermissions->html_footer();
?>
