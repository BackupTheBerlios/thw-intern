<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 29.07.2003
* Last edit: 29.07.2003
*
* referer.php
*
* Funktion:
* 			Ein einfacher referer, ruft eigentlich nur Link::referer() auf!
*
* Bemerkungen:
*
* TODO:
* - evtl. mal ausmisten!
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
	require_once('inc/classes/class_log.inc.php');
	require_once('inc/classes/class_database.inc.php');
	require_once('inc/classes/class_tb.inc.php');
	require_once('inc/classes/class_link.inc.php');
	// require_once('inc/classes/class_session.inc.php');
	// require_once("inc/classes/tb_classes.inc.php");

	$log = new Log();

	$db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$db->debug(0);

	$page = new Interface('default.css');


	if ($id)
	{
		$link = new Link();
		$link->referer($id);
	}
	else
	{
		$page->html_header();

		echo 	$page->dialog_box('THW ' . OV_NAME, 0, 0, 0, '98%')
			. '<br>';

		echo $page->dialog_box('Fehler', 'Whoups, ungültige Link-ID', 0, 0, '50%');

		$page->html_footer();
	}

?>
