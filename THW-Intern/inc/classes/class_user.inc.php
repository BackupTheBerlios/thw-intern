<?php

/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 15.07.2003
* Last edit: 17.07.2003
*
* class user
*
* Funktion:
* 			Diese Klasse stellt alle Funktionalitäten bereit um User zu verwalten!
*			Das umfasst:
*			- User anlegen : user_create()
*			- UI dazu: user_create_interface()
*			- User editieren : user_edit()
*			- UI dazu: user_edit_interface()
*
*
* Bemerkungen:
*
* TODO:
* - UI um einen User zu betrachten!
*
* DONE:
*
*******************************************************************************/
class User
{
	var
		$current_user
		;

	/*******************************************************************************
	* Created: 24.07.2003
	* Last edit: 24.07.2003
	*
	* function User()
	*
	* Funktion:
	*			Konstruktor! Meldet sich nur kurz im debug-logfile!
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
	function User()
	{
		global $log;
		$log->add_to_log('User::User()', 'Constructor called!', 'debug');
	}

	/*******************************************************************************
	* Created: 24.07.2003
	* Last edit: 24.07.2003
	*
	* function load_user($id)
	*
	* Funktion:
	*			Lädt alle nötigen Infos aus tb_user in eine Objektinterne
	*			Variable!
	*
	* Parameter:
	* - $id : Id des Users!
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function load_user($id)
	{
		global $log, $db;
		$log->add_to_log('User::load_user()', 'load_user(' . $id . ') called!', 'debug');

		/*******************************************************************************
		* Zuerstmal die aktuellen Userdaten holen:
		*******************************************************************************/
		$sql = '
				select
					' . TB_USER . '.*,
					date_format(' . TB_USER . '.geburtstag, "%d.%m.%Y") as geburtstag,
					date_format(' . TB_USER . '.last_action, "%d.%m.%Y %H:%i:%S") as last_action_readable
				from
					' . TB_USER . '
				where
					' . TB_USER . '.id = ' . $id . '
			';

