<?php

	require_once('inc/classes/class_form2.inc.php');
	require_once('inc/classes/class_form.inc.php');

	$page->title_bar();


	$form = new Form2($PHP_SELF, 'get');

		$fields = array();
/*
		$fields[0] = array (
				'type' => 'select',
				'description' => 'Vorhandene User :',
				'name' => 'auswahl',
				'title' => 'vorhandene User: ',
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Dies ist ein kurzes Textfeld'
							),
						1 => array(
								'name' => 'size',
								'value' => '20'
							)
					)*/
		$fields[] = array(
				'name' => 'auswahl',
				'type' => 'select',
				'title' => 'Vorhandene User',
				'important' => 1,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Vorhandene User'
							),
						1 => array(
								'name' => 'size',
								'value' => '15'
							)
					)
			);


		$sql = '
				select
					' . TB_USER . '.id,
					' . TB_USER . '.name,
					' . TB_USER . '.vorname,
					' . TB_USER . '.flag_active
				from
					' . TB_USER . '
				order by ' . TB_USER . '.name
			';

		$raw = $db->query($sql);
		if ($db->num_rows($raw))
		{
			$first_user = 0;
			$i = 0;
			while ($current = $db->fetch_array($raw))
			{
				if (!$first_user)
				{
					$first_user = $current[id];
				}
				$fields[0][selections][$i][value] = $current[id];
				$fields[0][selections][$i][title] = '[' . $current[flag_active] . '] ' . $current[name] . ', ' . $current[vorname];
				$i++;
			}
		}

		$presets = array(
				'auswahl' => $first_user
			);

		$fields[] = array(
				'type' => 'hidden',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'administration_user_view',
								'name' => 'action'
							)
					)
			);

		$fields[] = array(
				'type' => 'buttons',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'Zum User &gt;&gt;',
								'type' => 'submit',
								'name' => 'submit'
							)
					)
			);

		$form->load_presets($presets);

		$form->load_form($fields);

		$form->precheck_form();

		$message = $form->build_form();

		$form->form_shutdown();



		$menu = array();
		$menu[0][text] = 'Neuen User anlegen';
		$menu[0][link] = $PHP_SELF . '?action=administration_user_create';

		echo $page->dialog_box('Benutzerverwaltung', $message, $menu, 0, '50%');




?>
