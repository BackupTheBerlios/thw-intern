<?php

	require_once('inc/classes/class_helferliste.inc.php');

	$page->title_bar();

	$date = new tb_termin();

	// Muss �berhaupt schon was getan werden??
	if ($confirmed)
	{
		$date->remove_tb_termin($id);

		$message = '
				Der Termin wurde gel�scht...
			';


		$userliste = new tb_helferliste2();
		$message .= $userliste->drop_userlist($id);

		$menu = array();
		$menu[0][link] = $PHP_SELF . '?action=date';
		$menu[0][text] = '�bersicht';


		echo $page->dialog_box('Termin l�schen', $message, 0, $menu, '50%');
	}
	else
	{
		$message = 'Soll der Termin wirklich gel�scht werden?';
		$menu = array();
		$menu[0][link] = $PHP_SELF . '?action=date_delete&confirmed=1&id=' . $id;
		$menu[0][text] = 'Ja, l�schen';
		$menu[1][link] = $PHP_SELF . '?action=date_view&id=' . $id;
		$menu[1][text] = 'Nein, nicht l�schen';

		echo $page->dialog_box('Termin l�schen', $message, 0, $menu, '50%');
	}

?>

