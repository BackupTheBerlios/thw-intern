<?php

			$ForumOverview = new Page('Foren', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$ForumOverview->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=forum_createforum";
			$menu[1][text] = 'Neues Forum';

			$ForumOverview->pagetitle('Foren', $menu);

				$sql = "select * from " . DB_FOREN;

				$foren_raw = $db->query($sql);

				if ($db->num_rows($foren_raw))
				{
					echo '
						<br>
						<table width=95% border=0>
							<th class=bar>Forum</th><th class=bar>Diskussionen</th><th class=bar>Beiträge</th><th class=bar>Neue Beiträge</th><th class=bar>Beschreibung</th>';
					while ($current = $db->fetch_array($foren_raw))
					{
						$sql = "select count(*) as count from " . DB_FORUM . " where forum_id = $current[forum_id] and unfinished = 0";
						$count = $db->fetch_array($db->query($sql));
						$sql = "select count(*) as count_new from " . DB_FORUM . " where forum_id = $current[forum_id] and unfinished = 0 and unix_timestamp(date) > $user_last_login";
						$count_new = $db->fetch_array($db->query($sql));

						if (!$count_new[count_new])
						{
							$count_new[count_new] = '';
						}

						$sql = "select count(*) as count_threads from " . DB_FORUM . " where forum_id = $current[forum_id] and unfinished = 0 and ref_id = 0";
						$count_threads = $db->fetch_array($db->query($sql));

						echo '<tr><td><a href=' . $PHP_SELF . '?area=forum_view&id=' . $current[forum_id] . ' class=blue>' . $current[forum_name] . '</a></td><td align=right>' . $count_threads[count_threads] . '</td><td align=right>' . $count[count] . '</td><td align=center class=red><b>' . $count_new[count_new] . '</b></td><td style="font-size:10pt;">' . $current[forum_description] . '</td></tr>';
					}

					echo '
						</table>';
				}
				else
				{
					echo '<br>';
					$options = array();
					$options[right][text] = 'Zur Startseite';
					$options[right][link] = "$PHP_SELF";
					$error = new InfoBox('Keine Foren gefunden', 'Whoups, es wurden keine Foren gefunden...', $options);
				}

			$ForumOverview->html_footer();
?>
