<?php

			$UserRemover = new Page('User l�schen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$UserRemover->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=user_overview";
			$menu[1][text] = 'Userverwaltung';

			$UserRemover->pagetitle('User l�schen', $menu);
			echo '<br>';

			$sql = "select n_name, v_name from " . DB_USERS . " where id=$id";
			$current = $db->fetch_array($db->query($sql));

			if ($confirm)
			{
					$menu = array();
					$menu[right][link] = "$PHP_SELF?area=user_overview";
					$menu[right][text] = 'OK';

					$sql = "delete from " . DB_USERS . " where id=$id";
					$db->query($sql);

					$sql = "delete from " . DB_USERLISTS . " where user_id=$id";
					$db->query($sql);

					$output = 'Der User "' . $current[v_name] . ' ' . $current[n_name] . '" wurde gel�scht!';

					$Dialog = new InfoBox('User gel�scht', $output, $menu);
			}
			else
			{
					$menu = array();
					$menu[left][link] = "$PHP_SELF?area=user_overview";
					$menu[left][text] = 'N�';
					$menu[right][link] = "$PHP_SELF?area=user_delete&id=$id&confirm=1";
					$menu[right][text] = 'Ja';

					$output = 'Soll der User "' . $current[v_name] . ' ' . $current[n_name] . '" wirklich gel�scht werden?';
					$Dialog = new InfoBox('User l�schen?', $output, $menu);
			}

			$UserRemover->html_footer();
?>
