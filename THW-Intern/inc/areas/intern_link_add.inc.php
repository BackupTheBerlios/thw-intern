<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 27.05.2003
* Last edit: 28.07.2003
*
* report_create.inc.php
*
* Funktion:
*			Interface zu Link::save_link_interface()
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

	$link_add = new Link();

	echo $link_add ->save_link_interface();

?>
