<?php

	require_once('inc/classes/class_form2.inc.php');

// TODO :
// prüfen ob Userliste angehängt war und wenn keine mehr gewünscht wird, entfernen!
// prüfen ob Einheitenliste angehängt war, und evtl. entfernen!


	$form = new Form2($PHP_SELF, 'get', 'date_edit_dialog');

	function datestring2unix($date)
	{
		// TODO :
		// evtl. plausi-checks einbauen!

		// Zuerst Uhrzeit vom Datum trennen :
		$date_array = explode(" ", $date);

		// Uhrzeit splitten :
		$time = explode(":", $date_array[1]);
		// Datum splitten :
		$date = explode('.', $date_array[0]);

		// Beides zusammensetzen :
		$timestamp = mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
		return $timestamp;
	}

	$page->title_bar();


	// Zuerstmal alles aus der DB holen

	$termin = new tb_termin();
	$termin->load_tb_termin($id);

	// TODO: Presets evtl nur einmal laden!!! (Counter??)
	if (!$submit)
	{

		$presets = array();
		$presets[date_begin] = $termin->return_field('date_begin_readable');
		$presets[date_end] = $termin->return_field('date_end_readable');
		$presets[terminart] = $termin->return_field('terminart');
		$presets[kommentar] = $termin->return_field('kommentar');
		$presets[flag_kueche] = $termin->return_field('flag_kueche');

		$fields = array();

		$fields[0] = array(
				'type' => 'separator',
				'value' => '<b>Datum</b>'
			);

		$fields[] = array(
				'name' => 'date_begin',
				'type' => 'text',
				'title' => 'Anfangsdatum:',
				'important' => 1,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Hier das Anfangsdatum eingeben, Format: TT.MM.JJJJ HH:MM'
							),
						1 => array(
								'name' => 'size',
								'value' => '15'
							)
					)
			);

		$fields[] = array(
				'name' => 'date_end',
				'type' => 'text',
				'title' => 'Enddatum:',
				'important' => 1,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Hier das Enddatum eingeben, Format: TT.MM.JJJJ HH:MM'
							),
						1 => array(
								'name' => 'size',
								'value' => '15'
							)
					)
			);

		$fields[] = array(
				'type' => 'separator',
				'value' => '<b>Infos zum Termin</b>'
			);

		$fields[] = array(
				'name' => 'terminart',
				'type' => 'select',
				'title' => 'Termintyp',
				'important' => 1,
				'selections' => array(
					),
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Bitte den <b>Termintyp</b> auswählen!!'
							),
						1 => array(
								'name' => 'size',
								'value' => '1'
							)
					)
			);


		for ($i = 0; $i < count($DATE_TYPES); $i++)
		{
			$fields[4][selections][$i][title] = $DATE_TYPES[($i + 1)][name];
			$fields[4][selections][$i][value] = ($i + 1);
		}
		$fields[] = array(
				'name' => 'flag_kueche',
				'type' => 'checkbox',
				'title' => 'Küchenrelevant:',
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
								'value' => 'Wird bei diesem Termin Verpflegung durch die OV-Küche angefordert?'
							)
					)
			);

		$fields[] = array(
				'name' => 'kommentar',
				'type' => 'textarea',
				'title' => 'Kommentar:',
				'important' => 1,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Kurzer Kommentar worum es in dem Dienst geht!'
							),
						1 => array(
								'name' => 'cols',
								'value' => '40'
							),
						2 => array(
								'name' => 'rows',
								'value' => '10'
							)
					)
			);

		$fields[] = array(
				'type' => 'separator',
				'value' => '<b>Listen</b>'
			);

	}
	$fields[] = array(
			'type' => 'hidden',
			'important' => 0,
			'selections' => array(
					0 => array(
							'value' => 'date_edit',
							'name' => 'action'
						),
					1 => array(
							'value' => $id,
							'name' => 'id'
						)
				)
		);

	$fields[] = array(
			'type' => 'buttons',
			'important' => 0,
			'selections' => array(
					0 => array(
							'value' => 'Änderungen speichern&gt;&gt;',
							'type' => 'submit',
							'name' => 'submit'
						),
					1 => array(
							'value' => 'Reset',
							'type' => 'reset',
							'name' => 'reset'
						)
				)
		);
	$form->load_presets($presets);

	$form->load_form($fields);		// Formular laden
 	$form->precheck_form();

	if ($form->is_form_error())
	{
		$message = $form->build_form();
		$width = '60%';
	}
	else
	{
		if (!$flag_kueche)
		{
			$flag_kueche = 0;
		}

		$date_data = array(
				'date_begin' => datestring2unix($date_begin),
				'date_end' => datestring2unix($date_end),
				// 'flag_public' => $public,
				'kommentar' => htmlentities(trim($kommentar), ENT_QUOTES),
				'flag_edit' => 1,
				'terminart' => $terminart,
				'flag_kueche' => $flag_kueche
			);

		$last_id = $termin->add_tb_termin($date_data, $termin->return_field('ref_object_id'));

		$width = '50%';
		$message = 'Änderungen wurden übernommen!';

		$bottom_menu = array();
		$bottom_menu[0][text] = 'Termin betrachten';
		$bottom_menu[0][link] = $PHP_SELF . '?action=date_view&id=' . $id;

		$form->form_shutdown();
	}

	$menu = array();
	$menu[0][text] = 'Termin betrachten';
	$menu[0][link] = $PHP_SELF . '?action=date_view&id=' . $id;
	$menu[1][text] = 'Termin löschen';
	$menu[1][link] = $PHP_SELF . '?action=date_delete&id=' . $id;

	echo $page->dialog_box('Termin editieren', $message, $menu, $bottom_menu, $width);

?>