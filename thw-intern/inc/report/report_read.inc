<?php

			$ReportReader = new Page('Bericht lesen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$ReportReader->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=report_overview";
			$menu[1][text] = 'Berichte';
			$menu[2][link] = "$PHP_SELF?area=report_add";
			$menu[2][text] = 'Neuen Bericht';
			$menu[3][link] = "$PHP_SELF?area=report_edit&id=$id";
			$menu[3][text] = 'Bericht editieren';
			$menu[4][link] = "$PHP_SELF?area=report_delete&id=$id";
			$menu[4][text] = 'Bericht löschen';


			$ReportReader->pagetitle('Bericht lesen', $menu);

			$sql = "select id, description from " . DB_PHOTOS . " where report_id = $id order by priority desc";
			$tmp = $db->query($sql);
			if ($db->num_rows($tmp))
			{
				$photo_count = $db->num_rows($tmp);
			}
			else
			{
				$photo_count = 0;
				echo '';
			}

			$sql = "select * from " . DB_REPORTS . " where id=$id";
			$current = $db->fetch_array($db->query($sql));

			$temp = '<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td align=right><b>' . $report_types[$current[type]][name] . '</b> am <b>' . strftime('%e.%m.%Y', $current[begin]) . '</b><br><br></td></tr><tr><td>' . nl2br($current[report]) . '</td></tr></table>';

			echo '<br>';
			echo '<table width=95% align=center border=0 cellspacing=0 cellpadding=0>';

			echo '<tr><td valign=top>';
			$Reader = new InfoBox($current[heading], $temp, '', '98%');
			echo '</td>';

			if ($photo_count)
			{
				echo '<td valign=top width=150>';

				$output = 'Es sind <b>' . $photo_count . '</b> Bilder zu diesem Bericht vorhanden :<br>';

				$counter = 0;
				$prefix = PHOTO_LOCATION . $id . '/';

				if (PHOTOS_PER_REPORT > $photo_count)
				{
					$blubb = $photo_count;
				}
				else
				{
					$blubb = PHOTOS_PER_REPORT;
				}
				while ($counter < $blubb)
				{
					$current_photo = $db->fetch_array($tmp);

					$path = $prefix . $current_photo[id] . '.jpg';
					$thumb_path = $prefix . $current_photo[id] . '_thumb.jpg';
					$output .= '<a href="' . $PHP_SELF . '?area=photo_view&id=' . $current_photo[id] . '"><img src="' . $thumb_path . '" border=0></a><br><b style="font-size: 9pt;">' . $current_photo[description] . '</b><br><br>';

					$counter++;
				}

				if (PHOTOS_PER_REPORT < $photo_count)
				{
					$output .= '<br><a href="' . $PHP_SELF . '?area=photo_viewcatalog&id=' . $id . '" class=blue><b>&gt;&gt; Weitere Bilder</b></a>';
				}

				$Reader->InfoBox('Bilder', $output, '', '98%');

				echo '</td>';
			}
			echo '</tr>';

			$ReportReader->html_footer();
?>
