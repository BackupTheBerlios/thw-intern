<?php

			$NewsReader = new Page('News', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$NewsReader->html_header();

			$sql = "select id, heading, message, creator, unix_timestamp(date) as date from " . DB_NEWS . " where unfinished=0 and id=$read";
			$news = $db->query($sql);

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=news_archive";
			$menu[1][text] = 'News-Archiv';
			$menu[2][link] = "$PHP_SELF?area=news_add";
			$menu[2][text] = 'News hinzufügen';
			$menu[3][link] = "$PHP_SELF?area=news_delete&id=$read";
			$menu[3][text] = 'Beitrag löschen';
			$menu[4][link] = "$PHP_SELF?area=news_edit&id=$read";
			$menu[4][text] = 'Beitrag editieren';

			$NewsReader->pagetitle('News lesen', $menu);

			if ($db->num_rows($news))
			{
				$current = $db->fetch_array($news);

				echo '<br>';

				$message = '
						<table width=90% align=center>
							<tr>
								<td valign=top>
									von : <b>' . $NewsReader->user_link($current[creator]) . '</b><br>
									am : <b>' . strftime('%e.%m.%Y&nbsp;%H:%M', $current[date]) . '</b>
								</td>
								<td valign=top>
								' . $current[message] . '
								</td>
							</tr>
						</table>
						';

				$sql = "select id from " . DB_NEWS . " where unfinished=0 and unix_timestamp(date) > $current[date] order by date limit 1";

				$options = array();

				$tmp = $db->query($sql);

				if ($db->num_rows($tmp))
				{
					$blubb = $db->fetch_array($tmp);
					$options[0][text] = '&lt;&lt; weitere News';
					$options[0][link] = "$PHP_SELF?area=news_read&read=$blubb[id]";
					$options[1][text] = 'News-Archiv';
					$options[1][link] = "$PHP_SELF?area=news_archive";
				}
				else
				{
					$options[0][text] = 'News-Archiv';
					$options[0][link] = "$PHP_SELF?area=news_archive";
				}

				$sql = "select id from " . DB_NEWS . " where unfinished=0 and unix_timestamp(date) < $current[date] order by date desc limit 1";

				$tmp = $db->query($sql);

				if ($db->num_rows($tmp))
				{
					$blubb = $db->fetch_array($tmp);
					$hmpf[text] = 'weitere News &gt;&gt;';
					$hmpf[link] = "$PHP_SELF?area=news_read&read=$blubb[id]";
					array_push($options, $hmpf);
				}

				$Reader = new Column($current[heading], $message, $options, '95%');
				// $Reader = new InfoBox($current[heading], $current[message], 0, '95%');

			}
			else
			{
				$options = array();
				$options[right][text] = 'Zum News-Archiv';
				$options[right][link] = "$PHP_SELF?area=news_archive";
				$options[left][text] = 'Zurück zur Startseite';
				$options[left][link] = "$PHP_SELF";
				$error = new InfoBox('Nachricht nicht gefunden', 'Whoups, der Datenbankeintrag mit dieser ID wurde nicht gefunden... Datenbankfehler?', $options);
			}

			$NewsReader->html_footer();
?>
