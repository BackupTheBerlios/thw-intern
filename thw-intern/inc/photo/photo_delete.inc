<?php

			$PhotoRemover = new Page('Photo löschen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$PhotoRemover->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=photo_overview";
			$menu[1][text] = 'Photokataloge';

			$PhotoRemover->pagetitle('Photo löschen', $menu);

			$sql = "select heading from " . DB_REPORTS . " where id=$id";
			$current = $db->fetch_array($db->query($sql));
			
			echo '<br>';

			if ($confirm)
			{
					$menu = array();
					$menu[right][link] = "$PHP_SELF?area=photo_editcatalog&id=$id";
					$menu[right][text] = 'OK';

					$output = 'Das ausgewählte Photo wurde aus der Datenbank und vom Server gelöscht!';

					$prefix = PHOTO_LOCATION . $id . '/' . $remove_id;
					@unlink($prefix . '_thumb.jpg');
					@unlink($prefix . '.jpg');

					$sql = "delete from " . DB_PHOTOS . " where id=$remove_id";
					$db->query($sql);

					$Dialog = new InfoBox('Photo gelöscht', $output, $menu);
			}
			else
			{
					$menu = array();
					$menu[left][link] = "$PHP_SELF?area=photo_editcatalog&id=$id";
					$menu[left][text] = 'Nö';
					$menu[right][link] = "$PHP_SELF?area=photo_delete&id=$id&remove_id=$remove_id&confirm=1";
					$menu[right][text] = 'Ja';

					$path = PHOTO_LOCATION . $id . '/' . $remove_id . '_thumb.jpg';
					$output = '<center><img src=' . $path . ' border=0></center>';
					$output .= '<p>Soll dieses Photo wirklich gelöscht werden?</p>';
					$Dialog = new InfoBox('Bericht löschen?', $output, $menu);
			}

			$PhotoRemover->html_footer();
?>

