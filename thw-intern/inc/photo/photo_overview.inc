<?php

			$PhotoOverview = new Page('Photogalerie', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$PhotoOverview->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';

			$PhotoOverview->pagetitle('Photogalerie', $menu);

			$menu = array();

			if ($show_all)
			{
					$sql = "select distinct report_id from " . DB_PHOTOS . " order by date desc";
					// echo $sql;

					$menu[0][link] = "$PHP_SELF?area=photo_overview";
					$menu[0][text] = 'Nur aktuelle Kataloge';
			}
			else
			{
					$sql = "select distinct report_id from " . DB_PHOTOS . " order by date desc limit 10";
					// echo $sql;
					
					$menu[0][link] = "$PHP_SELF?area=photo_overview&show_all=1";
					$menu[0][text] = 'Alle Kataloge';
			}

					$output = '';

					$raw = $db->query($sql);

					$output .= '
							<table width=100% align=center border=0 cellspacing=3>
							';

					while ($current = $db->fetch_array($raw))
					{
						if ($current[report_id])
						{
							$sql = "select heading, begin, end from " . DB_REPORTS . " where id=$current[report_id]";
							$report = $db->fetch_array($db->query($sql));

							$output .= '
									<tr bgcolor=#F2F5FF>
										<td style="font-size: 9pt;" width=5>' . strftime('%e.%m.%Y', $report[begin]) . '</td>
										<td style="font-size: 9pt;" width=><a href="' . $PHP_SELF . '?area=photo_viewcatalog&id=' . $current[report_id] . '" class=blue alt="Diesen Katalog betrachten">' . $report[heading] . '</a></td>
										<td style="font-size: 9pt;" width=5><a href="' . $PHP_SELF . '?area=report_read&id=' . $current[report_id] . '" class=blue alt="Den dazugehörigen Bericht lesen">Bericht</a></td>
									</tr>';
						}
					}

					$output .= '</table>';

					echo '<br>';

					$Box = new Column('Aktuellste Photokataloge', $output, $menu, '93%');


					$output = 'Bilder können nur Berichten zugewiesen werden. Um einem Bericht Bilder hinzuzufügen einfach in der obigen Auflistung oder in der Gesamtliste einen auswählen und dann auf "<b>Katalog editieren</b>" klicken. Dort können dann über "<b>Bild hinzufügen</b>" Bilder hochgeladen werden.';
					$Box = new Column('Hinweis', $output, 0, '93%');


			echo '</table>';

			$PhotoOverview->html_footer();
?>
