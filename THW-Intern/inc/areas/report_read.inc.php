<?php

	$page->title_bar();

	require_once('inc/classes/class_helferliste.inc.php');

	$menu = array();
	$menu[0][link] = $PHP_SELF . '?action=report_edit&id=' . $id;
	$menu[0][text] = 'Bericht editieren';
	$menu[1][link] = $PHP_SELF . '?action=report_publish&id=' . $id;
	$menu[1][text] = 'Bericht freigeben/zurückziehen';
	$menu[2][link] = $PHP_SELF . '?action=report_delete&id=' . $id;
	$menu[2][text] = 'Bericht löschen';
	$menu[3][link] = $PHP_SELF . '?action=report_addphoto&id=' . $id;
	$menu[3][text] = 'Bilder hinzufügen';

	$bericht = new tb_bericht2();
	if ($bericht->load_tb_bericht($id))
	{

		$userliste = new tb_helferliste2($id);

		$userliste->load_userlist($id);

		$message = '
										<table width=100% align=center border=0>
											<tr>
												<td align=right class=small>
													<b>' . $bericht->return_field('date_begin') . '</b>
												</td>
											</tr>
											<tr>
												<td>
													<h2>' . $bericht->return_field('titel') . '</h2>
												</td>
											</tr>
											<tr>
												<td>
													<p>' . nl2br($bericht->return_field('text')) . '</p>
												</td>
											</tr>
											<tr>
												<td align=right class=small>
													erstellt von <b>' . $bericht->return_field('vorname') . ' ' . $bericht->return_field('name') . '</b> am ' . nl2br($bericht->return_field('date_create')) . '
												</td>
											</tr>
					';

		$is_user_admin = 0;

		if ( ($bericht->return_field('ref_user_id') == $session->user_info('id')) or ($session->user_info('rights') == ROOT))
		{
			$is_user_admin = 1;
		}

		$message .= '
										</table>
			';

		// Jetzt prüfen wir erst nochmal ob es Photos für diesen Bericht gibt!
		$sql = '
				select
					id,
					kommentar
				from
					' . TB_PHOTOS . '
				where
					ref_bericht_id = ' . $id . '
				order by
					priority desc
				limit
					' . ( MAX_PHOTOS_PER_REPORT + 1 ) . '
			';

		// echo $sql;

		$photos_raw = $db->query($sql);
		if ($db->num_rows($photos_raw))
		{
			// Sehr schön, es scheinen Photos zu existieren!

			// Haben wir mehr Photos als wir anzeigen??
			if ($db->num_rows($photos_raw) > MAX_PHOTOS_PER_REPORT )
			{
				// Ja, mehr Photos vorhanden als wir anzeigen ...
				$additional_photos = 1;
			}

			$photos = '
					<table width=100% align=center border=0>

						';

			for ($i = 0; $i < MAX_PHOTOS_PER_REPORT; $i++)
			{
				if ($i >= $db->num_rows($photos_raw))
				{
					break;
				}
				$current = $db->fetch_array($photos_raw);
				$photos .= '
						<tr>
							<td align=center>
								<a href="' . $PHP_SELF . '?action=report_viewphoto&photo_id=1&offset=' . $i . '&report_id=' . $id . '"><img src="' . PHOTO_PATH . '/' . $id . '/' . $current[id]. '_thumb.jpg" border=0></a>
							</td>
						</tr>
							<tr>
								<td align=center>
									' . $current[kommentar] . '
								</td>
							</tr>
					';
			}

			if ($additional_photos)
			{
				$photos .= '
						<tr>
							<td align=right>
								<a href="' . $PHP_SELF . '?action=report_viewphoto&report_id=' . $id . '">weitere Bilder &raquo;</a>
							</td>
						</tr>
					';
			}

			$photos .= '
					</table>
				';

			$photo_menu = array();
			$photo_menu[0][link] = $PHP_SELF . '?action=report_viewphoto&report_id=' . $id;
			$photo_menu[0][text] = 'Galerie';
		}

		if ($photos)
		{
			echo '
				<table width=96% align=center border=0 cellspacing=0 cellpadding=4>
					<tr>
						<td valign=top>
							' . $page->dialog_box('Bericht lesen', $message, $menu, 0, '100%') . '
							<br>
							' . $userliste->view_userlist_interface($id) . '
						</td>
						<td valign=top width=220>
							' . $page->dialog_box('Bilder', $photos, $photo_menu, 0, '100%') . '
						</td>
					</tr>
				</table>
				';
		}
		else
		{
			echo $page->dialog_box('Bericht lesen', $message, $menu, 0, '') . '<br><table width=95% align=center><tr><td>' . $userliste->view_userlist_interface($id) . '</td></tr></table>';
		}
	}
	else
	{
		echo 'URGS! Konnte den Bericht nicht laden!';
	}

?>

