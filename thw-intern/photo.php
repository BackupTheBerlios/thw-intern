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



	if ( $photo_id )
	{
				$sql = "select report_id, description from " . DB_PHOTOS . " where id = $photo_id";

				$current = $db->query( $sql );

				if ( $db->num_rows( $current ) )
				{
					$current = $db->fetch_array( $current );
					$location = PHOTO_LOCATION . $report_id . '/' . $photo_id . '.jpg';

					if ( !$report_id )
					{
						$report_id = $current[report_id];
					}

						$output = '
							<br>
							<table width=90% align=center class=' . IB_BORDER . ' border=0 cellspacing=1 cellpadding=0>
								<tr>
									<td>
										<table widtH=100% align=center  class=' . IB_BACKGROUND . ' border=0 cellspacing=0 cellpadding=0>
											<tR>
												<td>
													<table border=0 align=center>
														<tr>
															<td align=right>
																<img src="' . $location . '" border=0 title="' . $current[description] . '" alt="' . $current[description] . '">
															</td>
														</tr>
														<tr>
															<td align=center>
																' . $current[description] . '
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							';

				}
				else
				{
					$output = 'Error!';
				}

				$PhotoViewer = new Page('Photos', $area, 0, DEFAULT_STYLESHEET, $db, 0);
				$PhotoViewer->html_header_plain();

				$menu = array();
				$menu[0][link] = "$PHP_SELF";
				$menu[0][text] = 'Bilderübersicht';
				$menu[1][link] = "$PHP_SELF?report_id=$report_id";
				$menu[1][text] = 'Alle Bilder von diesem Bericht';
				$menu[2][link] = "reports.php?read_report=$report_id";
				$menu[2][text] = 'Den Bericht zu den Bildern lesen';

				$PhotoViewer->pagetitle('Bild betrachten', $menu);

				echo $output;

	}
	else if ( $report_id )
	{
				$sql = "select id, description from " . DB_PHOTOS . " where report_id = $report_id order by priority desc, id";
				$all_photos = $db->query( $sql );

				if ( $num_of_results = $db->num_rows( $all_photos ) )
				{
						$output = "";
						$rowcounter = 0;
						$colcounter = 0;
						$photocounter = 0;
						$no_exit = 1;

						$PhotoViewer = new Page('Photos', $area, 0, DEFAULT_STYLESHEET, $db, 0);
						$PhotoViewer->html_header_plain();

						$menu = array();
						$menu[0][link] = "$PHP_SELF";
						$menu[0][text] = 'Bilderübersicht';
						$menu[1][link] = "reports.php?read_report=$report_id";
						$menu[1][text] = 'Den Bericht zu den Bildern lesen';

						$PhotoViewer->pagetitle('Bilder betrachten', $menu);

						$output = '
									<br>
									<table width=90% align=center class=' . IB_BORDER . ' border=0 cellspacing=1 cellpadding=0>
										<tr>
											<td>
												<table widtH=100% align=center  class=' . IB_BACKGROUND . ' border=0 cellspacing=0 cellpadding=0>
													<tR>
														<td>
															<table border=0 align=center width=100% cellspacing=10>
							';

							for ( $row = 0; $no_exit ; $row++ )
							{
								$output .= '
																<tr>
									';

								for ( $col = 0; $col < 3; $col++)
								{
									$current = $db->fetch_array( $all_photos );

									$location = PHOTO_LOCATION . $report_id . '/' . $current[id] . '_thumb.jpg';
									$photocounter++;

									$output .= '
																	<td width=33% align=center valign=top>
																		<p><a href="' . $SELF_PHP. '?photo_id=' . $current[id] . '&report_id=' . $report_id . '"><img src="' . $location . '"></a>
																		</p>
																		<p>
																			' . $current[description] . '
																		</p>
																	</td>
										';

									// Exitcondition : Alle Photos durch
									if ( $photocounter == $num_of_results )
									{
										$no_exit = 0;
										break;
									}
								}

								$output .= '
																</tr>
									';

							}

						$output .= '
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									';
				}
				else
				{
					$output = "Error! No Pictures have been found for this report_id!";
				}

				echo $output;
	}
	else
	{
				$PhotoViewer = new Page('Bilder von ' . OV_NAME, $area, 0, DEFAULT_STYLESHEET, $db, 0);
				$PhotoViewer->html_header_plain();

				$menu = array();
				$menu[0][link] = "reports.php";
				$menu[0][text] = 'Berichtübersicht';

				$PhotoViewer->pagetitle('Bilder von ' . OV_NAME, $menu);


	}

	echo '<br>';

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


	$PhotoViewer->html_footer_plain();


