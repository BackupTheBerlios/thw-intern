<?php

	$page->title_bar();


	$bericht = new tb_bericht();
	$bericht->load_tb_bericht($id);

	// Muss �berhaupt schon was getan werden??
	if ($confirmed)
	{

		// echo 'calling tb_bericht::delete_tb_bericht(' . $bericht->return_field('tb_bericht_id') . ', ' . $id . ')...<br>';
		$bericht->delete_tb_bericht($bericht->return_field('tb_bericht_id'), $id);

		// Deaktiviert da wir f�r News keine Userlisten brauchen!
		// 		$userliste = new tb_helferliste();
		// 		$userliste->drop_list($bericht->return_field('tb_object_id'));

		$menu = array();
		$menu[0][link] = $PHP_SELF . '?action=news';
		$menu[0][text] = '�bersicht';

		$message = '
				Der Beitrag wurde gel�scht...
			';

		echo $page->dialog_box('News l�schen', $message, 0, $menu, '50%');
	}
	else
	{
		$message = 'Soll der Beitrag "<b>' . $bericht->return_field('titel') . '</b>" wirklich gel�scht werden?';
		$menu = array();
		$menu[0][link] = $PHP_SELF . '?action=news_delete&confirmed=1&id=' . $id;
		$menu[0][text] = 'Ja, l�schen';
		$menu[1][link] = $PHP_SELF . '?action=news';
		$menu[1][text] = 'Nein, nicht l�schen';
		$menu[2][link] = $PHP_SELF . '?action=news_read&id=' . $id;
		$menu[2][text] = 'Den Beitrag lesen';

		echo $page->dialog_box('News l�schen', $message, 0, $menu, '50%');
	}

?>

