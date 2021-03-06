<?php

			$DateDelete = new Page('Termin', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$DateDelete->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=date_overview";
			$menu[1][text] = 'Termin�bersicht';

			$DateDelete->pagetitle('Termin', $menu);
			echo '<br>';

			$sql = "select begin, info, type from " . DB_DATES . " where id=$id";
			$current = $db->fetch_array($db->query($sql));

			if ($confirm)
			{
					$menu = array();
					$menu[right][link] = "$PHP_SELF?area=date_overview";
					$menu[right][text] = 'Fertig';

					$output = 'Der Termin (' . $date_types[$current[type]][name] . ') am ' . strftime('%e.%m.%Y', $current[begin]) . ' wurde gel�scht!';

					if ($post_news)
					{
						$message = 'Der Termin (' . $date_types[$current[type]][name] . ') am ' . strftime('%e.%m.%Y um %H:%M', $current[begin]) . ' wurde von ' . $user_v_name . ' ' . $user_n_name . ' am ' . strftime('%e.%m.%Y um %H:%M') . ' gel�scht! Der entsprechende Termin entf�llt daher.';
						$sql = "insert into " . DB_NEWS . " (
											creator,
											date,
											heading,
											message,
											unfinished) values (
											$user_id,
											NULL,
											'Termin gel�scht',
											'$message',
											0)";

						$db->query($sql);

						$output .= '<br>Den News wurde eine entsprechende Nachricht hinzugef�gt!';
					}

					$sql = "delete from " . DB_DATES . " where id=$id";

					$db->query($sql);
					
					$sql = "delete from " . DB_USERLISTS . " where ref_id=$id";
					$db->query($sql);

					$Dialog = new InfoBox('Termin gel�scht', $output, $menu);
			}
			else
			{
					$menu = array();
					$menu[left][link] = "$PHP_SELF?area=date_overview";
					$menu[left][text] = 'N�';
					$menu[right][link] = "$PHP_SELF?area=date_delete&id=$id&confirm=1&post_news=$post_news";
					$menu[right][text] = 'Ja';

					$output = 'Soll der Termin (' . $date_types[$current[type]][name] . ') am ' . strftime('%e.%m.%Y', $current[begin]) . ' wirklich gel�scht werden?';
					$Dialog = new InfoBox('Termin l�schen?', $output, $menu);
			}


			$DateDelete->html_footer();
?>
