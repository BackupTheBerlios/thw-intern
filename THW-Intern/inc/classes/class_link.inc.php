<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Last edit: 27.07.2003
*
* class Link
*
* Funktion:
*
* Parameter:
*
* Rückgabe:
*
* Bemerkungen:
*
* TODO:
* - Referer schreiben
* - mehr viewmodes schreiben!
* - snap-in Architektur implementieren (function snap_in() mit standardisiertem
* 			Interface??)
*
* DONE:
*
*******************************************************************************/
class Link extends tb_object2
{
	var
			$temp
		;

	/*******************************************************************************
	* Last edit: 27.07.2003
	*
	* function Link()
	*
	* Funktion:
	*
	* Parameter:
	*
	* Rückgabe:
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function Link()
	{
		global $log, $db;
		$log->add_to_log('Link::Link()', 'Constructor called...', 'debug');
	}

	/*******************************************************************************
	* Last edit: 27.07.2003
	*
	* function Link()
	*
	* Funktion:
	*			Speichert einen neuen Link in der Datenbank!
	*
	* Parameter:
	*
	* Rückgabe:
	*
	* TODO:
	* - Prüfen ob Kategorien vorhanden sind?? Oder woanders??
	*
	* DONE:
	*

	Felder:
		URI
		description
		Kategorie

		flag_public
	*******************************************************************************/
	function save_link($link_data)
	{
		global $log, $db, $session;
		$log->add_to_log('Link::save_link()', 'save_link() called...', 'debug');

		if (count($link_data))
		{
			if ($link_data[uri])
			{
				$tb_object_data = array(
						'date_begin' => 0,
						'date_end' => 0,
						'flag_public' => $link_data[flag_public]
					);
				if ($tb_object_id = $this->create_tb_object($tb_object_data))
				{

					$sql = '
							insert into
								' . TB_LINKS . '
							(
								description,
								uri,
								ref_category_id,
								ref_object_id
							)
							values
							(
								"' . htmlentities(trim($link_data[description])) . '",
								"' . htmlentities(trim($link_data[uri])) . '",
								' . $link_data[ref_category_id] . ',
								' . $tb_object_id . '
							)
						';

					$db->query($sql);
					if ($db->affected_rows())
					{
						return($db->last_insert_id());
					}
					else
					{
						$log->add_to_log('Link::save_link()', 'Error while saving tb_link record! db:affected_rows() returned 0!', 'error');
						$log->add_to_log('Link::save_link()', 'Here\'s the query i used:', 'error');
						$log->add_to_log('Link::save_link()', $sql, 'error');
						return(0);
					}
				}
				else
				{
					$log->add_to_log('Link::save_link()', 'Error while creating tb_object record, create_tb_object returned 0!', 'error');
					return(0);
				}
			}
			else
			{
				$log->add_to_log('Link::save_link()', 'Urgs, save_link() called without an URI... breaking off!', 'debug');
				return(0);
			}
		}
		else
		{
			$log->add_to_log('Link::save_link()', 'Urgs, save_link() called without any data... breaking off!', 'debug');
			return(0);
		}
	}

