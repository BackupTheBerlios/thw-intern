<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 16.07.2003
* Last edit: 17.07.2003
*
* class tb_bericht2
*
* Funktion:
*			eine von tb_object abgeleitete Klasse die sich um das verwalten von
*			Berichten aller Art kümmert!
*
* Bemerkungen:
*
* TODO:
*
* DONE:
*
*******************************************************************************/
class tb_bericht2 extends tb_object2
{
	var
			$bericht							// Hier speichern wir den aktuell geladenen Bericht!
		;
	/*******************************************************************************
	* Created: 16.07.2003
	* Last edit: 17.07.2003
	*
	* function tb_bericht2()
	*
	* Funktion:
	* 			Konstruktor
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function tb_bericht2()
	{
		require_once('inc/classes/class_helferliste.inc.php');
	}

	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 20.07.2003
	*
	* function create_tb_bericht()
	*
	* Funktion:
	* 			Legt einen Bericht an...
	*
	* Parameter:
	* - $bericht_daten: Array, enthält die Daten über den Bericht!
	*
	* Bemerkungen:
	*			Funktioniert prima, habs gerade ausgetestet!! (o:
	*			Nie wieder DB-Zombies!!
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function create_tb_bericht($bericht_daten)
	{
		global $db, $log;

		$log->add_to_log('tb_bericht::create_tb_bericht', 'create_tb_bericht() started!', 'debug');

		/***************************************************************************
		* Hui, wir legen einen neuen Bericht an!
		*
		* Zuerst prüfen wir mal die Übergebenen Daten:
		***************************************************************************/
		if (count($bericht_daten))
		{
			/***************************************************************************
			* Ok, Daten scheinen da zu sein!! Dann können wir jetzt mal einen
			* Eintrag in tb_object anlegen!
			***************************************************************************/
			$tb_object_data = array(
					'date_begin' => $bericht_daten[date_begin],
					'date_end' => $bericht_daten[date_end],
					'flag_public' => $bericht_daten[flag_public]
				);

			$tb_object_id = $this->create_tb_object($tb_object_data);
			if ($tb_object_id)
			{

				/***************************************************************************
				* OK, wir haben einen Eintrag in tb_object! Daran hängen wir jetzt unseren Bericht!
				***************************************************************************/
				$sql = '
						insert into
							' . TB_BERICHT . '
							(
								ref_object_id,
								text,
								titel,
								berichtart,
								flag_freigegeben
							)
							values
							(
								' . $tb_object_id . ',
								"' . htmlentities(stripslashes(trim($bericht_daten[text]))) . '",
								"' . htmlentities(stripslashes(trim($bericht_daten[titel]))) . '",
								' . $bericht_daten[berichtart] . ',
								0
							)
					';

				$log->add_to_log('tb_bericht2::create_tb_bericht', $sql, 'debug');


				/***************************************************************************
				* Query ausführen und prüfen:
				***************************************************************************/
				$db->query($sql);

				if ($db->affected_rows() > 0)
				{
					/***************************************************************************
					* Juhu, alles in Ordnung!
					***************************************************************************/
					return($tb_object_id);
				}
				else
				{
					/***************************************************************************
					* URGS! Da ist was schiefgelaufen, es wurde nix gespeichert!
					* Jetzt müssen wir noch aufräumen!!!
					* tb_object Eintrag löschen:
					***************************************************************************/
					$this->remove_tb_object($tb_object_id);
					$log->add_to_log('tb_bericht::create_tb_bericht', 'Error while creating tb_bericht record! Aborting!', 'error');
					return(0);
				}
			}
			else
			{
				/***************************************************************************
				* Whoups, Fehler beim Anlegen eines tb_object-Eintrages!!
				***************************************************************************/
				$log->add_to_log('tb_bericht::create_tb_bericht', 'Error while creating tb_object record! tb_object::create_tb_object() returned 0! Aborting!', 'error');
				return(0);
			}
		}
		else
		{
			/***************************************************************************
			* *urgs* Da wurde ja gar nix übergeben!!!
			***************************************************************************/
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 20.07.2003
	* Last edit: 02.08.2003
	*
	* function tb_bericht_form($submit_caption, $hard_presets = 0, $method = 'get', $form_name = 'create_tb_bericht')
	*
	* Funktion:
	*
	* Parameter:
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function tb_bericht_form($submit_caption, $hard_presets = 0, $method = 'get', $form_name = 'create_tb_bericht')
	{
		global $log, $db, $form;
		$log->add_to_log('tb_bericht::tb_bericht_form', 'tb_bericht_form loaded!', 'debug');

		$fields = array();
		$tmp = 0;

		if ($hard_presets[berichtart])
		{
			$fields[$tmp++] = array(
					'type' => 'separator',
					'value' => 'Typ: ' . $GLOBALS[REPORT_TYPES][$hard_presets[berichtart]][name] . '<input type=hidden name=berichtart value=' . $hard_presets[berichtart] . '>'
				);
		}
		else
		{
			$fields[$tmp] = array(
					'name' => 'berichtart',
					'type' => 'select',
					'title' => 'Berichttyp:',
					'important' => 1,
					'selections' => array(
						),
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Bitte hier die Berichtart wählen!'
								),
							1 => array(
									'name' => 'size',
									'value' => '1'
								)
						)
				);

				for ($i = 0; $i < count($GLOBALS[REPORT_TYPES]); $i++)
				{
					$fields[$tmp][selections][$i][title] = $GLOBALS[REPORT_TYPES][($i + 1)][name];
					$fields[$tmp][selections][$i][value] = ($i + 1);
				}
				$tmp++;
			}

