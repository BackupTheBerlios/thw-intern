<?php

/*	$menu = array();
	$menu[0][action] = 'report';
	$menu[0][text] = 'Übersicht';
	$menu[1][action] = 'report_create';
	$menu[1][text] = 'Bericht anlegen';*/
	$page->title_bar();


	$bericht = new tb_bericht();

	// Muss überhaupt schon was getan werden??
	if ($publish)
	{
		if ($publish == 'true')
		{
			// Bericht freigeben!!
			$message = 'Der Bericht wurde freigegeben!!';
			$bericht->publish_tb_bericht($id, 1);
		}
		else
		{
			// Bericht zurückziehen!
			$message = 'Der Bericht wurde zurückgezogen!!';
			$bericht->publish_tb_bericht($id, 0);
		}

		$menu = array();
		$menu[0][link] = $PHP_SELF . '?action=report';
		$menu[0][text] = 'Berichte';
		$menu[1][link] = $PHP_SELF . '?action=report_read&id=' . $id;
		$menu[1][text] = 'Den Bericht lesen';


		echo $page->dialog_box('Bericht freigeben', $message, 0, $menu, '50%');
	}
	else
	{

		$sql = '
			select
				' . TB_BERICHT . '.titel,
				' . TB_BERICHT . '.flag_freigegeben
			from
				' . TB_BERICHT . '
			where
				ref_object_id = ' . $id . '
		';

		$current = $db->fetch_array($db->query($sql));

		// Ist der Bericht evtl. bereits freigegeben??
		if ($current[flag_freigegeben])
		{
			$message = 'Soll der Bericht "<b>' . $current[titel] . '</b>" wieder zurückgezogen werden?';
			$menu = array();
			$menu[0][link] = $PHP_SELF . '?action=report_publish&publish=false&id=' . $id;
			$menu[0][text] = 'Ja, zurückziehen';
			$menu[1][link] = $PHP_SELF . '?action=report';
			$menu[1][text] = 'Nein, nicht zurückziehen';
			$menu[2][link] = $PHP_SELF . '?action=report_read&id=' . $id;
			$menu[2][text] = 'Den Bericht lesen';
		}
		else
		{
			$message = 'Soll der Bericht "<b>' . $current[titel] . '</b>" freigegeben werden?';
			$menu = array();
			$menu[0][link] = $PHP_SELF . '?action=report_publish&publish=true&id=' . $id;
			$menu[0][text] = 'Ja, freigeben';
			$menu[1][link] = $PHP_SELF . '?action=report';
			$menu[1][text] = 'Nein, nicht freigeben';
			$menu[2][link] = $PHP_SELF . '?action=report_read&id=' . $id;
			$menu[2][text] = 'Den Bericht lesen';
		}


		echo $page->dialog_box('Bericht freigeben', $message, 0, $menu, '50%');
	}

?>

