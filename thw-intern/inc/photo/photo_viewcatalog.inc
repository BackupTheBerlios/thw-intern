<?php

			$PhotoCatalog = new Page('News-Archiv', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$PhotoCatalog->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=photo_overview";
			$menu[1][text] = 'Photokataloge';
			$menu[2][link] = "$PHP_SELF?area=photo_editcatalog&id=$id";
			$menu[2][text] = 'Katalog editieren';

			$PhotoCatalog->pagetitle('Photokatalog betrachten', $menu);

			echo '<br>';

			$sql = "select heading from " . DB_REPORTS . " where id=$id";
			$heading = $db->fetch_array($db->query($sql));
			$title = '<i>' . $heading[heading] . '</i>';

			$sql = "select count(*) as count from " . DB_PHOTOS . " where report_id=$id";
			$count = $db->fetch_array($db->query($sql));
			$sql = "select id, description from " . DB_PHOTOS . " where report_id=$id order by priority desc, id";
			// echo $sql;
			$photos_raw = $db->query($sql);
			$rowcounter = 0;
			$imagecounter = 0;
			$rowimagecounter = 0;

			while ($imagecounter < $count[count])
			{

				if ($rowimagecounter > 2)
				{
					$rowcounter = 0;
					$rowimagecounter = 0;
				}

				if ($rowcounter)
				{
					$current = $db->fetch_array($photos_raw);
					$prefix = PHOTO_LOCATION . $id . '/';

					$thumb_path = $prefix . $current[id] . '_thumb.jpg';

					$output .= '
							<td align=center valign=top width=33%>
								<a href="' . $PHP_SELF . '?area=photo_view&id=' . $current[id] . '"><img src="' . $thumb_path . '" border=0></a><br>
								' . $current[description]. '
							</td>';
					$imagecounter++;
					$rowimagecounter++;
				}
				else
				{
					if ($output)
					{
						$output .= '</tr><tr  bgcolor=#F2F5FF>';
					}
					else
					{
						$output = '<table width=100% align=center border=0 cellspacing=2><tr  bgcolor=#F2F5FF>';

					}
					$rowcounter++;
				}
			}
			$output .= '</tr></table>';

			$Box = new InfoBox($title, $output, 0, '95%');

			$PhotoCatalog->html_footer();
?>

