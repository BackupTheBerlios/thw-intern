<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Last edit: 12.08.2003
*
* class Log
*
* Funktion:
*			Eine einfache Logging-Klasse, die Ausgaben in verschiedene Logfiles
*			umleiten kann.
*
* Parameter:
*			Keine
*
* Rückgabe:
* 			Keine
*
* Bemerkungen:
*
* TODO:
*
* DONE:
* - Logfiles "on demand" öffnen
* - evtl. einen Globalen Logfilepfad einführen
*
*******************************************************************************/
class Log
{
	var
		$default_logfile_path,					// Wo liegen die Logfiles??
		$default_logfile,				// path to default Logfile
		$access_logfile,				// Logfile für Zugriffe
		$access_fd,					// Filedescriptor
		$fd								// Filedescriptor Logfile
		;

	/*******************************************************************************
	* Last edit: 03.07.2003
	*
	* function Log($logfile = 0, $default_logfile_path = 'var/log/'), Konstruktor
	*
	* Funktion:
	* 			Konstruktor setzt nur Variablen, Logfiles werden on demand
	*			geöffnet -> Resourcensparender! (o:
	*
	* Parameter:
	* - $logfile: hier kann man ein default-logfile übergeben
	* - $default_logfile_path: Wenn man ein anderes Verzeichnis für seine
	* 			Logfiles als 'var/log/' möchte, so muss man es hier kund tun!
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function Log($logfile = 0, $default_logfile_path = 'var/log/')
	{
		// In diesem Verzeichnis liegen die Logfiles!
		$this->default_logfile_path = $default_logfile_path;

		// Das Default-logfile
		$this->default_logfile = $logfile;
		if ($this->default_logfile)
		{
			;
		}
		else
		{
			$this->default_logfile = 'logfile';
		}
	}




	/*******************************************************************************
	* Last edit: 03.07.2003
	*
	* function open_log($logfile_to_open)
	*
	* Funktion:
	* 			öffnet das Logfile in $default_logfile_path und erstellt es,
	*			falls es nicht exisitert! Falls kein $logfile_to_open angegeben
	*			wird, wird das default-logfile geöffnet und ein Eintrag
	* 			eingefügt!
	*
	* Parameter:
	* - $logfile_to_open: das Logfile das geöffnet werden soll!
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*			Methode würgt per die() die Ausführung ab, falls Logfile nicht
	*			erstellt werden kann!
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function open_log($logfile_to_open = 0)
	{
		if (!$logfile_to_open)
		{
			$logfile_to_open = $this->default_logfile;
		}

		// Zuerst überprüfen wir ob das Logfile evtl. schon exisitert!
		if (is_writable($this->default_logfile_path . $logfile_to_open))
		{
			// Ok, das File gibt es, also öffnen wir es!
			$this->fd[$logfile_to_open] = fopen($this->default_logfile_path . $logfile_to_open, 'a');
		}
		else
		{
			// *urgs* Dieses File gibt es nicht, also erstellen!
			if ($this->fd[$logfile_to_open] = fopen( $this->default_logfile_path . $logfile_to_open, 'w'))
			{
				$this->add_to_log('Log::open_log', 'logfile ' . $this->default_logfile_path . $logfile_to_open . ' created...', $logfile_to_open);
			}
			else
			{
				die('Whoups! Konnte kein Logfile (' . $this->default_logfile_path . $logfile_to_open . ') erstellen! Breche ab... *urgs*');
			}
		}
	}



	/*******************************************************************************
	* Last edit: 03.07.2003
	*
	* function add_to_log($subsystem, $message, $logfile = 0)
	*
	* Funktion:
	*			Fügt dem Logfile $logfile einen Eintrag hinzu und öffnet diese
	*			Logfile falls es noch nicht offen ist über open_log()
	*			Wenn kein $logfile übergeben, dann wird in
	* 			$this->default_logfile geschrieben!
	*
	*			Log-Einträge kommen in folgendem Format:
	*			TT.MM.YY HH.MM.SS $subsystem: $message \n
	*
	* Parameter:
	* - $subsystem: Welches System verursacht diese Meldung?
	* - $message: die eigentliche Meldung
	* - $logfile: in welches Logfile soll geschrieben werden??
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function add_to_log($subsystem, $message, $logfile = 0)
	{
		if (!$logfile)
		{
			$logfile = $this->default_logfile;
		}

		// Jetzt prüfen wir ob das gewünschte Logfile überhaupt geöffnet ist...
		if (!$fd[$logfile])
		{
			$this->open_log($logfile);
		}

		// so, jetzt sollte das entsprechende Logfile offen sein, also schreiben
		// wir rein...
		if (!@fputs($this->fd[$logfile], (strftime('%d.%m.%Y %T') . " $subsystem: $message\n")))
		{
			die('*urgs* Fehler beim schreiben in folgendes Logfile: ' . $this->default_logfile_path . $logfile . '! Breche ab!!');
		}
	}


	/*******************************************************************************
	* Last edit: 10.07.2003
	*
	* function shutdown()
	*
	* Funktion:
	*			Schließt alle geöffneten Logfiles und schreibt vorher noch einen
	*			Zeilenumbruch (\n) in das Logfile, so das man einzelne Pageloads
	*			besser erkennen kann!
	*
	* Parameter:
	*			Keine
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	* - Evtl. in PHP5 durch einen Konstruktor ersetzen!
	*
	* DONE:
	*
	*******************************************************************************/
	function shutdown()
	{
		// Jetzt gehen wir einfach alle Einträge durch und schliesen sie!
		if (count($this->fd))
		{
			while ($current = each($this->fd))
			{
				@fputs($current[value], "\n");
				@fclose($current[value]);
			}
		}
	}

	/*******************************************************************************
	* Last edit: 12.08.2003
	*
	* function reset_logfile()
	*
	* Funktion:
	*			Schließt alle geöffneten Logfiles und schreibt vorher noch einen
	*			Zeilenumbruch (\n) in das Logfile, so das man einzelne Pageloads
	*			besser erkennen kann!
	*
	* Parameter:
	*			Keine
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	* - Evtl. in PHP5 durch einen Konstruktor ersetzen!
	*
	* DONE:
	*
	*******************************************************************************/
	function reset_logfile($file)
	{
		// Zuerst prüfen wir ob wir einen offenen desktriptor haben:
		if ($this->fd[$file])
		{
			// Ok, hier haben wir ein offenes Logfile, schließen
			@fclose($this->fd[$file]);
		}

		// Logfile zurücksetzen:
		$this->fd[$file] = @fopen('var/log/' . $file, 'w');
		@fclose($this->fd[$file]);

	}
}
?>
