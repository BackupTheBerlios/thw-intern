<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 24.07.2003
* Last edit: 24.07.2003
*
* administration_user_create.inc.php
*
* Funktion:
*			Erstellt ein User::user_create_interface() und ermöglicht so das
*			anlegen von Usern!
*
* Bemerkungen:
*
* TODO:
*
* DONE:
*
*******************************************************************************/

	$page->title_bar();

	require_once('inc/classes/class_form2.inc.php');
	require_once('inc/classes/class_user.inc.php');

	$temp = new User();
	echo '<table width=50% align=center border=0><tr><td>' . $temp->create_user_interface('administration_user_create') . '</td></tr></table>';

?>
