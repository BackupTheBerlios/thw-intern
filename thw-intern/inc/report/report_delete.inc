<?php

			$ReportRemover = new Page('Bericht lesen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$ReportRemover->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=report_overview";
			$menu[1][text] = 'Berichte';
			$menu[2][link] = "$PHP_SELF?area=report_add";
			$menu[2][text] = 'Neuen Bericht';

			$ReportRemover->pagetitle('Bericht lesen', $menu);
			echo '<br>';

			$sql = "select heading from " . DB_REPORTS . " where id=$id";
			$current = $db->fetch_array($db->query($sql));

			if ($confirm)
			{
					$menu = array();
					$menu[right][link] = "$PHP_SELF?area=report_overview";
					$menu[right][text] = 'Ja';

					$output = 'Der Bericht "' . $current[heading] . '" wurde gelöscht!';

					$sql = "delete from " . DB_REPORTS . " where id=$id";
					$db->query($sql);

					$output .= '<br>Noch zu implementieren : Bilder verschieben!';

					$Dialog = new InfoBox('Bericht gelöscht', $output, $menu);
			}
			else
			{
					$menu = array();
					$menu[left][link] = "$PHP_SELF?area=report_overview";
					$menu[left][text] = 'Nö';
					$menu[right][link] = "$PHP_SELF?area=report_delete&id=$id&confirm=1";
					$menu[right][text] = 'Ja';

					$output = 'Soll der Bericht "' . $current[heading] . '" wirklich gelöscht werden?';
					$Dialog = new InfoBox('Bericht löschen?', $output, $menu);
			}

			$ReportRemover->html_footer();
?>
