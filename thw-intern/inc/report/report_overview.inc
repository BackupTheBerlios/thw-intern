<?php

			$ReportOverview = new Page('Berichte', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$ReportOverview->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=report_add";
			$menu[1][text] = 'Neuen Bericht';

			$ReportOverview->pagetitle('Berichte', $menu);

// 			$sql = "select name from " . DB_REPORT_TYPES . "order by id";

			echo '
					<table width=95% align=center>
						<tr>
							<td width=50% valign=top>';

					$sql = "select id, heading, begin, end, type from " . DB_REPORTS . " where unfinished=0 and public=1 and type < 256 order by begin desc limit " . REPORTS_PER_COLUMN;
					$tmp = $db->query($sql);

					if ($db->num_rows($tmp))
					{
						$output = '<table width=100% align=center border=0 cellspacing=3>';
						while ($current = $db->fetch_array($tmp))
						{
							$output .= '
								<tr bgcolor=#F2F5FF>
									<td style="font-size: 9pt;" width=10%>' . strftime('%e.%m.%Y', $current[begin]) . '</td>
									<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[begin]) . '</td>
									<td style="font-size: 9pt;" width=2%>-</td>
									<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[end]) . '</td>
									<td style="font-size: 9pt;"><a href="' . $PHP_SELF . '?area=report_read&id=' . $current[id] . '" class=blue>' . $current[heading] . '</a></td>
								</tr>';
						}

						$output .= '</table>';
					}
					else
					{
						$output = 'Sorry, noch keine Berichte vorhanden!';
					}

					$Box = new Column('Die letzten Berichte', $output, 0, '99%');

					echo '
							</td>
							<td valign=top>';

					$sql = "select id, heading, begin, end, type from " . DB_REPORTS . " where unfinished=0 and public=0 and type < 256 order by begin desc limit " . REPORTS_PER_COLUMN;
					$tmp = $db->query($sql);

					if ($db->num_rows($tmp))
					{
						$output = '<table width=100% align=center border=0 cellspacing=3>';
						while ($current = $db->fetch_array($tmp))
						{
							$output .= '
								<tr bgcolor=#F2F5FF>
									<td style="font-size: 9pt;" width=10%>' . strftime('%e.%m.%Y', $current[begin]) . '</td>
									<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[begin]) . '</td>
									<td style="font-size: 9pt;" width=2%>-</td>
									<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[end]) . '</td>
									<td style="font-size: 9pt;"> ' . $current[id] . ' <a href="' . $PHP_SELF . '?area=report_read&id=' . $current[id] . '" class=blue>' . $current[heading] . '</a></td>
								</tr>';
						}

						$output .= '</table>';
					}
					else
					{
						$output = 'Sorry, momentan sind keine internen Berichte vorhanden!';
					}

					$Box = new Column('Die letzten internen Berichte', $output, 0, '99%');

					// $sql = "select id, heading, begin, end, type from " . DB_REPORTS . " where unfinished=0 and public=0 order by begin desc limit " . REPORTS_PER_COLUMN;
					$sql = '';
					$tmp = $db->query($sql);

					if ($db->num_rows($tmp))
					{
						$output = '<table width=100% align=center border=0 cellspacing=3>';
						while ($current = $db->fetch_array($tmp))
						{
							$output .= '
								<tr bgcolor=#F2F5FF>
									<td style="font-size: 9pt;" width=10%>' . strftime('%e.%m.%Y', $current[begin]) . '</td>
									<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[begin]) . '</td>
									<td style="font-size: 9pt;" width=2%>-</td>
									<td style="font-size: 9pt;" width=5%>' . strftime('%H:%M', $current[end]) . '</td>
									<td style="font-size: 9pt;"><a href="' . $PHP_SELF . '?area=report_read&id=' . $current[id] . '" class=blue>' . $current[heading] . '</a></td>
								</tr>';
						}

						$output .= '</table>';
					}
					else
					{
						$output = 'Sorry, noch keine OVCOM-Einträge! <b>Not ready, yet!</b>';
					}

					$Box = new Column('OVCOM', $output, 0, '99%');


					echo '	</td>
						</tr>';

			echo '</table>';


			$ReportOverview->html_footer();
?>
