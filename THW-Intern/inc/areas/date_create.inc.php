<?php

	require_once('inc/classes/class_form2.inc.php');
	require_once('inc/classes/class_helferliste.inc.php');

	if (1)
	{

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


		$tmp = 0;

		$page->title_bar();

		$form = new Form2($PHP_SELF, 'get', 'date_create_dialog');

		$fields = array();

		$fields[$tmp++] = array(
				'type' => 'separator',
				'value' => '<b>Datum</b>'
			);

		$fields[$tmp++] = array(
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
								'value' => '18'
							)
					)
			);

		$fields[$tmp++] = array(
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
								'value' => '18'
							),
						2 => array(
								'name' => 'onFocus',
								'value' => 'getBegindate(date_begin,this,\'time_force\',true,\'now\')'
							),
						3 => array(
								'name' => 'onBlur',
								'value' => 'chkDate(this,\'time_force\',true,date_begin.value)'
							)
					)
			);

		$fields[$tmp++] = array(
				'type' => 'separator',
				'value' => '<b>Infos zum Termin</b>'
			);

		$fields[$tmp] = array(
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
				$fields[$tmp][selections][$i][title] = $DATE_TYPES[($i + 1)][name];
				$fields[$tmp][selections][$i][value] = ($i + 1);
			}
			$tmp++;

		$fields[$tmp++] = array(
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

		$fields[$tmp++] = array(
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

		$fields[$tmp++] = array(
				'type' => 'hidden',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'date_create',
								'name' => 'action'
							)
					)
			);

		$fields[$tmp++] = array(
				'type' => 'buttons',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'Termin anlegen&gt;&gt;',
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


		if (!$GLOBALS[submit])
		{
			$form->set_precheck_error();
		}

		if ($form->is_form_error())
		{
			$message = $form->build_form();
			$width = '60%';
		}
		else
		{
			// Fertig!!! Daten eintragen!!!!!

			$width = '50%';
			$date = new tb_termin();

			if (!$flag_kueche)
			{
				$flag_kueche = 0;
			}

			$date_data = array(
					'date_begin' => datestring2unix($date_begin),
					'date_end' => datestring2unix($date_end),
					'flag_public' => $public,
					'kommentar' => trim($kommentar),
					'flag_edit' => 1,
					'terminart' => $terminart,
					'flag_kueche' => $flag_kueche
				);

			$last_id = $date->add_tb_termin($date_data);

			$message = 'Der Termin wurde gespeichert...';

			$message .= '<br>
								Weitere Optionen:
								<ul>
									<li><a href="' . $GLOBALS[PHP_SELF] . '?action=userlist_create&id=' . $last_id . '&type=date">Dem Termin eine Userliste anfügen</a></li>
								</ul>';



			$menu = array();
			$menu[0][link] = $PHP_SELF . '?action=date_create';
			$menu[0][text] = 'Weiteren Termin anlegen';
			$menu[1][link] = $PHP_SELF . '?action=date';
			$menu[1][text] = 'Userliste anhängen';
			$menu[2][link] = $PHP_SELF . '?action=date_view&id=' . $last_id;
			$menu[2][text] = 'Zum Termin';


			$form->form_shutdown();
		}

		echo $page->dialog_box('Termin anlegen', $message, 0, $menu, $width);
	}

?>

