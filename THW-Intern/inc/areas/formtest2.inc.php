 <?php

	require_once('inc/class_form2.inc.php');

	$menu = array();
	$menu[0][link] = $PHP_SELF . '?action=formtest';
	$menu[0][text] = 'Diese Seite neu laden';
	$menu[1][link] = $PHP_SELF . '?action=formtest';
	$menu[1][text] = 'Formulartest';

	$page->title_bar($menu);

	$menu = array();
	$menu[0][link] = $PHP_SELF . '?action=formtest2';
	$menu[0][text] = 'Diese Seite nochmals laden';
	$menu[1][link] = $PHP_SELF . '?pre_action=logoff';
	$menu[1][text] = 'close_session()';

	// Neues Formularobjekt:
	$form = new Form2($PHP_SELF, 'get');


	$fields = array();

	$fields[0] = array(
			'name' => 'textfeld1',
			'type' => 'text',
			'title' => 'Textfeld 1',
			'important' => 1,
			'attribs' => array(
					0 => array(
							'name' => 'title',
							'value' => 'Dies ist ein kurzes Textfeld'
						),
					1 => array(
							'name' => 'size',
							'value' => '20'
						)
				)
		);
	$fields[] = array(
			'name' => 'password',
			'type' => 'password',
			'title' => 'Password',
			'important' => 1,
			'attribs' => array(
					0 => array(
							'name' => 'title',
							'value' => 'Hier bitte ihr gewünschtes Passwort eingeben!'
						),
					1 => array(
							'name' => 'size',
							'value' => '15'
						)
				)
		);
	$fields[] = array(
			'name' => 'textarea',
			'type' => 'textarea',
			'title' => 'Text',
			'important' => 0,
			'attribs' => array(
					0 => array(
							'name' => 'title',
							'value' => 'Hier bitte ein paar Sätze eingeben!'
						),
					1 => array(
							'name' => 'cols',
							'value' => '30'
						),
					2 => array(
							'name' => 'rows',
							'value' => '10'
						)
				)
		);
	$fields[] = array(
			'type' => 'separator',
			'value' => '<b>Angaben zum User</b>'
		);

	$fields[] = array(
			'name' => 'checkbox',
			'type' => 'checkbox',
			'title' => 'User...',
			'important' => 1,
			'selections' => array(
					0 => array(
							'name' => 'value',
							'value' => 'blabla'
						)
				),
			'attribs' => array(
					0 => array(
							'name' => 'title',
							'value' => 'Bitte wählen Sie dieses Feld an!!'
						)
				)
		);

	if ($form->return_submitted_field('checkbox'))
	{
		// Erweiterte Auswahl:
		$fields[] = array(
				'name' => 'user',
				'type' => 'radio',
				'title' => 'Radiobuttons',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'blabla',
								'title' => '...total doof?',
								'description' => 'Wählen Sie hier wenn Sie <b>BlaBla </b>bestellen wollen!'
							),
						1 => array(
								'value' => 'blublu',
								'title' => '...DAU?',
								'description' => 'Wählen Sie hier wenn Sie <b>BluBlu</b> bestellen wollen!'
							)
					),
				'attribs' => array(
					)
			);
	}
	$fields[] = array(
			'name' => 'select[]',
			'type' => 'select',
			'title' => 'Dropdown',
			'important' => 0,
			'selections' => array(
					0 => array(
							'value' => 'blabla',
							'title' => 'BlaBla'
						),
					1 => array(
							'value' => 'blublu',
							'title' => 'BluBlu'
						),
					2 => array(
							'value' => 'bleble',
							'title' => 'BleBle'
						),
					3 => array(
							'value' => 'bloblo',
							'title' => 'BloBlo'
						)
				),
			'attribs' => array(
					0 => array(
							'name' => 'title',
							'value' => 'Dies ist ein <b>hochmodernes</b> Dropdown!!'
						),
					1 => array(
							'name' => 'size',
							'value' => '3'
						)
				)
		);

	$fields[] = array(
			'name' => 'radio',
			'type' => 'radio',
			'title' => 'Radiobuttons',
			'important' => 0,
			'selections' => array(
					0 => array(
							'value' => 'blabla',
							'title' => 'BlaBla',
							'description' => 'Wählen Sie hier wenn Sie <b>BlaBla </b>bestellen wollen!'
						),
					1 => array(
							'value' => 'blublu',
							'title' => 'BluBlu',
							'description' => 'Wählen Sie hier wenn Sie <b>BluBlu</b> bestellen wollen!'
						)
				),
			'attribs' => array(
				)
		);
	$fields[] = array(
			'type' => 'hidden',
			'important' => 0,
			'selections' => array(
					0 => array(
							'value' => 'formtest2',
							'name' => 'action'
						)
				)
		);
	$fields[] = array(
			'type' => 'buttons',
			'important' => 0,
			'selections' => array(
					0 => array(
							'value' => 'Los &gt;&gt;',
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




	$form->load_form($fields);		// Formular laden
 	$form->precheck_form();

	if ($form->is_form_error())
	{
		$message = $form->build_form();
	}
	else
	{
		// Fertig!!! Daten eintragen!!!!!
		$message = 'FERTIG';

		// Formular aus dem Speicher werfen:
		$form->form_shutdown();
	}

	echo $page->dialog_box('Die Testbox', $message, $menu, $menu2, '50%');
?>
