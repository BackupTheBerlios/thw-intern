<?php

	/***************************************************************************
	* Laden der Einstellungen und Bibliotheken
	***************************************************************************/
	require_once("etc/tables.inc.php");
	require_once("etc/names.inc.php");
	require_once("etc/database.inc.php");
	require_once("inc/classes/classes.inc.php");
	require_once("inc/classes/class_database.inc.php");
	require_once("inc/classes/class_log.inc.php");
	require_once("inc/classes/class_form2.inc.php");
	require_once("inc/classes/class_guestbook.inc.php");

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


	session_start();

	/***************************************************************************
	* Interface Objekt anlegen und den Header bauen
	***************************************************************************/
	$page = new Interface('default.css');
	$page->html_header();


	$guestbook = new Guestbook();

	/***************************************************************************
	* Titelbalken bauen
	***************************************************************************/
	$menu = array();
	$menu[0][text] = 'Eintrag hinzuf&uuml;gen';
	$menu[0][link] = $PHP_SELF . '?action=add_entry';
	
	echo 	$page->dialog_box('THW ' . OV_NAME . ' - G&auml;stebuch', 0, $menu, 0, '98%')
			. '<br>';

	/***************************************************************************
	* Hier geht die eigentliche Seite jetzt los...
	* Hier unterscheiden wir jetzt zwischen den verschiedenen Aktionen:
	***************************************************************************/
	if ($action)
	{
		/***************************************************************************
		* Ok, es wurde eine Aktion ausgew?hlt, also her damit:
		***************************************************************************/

		switch($action)
		{
			// Sp?ter kommt hier vielleicht mal mehr rein!
			case 'add_entry':			
			default:
					// Default: Interface zum hinzuf?gen eines Eintrages:					
					echo $guestbook->add_entry_interface();
		}
		
	}
	else
	{
		/***************************************************************************
		* Zeigen wir also einfach mal die letzten 10 Eintr?ge an:
		***************************************************************************/

		echo $guestbook->show_entries_interface(); 		
	}



	$page->html_footer($db->query_counter());
	$log->shutdown();

?>
