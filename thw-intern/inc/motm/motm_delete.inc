<?php

			$MOTMRemover = new Page('Spruch / Bild löschen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$MOTMRemover->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=news_archive";
			$menu[1][text] = 'Spruch / Bild des Monats';

			$MOTMRemover->pagetitle('Spruch / Bild löschen', $menu);
			echo '<br>';

			if ($confirm)
			{
					$menu = array();
					$menu[right][link] = "$PHP_SELF?area=motm";
					$menu[right][text] = 'Fertig';

					switch($type + 1)
					{
						case 256:			// Bild des Monats
							break;
						case 257:			// Spruch des Monats

							$output = 'Der Spruch wurde gelöscht!';

							break;
					}

					$sql = "delete from " . DB_REPORTS . " where id=$id";
					$db->query($sql);

					$Dialog = new InfoBox('Nachricht gelöscht', $output, $menu);
			}
			else
			{
					$sql = "select report, type, name from reports, report_types where reports.id=$id and report_types.id = type";
					$current = $db->fetch_array($db->query($sql));

					$menu = array();
					$menu[left][link] = "$PHP_SELF?area=news_motm";
					$menu[left][text] = 'Nö';
					$menu[right][link] = "$PHP_SELF?area=motm_delete&id=$id&confirm=1&type=$current[type]";
					$menu[right][text] = 'Ja';

					switch($current[type] + 1)
					{
						case 256:			// Bild des Monats
							break;
						case 257:			// Spruch des Monats

							$output = 'Soll der Spruch "<b>' . $current[report] . '</b>" wirklich gelöscht werden?';

							break;

						default :
							$output = 'Whoups, unbekannter Datentyp übergeben; Datenbankfehler??';

							$menu[left][link] = '';
							$menu[left][text] = '';
							$menu[right][link] = "$PHP_SELF?area=motm";
							$menu[right][text] = 'Lieber nicht löschen...';
					}

					$Dialog = new InfoBox('Nachricht löschen löschen?', $output, $menu);
			}

			$MOTMRemover->html_footer();
?>
