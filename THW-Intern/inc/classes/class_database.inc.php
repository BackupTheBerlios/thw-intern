<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 17.07.2003
* Last edit: 18.07.2003
*
* class Database
*
* Funktion:
*			Eine einfache Klasse die als Abstraktionsebene zwischen Software und
*			verschiedenen Datenbanksystemen dient!
*			Kümmert sich um die Verbindung zur Datenbank...
*
* Bemerkungen:
*
* TODO:
*
* DONE:
*
*******************************************************************************/
class Database
{
	var
		$host,				// Host auf dem die DB läuft
		$user,				// User zum anmelden
		$password,			// Kennwort
		$database,			// Datenbank
		$type,				// Typ, momentan nur MySQL implementiert
		$db,				// Filedescriptor
		$log,				// Referenz auf das Logobjekt!
		$debug,				// Dump all queries to the logfile?
		$query_counter;		// Anzahl der SQL-Queries


	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function Database($host, $user, $password, $database, $type = 'mysql')
	*
	* Funktion:
	*			Setzt ein paar interne Variablen und baut eine Verbindung zu dem
	*			angegebenen Datenbankserver auf!
	*
	* Parameter:
	* - $host : Wo läuft die Datenbank?
	* - $user : Als wer sollen wir uns anmelden?
	* - $password : selbsterklärend :D
	* - $database : welche Datenbank?
	* - $type : Was für ne Datenbank haben wir da?? Default ist MySQL
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	* 			Bis jetzt ist nur Mysql implementiert, aber andere RDBMS lassen
	*			sich sehr schnell implementieren!
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function Database($host, $user, $password, $database, $type = 'mysql')
	{
		global $log;
		$this->query_counter = 0;
		$this->type = $type;
  		$this->log = $log;
		$this->debug = 0;

		if ($host)
		{
			$this->host = $host;
		}

		if ($user)
		{
			$this->user = $user;
		}
		else
		{
			$log->add_to_log('database', 'No user given, aborting!', 'error');
			return(0);
		}

		if ($password)
		{
			$this->password = $password;
		}
		else
		{
			$log->add_to_log('database', 'No password given, this doesnt make much sense! Aborting!', 'error');
			return(0);
		}

		if ($database)
		{
			$this->database = $database;
		}
		else
		{
			$log->add_to_log('database', 'No database given, to which shall i connect to? Aborting!', 'error');
			return(0);
		}

		$this->connect_db();
	}

	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function affected_rows()
	*
	* Funktion:
	*			Gibt die Anzahl der bearbeiteten Reihen des letzten INSERT,
	*			UPDATE oder DELETE Querys zurück
	*
	* Parameter:
	*
	* Rückgabe:
	* 			Anzahl der bearbeiteten Reihen
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function affected_rows()
	{
			switch($this->type)
			{
				case 'mysql':
					return(mysql_affected_rows());
					break;
				default:
					die('Sorry, you selected an unsupported databasesystem.');
			}
	}

	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function last_insert_id()
	*
	* Funktion:
	*			Gibt die ID des letzten INSERT-Querys zurück!
	*
	* Parameter:
	*
	* Rückgabe:
	* 			ID des zuletzt angelegten INSERT-Querys!
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function last_insert_id()
	{
			switch($this->type)
			{
				case 'mysql':
					return(mysql_insert_id());
					break;
				default:
					die('Sorry, you selected an unsupported databasesystem.');
			}
	}

	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function last_insert_id()
	*
	* Funktion:
	*			Gibt die Anzahl der bis jetzt ausgeführten SQL-Querys zurück!
	*
	* Parameter:
	*
	* Rückgabe:
	* 			Anzahl der Querys!
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function query_counter()
	{
		return($this->query_counter);
	}

	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function fetch_array($raw)
	*
	* Funktion:
	*			Holt einen Array aus dem übergebenen Result-Set
	*
	* Parameter:
	* - $raw : Result-Set
	*
	* Rückgabe:
	* 			Array
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function fetch_array($raw)
	{
		if ($raw)
		{
			switch($this->type)
			{
				case 'mysql':
					return(mysql_fetch_array($raw));
					break;
				default:
					die('Sorry, you selected an unsupported databasesystem.');
			}
		}
		else
		{
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 23.07.2003
	* Last edit: 23.07.2003
	*
	* function fetch_row($raw)
	*
	* Funktion:
	*			Holt einen Array aus dem übergebenen Result-Set
	*
	* Parameter:
	* - $raw : Result-Set
	*
	* Rückgabe:
	* 			Array
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function fetch_row($raw)
	{
		if ($raw)
		{
			switch($this->type)
			{
				case 'mysql':
					return(mysql_fetch_row($raw));
					break;
				default:
					die('Sorry, you selected an unsupported databasesystem.');
			}
		}
		else
		{
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function query($sql)
	*
	* Funktion:
	*			Setzt ein Query an den Server ab!
	*
	* Parameter:
	* - $sql: Query
	*
	* Rückgabe:
	* 			Das Ergebnis des Querys
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function query($sql)
	{
		global $log;

		$this->query_counter++;

		if ($this->debug > 0)
		{
			$log->add_to_log('Database::query', 'Database debug #' . $this->query_counter . ': ' . $sql );
		}

		$log->add_to_log('Database::query()', 'Query: ' . $sql, 'sqldebug');		

		if ($sql)
		{
			switch($this->type)
			{
				case 'mysql':
					if ($tmp = mysql_query($sql))
					{
						return($tmp);
					}
					else
					{
						return(0);
					}
					break;
				default:
					die('Sorry, you selected an unsupported databasesystem.');
			}
		}
		else
		{
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function num_rows($tmp)
	*
	* Funktion:
	*			Gibt die Anzahl der Datensätze in $tmp zurück
	*
	* Parameter:
	* - $tmp : Result-Set
	*
	* Rückgabe:
	* 			Anzahl Datensätze
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function num_rows($tmp)
	{
		switch($this->type)
		{
			case 'mysql':
				if ($tmp)
				{
					return(mysql_num_rows($tmp));
				}
				else
				{
					return(0);
				}
				break;
			default:
				die('Sorry, you selected an unsupported databasesystem.');
		}
	}

	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function connect_db()
	*
	* Funktion:
	*			Baut eine persistente Verbindung (falls möglich) zu dem im
	*			Konstruktor angegebenen Datenbankserver auf!
	*
	* Parameter:
	*
	* Rückgabe:
	* 			Anzahl Datensätze
	*
	* Bemerkungen:
	*			ACHTUNG: Würgt das gesamte script mittels die() ab, falls die
	*			Verbindung fehlschlägt!
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function connect_db()
	{
		global $log;
		switch($this->type)
		{
			case 'mysql':
				if ($this->db = mysql_pconnect($this->host, $this->user, $this->password))
				{
					if (mysql_select_db($this->database, $this->db))
					{
						return(1);
					}
					else
					{
						$log->add_to_log('database', mysql_error());
						die(mysql_error());
					}
				}
				else
				{
					$log->add_to_log('database', mysql_error());
					die(mysql_error());
				}
				break;
			default:
				die('Sorry, you selected an unsupported databasesystem.');
		}
	}

	/*******************************************************************************
	* Created: 18.07.2003
	* Last edit: 11.08.2003
	*
	* update_row($id, $table, $attribs)
	*
	* Funktion:
	*
	* Parameter:
	*
	* Rückgabe:
	* 			Anzahl der bearbeiteten Reihen
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	* - Return-Werte überarbeitet: 1 bei Erfolg, 0 bei erfolgreichem Query
	* 			ohne etwas geändert zu haben und -1 bei Fehlern!
	*
	*******************************************************************************/
	function update_row($id, $table, $attribs, $where = 0)
	{
		/***************************************************************************
		* Alle wichtigen Objekte holen:
		***************************************************************************/
		global $log, $db;

		/***************************************************************************
		* Übergebene Daten prüfen:
		***************************************************************************/
		if (!$id)
		{
			/***************************************************************************
			* URGS! Fataler Fehler, keine ID übergeben!!!
			***************************************************************************/
			$log->add_to_log('Database::update_row()', 'Called without an ID, Aborting!! *urgs*', 'error');
			return(0);
		}

		if (!count($attribs))
		{
			/***************************************************************************
			* Whoups, keine Daten übergeben... was soll ich jetzt machen??
			***************************************************************************/
			$log->add_to_log('Database::update_row()', 'Called without any data, Aborting!! *urgs*', 'error');

			// Wir brechen mal lieber ab...
			return(0);
		}

		if (!$where)
		{
				$where = '
					id = ' . $id . '
					';
		}

		/***************************************************************************
		* Ok, Daten scheinen vollständig zu sein! Also weiter!
		* SQL-Query bauen:
		***************************************************************************/
		$sql = '
				update
					' . $table . '
				set';

		$first_run = 1;
		while ($current = each($attribs))
		{
			if (!$first_run)
			{
				$sql .= ',';
			}

			$sql .= '
					' . $current[value][name] . ' = "' . $current[value][value] . '"
				';

			$first_run = 0;
		}

		$sql .= '
					where
					' .
					$where;

		/***************************************************************************
		* Query ausführen und prüfen:
		***************************************************************************/
		$db->query($sql);

		$result = $db->affected_rows();
		if ($result > 0)
		{
			/***************************************************************************
			* Juhu, alles in Ordnung!
			***************************************************************************/
			return(1);
		}
		else if ($result == 0)
		{
			/***************************************************************************
			* Ok, Query ist durchgelaufen, hat aber keine Datensätze geändert!
			***************************************************************************/
			return(0);
		}
		else
		{
			/***************************************************************************
			* URGS! Da ist was schiefgelaufen, Fehler
			***************************************************************************/
			$log->add_to_log('Database::update_row', 'Error while updating record! DB::affected_rows() returned 0! Aborting!', 'error');
			$log->add_to_log('Database::update_row', 'The following SQL-query was used:', 'error');
			$log->add_to_log('Database::update_row', $sql, 'error');
			return(-1);
		}
	}


	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function list_tables()
	*
	* Funktion:
	*			Gibt einen Array mit allen Tabellen in einer DAtenbank zurück
	*
	* Parameter:
	*
	* Rückgabe:
	* 			Anzahl Datensätze
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function list_tables($database)
	{
		global $log;

		switch($this->type)
		{
			case 'mysql':
					return(mysql_list_tables($database));
				break;
			default:
				die('Sorry, you selected an unsupported databasesystem.');
		}
	}


	// Still uncommented! Diese Funktionen brauche ich eigentlich nicht mehr..
	function debug($debug)
	{
		$this->debug = $debug;
	}

	function debug_on()
	{
		if ($this->debug)
		{
			echo 'Debug ON!';
		}
		else
		{
			echo 'Debug OFF!';
		}
	}

	function server_info()
	{
		echo mysql_get_host_info();
	}

}
?>
