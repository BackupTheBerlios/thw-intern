<?php

	$page->title_bar();

	if ($file)
	{
		$LENGTH = 3000;
		/*******************************************************************************
		* Ok, es wurde ein Logfile ausgewählt!
		* Jetzt wollen wir nur $LENGTH Bytes anzeigen; dazu holen wir uns erstmal die
		* Dateigröße:
		*******************************************************************************/
		$filesize = filesize('var/log/' . $file);

		if ($logfile = fopen('var/log/' . $file, 'r'))
		{
			$title = 'Logfile: ' . $file;

			/*******************************************************************************
			* Jetzt berechnen wir das Offset:
			*******************************************************************************/
			$offset = $filesize - $LENGTH;	// Offset berechnen
			fseek($logfile, $offset);		// zum Offset springen

			fgets($logfile,1024); 	// Zum nächsten Newline springen,
									// damit nicht irgendwelche halben Zeilen auftauchen

			$message = '	Stand: ' . strftime('%d.%m.%Y %H:%M:%S') . ', es werden die letzten ' . $LENGTH . ' Bytes angezeigt:
							<p class=typewriter>';

			$message .= nl2br(fread($logfile, $LENGTH));	// Logfile ausgeben!

			$message .= '</p>';

			fclose($logfile);

			$width = '96%';
		}
		else
		{
			$title = 'Fehler';
			$message = '*Urgs* Konnte das Logfile <b>' . $file . '</b> nicht öffnen!';
			$width = '50%';
		}

	}
	else
	{
		/*******************************************************************************
		* Kein Logfile ausgewählt, also zeigen wir mal was wir haben:
		*******************************************************************************/


		if ($resetfile)
		{
			$log->reset_logfile($resetfile);
			$message = '<b class=ok>Logfile wurde zurückgesetzt!</b><br>';
		}

		$title = 'Logfile wählen';
		$message .= 'Dies sind alle auf dem Server vorhandenen Logfiles:
						<table width=100% align=center class=list_table>';

		if ($handle = opendir('var/log'))
		{
			
			$overall_size = 0;

			/* This is the correct way to loop over the directory. */
			while (false !== ($file = readdir($handle)))
			{
				if ($file == '..')
				{
					$file = 0;
				}
				if ($file == '.')
				{
					$file = 0;
				}

				if ($file)
				{
					$class = 'list_table_active';
					
					$overall_size += filesize('var/log/' . $file);

					$message .= '
								<tr class=' . $class . '>
									<td>
										<a href="' . $PHP_SELF . '?action=' . $action . '&file=' . $file . '&id=' . $id . '" title="Dieses Logfile betrachten!">' . $file . '</a>
									</td>
									<td width=30%>
										' . ceil((filesize('var/log/' . $file) / 1024)) . ' KBytes
									</td>
									<td width=10% align=center>
										<a href="' . $PHP_SELF . '?action=' . $action . '&resetfile=' . $file . '">reset</a>
									</td>
								</tr>';
				}
			}
			$message .= '
								<tr class=list_table_active>
									<td>
										<b>gesamt:</b>
									</td>
									<td>
										<b>' . ceil($overall_size / 1024) . ' KB</b>
									</td>
									<td>										
									</td>
								</tr>';
		}
		$message .= '
						</table>';

		$width = '50%';

	}
	echo $page->dialog_box($title, $message, 0, 0, $width);

?>
