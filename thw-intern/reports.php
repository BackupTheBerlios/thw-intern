<?php
	require('etc/system.inc');			// Systemweite Einstellungen laden
	require('etc/database.inc');			// Wo ist die Datenbank, wie ist der Login dazu?
	require('etc/definitions.inc');			// Definitionen laden
	require('inc/classes.inc');			// OOP-Klassen laden
	require('inc/functions.inc');			// diverse Funktionen
	
	// Datenbankobjekt anlegen!
	$db = new Database();

	setlocale (LC_ALL, 'de_DE');

	$GLOBALS[query_counter] = 0;

	if ($read_report)
	{
				$ReportReader = new Page('Bericht lesen', $area, 0, DEFAULT_STYLESHEET, $db, 0);
				$ReportReader->html_header_plain();

				$menu = array();
				$menu[0][link] = "$PHP_SELF";
				$menu[0][text] = 'Zurück zur Berichtübersicht';

				$ReportReader->pagetitle('Bericht lesen', $menu);

				$sql = "select id, description from " . DB_PHOTOS . " where report_id = $read_report order by priority desc";
				$tmp = $db->query($sql);
				if ($db->num_rows($tmp))
				{
					$photo_count = $db->num_rows($tmp);
				}
				else
				{
					$photo_count = 0;
					echo '';
				}

				$sql = "select * from " . DB_REPORTS . " where id=$read_report";
				$current = $db->fetch_array($db->query($sql));

				if ($current[public])
				{

						$sql = "select v_name, n_name from " . DB_USERS . " where id = $current[creator]";
						$creator = $db->fetch_array($db->query($sql));
						if ( !$creator )
						{
							$creator = "";
						}
						$temp = '
							<table width=100% cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td align=right>
										<b>' . $report_types[$current[type]][name] . '</b> am <b>' . strftime('%e.%m.%Y', $current[begin]) . '</b><br><br>
									</td>
								</tr>
								<tr>
									<td align=justify>' . nl2br($current[report]) . '
									</td>
								</tr>
								<tr>
									<td align=right><br> <b>' . $creator[v_name] . ' ' . $creator[n_name] . ', ' . strftime('%e.%m.%Y', $current[create_date]) . '</b>
									</td>
								</tr>
							</table>';

						echo '<br>';
						echo '<table width=95% align=center border=0 cellspacing=0 cellpadding=0>';

						echo '<tr><td valign=top>';
						$Reader = new InfoBox($current[heading], $temp, '', '98%');
						echo '</td>';

						if ($photo_count)
						{
							echo '<td valign=top width=150>';

							$output = 'Es sind <b>' . $photo_count . '</b> Bilder zu diesem Bericht vorhanden :<br>';

							$counter = 0;
							$prefix = PHOTO_LOCATION . $read_report . '/';

							if (PHOTOS_PER_REPORT > $photo_count)
							{
								$blubb = $photo_count;
							}
							else
							{
								$blubb = PHOTOS_PER_REPORT;
							}
							while ($counter < $blubb)
							{
								$current_photo = $db->fetch_array($tmp);

								$path = $prefix . $current_photo[id] . '.jpg';
								$thumb_path = $prefix . $current_photo[id] . '_thumb.jpg';
								$output .= '<a href="photo.php?photo_id=' . $current_photo[id] . '&report_id=' . $read_report . '"><img src="' . $thumb_path . '" border=0></a><br><b style="font-size: 9pt;">' . $current_photo[description] . '</b><br><br>';

								$counter++;
							}

							if (PHOTOS_PER_REPORT < $photo_count)
							{
								$output .= '<br><a href="photo.php?report_id=' . $read_report . '" class=blue><b>&gt;&gt; Weitere Bilder</b></a>';
							}

							$Reader->InfoBox('Bilder', $output, '', '98%');

							echo '</td>';
						}
						echo '</tr>';

						$ReportReader->html_footer_plain();
				}
				else
				{
					echo '<p><b>Sorrry, der gewählte Bericht ist nicht öffentlich oder existiert nicht (mehr)!</b></p>';
				}
	}
	else			// Startseite der Berichte, hier werden einfach mal alle Berichte aufgelistet!
	{

			$ReportReader = new Page('Photos von ' . OV_NAME, $area, 0, DEFAULT_STYLESHEET, $db, 0);
			$ReportReader->html_header_plain();

			$ReportReader->pagetitle('Berichte von ' . OV_NAME, 0);

			echo '<br>';

			echo $output;

			if (!$offset)
			{
				$offset = 0;
			}

			// Berichtüberschriften, Datum und Typ aus der Datenbank holen
			if ($filter)
			{
			$sql = "select
				id, heading, begin, end, type, MID(report,1,200)  as report
				from " . DB_REPORTS . "
				where unfinished=0 and public=1 and type < 256 and type=$filter
				order by begin desc limit $offset, 5";
			}
			else
			{
				$sql = "select
					id, heading, begin, end, type, MID(report,1,200)  as report
					from " . DB_REPORTS . "
					where unfinished=0 and public=1 and type < 256
					order by begin desc limit $offset, 5";
			}
			// echo $sql;
			$reports_raw = $db->query($sql);


			// Daten vorhanden``
			if ($db->num_rows($reports_raw))
			{

				// Tabelle öffnen
				$output = '<table width=100% align=center border=0 cellspacing=3>';

				// Tabellenzeilen erstellen
				while ($current = $db->fetch_array($reports_raw))
				{

					$output .= '
						<tr bgcolor=#F2F5FF>';

					// Überprüfen ob es Bilder zu diesem Bericht gibt
					$sql = "select id from " . DB_PHOTOS . " where report_id = $current[id] order by priority desc";
					$cover_photo_raw = $db->query($sql);
					$number_of_photos = $db->num_rows( $cover_photo_raw );

					if ( $number_of_photos )
					{

						// Titelphoto setzen
						$cover_photo = $db->fetch_array($cover_photo_raw);

						$image_path = PHOTO_LOCATION . $current[id] . '/' . $cover_photo[0] . '_thumb.jpg';
						$output .= '
							<td style="font-size: 9pt;" width=200 rowspan=4>
								<a href="' . $PHP_SELF . '?read_report=' . $current[id] . '" class=blue>
									<img src="' . $image_path. '" border=0>
								</a>
							</td>
							';
					}
					else
					{
						$output .= '
							<td style="font-size: 9pt;" width=10% rowspan=4>&nbsp;</td>
							';
					}

					// Berichttitel
					$output .='
							<td style="font-size: 9pt;" colspan=2><b><a href="' . $PHP_SELF . '?read_report=' . $current[id] . '" class=blue>' . $current[heading] . '</a></b></td>
						</tr>
						<tr>
						';

					// Datum und Typ des Berichts
					if ($filter)
					{
						$output .= '<td style="font-size: 9pt;" width=10% valign=top><a href="' . $PHP_SELF . '?offset=' . $offset . '" class=blue title="Alles anzeigen">' . $report_types[$current[type]][name] . '</a>&nbsp;am&nbsp;:</td>';
					}
					else
					{
						$output .= '<td style="font-size: 9pt;" width=10% valign=top><a href="' . $PHP_SELF . '?offset=' . $offset . '&filter=' . $current[type] . '" class=blue title="Nur den ausgewählten Typ anzeigen">' . $report_types[$current[type]][name] . '</a>&nbsp;am&nbsp;:&nbsp;</td>';
					}

					$output .= '
							<td style="font-size: 9pt;" valign=top>' . strftime('%e.%m.%Y', $current[begin]) . '</td>';

					$output .= '</tr><tr>';

					// Noch ein kurzer abschnitt aus dem Bericht

					$output .= '
							<td style="font-size: 9pt;" valign=top colspan=2>' . $current[report] . '... [<a href="' . $PHP_SELF . '?read_report=' . $current[id] . '" class=blue>mehr</a>]</td></tr>
							<td style="font-size: 9pt;" valign=bottom  align=right colspan=2> [<a href="' . $PHP_SELF . '?read_report=' . $current[id] . '" class=blue>kompletter Bericht</a>]
							';

					if ( $number_of_photos )
					{
						$output .= '
								[<a href="photo.php?report_id=' . $current[id] . '" class=blue>Bilder</a>]
							 ';
					}

					$output .= '
							</td></tr>
							';

				}

				/*
				 * Navigationsbalken mit vor und zurück!
				*/
				$output .= '<tr><td colspan=2>';
				if ($offset)
				{
					$output .= "<a href='$PHP_SELF?offset=" . ($offset - 5) . "' class=blue>&lt;&lt; zurück</a>";
				}
				$output .= '</td><td align=right>';

				// Überprüfen ob danach überhaupt noch Berichte kommen
				if ($filter)
				{
					$sql = "select id
									from " . DB_REPORTS . "
									where public = 1 and unfinished=0 and type = $filter
									order by begin desc limit " . ($offset + 5) . ", 5";
				}
				else
				{
					$sql = "select id
									from " . DB_REPORTS . "
									where public = 1 and unfinished=0 and type < 256
									order by begin desc limit " . ($offset + 5) . ", 5";
				}
				// echo $sql;

				if ($db->num_rows($db->query($sql)))			// Es liegen noch weitere Berichte vor
				{
					$output .= "<a href='$PHP_SELF?offset=" . ($offset + 5) . "' class=blue>weiter &gt;&gt;</a>";
				}

				$output .= '</td></tr>';

				$output .= "</table>";

				if ( $filter )
				{
					$title = 'Übersicht über Berichte (nur ' . $report_types[$filter][name] . ') :';
				}
				else
				{
					$title = 'Übersicht über Berichte (alle) :';
				}
				$Box = new Column( $title, $output, 0, '95%');

			}
			else
			{
				$title = "Fehler";
				$output = "Whoups!! Es wurden leider keine Einträge in der Datenbank gefunden! Evtl. musst du deine Suchparameter ändern!";
				$Box = new Column( $title, $output, 0, '70%');
			}

	}

	$output = '			<table width=95% align=center class=' . IB_BORDER . ' cellspacing=0 cellpadding=1>
							<tr>
								<td>
									<table width=100% align=center class=' . IB_BACKGROUND . ' cellspacing=0 cellpadding=0>
										<tr>
											<td align=center style="font-size: 9pt;">
												written by <a href="mailto:Jakob@TarnkappenBaum.org" class=blue>Jakob Külzer</a>, dies ist ein Teil von THW-Intern | SQL-Queries : <b>' . $GLOBALS[query_counter] . '</b> | visit <a href="http://www.tarnkappenbaum.org/" class=blue>www.TarnkappenBaum.org</a>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>';
	echo $output;

?>

