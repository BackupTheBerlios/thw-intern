<?php

			$PhotoViewer = new Page('Photo betrachten', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$PhotoViewer->html_header();

			/*
			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';

			$PhotoViewer->pagetitle('Photo betrachten', $menu);
			*/

			$sql = "select * from " . DB_PHOTOS . " where id=$id";

			$raw = $db->query($sql);

			if ($raw)
			{
				$current = $db->fetch_array($raw);

				$title = 'Bild betrachten';

				$location = PHOTO_LOCATION . $current[report_id] . '/' . $id . '.jpg';
				$output = '<table width=100% align=center border=0><tr><td align=center><img src="' . $location . '" border=0></td></tr>';
				$output .= '<tr><td align=center>' . $current[description] . ' </td></tr><tr><td align=center><a href="' . $PHP_SELF . '?area=photo_viewcatalog&id=' . $current[report_id] . '" class=blue>Zurück zum Katalog</a></td></tr></table>';

				$menu = array();

				$sql = "select id from " . DB_PHOTOS . " where priority > $current[priority] and report_id=$current[report_id] order by priority, id limit 1";
				$next = $db->query($sql);
				if ($db->num_rows($next))
				{
					$next = $db->fetch_array($next);
					$menu[right][text] = 'Weitere Bilder &gt;&gt;';
					$menu[right][link] = "$PHP_SELF?area=photo_view&id=$next[id]";
				}

				$sql = "select id from " . DB_PHOTOS . " where priority < $current[priority] and report_id = $current[report_id] order by priority, id desc limit 1";
				$prev = $db->query($sql);
				if ($db->num_rows($prev))
				{
					$prev = $db->fetch_array($prev);
					$menu[left][text] = '&lt;&lt; Weitere Bilder';
					$menu[left][link] = "$PHP_SELF?area=photo_view&id=$prev[id]";
				}


			}
			else
			{
				echo '<br>';
				$title = 'Bild nicht gefunden';
				$output = 'Das Bild mit der ID wurde nicht gefunden!';
				$menu = array();
				$menu[right][text] = 'Zurück zu den Katalogen';
				$menu[right][link] = "$PHP_SELF?area=photo_overview";
			}

			$Box = new InfoBox($title, $output, $menu);

			$PhotoViewer->html_footer();
?>
