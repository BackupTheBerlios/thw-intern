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

	$report_create = new tb_bericht2();

	echo '<table width=70% align=center border=0><tr><td>' . $report_create->create_tb_bericht_interface($hard_presets) . '</td></tr></table>';

?>
