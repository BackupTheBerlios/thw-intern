<?php

			$PhotoCatalogEditor = new Page('Photokatalog editieren', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$PhotoCatalogEditor->html_header();

			if ($change_priority)
			{
				$sql = "update " . DB_PHOTOS . " set priority = $priority where id=$change_id";
				// echo $sql;
				$db->query($sql);
			}

			if ($change_description)
			{
				$sql = "update " . DB_PHOTOS . " set description = '$description' where id=$change_id";
				// echo $sql;
				$db->query($sql);
			}


			if ($add_photo)
			{
				if ($image_count)
				{
					if ($start_upload)
					{
						$images_found = 0;
						$title = 'Bilder hochgeladen';
						for ($i = 0; $i < $image_count; $i++)
						{
							if ($_FILES[newimage][size][$i])
							{
								$images_found++;
							}
						}

						if ($images_found)
						{
							$output = '<p>Es wurden effektiv <b>' . $images_found . '</b> Bild(er) hochgeladen!</p>';

							$prefix = PHOTO_LOCATION . $id . '/';

							if (!is_dir($prefix))
							{
								mkdir($prefix, 0700);
								$output .= '<p>Verzeichnis (' . $prefix . ') wurde erstellt!</p>';
							}

							// chown($prefix, FILES_OWNER);

							$output .= '<p>Bilder werden jetzt in das Verzeichnis kopiert...</p>';
							for ($i = 0; $i < $image_count; $i++)
							{
								$sql = "insert into " . DB_PHOTOS . " (date, creator, report_id) values (" . time() . ", $user_id, $id)";
								$db->query($sql);
								$sql = "select last_insert_id() as last_id";
								$tmp = $db->fetch_array($db->query($sql));
								$reserved_id = $tmp[last_id];

								$output .= '<table width=100% align=center>';

								if (copy($_FILES[newimage][tmp_name][$i], ($prefix . $reserved_id . '.jpg')))
								{
									$output .= '<tr><td class=green>Bild #' . ($i + 1) . ' komplett! Location : ' . ($prefix . $reserved_id . '.jpg') . '</td></tr>';

									$image_properties = @GetImageSize($prefix . $reserved_id . '.jpg');
									if ($image_properties)
									{
										// 0 : width
										// 1 : heigth
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
										$output .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp; Größe : ' . $image_properties[0] . ' x ' . $image_properties[1] . ' -> scheint ' . $insert_string . ' zu sein!</td></tr>';


										if ($mode)		// Hochformat
										{
											$output .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp; Erstelle Thumbnail : 150 x 200</td></tr>';
											$h = 150;
											$w = 200;
											$thumb = ImageCreate(150, 200);
										}
										else
										{
											$output .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp; Erstelle Thumbnail : 200 x 150</td></tr>';
											$h = 200;
											$w = 150;
											$thumb = ImageCreate(200, 150);
										}

										if ($thumb)
										{
											$tmp_file = ImageCreateFromJPEG($prefix . $reserved_id . '.jpg');

											if (ImageCopyResized($thumb, $tmp_file, 0, 0, 0, 0, $h, $w, $image_properties[0],$image_properties[1]))
											{
												$thumb_path = $prefix . $reserved_id . '_thumb.jpg';

												if (ImagePNG($thumb, $thumb_path))
												{
													$output .= '<tr><td class=green>&nbsp;&nbsp;&nbsp;&nbsp; Thumbnail (PNG) zu Bild #' . ($i + 1 ) . ' komplett!</td></tr>';
												}
												else
												{
													$output .= '<tr><td class=red>WHOUPS!! Konnte das Thumbnail nicht speichern! -> Breche ab!</td></tr>';
													break;
												}
											}
											else
											{
												$output .= '<tr><td class=red>WHOUPS!! Konnte kein Thumbnail generieren (ImageCopyResized)! -> Räume auf und überspringe!</td></tr>';
												unlink($prefix . $reserved_id . '.jpg');
												$sql = "delete from " . DB_PHOTOS . " where id=$reserved_id";
												$db->query($sql);
											}
										}
										else
										{
											$output .= '<tr><td class=red>WHOUPS!! Konnte kein Thumbnail generieren (ImageCreate)! -> Breche ab!</td></tr>';
											break;
										}
									}
									else
									{
										$output .= '<tr><td class=red>WHOUPS!! Datei ' . ($i + 1) . ' ist gar kein Bild! -> überspringe #' . ($i + 1) . ' und räume auf!</td></tr>';
										unlink($prefix . $reserved_id . '.jpg');
										$sql = "delete from " . DB_PHOTOS . " where id=$reserved_id";
										$db->query($sql);
									}

								}
								else
								{
									$output .= '<tr><td class=red>WHOUPS! Das kopieren der Bilder ist fehlgeschlagen! Breche ab... </td></tr>';
									break;
								}
							}

							$output .= '</table>';
							$menu[right][text] = 'OK';
							$menu[right][link] = "$PHP_SELF?area=photo_editcatalog&id=$id";


						}
						else
						{
							$output = 'Whoups, hier sind keine Bilder angekommen! Sicher das du welche ausgewählt hast??';
							$menu[right][text] = 'Nochmal';
							$menu[right][link] = "$PHP_SELF?area=photo_editcatalog&add_photo=1&id=$id&image_count=$image_count";
						}


					}
					else
					{
							$title = 'Bild hinzufügen';

							$reserved_id = 0;

							$output = '
									<form action="' . $PHP_SELF . '" enctype="multipart/form-data" method=post>
										<table align=center>
										<tr><td><input type=hidden name="MAX_FILE_SIZE" value=500000></td></tr>';
								for ($i = 0; $i < $image_count; $i++)
								{
									$output .= '
										<tr><td><input type=file name=newimage[] maxlength=500000></td></tr>
										';
								}
							$output .= '
											<tr>
												<td align=right>
													<input type=submit value="Los" name=start_upload>
													<input type=hidden name=area value=photo_editcatalog>
													<input type=hidden name=add_photo value=1>
													<input type=hidden name=image_count value=' . $image_count . '>
													<input type=hidden name=id value=' . $id . '>
													<input type=hidden name=reserved_id value=' . $reserved_id . '>
												</td>
											</tr>
										</table>
									</form> ';

					}
				}
				else
				{
					$title = 'Anzahl der Bilder';
					$output = '<p>Bitte die Anzahl der hochzuladenden Bilder eingeben!</p>';
					$output .= '	<center>
								<form action=' . $PHP_SELF . ' method=get>
									<input type=hidden name=area value=photo_editcatalog>
									<input type=hidden name=add_photo value=1>
									<input type=hidden name=id value=' . $id . '>
									<input type=text name=image_count value=1 size=2><br>
									<input type=submit value="Los">
								</form></center>';
				}

				$Box = new InfoBox($title, $output, $menu, $width);
			}
			else
			{

				$menu = array();
				$menu[0][link] = "$PHP_SELF";
				$menu[0][text] = 'Startseite';
				$menu[1][link] = "$PHP_SELF?area=photo_overview";
				$menu[1][text] = 'Photokataloge';
				$menu[2][link] = "$PHP_SELF?area=photo_viewcatalog&id=$id";
				$menu[2][text] = 'Zurück zum Katalog';
				$menu[3][link] = "$PHP_SELF?area=photo_editcatalog&add_photo=1&id=$id";
				$menu[3][text] = 'Bild hinzufügen';

				$PhotoCatalogEditor->pagetitle('Photokatalog editieren', $menu);

				echo '<br>';

				$sql = "select heading from " . DB_REPORTS . " where id=$id";
				$heading = $db->fetch_array($db->query($sql));
				$title = '<i>' . $heading[heading] . '</i>';

				$sql = "select count(*) as count from " . DB_PHOTOS . " where report_id=$id";
				$count = $db->fetch_array($db->query($sql));
				$sql = "select id, description, priority from " . DB_PHOTOS . " where report_id=$id order by priority desc";
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
						$prefix = PHOTO_LOCATION . '/' . $id . '/';

						$thumb_path = $prefix . $current[id] . '_thumb.jpg';

						$output .= '
								<td align=center valign=top width=33%>
										<a href="" class=blue>verschieben</a> - <a href="' . $PHP_SELF . '?area=photo_delete&id=' . $id . '&remove_id=' . $current[id] . '" class=red>löschen</a>
									<br>
										<form action="' . $PHP_SELF . '" method=get>
											<input type=text name=priority size=2 value=' . $current[priority] . '>
											<input type=hidden name=area value=photo_editcatalog>
											<input type=hidden name=id value=' . $id . '>
											<input type=hidden name=change_id value=' . $current[id] . '>
											<input type=submit name=change_priority value=Los>
										</form>
									<br>
									<a href="' . $PHP_SELF . '?area=photo_view&id=' . $current[id] . '"><img src="' . $thumb_path . '" border=0></a><br>
									<br>
										<form action="' . $PHP_SELF . '" method=get>
											<input type=text name=description size=20 value="' . $current[description] . '">
											<input type=hidden name=area value=photo_editcatalog>
											<input type=hidden name=id value=' . $id . '>
											<input type=hidden name=change_id value=' . $current[id] . '>
											<input type=submit name=change_description value=Los>
										</form>
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
			}

			$PhotoCatalogEditor->html_footer();
?>

