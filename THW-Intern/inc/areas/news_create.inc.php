<?php

	function datestring2unix($date)
	{
		$date_array = explode(".", $date);
		$timestamp = mktime(0, 0, 0, $date_array[1], $date_array[0], $date_array[2]);
		return $timestamp;
	}

	$page->title_bar();

	require_once('inc/classes/class_form2.inc.php');

	// Neues Formularobjekt:
	$form = new Form2($PHP_SELF, 'get', 'news_create_dialog');

	$fields = array();

	$fields[] = array(
			'name' => 'public',
			'type' => 'checkbox',
			'title' => 'Öffentlich?',
			'important' => 0,
			'selections' => array(
					0 => array(
							'name' => 'value',
							'value' => '1'
						)
				),
			'attribs' => array(
					0 => array(
							'name' => 'title',
							'value' => 'Dieses Feld anwählen wenn dieser Eintrag öffentlich zugänglich sein soll!'
						)
				)
		);

	$fields[] = array(
			'name' => 'titel',
			'type' => 'text',
			'title' => 'Titel:',
			'important' => 1,
			'attribs' => array(
					0 => array(
							'name' => 'title',
							'value' => 'Hier eine kurze Überschrift eingeben'
						),
					1 => array(
							'name' => 'size',
							'value' => '25'
						)
				)
		);

	$fields[] = array(
			'name' => 'text',
			'type' => 'textarea',
			'title' => 'Nachricht:',
			'important' => 1,
			'attribs' => array(
					0 => array(
							'name' => 'title',
							'value' => 'Hier bitte ein paar Sätze eingeben!'
						),
					1 => array(
							'name' => 'cols',
							'value' => '60'
						),
					2 => array(
							'name' => 'rows',
							'value' => '20'
						)
				)
		);

	$fields[] = array(
			'type' => 'hidden',
			'important' => 0,
			'selections' => array(
					0 => array(
							'value' => 'news_create',
							'name' => 'action'
						),
						'1' => array(
								'value' => REPORTTYPE_NEWS,
								'name' => 'berichtart',
							)
				)
		);

	$fields[] = array(
			'type' => 'buttons',
			'important' => 0,
			'selections' => array(
					0 => array(
							'value' => 'News anlegen &gt;&gt;',
							'type' => 'submit',
							'name' => 'submit'
						),
					1 => array(
							'value' => 'Felder zurücksetzen',
							'type' => 'reset',
							'name' => 'reset'
						)
				)
		);

	$form->load_form($fields);
	$form->precheck_form();

	if ($form->is_form_error())
	{
		$message = $form->build_form();
		$width = '50%';
	}
	else
	{
		$width = '50%';
		$bericht = new tb_bericht();

		$bericht_data = array(
				'date_begin' => time(),
				'date_end' => time(),
				'flag_public' => $public[key],
				'text' => trim($text),
				'titel' => trim($titel),
				'berichtart' => $berichtart
			);

		$last_id = $bericht->add_tb_bericht($bericht_data);

		$message = 'Der Beitrag wurde gespeichert...';
		$menu = array();
		$menu[0][link] = $PHP_SELF . '?action=news_create';
		$menu[0][text] = 'Weitere News anlegen';
		$menu[1][link] = $PHP_SELF . '?action=news';
		$menu[1][text] = 'Übersicht';
		$menu[2][link] = $PHP_SELF . '?action=news_read&id=' . $last_id;
		$menu[2][text] = 'Beitrag lesen';
	}

		echo $page->dialog_box('News erstellen', $message, 0, $menu, $width);

?>

