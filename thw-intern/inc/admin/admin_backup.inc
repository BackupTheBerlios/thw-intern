<?php

			$AdminBackup = new Page('Administration - Backup', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$AdminBackup->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=admin_overview";
			$menu[1][text] = 'Administration';

			$AdminBackup->pagetitle('Backups', $menu);
			echo '<br>';

			if ($create_backup)
			{
				// Zuerst überprüfen wir ob das Verzeichnis für die Backups überhaupt vorhanden ist.
				if (!is_dir(BACKUP_LOCATION))
				{
					$output = '<p class=red> -> Whoups, das Backupverzeichnis existiert nicht!</p><p class=blue>Werde versuchen es zu erstellen...</p>';
					if (@mkdir(BACKUP_LOCATION))
					{
						$output .= '<p class=green> -> Verzeichniss erfolgreich erstellt!</p>';
						$continue = 1;
					}
					else
					{
						$output .= '<p class=red> -> Verzeichniss konnte nicht erstellt werden! -> <b>breche ab!</b></p>';
						$continue = 0;
					}
				}
				else
				{
					$output .= '<p class=green> -> Backupverzeichnis existiert!</p>';
					$continue = 1;
				}

				if ($continue)
				{
					$filename = strftime('backup-%e-%m-%Y', time());
					$output .= '<p class=blue> -> Daten werden jetzt in eine Datei (' . $filename . ') geschrieben...</p>';

					$sql = "load data infile " . $filename . " ";
					echo $sql;

				}

				$Box = new Column('Backup wird erstellt', $output, 0, '95%');
			}

			$output = '
				<p>Dieses Modul erstellt ein komplettes Abbild der Datenbankinhalte und speichert sie auf dem
				Server. Zusätzlich können die Backups heruntergeladen werden, was unbedingt empfohlen
				wird.</p>
				';
			$Box = new Column('Backup', $output, 0, '95%');

			echo '<table width=95% align=center border=0><tr><td width=50% valign=top>';

				$output = '
					<form action=' . $PHP_SELF . ' method=get>

						<input type=hidden name=area value=' . $area . '>
						<center><input type=submit name=create_backup value="Backup erstellen"></center>
					</form>';

				$Box = new Column('Backup erstellen', $output, 0, '100%');

			echo '</td><td width=50% valign=top>';

				$Box = new Column('Backup einspielen', $output, 0, '100%');

			echo '</td></tr></table>';

			$AdminBackup->html_footer();
?>
