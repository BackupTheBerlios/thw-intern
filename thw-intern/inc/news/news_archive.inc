<?php

			$NewsArchive = new Page('News-Archiv', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$NewsArchive->html_header();

			$news = $db->query($sql);

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=news_add";
			$menu[1][text] = 'News hinzufügen';

			$NewsArchive->pagetitle('News-Archiv', $menu);

					echo '<table width=90% align=center>';


							$sql = "select id, heading, message, creator, unix_timestamp(date) as date from " . DB_NEWS . " where unfinished=0 order by date desc limit 10";
							$tmp = $db->query($sql);
							while ($news = $db->fetch_array($tmp))
							{
								$message .= '
										<tr>
											<td>';

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
								$message .= '<a href=' . $PHP_SELF . '?area=news_read&read=' . $news[id] . ' class=blue><h3>' . $news[heading] . '</h3></a>
														</td>
													</tr>
													<tr>
														<td><i>'. strftime('%e.%m.%Y %H:%M', $news[date]) . ', von ' . $NewsArchive->user_link($news[creator]) . ' : </i><br>' . $text . '
														</td>
													</tr>
													<tr>
														<td>
															<hr width=100%>
														</td>
													</tR>';
							}


							echo $message;

				echo '</table>';

			$NewsArchive->html_footer();
?>
