<?php


	require_once('inc/classes/class_form2.inc.php');
	require_once('inc/classes/class_tb_bericht.inc.php');

	$page->title_bar();

	$bericht = new tb_bericht2();

	echo '<table width=70% align=center border=0><tr><td>' . $bericht->edit_tb_bericht_interface($id, 'post') . '</td></tr></table>';

?>

