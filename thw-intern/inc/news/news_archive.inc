<?php

			$NewsArchive = new Page('News-Archiv', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$NewsArchive->html_header();

			if (!$offset)
			{
				$offset = 0;
			}

			$sql = "select count(*) as count_all from " . DB_NEWS . " where unfinished=0";
			$num_of_news = $db->fetch_array($db->query($sql));
			$num_of_news = $num_of_news[count_all];
			$sql = "select id, heading, message, creator, unix_timestamp(date) as date from " . DB_NEWS . " where unfinished=0 order by date desc limit $offset, 5";
			$tmp = $db->query($sql);

			if ( $db->num_rows($tmp))
			{
					$message = '<table width=99% align=center border=0>';
					$message .= '
							<tr>
								<td colspan=2>
									<a href="' . $PHP_SELF . '" class=blue>Zurück zur Übersicht</a>
								</td>
							</tr>
							';
					while ($news = $db->fetch_array($tmp))
					{
						$message .= '
								<tr>
									<td colspan=2>';

						if (strlen($news[message]) > 100)
						{
							$text = substr($news[message], 0, 100);
							$text .= str_pad($input, 3, '.');
							$text .= ' [<a href=' . $PHP_SELF . '?area=news_read&read=' . $news[id] . ' class=blue>mehr</a>]';
						}
						else
						{
							$text = $news[message];
						}
						$message .= '
													<a href=' . $PHP_SELF . '?area=news_read&read=' . $news[id] . ' class=blue>
														<h3>
															' . $news[heading] . '
														</h3>
													</a>
												</td>
											</tr>
											<tr>
												<td colspan=2><i>'. strftime('%e.%m.%Y %H:%M', $news[date]) . ', von ' . $NewsArchive->user_link($news[creator]) . ' : </i><br>' . $text . '
												</td>
											</tr>
											<tr>
												<td colspan=2>
													<hr width=100%>
												</td>
											</tR>';
					}

					$message .= '
									<tr>
										<td style="font-size: 9pt;">
							';

						if ( $offset )
						{
							$message .= '

									[<a href="' . $PHP_SELF . '?area=news_archive&offset=' . ($offset - 5) . '" class=blue>&lt;&lt; zurück</a>]
									';
						}

					$message .= '
										</td>
										<td align=right style="font-size: 9pt;">
							';

						if ( ($offset + 5) < $num_of_news )
						{
							$message .= '
									[<a href="' . $PHP_SELF . '?area=news_archive&offset=' . ($offset + 5) . '" class=blue>weiter >></a>]
									';
						}

					$message .= '
										</td>
									</tr>
							';
					$message .= '</table>';
			}
			else
			{
				echo 'Error';
			}

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=news_add";
			$menu[1][text] = 'News hinzufügen';

			$Box = new InfoBox("News (Seite " . (( $offset + 5 ) / 5) . ")", $message, 0, '95%');

			$NewsArchive->html_footer();
?>
