<?php
	$menu = array();
	$menu[0][link] = $PHP_SELF . '?action=formtest';
	$menu[0][text] = 'Diese Seite neu laden';
	$menu[1][link] = $PHP_SELF . '?action=formtest';
	$menu[1][text] = 'Formulartest';

	$page->title_bar($menu);

	$menu = array();
	$menu[0][link] = $PHP_SELF . '?action=formtest&DESTROY_FORM=1			';
	$menu[0][text] = 'Diese Seite nochmals laden';
	$menu[1][link] = $PHP_SELF . '?pre_action=logoff';
	$menu[1][text] = 'close_session()';

		$fields = array();

		$fields[0] = array (
				'type' => 'radio',
				'value' => array(
								0 => array(
										'value' => 'irgendwas',
										'description' => 'Irgendwas'
									),
								1 => array(
										'value' => 'irgendwo',
										'description' => 'Irgendwo'
									),
								2 => array(
										'value' => 'irgendwie',
										'description' => 'Irgendwie'
									),
								3 => array(
										'value' => 'nochwas',
										'description' => 'Nochwas'
									)
							),
				'name' => 'NeRadioAuswahl',
				'predefined' => '2',
				'important' => '1',
				'description' => '1. Radiobuttons'
			);

		$fields[] = array (
				'type' => 'text',
				'value' => 'Bitte hier nichts reinschreiben!',
				'name' => 'bloedheit',
				'important' => '1',
				'description' => '2. Ne Textbox'
			);

		$fields[] = array (
				'type' => 'password',
				'value' => '',
				'name' => 'password',
				'important' => '1',
				'description' => '3. Ein Password-Feld',
				'size' => '8'
			);

		$fields[] = array (
				'type' => 'textarea',
				'value' => '',
				'name' => 'textfeld',
				'important' => '0',
				'description' => '4. Ein Textfeld',
				'size' => 'cols="27" rows="10"'
			);

		$fields[] = array (
				'type' => 'checkbox',
				'value' => '1',
				'name' => 'bloed',
				'important' => '0',
				'description' => '5. User blöd'
			);

		$fields[] = array (
				'type' => 'checkbox',
				'value' => '1',
				'name' => 'dumm',
				'important' => '0',
				'description' => '6. User dumm'
			);
		$fields[] = array (
				'type' => 'checkbox',
				'value' => '1',
				'name' => 'dau',
				'important' => '1',
				'description' => '7. DAU'
			);

		$fields[] = array (
				'type' => 'select',
				'description' => '8. Dropdown-Liste',
				'name' => 'dropdown',
				'size' => '5',
				// 'multiple' => 'multiple',
				'value' => array(
						'0' => array(
								'value' => 'p1',
								'description' => 'Punkt1'
							),
						'1' => array(
								'value' => 'p2',
								'description' => 'Punkt2'
							),
						'2' => array(
								'value' => 'p3',
								'description' => 'Punkt3'
							)
					)

			);

		$fields[] = array (
				'type' => 'hidden',
				'hidden_fields' => array(
						'0' => array(
								'value' => '1',
								'name' => 'geheim'
							),
						'1' => array(
								'value' => '6',
								'name' => 'geheimer'
							),
						'2' => array(
								'value' => 'formtest',
								'name' => 'action'
							)
					)
			);

		$fields[] = array (
				'type' => 'button',
				'buttons' => array(
						0 => array(
								'name' => 'submit',
								'value' => 'Blubb',
								'type' => 'submit'
							),
						1 => array(
								'name' => '',
								'value' => 'RESET',
								'type' => 'reset'
							)
					)
			);


	$form = new Form($PHP_SELF, 'get', $fields, $submit);
	$form->pre_process_form();


	// Vorgeschlagene Struktur:
	if ($submit)
	{
		// Hier könnten noch Applikationssprezifische Prüfungen kommen:
		if (0)
		{
			// Leberkäs!
		}

		// Bauen des Formulars:
		if ($form->is_pre_process_error())
		{
			$message = $form->build_form();
		}
		else
		{
			// Bzw. das Eintragen der Daten in die Datenbank oder sonst wo hin..
			$message = 'Alles in Ordnung!';
		}
	}
	else
	{
		// Standart, nix gedrückt, das erste mal hier!
		$message = $form->build_form();
	}

	echo $page->dialog_box('Die Testbox', $message, $menu, $menu2, '50%');
?>