/*	if ($read_report)
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
						$temp = '<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td align=right><b>' . $report_types[$current[type]][name] . '</b> am <b>' . strftime('%e.%m.%Y', $current[begin]) . '</b><br><br></td></tr><tr><td>' . nl2br($current[report]) . '</td></tr><tr><td align=right><br> <b>' . $creator[v_name] . ' ' . $creator[n_name] . ', ' . strftime('%e.%m.%Y', $current[create_date]) . '</b></td></tr></table>';

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
								$output .= '<a href="photos.php?photo_id=' . $current_photo[id] . '&report_id=' . $read_report . '"><img src="' . $thumb_path . '" border=0></a><br><b style="font-size: 9pt;">' . $current_photo[description] . '</b><br><br>';

								$counter++;
							}

							if (PHOTOS_PER_REPORT < $photo_count)
							{
								$output .= '<br><a href="photos.php?report_id=' . $read_report . '" class=blue><b>&gt;&gt; Weitere Bilder</b></a>';
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
			// Header setzen :
			$ReportReader = new Page('Berichte', $area, 0, DEFAULT_STYLESHEET, $db, 0);
			$ReportReader->html_header_plain();

			$output = '
						<table width=98% align=center border=0 class=IB_BORDER cellspacing=1 cellpadding=0>
							<tr>
								<td>
									<table width=100% align=center cellspacing=0 cellpadding=2>
										<tr class=bar>
											<td align=center style="font-size: 13pt;">
												Berichte von ' . OV_NAME . '
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<br>
						';

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
					$sql = "select id from " . DB_PHOTOS . " where report_id = $current[id] order by priority desc limit 1";
					if ($db->num_rows($cover_photo_raw = $db->query($sql)))
					{
						$cover_photo = $db->fetch_array($cover_photo_raw);

						$image_path = PHOTO_LOCATION . $current[id] . '/' . $cover_photo[id] . '_thumb.jpg';
						$output .= '
							<td style="font-size: 9pt;" width=200 rowspan=3><img src="' . $image_path. '" border=0></td>
							';
					}
					else
					{
						$output .= '
							<td style="font-size: 9pt;" width=10% rowspan=3>&nbsp;</td>
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
							<td style="font-size: 9pt;" valign=top colspan=2>' . $current[report] . '... [<a href="' . $PHP_SELF . '?read_report=' . $current[id] . '" class=blue>mehr</a>]</td></tr>';

				}
*/
				/*
				 * Navigationsbalken mit vor und zurück!
				*/
/*
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

	$output = '
				<table width=98% align=center border=0 class=IB_BORDER cellspacing=1 cellpadding=0>
					<tr>
						<td>
							<table width=100% align=center cellspacing=0 cellpadding=2>
								<tr class=IB_BACKGROUND>
									<td align=center style="font-size: 9pt;">
										written by <a href="mailto:Jakob@TarnkappenBaum.org" class=blue>Jakob Külzer</a>, dies ist ein Teil von THW-Intern | visit <a href="http://www.tarnkappenbaum.org/" class=blue>www.TarnkappenBaum.org</a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				';

	echo $output;
*/
?>

