<?php

			$MotmCreator = new Page('Spruch-hinzufügen', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$MotmCreator->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=motm_overview";
			$menu[1][text] = 'Sprüche/Bilder';

			$MotmCreator->pagetitle('Spruch hinzufügen', $menu);

			echo '<br>';

							if ($start_upload)
							{
								if ($caption)
								{
									$menu[right][text] = 'Ok';
									$menu[right][link] = "$PHP_SELF?area=motm";

									$date = mktime(0,0,0, ($month + 1), 0, strftime('%Y'));

									$sql = "insert into " . DB_REPORTS . " (creator, heading, report, create_date, unfinished, begin, end, type, public)
																values ($user_id, '', '$caption', " . time() . ", 0, $date, $date, 256, 0)";

									echo $sql;

									$db->query($sql);

									$sql = "select last_insert_id() as last_id";
									$tmp = $db->fetch_array($db->query($sql));
									$reserved_id = $tmp[last_id];

									// Überprüfen ob ein Bild da ist...
									if ($_FILES[newimage][size])
									{
										$title = 'Bild hochgeladen';
										$output = 'Bild ist da!';


										$prefix = PHOTO_LOCATION . 'motm/';

										if (!is_dir($prefix))
										{
											mkdir($prefix);
											$output .= '<p>Verzeichnis (' . $prefix . ') wurde erstellt!</p>';
										}

										if (copy($_FILES[newimage][tmp_name], ($prefix . $reserved_id . '.jpg')))
										{
											$output .= 'copy OK!!';
											$output .= '<br>copied to :'  . $prefix . $reserved_id . '.jpg';

												// Überprüfen ob wir überhaupt was auslesen können, also brauchbares Bild??
												$image_properties = @GetImageSize($prefix . $reserved_id . '.jpg');
												if ($image_properties)
												{
													// 0 : width
													// 1 : heigth
													// Überprüfen ob wir Hoch- oder Querformat haben
													if ($image_properties[0] < $image_properties[1])	// Hochformat
													{
														$insert_string = 'Hochformat';
														$mode = 1;
													}
													else
													{
														$insert_string = 'Querformat';
														$mode = 0;
													}

													// Thumbnail vorbereiten!
													if ($mode)		// Hochformat
													{
														$h = 150;
														$w = 200;
														$thumb = ImageCreate(150, 200);
													}
													else		// Querformat
													{
														$h = 200;
														$w = 150;
														$thumb = ImageCreate(200, 150);
													}

													if ($thumb)
													{
														$tmp_file = ImageCreateFromJPEG($prefix . $reserved_id . '.jpg');

														// Thumbnail erstellen!
														if (ImageCopyResized($thumb, $tmp_file, 0, 0, 0, 0, $h, $w, $image_properties[0],$image_properties[1]))
														{
															$thumb_path = $prefix . $reserved_id . '_thumb.jpg';

															if (ImagePNG($thumb, $thumb_path))
															{
																$output .= 'Thumbnail (PNG) komplett!';
															}
															else
															{
																$output .= 'WHOUPS!! Konnte das Thumbnail nicht speichern! -> Breche ab!';
																break;
															}
														}
														else
														{
															$output .= 'WHOUPS!! Konnte kein Thumbnail generieren (ImageCopyResized)! -> Räume auf und überspringe!';
															unlink($prefix . $reserved_id . '.jpg');
														}
													}
													else
													{
														$output .= 'WHOUPS!! Konnte kein Thumbnail generieren (ImageCreate)! -> Breche ab!';
														break;
													}
												}
												else
												{
													$output .= 'WHOUPS!! Datei ist gar kein Bild! -> überspringe und räume auf!';
													unlink($prefix . $reserved_id . '.jpg');
												}

										}
										else
										{
											$output = 'copy failed!!';
										}

									}
									else
									{
										$output .= '<p>Kein Bild ausgewählt!</p>';
									}
								}

							}
							else
							{
									$output = '
											<form action="' . $PHP_SELF . '" enctype="multipart/form-data" method=post>
												<table align=center border=0>
														<tr>
															<td align=right>
																Monat :
															</td>
															<td><select size=1 name=month>';

														for ($i = 1; $i < 13; $i++)
														{
															$output .= '<option value=' . $i . '>' . strftime('%B', mktime(1, 1, 1, $i, 1, strftime('%Y'))) . '</month>';
														}

										$output .= '
															</select></td>
														</tr>
												<tr><td><input type=hidden name="MAX_FILE_SIZE" value=500000></td></tr>
												<tr><td align=right>Bild (optional)</td><td><input type=file name=newimage maxlength=500000></td></tr>
												<tr><td align=right>Spruch :</td><td><input type=text size=15 name=caption></td></tr>
													<tr>
														<td align=right colspan=2>
															<input type=submit value="Los" name=start_upload>
															<input type=hidden name=area value=motm_add>
															<input type=hidden name=add_photo value=1>
															<input type=hidden name=type value=' . $type . '>
														</td>
													</tr>
													<tr><td colspan=2><b>Hinweis</b> : <br>Der Monat bezieht sich immer auf das aktuelle Jahr!</td></tr>
												</table>
											</form> ';

									$title = 'Bild auswählen';
							}
			$Box = new InfoBox($title , $output, $menu);

			$MotmCreator->html_footer();
?>
