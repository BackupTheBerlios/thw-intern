<?php

			$AdminPermissions = new Page('Administration - Berechtigung hinzufügen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$AdminPermissions->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=admin_overview";
			$menu[1][text] = 'Administration';
			$menu[2][link] = "$PHP_SELF?area=admin_permissions";
			$menu[2][text] = 'Zugangsrechte';

			$AdminPermissions->pagetitle('Berechtigung erstellen', $menu);
			echo '<br>';

			echo '	<form action=' . $PHP_SELF . ' method=get><input type=hidden name=area value=admin_permissions_add>';

			switch($submit)
			{
				case 'Eintragen':

					$new_area = trim($new_area);
					$description = htmlentities(trim($description));
					$sql = "insert into " . DB_PERMISSIONS . " (area, users, description) values ('$new_area', '', '$description')";
					$db->query($sql);
					$sql = "select last_insert_id() as id";
					$tmp = $db->fetch_array($db->query($sql));

					$options = array();
					$options[left][text] = 'User hinzufügen';
					$options[left][link] = "$PHP_SELF?area=admin_permissions_edit&id=$tmp[id]";
					$options[right][text] = 'Zurück';
					$options[right][link] = "$PHP_SELF?area=admin_permissions";

					$message = 'Erstelle neue Berechtigung : <br><br><b>' . $new_area . ' : ' . $description . '</b>';

					$message .= '<br><br>Dieser Berechtigung sind noch keine User zugeordnet!<br><br>';

					$Box = new Infobox('Neue Berechtigung', $message, $options);

					break;
				default:
					$message = 'Hier bitte die entsprechenden Werte eingeben :
							<table width=100% align=center border=0>
								<tr>
									<td>area : </td>
									<td><input type=text name=new_area value="' . $new_area . '"></td>
									<td>description : </td>
									<td><input type=text name=description></td>
								</tr>
								<tr>
									<td colspan=4 align=right>
										<input type=submit name=submit value="Eintragen">
									</td>
							</table>';

					$Box = new Infobox('Neue Berechtigung', $message, $options);
			}

			echo '	</form>';


			$AdminPermissions->html_footer();
?>
