<?php

	/***************************************************************************
	* Laden der Einstellungen und Bibliotheken
	***************************************************************************/
	require_once("etc/tables.inc.php");
	require_once("etc/names.inc.php");
	require_once("etc/menu.inc.php");
	require_once("etc/database.inc.php");
	require_once("inc/classes/classes.inc.php");
	require_once("inc/classes/class_database.inc.php");
	require_once("inc/classes/tb_classes.inc.php");
	require_once("inc/classes/class_log.inc.php");

	/***************************************************************************
	* Log Objekt anlegen
	***************************************************************************/
	$log = new Log();

	/***************************************************************************
	* Datenbankobjekt anlegen
	***************************************************************************/
	$db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$db->debug(0);

	/***************************************************************************
	* LOCALE auf DE setzen; strftime spuckt aber trotzdem "Monday"
	* statt Montag aus... Bug in PHP?
	***************************************************************************/
	setlocale (LC_ALL, 'de_DE');

	/***************************************************************************
	* Interface Objekt anlegen und den Header bauen
	***************************************************************************/
	$page = new Interface('default.css');
	$page->html_header();

	/***************************************************************************
	* Titelbalken bauen
	***************************************************************************/
	echo 	$page->dialog_box('THW ' . OV_NAME, 0, 0, 0, '98%')
			. '<br>';




	/***************************************************************************
	* Hier geht die eigentliche Seite jetzt los...
	***************************************************************************/


	/***************************************************************************
	* Wir betrachten ein einzelnes Photo, $view_photo
	* muss dazu gesetzt sein!
	* Parameter der Seite:
	* - $view_photo: muss gesetzt sein, egal auf was! aktiviert die
	* 		Bildansicht
	* - $offset: Welches Photo soll angezeigt werden? Offset gibt an welches
	* 		Bild ab dem ersten Bild (Nr. 0), welches die Datenbank bei einem
	*		"ORDER BY priority" ausspuckt, angezeigt werden soll!
	* - $report_id: Um die richtigen Bilder rauszukramen!
	***************************************************************************/
	if ($view_photo)
	{

		// Also, erstmal alles zu dem Bild aus der Datenbank holen!
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

		// prüfen ob wir auch einen Ergebnissatz haben...
		if ($db->num_rows($raw))
		{
			// Ok, den Kram aus der Datenbank haben wir...
			$current = $db->fetch_array($raw);

			$output = '
					<table widtH=100% align=center border=0>
						<tr>
							<td align=center>
								<img src="' . PHOTO_PATH . $report_id . '/' . $current[id] . '.jpg" border=0 title="' . $current[kommentar] . '">
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
			$menu[0][link] = $PHP_SELF;
			$menu[0][text] = 'Berichtübersicht';
			$menu[1][link] = $PHP_SELF . '?read_report=' . $report_id;
			$menu[1][text] = 'Zum Bericht';
			$menu[2][link] = $PHP_SELF . '?view_all_photos=' . $report_id;
			$menu[2][text] = 'Bildübersicht';

			$bottom_menu = array();
			$tmp = 0;


			/***************************************************************************
			* Jetzt generieren wir die Vor- und Zurückbuttons...
			***************************************************************************/

			// Diesen Button brauchen wir nur wenn wir nicht das erste Bild anschauen...
			if ($offset)
			{
				$bottom_menu[$tmp][link] = $PHP_SELF . '?view_photo=1&offset=' . ($offset - 1) . '&report_id=' . $report_id;
				$bottom_menu[$tmp][text] = '&laquo;Weitere Bilder';
				$tmp++;
			}

			// Gibts noch mehr Bilder??
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
				$bottom_menu[$tmp][link] = $PHP_SELF . '?view_photo=1&offset=' . ($offset + 1) . '&report_id=' . $report_id;
				$bottom_menu[$tmp][text] = 'Weitere Bilder &raquo;';
			}

			echo $page->dialog_box(0, $output, $menu, $bottom_menu, '96%');
		}
		else
		{
			/***************************************************************************
			* Fehler, das Bild das angezeigt werden soll exisitert nicht in
			* der Datenbank... *urgs*
			***************************************************************************/
			echo $page->dialog_box('Fehler', '<p class=error>Whoups, konnte das gewählte Bild nicht laden... Datenbankfehler?</p>', 0, 0, '50%');
		}

	}

	/***************************************************************************
	* Hier wollen wir eine Galerie zu einem Bericht,
	* $view_all_photos muss gesetzt sein und eine gültige
	* Bericht-ID enthalten
	* Parameter der Seite:
	* - $view_all_photos: startet die Galerie und enthält die ID des Berichts
	* - $offset: Welche Seite soll angezeigt werden
	***************************************************************************/
	else if ($view_all_photos)
	{
		if (!$offset)
		{
			$offset = 0;
		}

	/***************************************************************************
	* Zuerst holen wir uns einfach mal die Daten zu allen Bildern im
	* aktuellen Offset!
	*  Wir holen einen Datensatz mehr als wir benötigen:
	*			LIMIT
	*				' . $offset . ', ' . (PHOTOS_PER_GALLERY + 1) . '
	* Denn wenn wir dann einen Datensatz mehr haben als wir benötigen,
	* sparen wir uns ein weiteres Query um festzustellen ob es noch mehr
	* Bilder gibt.
	***************************************************************************/
		$sql = '
				select
					id,
					kommentar
				from
					' . TB_PHOTOS . '
				where
					ref_bericht_id = ' . $view_all_photos . '
				order by
					priority desc
				limit
					' . $offset . ', ' . (PHOTOS_PER_GALLERY + 1) . '
			';

		// echo $sql;
		$raw = $db->query($sql);

		// Gibts überhaupt entsprechende Datensätze??
		if ($db->num_rows($raw))
		{
			// Ja, gibts; Also weiter...
			$menu = array();
			$menu[0][link] = $PHP_SELF;
			$menu[0][text] = 'Berichtübersicht';
			$menu[1][link] = $PHP_SELF . '?read_report=' . $view_all_photos;
			$menu[1][text] = 'Zum Bericht';

			$number_of_rows = 0;
			$number_of_photos = 0;
			$number_of_showen_photos = 0;

			// Jetzt prüfen wir die Anzahl der Ergebnisse...
			$number_of_photos = $db->num_rows($raw);
			if ($number_of_photos > PHOTOS_PER_GALLERY)
			{
				// Wir haben mehr Ergebnisse als wir benötigen, also setzen
				// wir den Counter zurück..
				$number_of_photos = PHOTOS_PER_GALLERY;
			}

			$number_of_rows = ceil($number_of_photos / 3);

			$output = '
				<table width=100% align=center border=0 cellspacing=5>
					';

				// Jetzt gehen wir die Ergebnisse durch; Wir fangen mit
				// den Reihen an...
				for ($row = 0; $row < $number_of_rows; $row++)
				{
					$output .= '
							<tr>
						';

					// So, jetzt sind wir in einer Reihe, jetzt gehen wir Bild
					// für Bild durch...
					for ($photo = 0; $photo < 3; $photo++)
					{
						if ($number_of_showen_photos == $number_of_photos)
						{
							// Wenn wir schon alle Bilder angezeigt haben,
							// brechen wir natürlich ab...
							break;
						}

						// Andernfalls machen wir weiter und zeigen das Bild an
						$current = $db->fetch_array($raw);

						$output .= '
								<td align=center valign=top class=list_table_active>
									<a href="' . $PHP_SELF . '?view_photo=1&offset=' . ($offset + $number_of_showen_photos) . '&report_id=' . $view_all_photos . '">
										<img src="' . PHOTO_PATH . $view_all_photos . '/' . $current[id] . '_thumb.jpg" border=0 title="' . $current[kommentar] . '" alt="' . $current[kommentar] . '"><br>
										' . $current[kommentar] . '
									</a>
								</td>
							';

						$number_of_showen_photos++;
					}
					$output .= '
							</tr>
						';
				}
			$output .= '
				</table>
			';


			/***************************************************************************
			* Jetzt bauen wir unsere Vor- und Zurückbuttons...
			***************************************************************************/
			$tmp = 0;
			$bottom_menu = array();

			// Wir haben ein offset gegeben, also brauchen wir eine zurückbutton
			if ($offset)
			{
				$bottom_menu[$tmp][link] = $PHP_SELF . '?view_all_photos=' . $view_all_photos . '&offset=' . ($offset - PHOTOS_PER_GALLERY);
				$bottom_menu[$tmp][text] = '&lt;&lt;Vorige Seite';
				$tmp++;
			}

			// Haben wir Mehr Bilder als wir anzeigen??
			if ($db->num_rows($raw) > PHOTOS_PER_GALLERY)
			{
				// Dann müssen wir noch eine Seite generieren und hier ist der
				// Button dazu:
				$bottom_menu[$tmp][link] = $PHP_SELF . '?view_all_photos=' . $view_all_photos . '&offset=' . ($offset + PHOTOS_PER_GALLERY);
				$bottom_menu[$tmp][text] = 'Nächste Seite&gt;&gt;';
			}

			echo $page->dialog_box(0, $output, $menu, $bottom_menu, '96%');
		}
		else
		{
			/***************************************************************************
			* Fehler, es gibt keine Bilder zu dieser Bericht-ID...
			***************************************************************************/
			echo $page->dialog_box('Fehler', '<p class=error>Whoups, konnte keine Bilder für diesen Bericht finden... Datenbankfehler? *urgs*</p>', 0, 0, '50%');
		}

	}

	/***************************************************************************
	* Einen einzelnen Bericht lesen
	* $read_report muss eine gültige Bericht-ID enthalten
	* Parameter der Seite:
	* - $read_report : gültige Bericht-ID
	***************************************************************************/
	else if ($read_report)
	{
		$bericht = new tb_bericht();
		// Erstmal die Daten zu dem gewünschten Bericht holen!
		if ($bericht->load_tb_bericht($read_report, 1))
		{
			$output = '
									<table width=100% align=center border=0 cellpadding=1>
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
											<td align=justify>
												<p>' . nl2br($bericht->return_field('text')) . '</p>
											</td>
										</tr>
										<tr>
											<td align=right class=small>
												erstellt von <b>' . $bericht->return_field('vorname') . ' ' . $bericht->return_field('name') . '</b> am ' . nl2br($bericht->return_field('date_create')) . '
											</td>
										</tr>
									</table>
				';

		}
		else
		{
			$output = 'Whoups, der gewählte Bericht kann nicht angezeigt werden, er scheint nicht öffentlich zu sein!';
		}

		// echo $page->dialog_box(0, $output, 0, 0, '96%');
		// Jetzt prüfen wir erst nochmal ob es Photos für diesen Bericht gibt!
		$sql = '
				select
					id,
					kommentar
				from
					' . TB_PHOTOS . '
				where
					ref_bericht_id = ' . $read_report . '
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
				if ($i == $db->num_rows($photos_raw))
				{
					break;
				}
				$current = $db->fetch_array($photos_raw);
				$photos .= '
						<tr>
							<td align=center>
								<a href="' . $PHP_SELF . '?view_photo=1&offset=' . $i . '&report_id=' . $read_report . '"><img src="' . PHOTO_PATH . '/' . $read_report . '/' . $current[id]. '_thumb.jpg" border=0></a>
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
								<a href="' . $PHP_SELF . '?view_all_photos=' . $read_report . '">weitere Bilder &raquo;</a>
							</td>
						</tr>
					';
			}

			$photos .= '
					</table>
				';
		}

		$menu = array();
		$menu[0][link] = $PHP_SELF;
		$menu[0][text] = 'Berichtübersicht';

		if ($photos)
		{
			$menu[1][link] = $PHP_SELF . '?view_all_photos=' . $read_report;
			$menu[1][text] = 'Bildergalerie';

			echo '
				<table width=96% align=center border=0 cellspacing=0 cellpadding=4>
					<tr>
						<td valign=top>
							' . $page->dialog_box(0, $output, $menu, 0, '100%') . '
						</td>
						<td valign=top width=220>
							' . $page->dialog_box(0, $photos, 0, 0, '100%') . '
						</td>
					</tr>
				</table>
				';
		}
		else
		{
			echo $page->dialog_box(0, $output, $menu, 0, '96%');
		}

	}

	/***************************************************************************
	* Es wurde keine der obigen Aktionen ausgewählt, also
	* zeigen wir einfach die Berichtübersich an!
	* Parameter der Seite :
	* - $offset : gibt die Seite an die angezeigt wird!
	***************************************************************************/
	else
	{
		if (!$offset)
		{
			$offset = 0;
		}

		$sql = '
				select
					date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y") as date_begin_readable,
					date_format(' . TB_OBJECT . '.date_end, "%d.%m.%Y") as date_end_readable,
					' . TB_BERICHT . '.*,
					MID(' . TB_BERICHT . '.text, 1, 400) as text
				from
					' . TB_OBJECT . ',
					' . TB_BERICHT . '
				where
					' . TB_OBJECT . '.flag_public = 1
					and
					' . TB_BERICHT . '.flag_freigegeben = 1
					and
					' . TB_BERICHT . '.ref_object_id = ' . TB_OBJECT . '.id
				order by
					' . TB_OBJECT . '.date_begin desc
				limit
					' . $offset . ', ' . ( REPORTS_PER_PAGE + 1) . '
			';

		// echo $sql;

		$tmp = 0;
		$raw = $db->query($sql);

		if ($db->num_rows($raw))
		{

			$output = '
					<table width=100% align=center border=0 class=list_table>
				';

			$i = 0;
			while ($current = $db->fetch_array($raw))
			{
				if ($tmp == REPORTS_PER_PAGE)
				{
					break;
				}
				$tmp++;

				// Haben wir Photos?
				$sql = '
						select
							id
						from
							' .  TB_PHOTOS . '
						where
							ref_bericht_id = ' . $current[ref_object_id] . '
						order by
							priority desc
						limit
							1
					';
				// echo $sql . '<br>';
				$photo_raw = $db->query($sql);

				if ($db->num_rows($photo_raw))
				{
					$temp = $db->fetch_array($photo_raw);
					$temp_string = '
								<a href="' . $PHP_SELF . '?read_report=' . $current[ref_object_id] . '">
									<img src="' . PHOTO_PATH . $current[ref_object_id] . '/' . $temp[id] . '_thumb.jpg" border=0>
								</a>
						';
					$temp_string_bottom = ' [<a href="' . $PHP_SELF . '?view_all_photos=' . $current[ref_object_id] . '">Bildergalerie</a>] ';
				}
				else
				{
					$temp_string = '
								<img src="usr/icons/no_image.png" border=0>
						';
					$temp_string_bottom = '';
				}

				$i++;
				$output .= '
						<tr>
							<td rowspan=4 class=list_table_active width=200 align=center>
								' . $temp_string . '
							</td>
							<td class=list_table_active>
								<a href="' . $PHP_SELF . '?read_report=' . $current[ref_object_id] . '"><b>' . $current[titel] . '</b></a>
							</td>
						</tr>
						<tr>
							<td>
								<b>' . $REPORT_TYPES[$current[berichtart]][name] . '</b> am <b>' . $current[date_begin_readable] . '</b>
							</td>
						</tr>
						<tr>
							<td>
								' . nl2br($current[text]) . '...
							</td>
						</tr>
						<tr>
							<td align=right valign=bottom>
								' . $temp_string_bottom . ' [<a href="' . $PHP_SELF . '?read_report=' . $current[ref_object_id] . '">Bericht lesen</a>]
							</td>
						</tr>
					';
			}

			$output .= '
					</table>
					<br>
				';

			$temp = 0;

			$menu = array();

			if ($offset)
			{
				$menu[$temp][text] = '&lt;&lt; Neuere Berichte';
				$menu[$temp][link] = $PHP_SELF . '?offset=' . ($offset - REPORTS_PER_PAGE);
				$temp++;
			}

			if ($db->num_rows($raw) > REPORTS_PER_PAGE)
			{
				$menu[$temp][text] = '&Auml;ltere Berichte &gt;&gt;';
				$menu[$temp][link] = $PHP_SELF . '?offset=' . ($offset + REPORTS_PER_PAGE);
			}

		}
		else
		{
			$output = 'Whoups! Keine Berichte gefunden...';
		}

		echo $page->dialog_box('Berichte', $output, 0, $menu, '96%');


	}

	$page->html_footer($db->query_counter());
	$log->shutdown();

?>
