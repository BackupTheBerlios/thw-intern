<?php

/*******************************************************************************
* (c) 2003 Jakob K?lzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 25.07.2003
* Last edit: 25.07.2003
*
* class guestbook
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
class Guestbook
{	
	/*******************************************************************************
	* Created: 25.07.2003
	* Last edit: 25.07.2003
	*
	* function Guestbook()
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
	function Guestbook()
	{
		global $log;
		$log->add_to_log('Guestbook::Guestbook()', 'Constructor called!', 'guestbook');
	}

	/*******************************************************************************
	* Created: 25.07.2003
	* Last edit: 13.08.2003
	*
	* function add_entry($data)
	*
	* Funktion:
	* 			Speichert einen Datensatz in tb_guestbook!
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
	function add_entry($data)
	{
		global $log, $db;
		$log->add_to_log('Guestbook::add_entry()', 'add_entry() called', 'guestbook');

		if (count($data))
		{
			$sql = '
					insert into
						' . TB_GUESTBOOK . '
					(
						name,
						date,
						message,
						email,
						homepage,
						ip
					)
					values
					(
						"' . htmlentities(trim($data[name])) . '",
						"' . strftime('%Y-%m-%d %H:%M:%S') . '",
						"' . htmlentities(trim($data[message])) . '",
						"' . htmlentities(trim($data[email])) . '",
						"' . htmlentities(trim($data[homepage])) . '",
						"' . $GLOBALS[REMOTE_ADDR] . '"
					)
				';

			$db->query($sql);
			if ($db->affected_rows() > 0)
			{
				return(1);
			}
			else
			{
				$log->add_to_log('Guestbook::add_entry()', 'ERROR: Problem with inserting entry, db::affected_rows returned 0! Aborting...', 'guestbook');
				$log->add_to_log('Guestbook::add_entry()', 'ERROR: Here\'s the query i used:', 'guestbook');
				$log->add_to_log('Guestbook::add_entry()', $sql, 'guestbook');
				return(0);
			}
		}
		else
		{
			$log->add_to_log('Guestbook::add_entry()', 'ERROR: add_entry() called without any data! Aborting...', 'guestbook');
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 25.07.2003
	* Last edit: 13.08.2003
	*
	* function add_entry_interface($data)
	*
	* Funktion:
	* 			Baut ein schönes UI für add_entry()
	*
	* Parameter:
	*	
	*******************************************************************************/
	function add_entry_interface()
	{
		global $log, $db, $page;
		$log->add_to_log('Guestbook::add_entry_interface()', 'add_entry_interface() called', 'guestbook');

		$tmp = 0;

		$form = new Form2($GLOBALS[PHP_SELF], 'post', 'guestbook', $action = $GLOBALS[action]);

		$fields = array();

		$fields[$tmp++] = array(
				'name' => 'name',
				'type' => 'text',
				'title' => 'Name:',
				'important' => 1,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Ihr Name'
							),
						1 => array(
								'name' => 'size',
								'value' => '20'
							)
					)
			);
		$fields[$tmp++] = array(
				'name' => 'email',
				'type' => 'text',
				'title' => 'E-Mail:',
				'important' => 0,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Ihre E-Mail-Adresse'
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
		$fields[$tmp++] = array(
				'name' => 'homepage',
				'type' => 'text',
				'title' => 'Homepage:&nbsp;http://',
				'important' => 0,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Ihre Homepage'
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
		$fields[$tmp++] = array(
				'name' => 'message',
				'type' => 'textarea',
				'title' => 'Nachricht:',
				'important' => 1,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Hier bitte ein paar S&auml;tze eingeben!'
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
			
		$fields[$tmp++] = array(
				'type' => 'hidden',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'add_entry',
								'name' => 'action'
							)
					)
			);
			
		$fields[$tmp++] = array(
				'type' => 'buttons',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'speichern&gt;&gt;',
								'type' => 'submit',
								'name' => 'submit'
							),
						1 => array(
								'value' => 'Zur&uuml;cksetzen',
								'type' => 'reset',
								'name' => 'reset'
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
			$width = '50%';
			$title = 'Neuer Eintrag';
		}
		else
		{

			$data = array(
					'name' => $form->return_submitted_field('name'),
					'email' => $form->return_submitted_field('email'),
					'homepage' => 'http://' . $form->return_submitted_field('homepage'),
					'message' => $form->return_submitted_field('message')
				);

			if ($this->add_entry($data))
			{
				$message = '<p align=center class=ok>Vielen Dank f&uuml;r Ihren Eintrag! (o:</P>';
				$title = 'Nachricht gespeichert';
				$bottom_menu = array();
				$bottom_menu [0][text] = 'Zur&uuml;ck zur &Uuml;bersicht';
				$bottom_menu [0][link] = $GLOBALS[PHP_SELF];
			}
			else
			{
				$log->add_to_log('Guestbook::add_entry_interface()', 'add_entry_interface() error!! add_entry() returned 0! Aborting', 'guestbook');
				$message = 'Whoups! Beim Eintragen der Nachricht ist ein Fehler aufgetreten, haben sie möglicherweise zweimal auf "speichern" geklickt??';
				$title = 'Fehler';
			}

		}

		return($page->dialog_box($title, $message, 0, $bottom_menu, '50%'));

	}

	/*******************************************************************************
	* Created: 13.08.2003
	* Last edit: 13.08.2003
	*
	* function show_entries()
	*
	* Funktion:
	*			holt die angegebenen Datensätze aus der Datenbank!
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
	function show_entries_interface($admin = 0)
	{
		global $log, $page, $db;
		$log->add_to_log('Guestbook::show_entries_interface()', 'show_entries_interface() called!', 'guestbook');
		
		// Zuerst brauchen wir mal die Anzahl der Einträge:
		$sql = '
				select
					count(*) as count
				from
					' . TB_GUESTBOOK . '
			';
		$count = $db->fetch_array($db->query($sql));
		$count = $count[count];
		
		// Liegt überhaupt ein Offset vor?
		if (!$GLOBALS[offset] or ($GLOBALS[offset] < 0))
		{
			$GLOBALS[offset] = 0;
		}
		
		// Dann benötigen wir die Einträge am aktuellen Offset
		$sql = '
				select 
					id,
					name,
					date_format(date, "%d.%m.%Y %H:%i") as date_readable,
					message,
					email,
					homepage,
					ip
				from
					' . TB_GUESTBOOK . '
				order by 
					date desc
				limit 
					' . $GLOBALS[offset] . ', 10
			';
		$entries_raw = $db->query($sql);
		if ($db->num_rows($entries_raw))
		{
			// Ok, wir haben ein paar einträge:
			$width = '80%';
			
			$message = '
							<table width=100% align=center class=list_table border=0>';
				
			while ($current = $db->fetch_array($entries_raw))
			{
				if ($current[email])
				{
					$email = '<a href="mailto:' . $current[email] . '">email</a>';						
				}
				else
				{
					$email = '<b class=list_table_inactive>email</b>';
				}
				
				if ($current[homepage])
				{
					$url = '<a href="' . $current[homepage] . '">www</a>';						
				}
				else
				{
					$url = '<b class=list_table_inactive>www</b>';
				}
				
				if ($admin)
				{
					$ip = '[<b>' . $current[ip] . '</b>]';
				}				
								
				$message .= '
								<tr>
									<td class=list_table_active>
										<b>' . $current[date_readable] . '</b> - <b>' . $current[name] . '</b> [' . $email . '] [' . $url . '] ' . $ip . ' 
									</td>
								</tr>
								<tr>
									<td>
										' . nl2br($current[message]) . ' 
									</td>
								</tr>';
								
				if ($admin)
				{
					$message .= '
								<tr>
									<td align=right>
										<a href="' . $GLOBALS[PHP_SELF] . '?action=' . $GLOBALS[action] . '&remove_entry=1&id=' . $current[id] . '">l&ouml;schen</<
									</td>
								</tr>';									
				}				
								
				$message .= '
								<tr>
									<td>
										&nbsp;
									</td>
								</tr>';									
			}
				
			$message .= '
							</table>';
							
							
			// Jetzt schauen wir mal ob wir Navigationslinks brauchen:
			if ($count > 10)
			{
				// Ok, mehr als 10 Einträge, also mehr als auf eine Seite passt!
				// Hier machen Nav-Links Sinn:
				$tmp = 0;
				$bottom_menu = array();
				
				if ($GLOBALS[offset] > 0)
				{
					// Wenn wir nen Offset haben, dann gehts auch zurück:
					$bottom_menu[$tmp][text] = '&lt;&lt;Neuere Eintr&auml;ge'; 
					$bottom_menu[$tmp][link] = $GLOBALS[PHP_SELF] . '?offset=' . ($GLOBALS[offset] - 10) . '&action=' . $GLOBALS[action];
					$tmp++;
				}
								
				if (($GLOBALS[offset] + 10) < $count)
				{						
					// Da sind noch Einträge übrig, also gehts auch weiter:
					$bottom_menu[$tmp][text] = '&Auml;ltere Eintr&auml;ge &gt;&gt;'; 
					$bottom_menu[$tmp][link] = $GLOBALS[PHP_SELF] . '?offset=' . ($GLOBALS[offset] + 10) . '&action=' . $GLOBALS[action];
				}
				
				$menu = $bottom_menu;
			}			

		}
		else
		{
			// Whoups, daö haben wir keine Einträge gefunden... Fehler?
			$message = '<p class=error>Keine Beitr&auml;ge gefunden!</p>';
			$width = '50%';
		}
		
		return($page->dialog_box(0, $message, $menu, $bottom_menu, $width));
	}

	/*******************************************************************************
	* Created: 25.07.2003
	* Last edit: 25.07.2003
	*
	* function remove_entry($id)
	*
	* Funktion:
	*			Löscht einen Eintrag aus dem Gästebuch!
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
	function remove_entry($id)
	{
		global $log, $db;
		$log->add_to_log('Guestbook::remove_entry()', 'remove_entry() called!', 'guestbook');
		
		$sql = '
				delete from
					' . TB_GUESTBOOK . '
				where 
					id = ' . $id . '
				limit 1
			';
		$db->query($sql);
		if ($db->affected_rows() > 0)
		{
			return(1);
		}
		else
		{
			return(0);
		}
	}

}
?>
