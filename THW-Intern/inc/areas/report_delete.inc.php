<?php

	$page->title_bar();


	$bericht = new tb_bericht();
	$bericht->load_tb_bericht($id);

	// Muss �berhaupt schon was getan werden??
	if ($confirmed)
	{
		// echo 'calling tb_bericht::delete_tb_bericht(' . $bericht->return_field('tb_bericht_id') . ', ' . $id . ')...<br>';
		$bericht->delete_tb_bericht($bericht->return_field('tb_bericht_id'), $id);

		// $bericht->delete_tb_bericht($bericht->return_field('id'), $id);

		$userliste = new tb_helferliste();
		$userliste->drop_list($bericht->return_field('ref_object_id'));

		$menu = array();
		$menu[0][link] = $PHP_SELF . '?action=report';
		$menu[0][text] = 'Berichte';

		$message = '
				Der Bericht wurde gel�scht...
			';

		echo $page->dialog_box('Bericht l�schen', $message, 0, $menu, '50%');
	}
	else
	{
		$message = 'Soll der Bericht "<b>' . $bericht->return_field('titel') . '</b>" wirklich gel�scht werden?';
		$menu = array();
		$menu[0][link] = $PHP_SELF . '?action=report_delete&confirmed=1&id=' . $id;
		$menu[0][text] = 'Ja, l�schen';
		$menu[1][link] = $PHP_SELF . '?action=report';
		$menu[1][text] = 'Nein, nicht l�schen';
		$menu[2][link] = $PHP_SELF . '?action=report_read&id=' . $id;
		$menu[2][text] = 'Den Bericht lesen';

		echo $page->dialog_box('Bericht l�schen', $message, 0, $menu, '50%');
	}

?>