		/*******************************************************************************
		* Query absetzen und prüfen:
		*******************************************************************************/
		$raw = $db->query($sql);
		if ($db->num_rows($raw))
		{
			$this->current_user = $db->fetch_array($raw);
			$log->add_to_log('User::load_user()', 'Loaded user ' . $id . '!', 'debug');
			return(1);
		}
		else
		{
			$log->add_to_log('User::load_user()', 'Urgs! Tried to load a nonexisting user! ID: ' . $id);
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 24.07.2003
	* Last edit: 24.07.2003
	*
	* function user_info($field)
	*
	* Funktion:
	*			Gibt ein Feld aus dem Objektinternen Userarray zurück!
	*
	* Parameter:
	* - $field: welches Feld darfs denn sein?
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function user_info($field)
	{
		global $log;
		$log->add_to_log('User::user_info()', 'user_info(' . $field . ') called!', 'debug');

		return($this->current_user[$field]);
	}

	/*******************************************************************************
	* Created: 24.07.2003
	* Last edit: 24.07.2003
	*
	* function create_user($user_data)
	*
	* Funktion:
	*				Trägt einen User in die Datenbank ein!
	*
	* Parameter:
	* - $user_data : assoziativer array mit folgenden Feldern:
	*				- name
	*				- vorname
	*				- email
	*				- geburtstag
	*				- rang
	*				- einheit
	*
	* Bemerkungen:
	*			Die Funktion generiert automatisch ein 8 stelliges Passwort für
	*			den User!
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function create_user($user_data)
	{
		global $log, $db;
		$log->add_to_log('User::create_user()', 'create_user() called!', 'debug');
		if (count($user_data))
		{
			/*******************************************************************************
			* Zufallspasswort generieren:
			*******************************************************************************/
			$new_pass = md5(date("D,G,s,l", time()));
			$password = substr($new_pass, 1, 8);

			/*******************************************************************************
			* SQL bauen:
			*******************************************************************************/
			$sql = '
					insert
					into
						' . TB_USER . '
						(
							name,
							vorname,
							email,
							geburtstag,
							rang,
							password,
							flag_active,
							flag_online,
							last_action,
							last_login,
							ref_einheit_id
						)
						values
						(
							"' . $user_data[name] . '",
							"' . $user_data[vorname] . '",
							"' . $user_data[email] . '",
							"' . $user_data[geburtstag] . '",
							"' . $user_data[rang] . '",
							"' . $password . '",
							1,
							0,
							0,
							0,
							"' . $user_data[einheit] . '"
						)
				';

			/*******************************************************************************
			* Query absetzen und prüfen:
			*******************************************************************************/
			$db->query($sql);
			if ($db->affected_rows())
			{
				$log->add_to_log('User::create_user()', 'User ' . $user_data[vorname] . ' ' . $user_data[name] . ' created!');
				return($db->last_insert_id());
			}
			else
			{
				$log->add_to_log('User::create_user()', 'Error while creating user! affected_rows() returned 0!! Aborting!', 'error');
				$log->add_to_log('User::create_user()', 'I used the following SQL-query:', 'error');
				$log->add_to_log('User::create_user()', $sql, 'error');
				return(0);
			}
		}
		else
		{
			$log->add_to_log('User::create_user()', 'Error while creating user! No userdata given!! Aborting!');
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 24.07.2003
	* Last edit: 24.07.2003
	*
	* function user_form($submit_caption, $action, $id = 0, $restricted = 0)
	*
	* Funktion:
	*			Erstellt die Formularfelder die nötig sind um einen User zu
	*			editieren oder anzulegen!! Kann mit $restricted in einen
	*			restriktiven Modus versetzt werden, der nur Felder anzeigt
	*			die keine besonderen Rechte benötigen um verändert zu
	*			werden...
	*
	* Parameter:
	* - $submit_caption: Wie soll der Submit-Button beschriftet werden?
	* - $action: welche action soll gesetzt werden??
	* - $id: zum übergeben einer ID
	* - $edit_mode: Wenn gesetzt, werden Passwortfelder angezeigt!
	* - $restricted: restriktiver Modus
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function user_form($submit_caption, $action, $id = 0, $edit_mode = 0, $restricted = 0)
	{
		global $log;
		if ($restricted)
		{
			$log->add_to_log('User::user_form()', 'user_form() called in restricted mode!', 'debug');
		}
		else
		{
			$log->add_to_log('User::user_form()', 'user_form() called in normal mode!', 'debug');
		}

		$tmp = 0;

		$fields = array();

		$fields[$tmp++] = array(
				'type' => 'separator',
				'value' => '<b>Person</b>'
			);

		if (!$restricted)
		{
			$fields[$tmp++] = array(
					'name' => 'name',
					'type' => 'text',
					'title' => 'Name:',
					'important' => 1,
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Name des Users'
								),
							1 => array(
									'name' => 'size',
									'value' => '20'
								)
						)
				);
			$fields[$tmp++] = array(
					'name' => 'vorname',
					'type' => 'text',
					'title' => 'Vorname:',
					'important' => 1,
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Vorname des Users'
								),
							1 => array(
									'name' => 'size',
									'value' => '20'
								)
						)
				);

		}

		$fields[$tmp++] = array(
				'name' => 'geburtstag',
				'type' => 'text',
				'title' => 'Geburtstag:',
				'important' => 0,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Geburtstag des Users, Format: TT.MM.JJJJ'
							),
						1 => array(
								'name' => 'size',
								'value' => '10'
							),
						2 => array(
								'name' => 'onBlur',
								'value' => 'chkDate(geburtstag, false)'
							)
					)
			);

  		$fields[$tmp++] = array(
				'type' => 'separator',
				'value' => '<b>Erreichbarkeit</b>'
			);

		$fields[$tmp++] = array(
				'name' => 'email',
				'type' => 'text',
				'title' => 'E-Mail:',
				'important' => 0,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'E-Mail-Adresse des Users'
							),
						1 => array(
								'name' => 'size',
								'value' => '20'
							),
						2 => array(
								'name' => 'onBlur',
								'value' => 'chkMailaddress(email)'
							)
					)
			);

		if (!$restricted)
		{

			$fields[$tmp++] = array(
					'type' => 'separator',
					'value' => '<b>Funktion im OV</b>'
				);

			$fields[$tmp] = array(
					'name' => 'funktion',
					'type' => 'select',
					'title' => 'Funktion:',
					'important' => 1,
					'selections' => array(
						),
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Hier bitte die <b>Primärfunktion</b> des Users auswählen!!'
								),
							1 => array(
									'name' => 'size',
									'value' => '1'
								)
						)
				);

				for ($i = 0; $i < count($GLOBALS[RANG]); $i++)
				{
					$fields[$tmp][selections][$i][value] = ($i + 1);
					$fields[$tmp][selections][$i][title] = $GLOBALS[RANG][($i + 1)][name];
				}
			$tmp++;

			$fields[$tmp] = array(
					'name' => 'einheit',
					'type' => 'select',
					'title' => 'Einheit:',
					'important' => 1,
					'selections' => array(
						),
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Hier bitte die <b>Einheit</b> des Users auswählen!!'
								),
							1 => array(
									'name' => 'size',
									'value' => '1'
								)
						)
				);

				$i = 0;
				while ($current = each($GLOBALS[EINHEITEN]))
				{
					$fields[$tmp][selections][$i][value] =  $current[0];
					$fields[$tmp][selections][$i][title] = $current[1][name];
					$i++;
				}
			$tmp++;
		}

		if ($edit_mode)
		{
			$fields[$tmp++] = array(
					'type' => 'separator',
					'value' => '<b>Login</b>'
				);

			if ($restricted)
			{
				$fields[$tmp++] = array(
						'name' => 'old_password',
						'type' => 'password',
						'title' => 'Altes Passwort:',
						'important' => 0,
						'attribs' => array(
								0 => array(
										'name' => 'title',
										'value' => 'Hier dein altes Passwort eingeben!'
									),
								1 => array(
										'name' => 'size',
										'value' => '10'
									)
							)
					);

			}

			$fields[$tmp++] = array(
					'name' => 'new_password',
					'type' => 'password',
					'title' => 'Neues Passwort:',
					'important' => 0,
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Hier dein neues Passwort eingeben!'
								),
							1 => array(
									'name' => 'size',
									'value' => '10'
								)
						)
				);
			$fields[$tmp++] = array(
					'name' => 'new_password_confirm',
					'type' => 'password',
					'title' => 'Neues Passwort wdh.:',
					'important' => 0,
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Hier dein neues Passwort nochmals eingeben!'
								),
							1 => array(
									'name' => 'size',
									'value' => '10'
								)
						)
				);
		}

		$fields[$tmp++] = array(
				'type' => 'hidden',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => $action,
								'name' => 'action'
							),
						1 => array(
								'value' => $id,
								'name' => 'id'
							)
					)
			);

		if ($edit_mode)
		{
			$fields[$tmp++] = array(
					'type' => 'separator',
					'value' => '<p align=left><b>Achtung:</b><ul align=left><li>Wenn keine Änderungen vorgenommen werden, wird die Software beim Speichern einen Fehler melden!</li>
					<li>Wenn du dein Passwort änderst wirst du danach aus dem internen Bereich rausfliegen, da das bei dir gespeicherte Passwort nicht mehr stimmt! (TODO: reset_cookies())</li></ul></p>'
				);
		}

		$fields[$tmp++] = array(
				'type' => 'buttons',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => $submit_caption . '&gt;&gt;',
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
	* Created: 24.07.2003
	* Last edit: 24.07.2003
	*
	* function update_user($id, $user_data)
	*
	* Funktion:
	*			Updatet einen Usereintrag!
	*
	* Parameter:
	* - $id : Id des users
	* - $user_data: Die Daten die geändert werden sollen!
	*
	* Bemerkungen:
	*
	* TODO:
	* - Exception-Handling!!!!!!
	*
	* DONE:
	*
	*******************************************************************************/
	function update_user($id, $user_data)
	{
		global $log, $db;
		$log->add_to_log('User::update_user()', 'upadate_user(' . $id . ') called!', 'debug');

		return($db->update_row($id, TB_USER, $user_data));
	}


	/*******************************************************************************
	* Created: 24.07.2003
	* Last edit: 24.07.2003
	*
	* function function create_user_interface($action)
	*
	* Funktion:
	*			Erstellt ein UI zum anlegen von Usern und trägt diese auch
	*			mittels create_user() in die Datenbank ein!
	*
	* Parameter:
	* - $action : Auf welcher Seite sind wir?
	*
	* Bemerkungen:
	*			Diese Funktion gibt selbst nichts aus; Der Return-Code enthält
	*			eine komplette HTML-Tabelle mit allen Feldern enthalten...
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function create_user_interface($action)
	{
		global $log, $db, $page;
		$log->add_to_log('User::create_user_interface()', 'create_user_interface() called!', 'debug');

		$form = new Form2($PHP_SELF, 'get', 'user_create');

		$fields = $this->user_form('User anlegen', $action, 0, 0);
		$form->load_form($fields);
		$form->precheck_form();

		if ($form->is_form_error())
		{
			$title = 'User anlegen';
			$message = $form->build_form();
		}
		else
		{
 			$tmp = explode('.', $form->return_submitted_field('geburtstag'));
			$geburtstag = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0];
			$user_data = array(
					'name' => trim($form->return_submitted_field('name')),
					'vorname' => trim($form->return_submitted_field('vorname')),
					'email' => trim($form->return_submitted_field('email')),
					'geburtstag' => $geburtstag,
					'rang' => $form->return_submitted_field('rang'),
					'einheit' => $form->return_submitted_field('einheit')
				);

			if ($temp = $this->create_user($user_data))
			{
				$title = 'User angelegt';
				$message = 'Der User <b>' . trim($form->return_submitted_field('vorname')) . ' ' . trim($form->return_submitted_field('name')) . '</b> wurde erfolgreich angelegt!';
				$bottom_menu = array();
				$bottom_menu[0][text] = 'Zum User';
				$bottom_menu[0][link] = $GLOBALS[PHP_SELF] . '?action=administration_user_view&auswahl=' . $temp;
				$bottom_menu[1][text] = 'Weitere User anlegen';
				$bottom_menu[1][link] = $GLOBALS[PHP_SELF] . '?action=administration_user_create';
			}
			else
			{
				$log->add_to_log('User::create_user_interface()', 'Error while creating user! User::create_user() returned 0!! Aborting!', 'error');
				$title = 'Fehler';
				$message = 'Urgs! Beim anlegen des Users ist ein Fehler aufgetreten! Möglicherweise existiert dieser User bereits! (logfiles?)';
				$bottom_menu = array();
				$bottom_menu[0][text] = 'Weitere User anlegen';
				$bottom_menu[0][link] = $GLOBALS[PHP_SELF] . '?action=administration_user_create';
			}

			$form->form_shutdown();
		}
		return($page->dialog_box($title, $message, $menu, $bottom_menu, '100%'));
	}

	/*******************************************************************************
	* Created: 24.07.2003
	* Last edit: 24.07.2003
	*
	* function function edit_user_interface($id, $action, $restricted = 1)
	*
	* Funktion:
	*			Ähnlich zu create_user_interface(); Erstellt ein UI zum
	*			editieren von Usern, wahlweise im restriktiven oder im normalen
	*			Modus! Diese FUnktion lädt automatisch die Werte des Users...
	*
	* Parameter:
	* - $id : ID des zu editierenden Users
	* - $action : Auf welcher Seite sind wir?
	* - $restricted : Restriktiver Modus?
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function edit_user_interface($id, $action, $restricted = 1, $finish_menu = 0)
	{
		global $log, $db, $page, $session;
		$log->add_to_log('User::edit_user_interface()', 'edit_user_interface() called!', 'debug');

		/*******************************************************************************
		* Zuerstmal die Userdaten holen:
		*******************************************************************************/
		if ($this->load_user($id))
		{

			$log->add_to_log('User::edit_user_interface()', 'Ok, userdata loaded successfuly, commencing!', 'debug');

			$form = new Form2($GLOBALS[PHP_SELF], 'get', 'user_create');

			$fields = $this->user_form('abspeichern', $action, $id, 1, $restricted);

			$presets = array(
					'geburtstag' => $this->user_info('geburtstag'),
					'email' => $this->user_info('email'),
					'name' => $this->user_info('name'),
					'vorname' => $this->user_info('vorname'),
					'funktion' => array(
							0 =>  $this->user_info('rang')
						),
					'einheit' => array(
							0 =>  $this->user_info('ref_einheit_id')
						)
				);

			$form->load_presets($presets);

			$form->load_form($fields);

			$form->precheck_form();

			$password = 0;
			if ($form->return_submitted_field('old_password') or (!$restricted and $form->return_submitted_field('new_password')))
			{
				// $log->add_to_log('User::edit_user_interface()', 'Requesting password-change!', 'debug');
				/*******************************************************************************
				* Hossa, da will jemand sein Passwort ändern:
				******************************************************************************/
				if ( $form->return_submitted_field('new_password') != $this->user_info('password') )
				{
					if (($form->return_submitted_field('old_password') == $this->user_info('password')) or (!$restricted))
					{

						if ($form->return_submitted_field('new_password') == $form->return_submitted_field('new_password_confirm'))
						{
							$password = $form->return_submitted_field('new_password');
							$message = '<b class=ok>Passwort wurde geändert!</b>';
						}
						else
						{
							$form->set_precheck_error();
							$message = '<b class=error>Die beiden neuen Passwörter stimmen nicht überein!</b>';
						}
					}
					else
					{
						$form->set_precheck_error();
						$message = '<b class=error>Das eingegebene Passwort ist nicht richtig!</b>';
					}
				}
			}

			if (!$GLOBALS[submit])
			{
				$form->set_precheck_error();
			}

			if ($form->is_form_error())
			{
				$title = 'User editieren';
				$message .= $form->build_form();
			}
			else
			{
				$tmp = explode('.', $form->return_submitted_field('geburtstag'));
				$geburtstag = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0];
				$user_data = array();

				if (!$restricted)
				{
					$user_data[] = array(
							'name' => 'name',
							'value' => trim($form->return_submitted_field('name'))
						);
					$user_data[] = array(
							'name' => 'vorname',
							'value' => trim($form->return_submitted_field('vorname'))
						);
					$user_data[] = array(
							'name' => 'rang',
							'value' => trim($form->return_submitted_field('funktion'))
						);
					$user_data[] = array(
							'name' => 'ref_einheit_id',
							'value' => trim($form->return_submitted_field('einheit'))
						);
				}
				$user_data[] = array(
						'name' => 'email',
						'value' => trim($form->return_submitted_field('email'))
					);
				$user_data[] = array(
						'name' => 'geburtstag',
						'value' => $geburtstag
					);

				if ($password)
				{
					$user_data[] = array(
							'name' => 'password',
							'value' => $password
						);
				}

				if ($this->update_user($id, $user_data) > 0)
				{
					$title = 'User geändert!';
					$message = 'Die Änderungen wurden erfolgreich abgespeichert!';

					if ($finish_menu)
					{
						$bottom_menu = $finish_menu;
					}
					else
					{
						$bottom_menu = array();
						$bottom_menu[0][text] = 'Zum User';
						$bottom_menu[0][link] = $GLOBALS[PHP_SELF] . '?action=administration_user_view&auswahl=' . $id;
					}
				}
				else
				{
					$log->add_to_log('User::edit_user_interface()', 'Error while saving user (ID: ' . $id . ')! User::update_user() returned < 0!! Aborting!', 'error');
					$title = 'Fehler';
					$message = 'Urgs! Beim abspeichern der Änderungen ist etwas schiefgelaufen!! (logfiles?)';
					$bottom_menu = array();
					$bottom_menu[0][text] = '&lt;&lt;User nochmal editieren';
					$bottom_menu[0][link] = $GLOBALS[PHP_SELF] . '?action=' . $action . '&id=' . $id;
				}

				$form->form_shutdown();
			}
			return($page->dialog_box($title, $message, $menu, $bottom_menu, '100%'));
		}
		else
		{
			$log->add_to_log('User::edit_user_interface()', 'Error while loading user! User::load_user() returned 0!! Aborting!', 'error');
			return($page->dialog_box('Fehler', 'Whoups!! Konnte den gewählten User (ID: <b>' . $id . '</b>) nicht finden... breche ab!', 0, 0, '100%'));
		}
	}

	/*******************************************************************************
	* Created: 24.07.2003
	* Last edit: 24.07.2003
	*
	* function view_user_interface($id, $restricted = 1)
	*
	* Funktion:
	*
	* Parameter:
	* - $id : ID des zu editierenden Users
	* - $restricted : Restriktiver Modus?
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function view_user_interface($id, $restricted = 1, $menu = 0, $title = 'User betrachten')
	{
		global $log, $db, $page;
		$log->add_to_log('User::view_user_interface()', 'view_user_interface(' . $id . ') called!', 'debug');

		/*******************************************************************************
		* Zuerstmal die Userdaten holen:
		*******************************************************************************/
		if ($this->load_user($id))
		{
			$message = '
						<table width=100% align=center border=0 class=list_table>
							<tr>
								<td class=list_table_active align=right>
									Name:
								</td>
								<td>
									<b>' . $this->user_info('vorname') . ', ' . $this->user_info('name') . '</b>
								</td>
							</tr>
							<tr>
								<td class=list_table_active align=right>
									Online:
								</td>
								<td>
									<b>';

			if ($this->user_info('flag_online'))
			{
				$message .= '
					Ja </b> (letzter Pageload : <b>' . $this->user_info('last_action_readable') . '</b>)
					';
			}
			else
			{
				$message .= '
					Nein
				</b>
					';
			}

			$message .=
									'</b>
								</td>
							</tr>
							<tr>
								<td class=list_table_active align=right>
									Geburtstag:
								</td>
								<td>
									<b>' . $this->user_info('geburtstag') . '</b>
								</td>
							</tr>
							<tr>
								<td class=list_table_active align=right>
									E-Mail:
								</td>
								<td>
									<a href="mailto:' . $this->user_info('email') . '"><b>' . $this->user_info('email') . '</b></a>
								</td>
							</tr>
							<tr>
								<td class=list_table_active align=right>
									Funktion:
								</td>
								<td>
									<b>' . $GLOBALS[RANG][$this->user_info('rang')][name] . '</b>
								</td>
							</tr>
							<tr>
								<td class=list_table_active align=right>
									Einheit:
								</td>
								<td>
									<b>' . $GLOBALS[EINHEITEN][ ( floor( ( $this->user_info('ref_einheit_id')) /10 ) * 10 ) ][name] . ', ' . $GLOBALS[EINHEITEN][$this->user_info('ref_einheit_id')][name] . '</b>
								</td>
							</tr>';

			if (!$restricted)
			{
				$message .= '
							<tr>
								<td width=40% align=right class=list_table_active align=right>
									Passwort:
								</td>
								<td>
									<b>
										';
				if ($this->user_info('password'))
				{
					$message .= 'Ja	';
				}
				else
				{
					$message .= 'Nein';
				}
				$message .= '
									</b>
								</td>
							</tr>';
			}

			$message .= '
						</table>';


			return($page->dialog_box($title, $message, $menu, 0, '100%'));
		}
		else
		{
			$log->add_to_log('User::view_user_interface()', 'Error while loading user! User::load_user() returned 0!! Aborting!', 'error');
			return($page->dialog_box('Fehler', 'Whoups!! Konnte den gewählten User (ID: <b>' . $id . '</b>) nicht finden... breche ab!', 0, 0, '100%'));
		}
	}
}
?>