	/*******************************************************************************
	* Last edit: 27.07.2003
	*
	* function link_form($submit_caption, $action)
	*
	* Funktion:
	*			Speichert einen neuen Link in der Datenbank!
	*
	* Parameter:
	*
	* Rückgabe:
	*
	* TODO:
	* - Prüfen ob Kategorien vorhanden sind?? Oder woanders??
	*
	* DONE:
	*
	*******************************************************************************/
	function link_form($submit_caption, $action, $id = 0)
	{
		global $log, $db, $session;
		$log->add_to_log('Link::link_form()', 'link_form() called...', 'debug');

		$tmp = 0;
		$fields = array();

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
								'value' => 'Soll dieser Link in der öffentlichen Linkliste erscheinen?!'
							)
					)
			);

		$fields[$tmp] = array(
				'name' => 'ref_category_id',
				'type' => 'select',
				'title' => 'Kategorie:',
				'important' => 1,
				'selections' => array(
					),
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Bitte hier die Kategorie auswählen!'
							),
						1 => array(
								'name' => 'size',
								'value' => '1'
							)
					)
			);

			$i = 0;
			while ($current = each($GLOBALS[LINK_CATEGORIES]))
			{
				$fields[$tmp][selections][$i][title] = $current[value];
				$fields[$tmp][selections][$i][value] = $current[key];
				$i++;
			}
			$tmp++;

			$fields[$tmp++] = array(
					'name' => 'uri',
					'type' => 'text',
					'title' => 'URI:',
					'important' => 1,
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Hier die komplette Adresse eingeben, maximal 255 Zeichen!:'
								),
							1 => array(
									'name' => 'size',
									'value' => '30'
								)
						)
				);
			$fields[$tmp++] = array(
					'name' => 'description',
					'type' => 'text',
					'title' => 'Beschreibung:',
					'important' => 1,
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Hier bitte eine kurze Beschreibung eingeben, max. 255 Zeichen!!:'
								),
							1 => array(
									'name' => 'size',
									'value' => '30'
								)
						)
				);

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
	* Last edit: 28.07.2003
	*
	* function Link()
	*
	* Funktion:
	*			Speichert einen neuen Link in der Datenbank!
	*
	* Parameter:
	*
	* Rückgabe:
	*
	* TODO:
	* - Prüfen ob Kategorien vorhanden sind?? Oder woanders??
	*
	* DONE:
	*
	*******************************************************************************/
	function save_link_interface()
	{
		global $log, $db, $session, $page;
		$log->add_to_log('Link::save_link_interface()', 'save_link_interface() called...', 'debug');

		require_once('inc/classes/class_form2.inc.php');

		$form = new Form2($GLOBALS[PHP_SELF], $method, 'save_link_interface');

		$fields = $this->link_form('Los&gt;&gt;', 'intern_link_add');
		$form->load_form($fields);

		$presets = array(
				'uri' => 'http://'
			);
		$form->load_presets($presets);

		$form->precheck_form();

		if (!$GLOBALS[submit])
		{
			$form->set_precheck_error();
		}

		if ($form->return_submitted_field('uri') == 'http://')
		{
			$message = '<p class=error>Zumindest eine Adresse muss eingegeben werden!</p>';
			$form->set_precheck_error();
		}

		if ($form->is_form_error())
		{
			$message .= $form->build_form();
			$width = '50%';
			$title = 'Link hinzufügen';
		}
		else
		{
			$message = 'Der Link wird abgespeichert...';
			$title = '';

			$link_data = array(
					'flag_public' => $form->return_submitted_field('flag_public'),
					'uri' => $form->return_submitted_field('uri'),
					'description' => $form->return_submitted_field('description'),
					'ref_category_id' => $form->return_submitted_field('ref_category_id')
				);

			$link_id = $this->save_link($link_data);

			if ($link_id)
			{
				$title = 'Link gespeichert';
				$message .= '<b class=ok>OK</b>';
				$menu = array();
				$menu[0][text] = 'Zur Link übersicht';
				$menu[0][link] = $GLOBALS[PHP_SELF] . '?action=intern_link';
				$menu[1][text] = 'Weitere Links anlegen';
				$menu[1][link] = $GLOBALS[PHP_SELF] . '?action=intern_link_add';
			}

			else
			{
				$title = 'Fehler';
				$message .= '<br><b class=error>Whoups! Da ist was schiefgelaufen; Der Link wurde nicht gespeichert!!</b>';
			}
			$form->form_shutdown();
		}

		$width = '50%';
		echo $page->dialog_box($title, $message, 0, $menu, $width);
	}


	/*******************************************************************************
	* Last edit: 28.07.2003
	*
	* function edit_link_interface($id)
	*
	* Funktion:
	*			Editiert einen Link ...
	*
	* Parameter:
	*
	* Rückgabe:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function edit_link_interface($id)
	{
		global $log, $db, $session, $page;
		$log->add_to_log('Link::edit_link_interface()', 'edit_link_interface() called...', 'debug');

		require_once('inc/classes/class_form2.inc.php');

		$form = new Form2($GLOBALS[PHP_SELF], $method, 'edit_link_interface');

		$fields = $this->link_form('Los&gt;&gt;', 'intern_link_edit', $id);
		$form->load_form($fields);


		$sql = '
				select
					' . TB_LINKS . '.id,
					ref_object_id,
					uri,
					ref_category_id,
					description,
					date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y %H:%i") as date_create_readable,
					' . TB_OBJECT . '.flag_public
				from
					' . TB_LINKS . ',
					' . TB_OBJECT . '
				where
					' . TB_OBJECT . '.id = ' . $id . '
					and
					' . TB_OBJECT . '.id = ' . TB_LINKS . '.ref_object_id
				order by
					date_create desc
				limit 10
			';

		$raw = $db->query($sql);

		if ($db->num_rows($raw))
		{
			$current = $db->fetch_array($raw);

			$presets = array(
					'uri' => $current[uri],
					'description' => $current[description],
					'flag_public' => $current[flag_public],
					'ref_category_id' => $current[ref_category_id]
				);
			$form->load_presets($presets);

			$form->precheck_form();

			if (!$GLOBALS[submit])
			{
				$form->set_precheck_error();
			}

			if ($form->is_form_error())
			{
				$message .= $form->build_form();
				$width = '50%';
				$title = 'Link editieren';
			}
			else
			{
				$message = 'Der Link wird abgespeichert...';

				$link_data = array(
							0 => array(
									'name' => 'flag_public',
									'value' => $form->return_submitted_field('flag_public')
								)
						);

				$this->update_tb_object($link_data);

				$link_data = array(
							0 => array(
									'name' => 'uri',
									'value' => trim($form->return_submitted_field('uri'))
								),
							1 => array(
									'name' => 'description',
									'value' => htmlentities(trim($form->return_submitted_field('description')))
								),
							2 => array(
									'name' => 'ref_category_id',
									'value' => $form->return_submitted_field('ref_category_id')
								)
					);

				$title = 'Link gespeichert!';
				if ($db->update_row($current[id] , TB_LINKS, $link_data))
				{
					$message .= '<b class=ok>OK</b>';
				}
				else
				{
					$message .= '<b class=warning><br>Warnung</b>! Es wurden keine Datensätze geändert! Evtl. hast du gar nix geändert!...(siehe logfiles)';
				}
				$menu = array();
				$menu[0][text] = '&lt;&lt;Zur Link übersicht';
				$menu[0][link] = $GLOBALS[PHP_SELF] . '?action=intern_link';

				$form->form_shutdown();
			}

			$width = '50%';
		}
		else
		{
			$title = 'Fehler';
			$message = 'Whoups! Der ausgewählte Link wurde nicht gefunden... *urgs*';
			$width = '50%';
			$menu = array();
			$menu[0][text] = '&lt;&lt;Zur Link übersicht';
			$menu[0][link] = $GLOBALS[PHP_SELF] . '?action=intern_link';
		}

		return($page->dialog_box($title, $message, 0, $menu, $width));

	}



	/*******************************************************************************
	* Last edit: 28.07.2003
	*
	* function remove_links($ids)
	*
	* Funktion:
	*			Löscht alle übergebenen Links aus der Datenbank!
	*
	* Parameter:
	*
	* Rückgabe:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function remove_links($ids)
	{
		global $log, $db;
		$log->add_to_log('Link::remove_links()', 'remove_links() called...', 'debug');

		if (count($ids))
		{
			$removed = 0;
			while ($current = each($ids))
			{
				if ($this->remove_tb_object($current[value]))
				{
					$sql = '
							delete from
								' . TB_LINKS . '
							where
								ref_object_id = ' . $current[value] . '
						';
					$removed++;
				}
				else
				{
					$log->add_to_log('Link::remove_links()', 'Urgs! Error while removing tb_object record with id ' . $current[value] . '...', 'debug');
				}
			}

			return(removed);
		}
		else
		{
			$log->add_to_log('Link::remove_links()', 'Whoups, remove_links() called without any ids...', 'debug');
			return(0);
		}
	}

	/*******************************************************************************
	* Last edit: 28.07.2003
	*
	* function remove_link($ids)
	*
	* Funktion:
	*			Löscht alle übergebenen Links aus der Datenbank!
	*
	* Parameter:
	*
	* Rückgabe:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function remove_links_interface($id, $action = 'intern_link_remove')
	{
		global $log, $db, $page;
		$log->add_to_log('Link::remove_links_interface()', 'remove_links_interface() called...', 'debug');

		if ($id)
		{
			$title = 'Links löschen';

			if ($GLOBALS[confirmed])
			{
				$ids = array();
				$ids[] = $id;

				if ($temp = $this->remove_links($ids))
				{
					$title = 'Fertig';
					$message = 'Link gelöscht!';
					$bottom_menu = array();
					$bottom_menu[0][text] = 'OK&gt;&gt;';
					$bottom_menu[0][link] = $GLOBALS[PHP_SELF] . '?action=intern_link';

				}
				else
				{
					$title = 'Fehler';
					$message = 'Beim löschen ist ein Fehler aufgetreten!! *urgs*';
					$bottom_menu = array();
					$bottom_menu[0][text] = '&lt;&lt;Urgs, na gut!';
					$bottom_menu[0][link] = $GLOBALS[PHP_SELF] . '?action=intern_link';
				}
			}
			else
			{
				$message = 'Soll der Link wirklich gelöscht werden?';
				$bottom_menu = array();
				$bottom_menu[0][text] = '&lt;&lt; Nein';
				$bottom_menu[0][link] = $GLOBALS[PHP_SELF] . '?action=intern_link&view_link=' . $id;
				$bottom_menu[1][text] = 'Ja, löschen&gt;&gt;';
				$bottom_menu[1][link] = $GLOBALS[PHP_SELF] . '?action=' . $action . '&id=' . $id . '&confirmed=1';
			}
		}
		else
		{
			$title = 'Warnung';
			$message = 'Es wurden keine zu löschenden Einträge übergeben... ';

			$log->add_to_log('Link::remove_links()', 'Whoups, remove_links_interface() called without an id...', 'debug');
		}

		return($page->dialog_box($title, $message, $menu, $bottom_menu, '50%'));
	}


	/*******************************************************************************
	* Last edit: 28.07.2003
	*
	* function list_links($category, $action, $public = 1)
	*
	* Funktion:
	*			Zeigt Links an;
	*			Wenn category gesetzt ist, wird nur diese Kategorie angezeigt,
	*			andere Kategorien sind dann nicht anzeigbar! Wenn Public = 0
	*			Dann werden auch inoffizielle Links gezeigt!
	*
	* Parameter:
	*
	* Rückgabe:
	*
	* TODO:
	* - Mehr viewmodes einbauen!! Bis jetzt gibts nur latest, welcher die
	* 			letzten 10 Links anzeigt!!!
	*
	* DONE:
	*
	*******************************************************************************/
	function list_links($category, $action, $filter = 0, $public = 1)
	{
		global $log, $db, $session, $page;
		$log->add_to_log('Link::list_links()', 'list_links() called...', 'debug');


		if (!is_array($filter))
		{
			$filter = array();
		}

		if (!$filter[viewmode])
		{
			$filter[viewmode] = 'latest';
		}

		switch($filter)
		{
			default:
			case 'latest':
					/*******************************************************************************
					* Wir wollen die letzten 10 Links oder so...
					*******************************************************************************/

					$sql = '
							select
								ref_object_id,
								uri,
								ref_category_id,
								description,
								date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y %H:%i") as date_create_readable,
								date_create
							from
								' . TB_LINKS . ',
								' . TB_OBJECT . '
							where
								' . TB_OBJECT . '.id = ' . TB_LINKS . '.ref_object_id
							order by
								date_create desc
							limit 10
						';

					$raw = $db->query($sql);

					if ($db->num_rows($raw))
					{
						$message = '
								<table width=100% align=center border=0 class=list_table>
									<th>Link</th><th>Beschreibung</th>
							';

						while ($current = $db->fetch_array($raw))
						{
							if($current[date_create] > $session->user_info('last_login'))
							{
								$class = 'list_table_less_important';
							}
							else
							{
								$class = 'list_table_active';
							}

							$message .= '
									<tr class=' . $class . '>
										<td>
											<a href="' . $PHP_SELF . '?action=intern_link&view_link=' . $current[ref_object_id] . '" title="Hier klicken um mehr Infos zu dem Link zu erhalten!">' . $current[uri] . '</a>
										</td>
										<td>
											' . $current[description] . '
										</td>
									</tr>
								';
						}

						$message .= '
								</table>';
					}
					else
					{
						$message = 'Es wurden keine Links gefunden... Sind überhaupt welche eingetragen?';
					}

					$title = 'Die neusten 10 Links';

					$menu = array();
					$menu[0][text] = 'Link anlegen';
					$menu[0][link] = $GLOBALS[PHP_SELF] . '?action=intern_link_add';

					return($page->dialog_box($title, $message, $menu, 0, '100%'));

				break;
		}
	}


	/*******************************************************************************
	* Last edit: 28.07.2003
	*
	* function view_single_link($id, $menu = 0)
	*
	* Funktion:
	* 			Zeigt einen ausgewählten Link an!
	*
	* Parameter:
	*
	* Rückgabe:
	*
	* TODO:
	* - evtl. prüfen ob der Link überhaupt angezeigt werden darf (öffentlich?)
	*
	* DONE:
	*
	*******************************************************************************/
	function view_single_link($id, $menu = 0)
	{
		global $log, $db, $session, $page;

		$log->add_to_log('Link::view_single_link()', 'view_single_link() called...', 'debug');

		$sql = '
				select
					' . TB_LINKS . '.*,
					' . TB_OBJECT . '.*,
					date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y %H:%i") as date_create_readable,
					' . TB_USER . '.name,
					' . TB_USER . '.vorname
				from
					' . TB_LINKS . ',
					' . TB_OBJECT . ',
					' . TB_USER . '
				where
					' . TB_OBJECT . '.id = ' . $id . '
					and
					ref_object_id = ' . $id . '
					and ' . TB_USER . '.id = ' . TB_OBJECT . '.ref_user_id
			';

		$raw = $db->query($sql);

		if ($db->num_rows($raw))
		{
			$current = $db->fetch_array($raw);

			$title = 'Link';
			$message = '
					<table width=100% align=center border=0 class=list_table>
						<tr>
							<td class=list_table_active align=right>
								Kategorie:
							</td>
							<td>
								<b>' . $GLOBALS[LINK_CATEGORIES][$current[ref_category_id]] . '</b>
							</td>
						</tr>
						<tr>
							<td class=list_table_active align=right>
								URL:
							</td>
							<td>
								<b><a href="referer.php?id=' . $id . '" target=new>' . $current[uri] . '</a></b>
							</td>
						</tr>
						<tr>
							<td class=list_table_active align=right>
								Beschreibung:
							</td>
							<td>
								<b>' . $current[description] . '</b>
							</td>
						</tr>
						<tr>
							<td class=list_table_active align=right>
								Clicks:
							</td>
							<td>
								<b>' . $current[counter] . '</b>
							</td>
						</tr>
						<tr>
							<td class=list_table_active align=right>
								Erstellt von:
							</td>
							<td>
								<b>' . $current[vorname] . ' ' . $current[name] . '</b>
							</td>
						</tr>
						<tr>
							<td class=list_table_active align=right>
								am:
							</td>
							<td>
								<b>' . $current[date_create_readable] . '</b>
							</td>
						</tr>
					</table>
				';


			if (!$menu)
			{
				$menu = array();
				$menu[0][text] = 'Neuer Link';
				$menu[0][link] = $GLOBALS[PHP_SELF] . '?action=intern_link_add';
				$menu[1][text] = 'Link editeren';
				$menu[1][link] = $GLOBALS[PHP_SELF] . '?action=intern_link_edit&id=' . $id;
				$menu[2][text] = 'Link löschen';
				$menu[2][link] = $GLOBALS[PHP_SELF] . '?action=intern_link_remove&id=' . $id;
			}
			return($page->dialog_box($title, $message, $menu, 0, '100%'));
		}
		else
		{
			$log->add_to_log('Link::view_single_link()', 'Error while loading tb_links record...', 'debug');
			return(0);
		}
	}


	/*******************************************************************************
	* Last edit: 28.07.2003
	*
	* function referer($id)
	*
	* Funktion:
	* 			Ein einfacher referer, der die Anzahl der klicks speichert und
	*			den User weiterleitet!
	*
	* Parameter:
	* - $id : Wohin solls gehen?
	*
	* Rückgabe:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function referer($id)
	{
		global $log, $db, $session, $page;

		$sql = '
				select
					uri
				from
					' . TB_LINKS . '
				where
					ref_object_id = ' . $id . '
			';

		$raw = $db->query($sql);
		if ($db->num_rows($raw))
		{
			$current = $db->fetch_array($raw);

			$sql = '
					update
						' . TB_LINKS . '
					set
						counter = counter + 1
					where
						ref_object_id = ' . $id . '
				';

			$db->query($sql);

			header("Location: " . $current[uri]);

		}
		else
		{
			/*******************************************************************************
			* Whoups, den link gibts nicht...
			*******************************************************************************/
			$log->add_to_log('Link::referer()', 'Whoups, invalid id given...');
			return(0);
		}
	}
}
?>
