<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 24.07.2003
* Last edit: 24.07.2003
*
* administration_user_edit.inc.php
*
* Funktion:
*			Erstellt ein User::user_edit_interface() und ermöglicht so das
*			editieren eines Accounts!
*
* Bemerkungen:
*
* TODO:
*
* DONE:
*
*******************************************************************************/

	require_once('inc/classes/class_form2.inc.php');
	require_once('inc/classes/class_user.inc.php');

	$page->title_bar();

	$user_edit = new User();

	echo '<table width=50% align=center border=0><tr><td>' . $user_edit->edit_user_interface($id, 'administration_user_edit', 0) . '</td></tr></table>';

?>
