<?php

			$MotmOverview = new Page('Spruch/Bild des Monats', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$MotmOverview->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=motm_add";
			$menu[1][text] = 'Neuen Spruch';

			$MotmOverview->pagetitle('Spruch/Bild des Monats', $menu);

			echo '<br>';

				$sql = "select id, report, type, begin from " . DB_REPORTS . " where type >= 256 order by begin desc limit 50";

				$raw = $db->query($sql);

				if ($db->num_rows($raw))
				{
					$output = '
							<form action=' . $PHP_SELF . ' method=get>
							<table width=100% align=center border=0>
								<tr>
									<td valign=right width=12>
											<select size=10 name=id>';

							while ($current = $db->fetch_array($raw))
							{

								$output .= '
											<option value=' . $current[id] . '>[' . strftime('%B %Y', $current[begin]) . '] ' . $current[report] . '</option>
										';
							}

							$output .= '
											</select>
									</td>
									<td valign=top>
										<button type=submit name=area value=motm_view>betrachten</button>
										<button type=submit name=area value=motm_delete>löschen</button>

										<table width=100% align=center bgcolor=#F2F5FF>
											<tr>
												<td>
													Benutzer des Internet Explorers bitte diese Buttons benutzen :
												</td>
											</tr>
											<tr>
												<td>
													<input type=radio name=area value=motm_view> betrachten <br>
													<input type=radio name=area value=motm_delete> löschen <br>
												</td>
											</tr>
											<tr>
												<td align=center>
													<input type=submit value="Los">
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</form>';

						$MotmListe = new Infobox('Liste', $output, 0, '90%');

				}

			$MotmOverview->html_footer();
?>