		$fields[$tmp++] = array(
				'name' => 'public',
				'type' => 'checkbox',
				'title' => 'Öffentlich:',
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
								'value' => 'Soll dieser Bericht öffentlich sein?? <b>Achtung</b>, der Bericht muss dann noch freigegeben werden!'
							)
					)
			);

		$fields[$tmp++] = array(
				'name' => 'date_begin',
				'type' => 'text',
				'title' => 'Anfangsdatum:',
				'important' => 1,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Hier das Anfangsdatum eingeben, Format: TT.MM.JJJJ'
							),
						1 => array(
								'name' => 'size',
								'value' => '10'
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
								'value' => 'Hier das Enddatum eingeben, Format: TT.MM.JJJJ'
							),
						1 => array(
								'name' => 'size',
								'value' => '10'
							),
						2 => array(
								'name' => 'onFocus',
								'value' => 'getBegindate(date_begin, date_end, false, true)'
							),
						3 => array(
								'name' => 'onBlur',
								'value' => 'chkDate(date_end, false, false)'
							)
					)
			);

		$fields[$tmp++] = array(
				'name' => 'titel',
				'type' => 'text',
				'title' => 'Titel:',
				'important' => 1,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Hier den Titel eingeben'
							),
						1 => array(
								'name' => 'size',
								'value' => '25'
							)
					)
			);

		$fields[$tmp++] = array(
				'name' => 'text',
				'type' => 'textarea',
				'title' => 'Bericht:',
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

// 		$userlist = new tb_helferliste2();
// 		$fields = array_merge($fields, $userlist->create_userlist_dialog($tmp));

		$fields[$tmp] = array(
				'type' => 'hidden',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => $GLOBALS[action],
								'name' => 'action'
							),
						1 => array(
								'value' => $GLOBALS[id],
								'name' => 'id'
							)

					)
			);


		if ($hard_presets)
		{
			$i = 2;
			while ($temp = each($hard_presets))
			{
				$fields[$tmp][selections][$i][name] = $temp[value][name];
				$fields[$tmp][selections][$i][value] = $temp[value][value];
				$i++;
			}
		}

		$tmp++;


		$fields[$tmp++] = array(
				'type' => 'buttons',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => $submit_caption,
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


		return($fields);
	}

	/*******************************************************************************
	* Created: 20.07.2003
	* Last edit: 03.08.2003
	*
	* function create_tb_bericht_interface()
	*
	* Funktion:
	* 			Interface zu create_tb_bericht()
	*
	* Parameter:
	* - $hard_presets: array der feste Voreinstellungen enthält! Die Felder für
	*			diese Voreinstellungen werden ausgeblendet!
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function create_tb_bericht_interface($hard_presets = 0, $method = 'post', $form_name = 'create_tb_bericht')
	{
		/***************************************************************************
		* Objekte importieren:
		***************************************************************************/
		global $db, $log, $page, $form;

		$log->add_to_log('tb_bericht::create_tb_bericht_interface', 'create_tb_bericht_interface loaded!', 'debug');

		/***************************************************************************
		* Formularbibliothek laden:
		***************************************************************************/
		require_once('inc/classes/class_form2.inc.php');

		/***************************************************************************
		* Formularobjekt erstellen, Felder bauen:
		***************************************************************************/
		$form = new Form2($GLOBALS[PHP_SELF], $method, $form_name, $action = $GLOBALS[action]);

		/***************************************************************************
		* Ok, wir sind noch ganz  am Anfang:
		***************************************************************************/
		$title = 'Bericht anlegen - Schritt 1 / 3';
		$tmp = 0;

		if (!$GLOBALS[select_public])
		{
			$fields[$tmp++] = array(
					'type' => 'separator',
					'value' => 'Soll der zu erstellende Bericht öffentlich sein?'
				);
			$fields[$tmp++] = array(
					'name' => 'flag_public',
					'type' => 'checkbox',
					'title' => 'Öffentlich:',
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
									'value' => 'Soll dieser Bericht öffentlich sein?? <b>Achtung</b>, der Bericht muss dann noch freigegeben werden!'
								)
						)
				);
			$fields[$tmp++] = array(
					'type' => 'hidden',
					'important' => 0,
					'selections' => array(
							0 => array(
									'name' => 'select_public',
									'value' => 1
								)
						)
				);
		}
		else
		{
			$title = 'Bericht anlegen - Schritt 2 / 3';
			if ($GLOBALS[flag_public])
			{
				$blubb = '<b>Ja</b>';
			}
			else
			{
				$blubb = '<b>Nein</b>';
			}

			$fields[$tmp++] = array(
					'type' => 'separator',
					'value' => 'Bericht öffentlich: ' . $blubb
				);
			$fields[$tmp++] = array(
					'type' => 'hidden',
					'important' => 0,
					'selections' => array(
							0 => array(
									'name' => 'flag_public',
									'value' => $GLOBALS[flag_public]
								),
							1 => array(
									'name' => 'select_public',
									'value' => 1
								)
						)
				);

			if (!$GLOBALS[berichtart])
			{
				$fields[$tmp] = array(
						'name' => 'berichtart',
						'type' => 'select',
						'title' => 'Berichttyp:',
						'important' => 1,
						'selections' => array(
							),
						'attribs' => array(
								0 => array(
										'name' => 'title',
										'value' => 'Bitte hier die Berichtart wählen!'
									),
								1 => array(
										'name' => 'size',
										'value' => '1'
									)
							)
					);

				$i = 0;
				while ($current = each($GLOBALS[REPORT_TYPES]))
				{
					if ($GLOBALS[flag_public])
					{
						if ($current[value][public])
						{
							$fields[$tmp][selections][$i][title] = $current[value][name];
							$fields[$tmp][selections][$i][value] = $current[key];
							$i++;
						}
					}
					else
					{
						$fields[$tmp][selections][$i][title] = $current[value][name];
						$fields[$tmp][selections][$i][value] = $current[key];
						$i++;
					}
				}
				$tmp++;
			}
			else
			{
				$title = 'Bericht anlegen - Schritt 3 / 3';

				$fields[$tmp++] = array(
						'type' => 'separator',
						'value' => 'Berichttyp: <b> ' . $GLOBALS[REPORT_TYPES][$GLOBALS[berichtart]][name] . '</b>'
					);
				$fields[$tmp++] = array(
						'type' => 'hidden',
						'important' => 0,
						'selections' => array(
								0 => array(
										'name' => 'berichtart',
										'value' => $GLOBALS[berichtart]
									)
							)
					);

				if ($GLOBALS[REPORT_TYPES][$GLOBALS[berichtart]][date])
				{
					$fields[$tmp++] = array(
							'name' => 'date_begin',
							'type' => 'text',
							'title' => 'Anfangsdatum:',
							'important' => 1,
							'attribs' => array(
									0 => array(
											'name' => 'title',
											'value' => 'Hier das Anfangsdatum eingeben, Format: TT.MM.JJJJ'
										),
									1 => array(
											'name' => 'size',
											'value' => '10'
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
											'value' => 'Hier das Enddatum eingeben, Format: TT.MM.JJJJ'
										),
									1 => array(
											'name' => 'size',
											'value' => '10'
										),
									2 => array(
											'name' => 'onFocus',
											'value' => 'getBegindate(date_begin,this,\'time_purge\',true,\'now\')'
										),
									3 => array(
											'name' => 'onBlur',
											'value' => 'chkDate(this,\'time_purge\',true,date_begin.value)'
										)
								)
						);
				}

				$fields[$tmp++] = array(
						'name' => 'titel',
						'type' => 'text',
						'title' => 'Titel:',
						'important' => 1,
						'attribs' => array(
								0 => array(
										'name' => 'title',
										'value' => 'Hier den Titel eingeben'
									),
								1 => array(
										'name' => 'size',
										'value' => '25'
									)
							)
					);

				$fields[$tmp++] = array(
						'name' => 'text',
						'type' => 'textarea',
						'title' => 'Bericht:',
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
			}
		}

		$fields[$tmp++] = array(
				'type' => 'hidden',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => $GLOBALS[action],
								'name' => 'action'
							)
						)
			);
		$fields[$tmp++] = array(
				'type' => 'buttons',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'Weiter&gt;&gt;',
								'type' => 'submit',
								'name' => 'submit'
							),
						1 => array(
								'value' => 'Reset',
								'type' => 'reset'
							)
					)
			);

		$form->load_form($fields);
		$form->precheck_form();

		if (!$GLOBALS[submit])
		{
			$form->set_precheck_error();
		}

		if ($form->is_form_error())
		{
			$message = $form->build_form();
		}
		else
		{
			$title = 'Bericht wird angelegt';

			if (!$GLOBALS[flag_public])
			{
				$GLOBALS[flag_public] = 0;
			}

			$bericht_data = array(
					'date_begin' => strftime('%Y-%m-%d %H:%M:%S', $this->datestring2unix($GLOBALS[date_begin])),
					'date_end' => strftime('%Y-%m-%d %H:%M:%S', $this->datestring2unix($GLOBALS[date_end])),
					'flag_public' => $GLOBALS[flag_public],
					'text' => $GLOBALS[text],
					'titel' => $GLOBALS[titel],
					'berichtart' => $GLOBALS[berichtart]
				);

			if ($temp = $this->create_tb_bericht($bericht_data))
			{
				$message = '
									<p class=ok>Der Bericht "<B>' . $GLOBALS[titel] . '</b>" wurde angelegt!</p>
									Weitere Optionen:
									<ul>';
				if ((!$GLOBALS[flag_public]) and $GLOBALS[REPORT_TYPES][$GLOBALS[berichtart]][userlist])
				{
					$message .= '
										<li><a href="' . $GLOBALS[PHP_SELF] . '?action=userlist_create&id=' . $temp . '">Dem Bericht eine Userliste anfügen</a></li>';
				}
				$message .= '
										<li><a href="' . $GLOBALS[PHP_SELF] . '?action=report_addphoto&id=' . $temp . '">Dem Bericht Photos anfügen</a></li>
									</ul>';



				$tmp = 0;
				$menu = array();
				$menu[$tmp][text] = 'Bericht erstellen';
				$menu[$tmp++][link] = $GLOBALS[PHP_SELF] . '?action=report_create';
				$menu[$tmp][text] = 'Bericht gleichen Typs erstellen';
				$menu[$tmp++][link] = $GLOBALS[PHP_SELF] . '?action=report_create&select_public=' . $GLOBALS[select_public] . '&flag_public=' . $GLOBALS[flag_public] . '&berichtart=' . $GLOBALS[berichtart] . ' ';
				$menu[$tmp][text] = 'Zum Bericht';
				$menu[$tmp++][link] = $GLOBALS[PHP_SELF] . '?action=report_read&id=' . $temp;

				$form->form_shutdown();
			}
			else
			{
				$message = '<p class=error>Beim anlegen des Berichts ist ein Fehler augetreten! (logfiles?)</p>';
			}

		}

		/***************************************************************************
		* Output ausgeben:
		***************************************************************************/
		return($page->dialog_box($title, $message, $top_menu, $menu, '100%'));
	}


	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function update_tb_bericht()
	*
	* Funktion:
	* 			updated einen Bericht...
	*
	* Bemerkungen:
	*

	* - $tb_object_data: Enthält NUR die Daten die geändert werden sollen und
	* 			zwar in folgender Form:
	*				tb_object_data = array(
	*							0 => array(
	*										'name' => 'Datenbankfeld1',
	*										'value' => 'WertFürDatenbankfeld1'
	*									)
	*						);

	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function update_tb_bericht()
	{
	}



	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function function remove_tb_bericht()
	*
	* Funktion:
	* 			Löscht einen Bericht...
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function remove_tb_bericht()
	{

	}

	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function load_tb_bericht($id)
	*
	* Funktion:
	* 			Lädt einen Bericht (mit allen Zusätzen??)
	*
	* Parameter:
	* - $id : tb_object_id
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function load_tb_bericht($id)
	{
		global $db, $log;

		if ($id)
		{
			/***************************************************************************
			* Also, erstmal SQL basteln:
			***************************************************************************/
			$sql = '
					select
						' . TB_BERICHT . '.*,
						' . TB_BERICHT . '.id as tb_bericht_id,
						' . TB_OBJECT . '.*,
						date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y %H:%i") as date_create,
						date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y") as date_begin,
						date_format(' . TB_OBJECT . '.date_end, "%d.%m.%Y") as date_end,
						' . TB_USER . '.name,
						' . TB_USER . '.vorname
					from
						' . TB_BERICHT . ',
						' . TB_OBJECT . ',
						' . TB_USER . '
					where
						' . TB_OBJECT . '.id = ' . $id . '
						and
						' . TB_OBJECT . '.id = ' . TB_BERICHT . '.ref_object_id
						and
						' . TB_USER . '.id = ' . TB_OBJECT . '.ref_user_id
				';

			/***************************************************************************
			* Query ausführen und prüfen:
			***************************************************************************/
			$raw = $db->query($sql);
			if ($db->num_rows($raw))
			{
				/***************************************************************************
				* OK, alles klar!! Daten für return_field() zugänglich machen:
				***************************************************************************/
				$this->bericht = $db->fetch_array($raw);
				return(1);
			}
			else
			{
				/***************************************************************************
				* hmm... Kein Datensatz gefunden.. Ist das ein Fehler??
				* Wir schreiben auf jeden Fall mal einen Eintrag ins Logfile!
				***************************************************************************/
				$log->add_to_log('tb_bericht::load_tb_bericht', 'Whoups! Found no record for ID ' . $id . ' in ' . TB_BERICHT . '! That\'s odd...');
				return(0);
			}

		}
		else
		{
			/***************************************************************************
			* hmm... Keine ID übergeben; Also auch nix zurück!
			***************************************************************************/
			$log->add_to_log('tb_bericht::load_tb_bericht', 'Whoups! No ID given! That\'s odd...');
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 17.07.2003
	* Last edit: 17.07.2003
	*
	* function return_field($field)
	*
	* Funktion:
	* 			Gibt ein Feld aus dem geladenen Array zurück...
	*
	*******************************************************************************/
	function return_field($field)
	{
		return($this->bericht[$field]);
	}

	/*******************************************************************************
	* Created: 06.08.2003
	* Last edit: 06.08.2003
	*
	* function list_tb_bericht()
	*
	* Funktion:
	*
	* Bemerkungen:
	$filter = array(
			'fields' => array(
					'name' => 'value'
				),
			'options' => array(
					'name' => 'value'
				),
			'tb_fields' => array(				// Optional
					'name' => 'value'
				)
		);
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function list_tb_bericht($filter)
	{
		global $log, $db;

		if (!$filter)
		{
			$filter = array();
		}

		if (!$filter[tb_fields])
		{
			/***************************************************************************
			* Keine Optionen gegeben, also nehmen wir defaults, diese
			* sollten eigentlich genügen:
			***************************************************************************/
			$filter[tb_fields] = array();
			$filter[tb_fields] = array(
					0 => 'date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y %H:%i") as date_begin_readable',
					1 => 'date_format(' . TB_OBJECT . '.date_end, "%d.%m.%Y %H:%i") as date_end_readable',
					2 => TB_OBJECT . '.date_lastchange',
					3 => TB_OBJECT . '.date_create'
				);
		}

		if (!$filter[fields])
		{
			/***************************************************************************
			* Keine Optionen gegeben, also nehmen wir defaults, diese
			* sollten eigentlich genügen:
			***************************************************************************/
			$filter[fields] = array();
			$filter[fields] = array(
					0 => TB_BERICHT . '.titel',
					1 => TB_BERICHT . '.berichtart',
					2 => TB_BERICHT . '.ref_object_id'
				);
		}
		if (!$filter[where])
		{
			/***************************************************************************
			* Keine Optionen gegeben, also nehmen wir defaults, diese
			* sollten eigentlich genügen:
			***************************************************************************/
			$filter[where] = array();
			$filter[where] = array(
					0 => array(
							'name' => 'date_begin',
							'operator' => '>',
							'value' => 'now() - 10'
						)
				);
		}

		if (!$filter[limit])
		{
			$filter[limit] = array(
					0 => 0,
					1 => 10
				);
		}


		reset($filter[tb_fields]);

		$sql = '
				select';
		while ($current = each($filter[tb_fields]))
		{
			$sql .= '
					' . $current[value] . ',';
		}

		$firstrun = 1;
		while ($current = each($filter[fields]))
		{
			if ($firstrun)
			{
				$temp = '';
				$firstrun = 0;
			}
			else
			{
				$temp = ',';
			}

			$sql .= $temp . '
					' . $current[value];
		}

		$sql .= '
				from
					' . TB_BERICHT . ',
					' . TB_OBJECT . '
				where
						' . TB_OBJECT . '.id = ' . TB_BERICHT . '.ref_object_id';

		while ($current = each($filter[where]))
		{
			$sql .= '
					and
						' . $current[value][name] . ' ' . $current[value][operator] . ' ' . $current[value][value];
		}

		$sql .= '
				order by
					date_create desc';

		$sql .= '
				limit
					' . $filter[limit][0] . ', ' . $filter[limit][1];

		// echo nl2br($sql);
		return($raw = $db->query($sql));
	}

	/*******************************************************************************
	* Created: 06.08.2003
	* Last edit: 06.08.2003
	*
	* function list_tb_bericht()
	*
	* Funktion:
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function list_tb_bericht_interface($title, $mode)
	{
		global $log, $db, $page;

		switch ($mode)
		{
			case 'news':
					$filter = array(
						'tb_fields' => array(
								0 => 'date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y %H:%i") as date_create_readable'
							)
						);
					$filter[where] = array(
								0 => array(
										'name' => 'berichtart',
										'operator' => '=',
										'value' => REPORTTYPE_NEWS
									)
							);
				break;

			case 'diary':
					$filter = array(
						'tb_fields' => array(
								0 => 'date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y") as date_create_readable'
							)
						);
					$filter[where] = array(
								0 => array(
										'name' => 'berichtart',
										'operator' => '=',
										'value' => REPORTTYPE_DIARY
									)
							);
					$filter[limit] = array(
								0 => 0,
								1 => 5
							);
					$message = 'Das ist in den letzten Tagen in der Unterkunft so passiert:';
				break;

			case 'private_reports':
					$filter = array(
						'tb_fields' => array(
								0 => 'date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y") as date_begin_readable',
								1 => 'date_format(' . TB_OBJECT . '.date_end, "%d.%m.%Y") as date_end_readable'
							)
						);
					$filter[where] = array(
								0 => array(
										'name' => 'flag_public',
										'operator' => '=',
										'value' => 0
									)
							);
				break;

			default:
				$filter = 0;
		}


		$raw = $this->list_tb_bericht($filter);

		$message .= '
				<table width=100% align=center class=list_table>';
		while ($current = $db->fetch_array($raw))
		{
			$class = 'list_table_active';
			$message .= '
					<tr class=' . $class . '>';

			if ($current[date_create_readable])
			{
				$message .= '
						<td>' . $current[date_create_readable] . '</td>';
			}

			if ($current[date_begin_readable])
			{
				$message .= '
						<td>' . $current[date_begin_readable] . '</td>';
			}
			if ($current[date_end_readable])
			{
				$message .= '
						<td>' . $current[date_begin_readable] . '</td>';
			}
			if ($current[titel])
			{
				$message .= '
						<td><a href="' . $GLOBALS[PHP_SELF] . '?action=report_read&id=' . $current[ref_object_id] . '"><b>' . $current[titel] . '</b></a></td>';
			}
			$message .= '
					</tr>';
		}
		$message .= '
				</table>';

		return($page->dialog_box($title, $message, 0, 0, '100%'));
	}

	/*******************************************************************************
	* Created: 20.07.2003
	* Last edit: 03.08.2003
	*
	* function edit_tb_bericht_interface()
	*
	* Funktion:
	*
	* Parameter:
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function edit_tb_bericht_interface($id, $method = 'get', $form_name = 'edit_tb_bericht')
	{
		/***************************************************************************
		* Objekte importieren:
		***************************************************************************/
		global $db, $log, $page;

		if ($this->load_tb_bericht($id))
		{
			$log->add_to_log('tb_bericht::edit_tb_bericht_interface', 'edit_tb_bericht_interface() loaded!', 'debug');

			/***************************************************************************
			* Formularbibliothek laden:
			***************************************************************************/
			require_once('inc/classes/class_form2.inc.php');

			/***************************************************************************
			* Formularobjekt erstellen, Felder bauen:
			***************************************************************************/
			$form = new Form2($GLOBALS[PHP_SELF], $method, $form_name, $GLOBALS[action]);


			$presets = array(
					'berichtart' => $this->return_field('berichtart'),
					'public' => $this->return_field('flag_public'),
					'date_begin' => $this->return_field('date_begin'),
					'date_end' => $this->return_field('date_end'),
					'titel' => $this->return_field('titel'),
					'text' => $this->return_field('text')
				);
			$form->load_presets($presets);

			$fields = $this->tb_bericht_form('abspeichern&gt;&gt;', $hard_presets, $method, $form_name);

			/***************************************************************************
			* Ok, Felder sind jetzt da, lesen wir sie ein:
			***************************************************************************/
			$form->load_form($fields);
			$form->precheck_form();

			if ($form->is_form_error())
			{
				$message = $form->build_form();
				$width = '50%';
				$title = 'Bericht editieren';
			}
			else
			{
				$tb_object_data = array(
					0 => array(
							'name' => 'date_begin',
							'value' => strftime('%Y-%m-%d %H:%M:%S', $this->datestring2unix($GLOBALS[date_begin]))
						),
					1 => array(
							'name' => 'date_end',
							'value' => strftime('%Y-%m-%d %H:%M:%S', $this->datestring2unix($GLOBALS[date_end]))
						),
					2 => array(
							'name' => 'flag_public',
							'value' => $GLOBALS[public]
						)
					);

				if ($this->update_tb_object($id, $tb_object_data) != -1)
				{
					$bericht_data = array(
						0 => array(
								'name' => 'text',
								'value' => $GLOBALS[text]
							),
						1 => array(
								'name' => 'titel',
								'value' => $GLOBALS[titel]
							),
						2 => array(
								'name' => 'berichtart',
								'value' => $GLOBALS[berichtart][0]
							)
						);

					if ($db->update_row($id, TB_BERICHT, $bericht_data,  'ref_object_id = ' . $id) != -1)
					{
						/*******************************************************************************
						* Ok, alles klar, Bericht wurde verändert!
						***************************************************************************/
						$title = 'Bericht editieren';
						$width = '50%';
						$message = 'Der Bericht wurde abgespeichert!';

						$menu = array();
						$menu[0][link] = $PHP_SELF . '?action=report';
						$menu[0][text] = 'Berichtübersicht';
						$menu[1][link] = $PHP_SELF . '?action=report_read&id=' . $id;
						$menu[1][text] = 'Zum Bericht';
					}
					else
					{
						/*******************************************************************************
						* Hmm, da ist was schiefgelaufen!
						***************************************************************************/
						$title = 'Bericht editieren - Fehler';
						$message = 'Whoups!! Beim Abspeichern des Berichts ist ein Fehler aufgetreten! Bitte einen Admin kontaktieren! (Fehler bei $DB::update_row())';
						$width = '50%';

						$menu = array();
						$menu[0][link] = $PHP_SELF . '?action=report_edit&id=' . $id;
						$menu[0][text] = 'Bericht nochmal editieren';
						$menu[1][link] = $PHP_SELF . '?action=report';
						$menu[1][text] = 'Berichtübersicht';
					}
				}
				else
				{
					/*******************************************************************************
					* Hmm, da ist was schiefgelaufen!
					***************************************************************************/
					$title = 'Bericht editieren - Fehler';
					$message = 'Whoups!! Beim Abspeichern des Berichts ist ein Fehler aufgetreten! Bitte einen Admin kontaktieren! (Fehler bei tb_object::update_tb_object())';
					$width = '50%';

					$menu = array();
					$menu[0][link] = $PHP_SELF . '?action=report_edit&id=' . $id;
					$menu[0][text] = 'Bericht nochmal editieren';
					$menu[1][link] = $PHP_SELF . '?action=report';
					$menu[1][text] = 'Berichtübersicht';
				}

				/*******************************************************************************
				* Formular aus dem Speicher werfen!
				***************************************************************************/
				$form->form_shutdown();
			}

			return($page->dialog_box($title, $message, 0, $menu, $width));
		}
		else
		{
			return($page->dialog_box('Fehler', '<p class=error>Whoups, beim laden des Berichtes ist was schiefgelaufen! Breche ab...</p>', 0, 0, '50%'));
		}
	}

}

?>
