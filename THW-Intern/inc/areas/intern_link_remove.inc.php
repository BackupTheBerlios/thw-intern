<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 28.05.2003
* Last edit: 28.07.2003
*
* report_create.inc.php
*
* Funktion:
*			Interface zu Link::remove_links_interface()
*
* Bemerkungen:
*
* TODO:
*
* DONE:
*
*******************************************************************************/

	require_once('inc/classes/class_link.inc.php');

	$page->title_bar();

	$link_remove = new Link();

	echo $link_remove ->remove_links_interface($id);

?>
