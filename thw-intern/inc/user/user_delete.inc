<?php

			$UserRemover = new Page('User löschen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$UserRemover->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=user_overview";
			$menu[1][text] = 'Userverwaltung';

			$UserRemover->pagetitle('User löschen', $menu);
			echo '<br>';

			$sql = "select n_name, v_name from " . DB_USERS . " where id=$id";
			$current = $db->fetch_array($db->query($sql));

			if ($confirm)
			{
					$menu = array();
					$menu[right][link] = "$PHP_SELF?area=user_overview";
					$menu[right][text] = 'OK';

					$output = 'Der User "' . $current[v_name] . ' ' . $current[n_name] . '" wurde gelöscht!';

					$sql = "delete from " . DB_USERS . " where id=$id";
					$db->query($sql);

					$Dialog = new InfoBox('User gelöscht', $output, $menu);
			}
			else
			{
					$menu = array();
					$menu[left][link] = "$PHP_SELF?area=user_overview";
					$menu[left][text] = 'Nö';
					$menu[right][link] = "$PHP_SELF?area=user_delete&id=$id&confirm=1";
					$menu[right][text] = 'Ja';

					$output = 'Soll der User "' . $current[v_name] . ' ' . $current[n_name] . '" wirklich gelöscht werden?';
					$Dialog = new InfoBox('User löschen?', $output, $menu);
			}

			$UserRemover->html_footer();
?>
