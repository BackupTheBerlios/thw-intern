<?php

// 	function datestring2unix($date)
// 	{
// 		$date_array = explode(".", $date);
// 		$timestamp = mktime(0, 0, 0, $date_array[1], $date_array[0], $date_array[2]);
// 		return $timestamp;
// 	}

	require_once('inc/classes/class_form2.inc.php');
	require_once('inc/classes/class_tb_bericht.inc.php')

	$bericht = new tb_bericht2();
/*	$bericht->load_tb_bericht($id);*/

 	$page->title_bar();



	echo '<table width=70% align=center border=0><tr><td>' . $bericht->edit_tb_bericht_interface($id, 'post') . '</td></tr></table>';

// 	// Neues Formularobjekt:
// 	$form = new Form2($PHP_SELF, 'get', 'news_create_dialog');
// 
// 	$fields = array();
// 
// 	$fields[] = array(
// 			'name' => 'public',
// 			'type' => 'checkbox',
// 			'title' => 'Öffentlich?',
// 			'important' => 0,
// 			'selections' => array(
// 					0 => array(
// 							'name' => 'value',
// 							'value' => '1'
// 						)
// 				),
// 			'attribs' => array(
// 					0 => array(
// 							'name' => 'title',
// 							'value' => 'Dieses Feld anwählen wenn dieser Eintrag öffentlich zugänglich sein soll!'
// 						)
// 				)
// 		);
// 
// 	$fields[] = array(
// 			'name' => 'titel',
// 			'type' => 'text',
// 			'title' => 'Titel:',
// 			'important' => 1,
// 			'attribs' => array(
// 					0 => array(
// 							'name' => 'title',
// 							'value' => 'Hier eine kurze Überschrift eingeben'
// 						),
// 					1 => array(
// 							'name' => 'size',
// 							'value' => '25'
// 						)
// 				)
// 		);
// 
// 	$fields[] = array(
// 			'name' => 'text',
// 			'type' => 'textarea',
// 			'title' => 'Nachricht:',
// 			'important' => 1,
// 			'attribs' => array(
// 					0 => array(
// 							'name' => 'title',
// 							'value' => 'Hier bitte ein paar Sätze eingeben!'
// 						),
// 					1 => array(
// 							'name' => 'cols',
// 							'value' => '60'
// 						),
// 					2 => array(
// 							'name' => 'rows',
// 							'value' => '20'
// 						)
// 				)
// 		);
// 
// 	$fields[] = array(
// 			'type' => 'hidden',
// 			'important' => 0,
// 			'selections' => array(
// 					0 => array(
// 							'value' => 'news_edit',
// 							'name' => 'action'
// 						),
// 					1 => array(
// 								'value' => REPORTTYPE_NEWS,
// 								'name' => 'berichtart',
// 						),
// 					2 => array(
// 								'value' => $id,
// 								'name' => 'id'
// 						)
//
// 				)
// 		);
//
// 	$fields[] = array(
// 			'type' => 'buttons',
// 			'important' => 0,
// 			'selections' => array(
// 					0 => array(
// 							'value' => 'abspeichern &gt;&gt;',
// 							'type' => 'submit',
// 							'name' => 'submit'
// 						),
// 					1 => array(
// 							'value' => 'Felder zurücksetzen',
// 							'type' => 'reset',
// 							'name' => 'reset'
// 						)
// 				)
// 		);
//
// 	$presets = array(
// 			'public' => $bericht->return_field('flag_public'),
// 			'text' => $bericht->return_field('text'),
// 			'titel' => $bericht->return_field('titel')
// 		);
//
// 	$form->load_form($fields);
// 	$form->load_presets($presets);
// 	$form->precheck_form();
//
// 	if ($form->is_form_error())
// 	{
// 		$message = $form->build_form();
// 		$width = '50%';
// 	}
// 	else
// 	{
// 		$width = '50%';
//
// 		$bericht_data = array(
// 				'date_begin' => time(),
// 				'date_end' => time(),
// 				'flag_public' => $form->return_submitted_field('public'),
// 				'text' => $form->return_submitted_field('text'),
// 				'titel' => $form->return_submitted_field('titel'),
// 				'berichtart' => $berichtart
// 			);
//
// 		$bericht->update_tb_bericht($bericht->return_field('tb_bericht_id'), $id, $bericht_data);
//
// 		$message = 'Der Beitrag wurde gespeichert...';
// 		$menu = array();
// 		$menu[0][link] = $PHP_SELF . '?action=news_create';
// 		$menu[0][text] = 'News anlegen';
// 		$menu[1][link] = $PHP_SELF . '?action=news';
// 		$menu[1][text] = 'Übersicht';
// 		$menu[2][link] = $PHP_SELF . '?action=news_read&id=' . $id;
// 		$menu[2][text] = 'Beitrag lesen';
// 	}
//
// 	echo $page->dialog_box('News editieren', $message, 0, $menu, $width);

?>
