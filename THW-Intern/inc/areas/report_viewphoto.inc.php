<?php

	$page->title_bar();

	// Wir schauen uns ein Photo an...
	if ($photo_id)
	{
		// Erstmal Infos aus der Datenbank holen...

		$sql = '
				select
					id,
					kommentar
				from
					' . TB_PHOTOS . '
				where
					ref_bericht_id = ' . $report_id . '
				order by
					priority desc
				limit
					' . $offset . ', 1
			';

		$raw = $db->query($sql);
		if ($current = $db->fetch_array($raw))
		{
			$message = '
					<table width=100% align=center border=0>
						<tr>
							<td align=center>
								<img src="' . PHOTO_PATH . $report_id . '/' . $current[id] . '.jpg">
							</td>
						</tr>
						<tr>
							<td align=center>
								' . $current[kommentar] . '
							</td>
						</tr>
					</table>
				';

			$menu = array();
			$menu[0][link] = $PHP_SELF . '?action=report_read&id=' . $report_id;
			$menu[0][text] = 'Zum Bericht';
			$menu[1][link] = $PHP_SELF . '?action=report_viewphoto&report_id=' . $report_id;
			$menu[1][text] = 'Alle Bilder';

			$bottom_menu = array();
			$tmp = 0;

			// Diesen Button brauchen wir nur wenn wir nicht das erste Bild anschauen...
			if ($offset)
			{
				$bottom_menu[$tmp][link] = $PHP_SELF . '?action=report_viewphoto&photo_id=1&offset=' . ($offset - 1) . '&report_id=' . $report_id;
				$bottom_menu[$tmp][text] = '&laquo;Weitere Bilder';
				$tmp++;
			}

			$sql = '
					select
						count(*) as count
					from
						' . TB_PHOTOS . '
					where
						ref_bericht_id = ' . $report_id . '
				';

			$temp = $db->fetch_array($db->query($sql));

			// Und diesen button brauchen wir nur, wenn wir nicht beim letzten Bild sind...
			if (!($temp['count'] == ($offset + 1)))
			{
				$bottom_menu[$tmp][link] = $PHP_SELF . '?action=report_viewphoto&photo_id=1&offset=' . ($offset + 1) . '&report_id=' . $report_id;
				$bottom_menu[$tmp][text] = 'Weitere Bilder &raquo;';
			}


			echo $page->dialog_box(0, $message, $menu, $bottom_menu, '');
		}
		else
		{
			$message = 'Whoups, konnte das gewünschte Bild nicht laden... )o:';
			echo $page->dialog_box('Fehler...', $message, 0, 0, '50%');
		}
	}
	// Hier wollen wir eine Gallerie eines Berichts...
	else if ($report_id)
	{
		if ($submit)
		{
			for ($i = 0; $i < count($delete_id); $i++)
			{
				// Soll ein Bild gelöscht werden?
				if ($delete_id[$i])
				{
					// echo 'deleting Image ' . $delete_id[$i] . '... (FAKE)<br>';
					$sql = '
							delete
							from
								' . TB_PHOTOS . '
							where
								id = ' . $delete_id[$i] . '
						';
					// echo $sql . '<br>';
					$db->query($sql);
					@unlink(PHOTO_PATH . $report_id . '/' . $delete_id[$i] . '_thumb.jpg');
					@unlink(PHOTO_PATH . $report_id . '/' . $delete_id[$i] . '.jpg');

				}
			}

			for ($i = 0; $i < count($move_id); $i++)
			{
				// Soll ein Bild verschoben werden?
				if ($move_id[$i])
				{
					echo 'moving image ' . $move_id[$i] . '... (FAKE)<br>';
					$sql = '
							update
								' . TB_PHOTOS . '
							set
								ref_bericht_id = ' . $move_target . '
							where
								id = ' . $move_id[$i] . '
						';
					echo $sql . '<br>';


// 					$db->query($sql);
// 					@unlink(PHOTO_PATH . $report_id . '/' . $delete_id[$i] . '_thumb.jpg');
// 					@unlink(PHOTO_PATH . $report_id . '/' . $delete_id[$i] . '.jpg');

				}
			}

			// Ok, dann gehen wir jetzt id für id durch und tragen die neuen Werte in die DB ein...
			for ($i = 0; $i < count($edit_id); $i++)
			{

				if (!$priority[$i])
				{
					$priority[$i] = 0;
				}

				$sql = '
						update
							' . TB_PHOTOS . '
						set
							priority = ' . $priority[$i] . ',
							kommentar = "' . htmlentities($kommentar[$i]) . '"
						where
							id = ' . $edit_id[$i] . '
					';
				// echo $sql . '<br>';
				$db->query($sql);
			}
		}

		if (!$offset)
		{
			$offset = 0;
		}

		// Zuerst holen wir uns einfach mal die Daten zu allen Bildern im aktuellen Offset!
		$sql = '
				select
					id,
					kommentar,
					priority
				from
					' . TB_PHOTOS . '
				where
					ref_bericht_id = ' . $report_id . '
				order by
					priority desc
				limit
					' . $offset . ', ' . (PHOTOS_PER_GALLERY + 1) . '
			';

		// echo $sql;
		$raw = $db->query($sql);

		if ($db->num_rows($raw))
		{
			// Holen wir mal ein paar Infos zu dem dazugehörigen Bericht...
			$bericht = new tb_bericht();
			$bericht->load_tb_bericht($report_id);

			$menu = array();
			$menu[0][link] = $PHP_SELF . '?action=report';
			$menu[0][text] = 'Berichtübersicht';
			$menu[1][link] = $PHP_SELF . '?action=report_read&id=' . $report_id;
			$menu[1][text] = 'Zum Bericht';
			$menu[1][title] = 'Den Bericht zu den Bildern lesen';


			// Jetzt prüfen wir erstmal ob wir SU sind...
			$is_admin = 0;
			if ( ($bericht->return_field('ref_user_id') == $session->user_info('id')) or ($session->user_info('rights') == ROOT))
			{
				$is_admin = 1;
				// OOOOOOOOk, wir sind SU! Dann fügen wir noch einen weiteren Menüpunkt hinzu:
				if ($editmode)			// Ist der Button schonmal gedrückt worden??
				{
					$menu[2][link] = $PHP_SELF . '?action=report_viewphoto&report_id=' . $report_id . '&editmode=0';
					$menu[2][text] = 'Normalmodus';
					$menu[2][title] = 'Zurück zum Normalmodus';
				}
				else
				{
					$menu[2][link] = $PHP_SELF . '?action=report_viewphoto&report_id=' . $report_id . '&editmode=1';
					$menu[2][text] = 'Editiermodus';
					$menu[2][title] = 'Hier können Bilder mit Kommentaren versehen werden, deren Priorität verändert und gelöscht werden';
				}
				$menu[3][link] = $PHP_SELF . '?action=report_addphoto&id=' . $report_id;
				$menu[3][text] = 'Bilder hinzufügen';
				$menu[3][title] = 'Hier können Bilder hinzugefügt werden!';
			}

			$number_of_rows = 0;
			$number_of_photos = 0;
			$number_of_showen_photos = 0;

			$number_of_photos = $db->num_rows($raw);
			if ($number_of_photos > PHOTOS_PER_GALLERY)
			{
				$number_of_photos = PHOTOS_PER_GALLERY;
			}

			$number_of_rows = ceil($number_of_photos / 3);

			$output = '
						<h2>' . $bericht->return_field('titel') . '</h2>
					';

			if ($is_admin AND $editmode)
			{
				$output .= '
						<form action="' . $PHP_SELF . '" method=get>
					';
			}

			$output .= '
				<table width=100% align=center border=0 cellspacing=5>
					';

				for ($row = 0; $row < $number_of_rows; $row++)
				{
					$output .= '
							<tr>
						';


					for ($photo = 0; $photo < 3; $photo++)
					{
						if ($number_of_showen_photos == $number_of_photos)
						{
							break;
						}

						$current = $db->fetch_array($raw);

						if ($is_admin AND $editmode)		// Editiermodus
						{
								$output .= '
										<td align=center valign=top>
											<a href="' . $PHP_SELF . '?action=report_viewphoto&photo_id=1&offset=' . ($offset + $number_of_showen_photos) . '&report_id=' . $report_id . '">
												<img src="' . PHOTO_PATH . $report_id . '/' . $current[id] . '_thumb.jpg" border=0 title="' . $current[kommentar] . '" alt="' . $current[kommentar] . '">
											</a>
											<br>
												<table width=100% align=center border=0  class=list_table_inactive>
													<tr>
														<td colspan=3 align=center>
															<input type=text size=20 value="' . $current[kommentar] . '" name="kommentar[]" title="Kommentar zum Bild">
														</td>
													</tr>
													<tr>
														<td align=right>
															<input type=text size=2 value="' . $current[priority] . '" name="priority[]" title="Priorität des Bildes; Bilder mit hoher Priorität werden weiter vorne angezeigt!">
														</td>
														<td>
															<input type=checkbox name="delete_id[]" value="' . $current[id] . '" title="Das Bild löschen"> löschen
														</td>
														<td>
															<input type=checkbox name="move_id[]" value="' . $current[id] . '" title="Dieses Bild verschieben"> versch.
														</td>
													</tr>
												</table>
												<input type=hidden value="' . $current[id] . '" name="edit_id[]">
										</td>
									';
						}
						else									// Normalmodus
						{
								$output .= '
										<td align=center valign=top class=list_table_active>
											<a href="' . $PHP_SELF . '?action=report_viewphoto&photo_id=1&offset=' . ($offset + $number_of_showen_photos) . '&report_id=' . $report_id . '">
												<img src="' . PHOTO_PATH . $report_id . '/' . $current[id] . '_thumb.jpg" border=0 title="' . $current[kommentar] . '" alt="' . $current[kommentar] . '">
											</a>
											<br>
											' . $current[kommentar] . '
										</td>
									';
						}

						$number_of_showen_photos++;
					}

					$output .= '
							</tr>
						';
				}

			$output .= '
				</table>
			';

			if ($is_admin AND $editmode)
			{
				$output .= '
							<input type=hidden name=action value="' . $action . '">
							<input type=hidden name=report_id value="' . $report_id . '">
							<input type=hidden name=editmode value="' . $editmode . '">
							<input type=hidden name=offset value="' . $offset . '">

							<table widtH=100% align=center border=0 style="margin-top: 10pt;"  class=list_table_inactive>
								<tr>
					';

				// Alle möglichen Berichte holen wohin man die Bilder verschieben kann
				$sql = '
						select
							titel,
							ref_object_id
						from
							' . TB_BERICHT . '
						where
							ref_object_id != ' . $report_id . '
							and
							berichtart <= ' . REPORTTYPE_MOD . '
					';

				// echo $sql;

				$raw = $db->query($sql);
				if ($db->num_rows($raw))
				{
					$output .= '
								<tr>
									<td align=right>
										Verschieben nach:
									</td>
									<td width=10%>
										<select size=1 name=move_target style="font-size:9pt;" title="Wohin sollen die ausgewählten Bilder verschoben werden??">
						';

					while ($tmp = $db->fetch_array($raw))
					{
						$output .= '
											<option value=' . $tmp[ref_object_id] . '>' . $tmp[titel] . '</option>
							';
					}

					$output .= '
										</select>
									</td>
								</tr>
						';
				}

			 	$output .= '
								<tr>
									<td align=right colspan=2>
										<input type=submit name=submit value="Änderungen übernehmen">
										<input type=reset value="Felder zurücksetzen">
									</td>
								</tr>
							</table>
						</form>
					';
			}


			$tmp = 0;
			$bottom_menu = array();

			// Wir haben ein offset gegeben, also brauchen wir eine zurückbutton
			if ($offset)
			{
				$bottom_menu[$tmp][link] = $PHP_SELF . '?action=report_viewphoto&report_id=' . $report_id . '&editmode=' . $editmode . '&offset=' . ($offset - PHOTOS_PER_GALLERY);
				$bottom_menu[$tmp][text] = '&lt;&lt;Vorige Seite';
				$tmp++;
			}

			// Haben wir Mehr Bilder als wir anzeigen??
			if ($db->num_rows($raw) > PHOTOS_PER_GALLERY)
			{
				$bottom_menu[$tmp][link] = $PHP_SELF . '?action=report_viewphoto&report_id=' . $report_id . '&editmode=' . $editmode . '&offset=' . ($offset + PHOTOS_PER_GALLERY);
				$bottom_menu[$tmp][text] = 'Nächste Seite&gt;&gt;';
			}

			echo $page->dialog_box(0, $output, $menu, $bottom_menu, '96%');
		}
		else
		{

			$menu = array();
			$menu[0][link] = $PHP_SELF . '?action=report';
			$menu[0][text] = 'Berichtübersicht';
			$menu[1][link] = $PHP_SELF . '?action=report_read&id=' . $report_id;
			$menu[1][text] = 'Zum Bericht';
			$menu[1][title] = 'Den Bericht zu den nicht vorhandenen Bildern lesen';

			echo $page->dialog_box('Fehler', '<p class=error>Whoups, zu dem ausgewählten Bericht wurden keine Bilder gefunden! Hast du evtl. gerade das letzte gelöscht?</p>', $menu, 0, '50%');
		}


	}
	// Und hier einfach mal alles...
	else
	{
	}

?>

