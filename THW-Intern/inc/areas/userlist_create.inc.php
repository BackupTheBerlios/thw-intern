<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: ??.05.2003
* Last edit: 22.07.2003
*
* userlist_delete.inc.php
*
* Funktion:
*
* Bemerkungen:
*
* TODO:
*
* DONE:
*
*******************************************************************************/

	require_once('inc/classes/class_form2.inc.php');
	require_once('inc/classes/class_helferliste.inc.php');


	$page->title_bar();

	$userliste = new tb_helferliste2();

	$menu = array();

	echo '<table width=70% align=center border=0><tr><td>' . $userliste->create_userlist_interface($id, $action, $type) . '</td></tr></table>';

?>
