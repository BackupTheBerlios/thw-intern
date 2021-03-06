<?php

			$PermissionRemover = new Page('Berechtigung l�schen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$PermissionRemover->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=admin_overview";
			$menu[1][text] = 'Administration';
			$menu[2][link] = "$PHP_SELF?area=admin_permissions";
			$menu[2][text] = 'Zugangsrechte';

			$PermissionRemover->pagetitle('Berechtigung l�schen', $menu);
			echo '<br>';

			$sql = "select description from " . DB_PERMISSIONS . " where id=$id";
			$current = $db->fetch_array($db->query($sql));

			if ($confirm)
			{
					$menu = array();
					$menu[right][link] = "$PHP_SELF?area=admin_permissions";
					$menu[right][text] = 'OK';

					$output = 'Die Berechtigung "' . $current[description] . '" wurde gel�scht!';

					$sql = "delete from " . DB_PERMISSIONS . " where id=$id";
					$db->query($sql);

					$Dialog = new InfoBox('Berechtigung gel�scht', $output, $menu);
			}
			else
			{
					$menu = array();
					$menu[left][link] = "$PHP_SELF?area=admin_permissions";
					$menu[left][text] = 'N�';
					$menu[right][link] = "$PHP_SELF?area=admin_permissions_delete&id=$id&confirm=1";
					$menu[right][text] = 'Jepp';

					$output = 'Soll die Berechtigung "' . $current[description] . '" wirklich gel�scht werden?';
					$Dialog = new InfoBox('Berechtigung l�schen?', $output, $menu);
			}

			$PermissionRemover->html_footer();
?>
