<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 26.05.2003
* Last edit: 17.07.2003
*
* intern.php
*
* Funktion:
* 			Hier laufen alle Fäden zusammen (o:
*
* Bemerkungen:
*
* TODO:
*
* DONE:
*
*******************************************************************************/

	/***************************************************************************
	* Laden der Einstellungen
	***************************************************************************/
	require_once("etc/tables.inc.php");
	require_once("etc/names.inc.php");
	require_once("etc/menu.inc.php");
	require_once("etc/database.inc.php");

	/***************************************************************************
	* Laden der wichtigsten Bibliotheken und Klassen
	***************************************************************************/
	require_once("inc/classes/classes.inc.php");
	require_once('inc/classes/class_database.inc.php');
	require_once('inc/classes/class_session.inc.php');

	require_once('inc/classes/class_lister.inc.php');
	require_once('inc/classes/class_log.inc.php');

	require_once('inc/classes/class_tb.inc.php');
	require_once('inc/classes/class_tb_bericht.inc.php');
	require_once('inc/classes/class_link.inc.php');

	require_once("inc/classes/tb_classes.inc.php");

	/***************************************************************************
	* Locale setzen, damit strftime deutsche Wochentage ausgibt!
	* FUNKTIONIERT NICHT!! BUG IN PHP??
	***************************************************************************/
	setlocale("LC_TIME", 'DE');

	/***************************************************************************
	* Log-Objekt
	***************************************************************************/
	$log = new Log();

	/***************************************************************************
	* Datenbankobjekt
	***************************************************************************/
	$db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$db->debug(0);

	/***************************************************************************
	* Session Objekt und prüfen ob Session vorliegt:
	***************************************************************************/
	$session = new Session();
	if ($session->check_session())
	{
		/***************************************************************************
		* Startseite setzen falls keine Seite ausgewählt wurde!
		***************************************************************************/
		if (!$action)
		{
			$action = DEFAULT_STARTPAGE;
		}

		/***************************************************************************
		* Rechtemanagement initialisieren!
		***************************************************************************/
		$rights = new Rights();

		/***************************************************************************
		* Darf der User hier überhaupt rein??
		***************************************************************************/
		if ($rights->check_right($session->user_info('id'), $action))
		{
			/***************************************************************************
			* Jo, hier darf er rein!
			***************************************************************************/
		}
		else
		{
			/***************************************************************************
			* Nein, darf er nicht! Also auf ACCESS_DENIED umleiten!
			***************************************************************************/
			$action = ACCESS_DENIED;
		}

		if ($pre_action)
		{
			include('inc/areas/' . $pre_action . '.inc.php');
		}

		/***************************************************************************
		* Interface Objekt initialisieren!!
		* Ggf. in der Zukunft ein Userdefiniertes Stylesheet laden...
		***************************************************************************/

		$stylesheet = $session->user_info('stylesheet');
		if (!$stylesheet)
		{
			$stylesheet = 'default.css';
		}
		$page = new Interface($stylesheet);
		$page->html_header();

		/***************************************************************************
		* Die eigentliche Seite laden und evtl. nicht vorhandene Seiten
		* abfangen...
		***************************************************************************/
		if (include('inc/areas/' . $action . '.inc.php'))
		{

		}
		else
		{
			/***************************************************************************
			* Nicht vorhandene Seite auf die Startseite umlenken!
			***************************************************************************/
			require('inc/areas/' . DEFAULT_STARTPAGE . '.inc.php');
		}

		$page->html_footer();

	}
	else
	{
		/***************************************************************************
		* Whoups, anscheinend sind wir nicht angemeldet!! Also zum Login!
		***************************************************************************/
		header("Location: login.php");
	}

	/***************************************************************************
	* Aufräumen!!
	* Wird hoffentlich bald durch Destruktoren erledigt!
	***************************************************************************/
	$log->shutdown();
?>


