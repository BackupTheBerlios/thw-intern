<?php

			$NewsRemover = new Page('News l�schen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$NewsRemover->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=news_archive";
			$menu[1][text] = 'News-Archiv';

			$NewsRemover->pagetitle('News l�schen', $menu);
			echo '<br>';

			if ($confirm)
			{
					$menu = array();
					$menu[right][link] = "$PHP_SELF?area=news_archive";
					$menu[right][text] = 'Fertig';

					$output = 'Die Nachricht wurde gel�scht!';

					$sql = "delete from " . DB_NEWS . " where id=$id";
					$db->query($sql);

					$Dialog = new InfoBox('Nachricht gel�scht', $output, $menu);
			}
			else
			{
					$menu = array();
					$menu[left][link] = "$PHP_SELF?area=news_archive";
					$menu[left][text] = 'N�';
					$menu[right][link] = "$PHP_SELF?area=news_delete&id=$id&confirm=1";
					$menu[right][text] = 'Ja';

					$output = 'Soll die Nachricht wirklich gel�scht werden?';
					$Dialog = new InfoBox('Nachricht l�schen l�schen?', $output, $menu);
			}

			$NewsRemover->html_footer();
?>
