<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: ??.05.2003
* Last edit: 22.07.2003
*
* report_create.inc.php
*
* Funktion:
*			Interface zu tb_bericht::create_tb_bericht!
*
* Bemerkungen:
*
* TODO:
*
* DONE:
*
*******************************************************************************/

	require_once('inc/classes/class_form2.inc.php');

	$page->title_bar();

	$link = new Link();

	if($view_link)
	{
		echo '<table width=50% align=center border=0><tr><td>' . $link->view_single_link($view_link) . '</td><tr></table>';
	}
	else
	{
		echo '<table width=70% align=center border=0><tr><td>' . $link->list_links(0, $action, 0) . '</td><tr></table>';
	}

?>
