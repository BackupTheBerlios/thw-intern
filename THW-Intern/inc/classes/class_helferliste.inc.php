<?php

/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 31.07.2003
* Last edit: 31.07.2003
*
* class_helferliste.inc.php
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
class tb_helferliste2
{
	var
		$queued,				// Warteschlange
		$participation,			// Teilnehmer
		$current_userlist,	// aktuelle Userliste
		$user_in_list,			// ist der aktuelle User in der liste eingetragen?
		$statusmessage,		// aktuelle Fehlermeldung!
		$temp
		;


	/*******************************************************************************
	* Created: 31.07.2003
	* Last edit: 01.08.2003
	*
	* function tb_helferliste2($id)
	*
	* Funktion:
	*			Konstruktor! Prüft selbstständig ob in der akutellen Session
	*			Anfragen für User eintragen/löschen oder status änderungen
	*			vorliegen und führt dieses ggf. aus.
	*
	* Parameter:
	* - $id : ID des tb_objects an dem die Userliste hängt!
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function tb_helferliste2($id = 0)
	{
		global $log;
		$log->add_to_log('tb_helferliste2::tb_helferliste2', 'Constructor called....', 'debug');

		// prüfen ob vielleicht jemand einen User eingetragen hat:
		if ($GLOBALS[add_user])
		{
			$this->load_userlist($id);
			$this->insert_userlist_entry($id, $GLOBALS[ref_user_id], $GLOBALS[kommentar]);
		}

		// prüfen ob wir ein flag ändern wollen:
		if ($GLOBALS[change_flag])
		{
			$this->load_userlist($id);

			switch($GLOBALS[change_flag])
			{
				case 'flag_drin':
					// prüfen wir mal ob wir das überhaupt ausführen dürfen:
					if (($GLOBALS[change_flag] == 'flag_drin') and $GLOBALS[value])
					{
						if ($this->num_participating < $this->current_userlist[slots])
						{
							$this->change_flag($GLOBALS[change_id], $GLOBALS[change_flag], $GLOBALS[value]);
						}
						else
						{
							$this->statusmessage = '<p class=error>Kann keine User mehr in die aktive Liste übertragen, sie ist voll!</p>';
						}
					}
					else
					{
						$this->change_flag($GLOBALS[change_id], $GLOBALS[change_flag], $GLOBALS[value]);
					}
					break;

				case 'funktion':
						// Wir ändern eine Funktion eines Uses:
						$this->change_flag($GLOBALS[change_id], $GLOBALS[change_flag], $GLOBALS[value]);
					break;
			}
		}

		if ($GLOBALS[remove_user])
		{
 			$this->remove_userlist_entry($id, $GLOBALS[ref_user_id]);
		}
	}

	/*******************************************************************************
	* Created: 31.07.2003
	* Last edit: 01.08.2003
	*
	* function append_userlist($id, $list_attribs)
	*
	* Funktion:
	*			Erstellt eine Userliste und hängt diese an ein tb_object mit
	*			der ID $id an.
	*
	* Parameter:
	* - $id : ID des tb_objects an dem die Userliste hängen soll!
	* - $list_attribs: Attribute der Liste; die Funktion ist hier sehr
	*			Fehlerressistent, der Array kann eigentlich sogar leer sein,
	*			die Werte werden dann auf default gesetzt!
	*			Struktur von $list_attribs:
					array(
							'deadline' => ... // Wann soll die Liste automatisch geschlossen werden?
							'slots' => ... // Wieviele Plätze soll die "aktive" Liste bieten??
							'flag_hidden' => ... // Versteckte (=Einheiten) Liste?
							'flag_comment' => ... // Soll das Kommentarfeld zur verfügung stehen?
							'flag_funktion' => ... // Soll die Funktionsauswahl zu Verfügung stehen?
							'flag_autojoin' => ... // Sollen User die sich eintragen automatisch in die aktive
															// Liste übertragen werden falls noch platz ist?
						)
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function append_userlist($id, $list_attribs)
	{
		global $db, $log;
		$log->add_to_log('tb_object::append_userlist', 'append_userlist() called...', 'debug');

		if (count($list_attribs))
		{
			if ($id)
			{
				/***************************************************************************
				* Nicht gesetzte Variablen auf Default setzen!
				***************************************************************************/
				if (!$list_attribs[deadline])
				{
					$list_attribs[deadline] = 0;
				}
				if (!$list_attribs[slots])
				{
					$list_attribs[slots] = 0;
				}
				if (!$list_attribs[flag_hidden])
				{
					$list_attribs[flag_hidden] = 0;
				}
				if (!$list_attribs[flag_comment])
				{
					$list_attribs[flag_comment] = 0;
				}
				if (!$list_attribs[flag_funktion])
				{
					$list_attribs[flag_funktion] = 0;
				}
				if (!$list_attribs[flag_autojoin])
				{
					$list_attribs[flag_autojoin] = 1;
				}

				/***************************************************************************
				* SQL-Query für die Liste an sich bauen:
				***************************************************************************/
				$sql = '
						insert into
							' . TB_HELFERLISTE . '
						(
							ref_object_id,
							deadline,
							flag_open,
							flag_autojoin,
							disclaimer,
							slots,
							flag_hidden,
							flag_comment,
							flag_funktion
						)
						values
						(
							' . $id . ',
							"' . $list_attribs[deadline] . '",
							1,
							' . $list_attribs[flag_autojoin] . ',
							"' . htmlentities(trim($list_attribs[disclaimer])) . '",
							' . $list_attribs[slots] . ',
							' . $list_attribs[flag_hidden] . ',
							' . $list_attribs[flag_comment] . ',
							' . $list_attribs[flag_funktion] . '
						)
					';

				// echo $sql;

				/***************************************************************************
				* Query ausführen und prüfen:
				***************************************************************************/
				$db->query($sql);

				if ($db->affected_rows() > 0)
				{
					/***************************************************************************
					* Juhu, alles in Ordnung!
					***************************************************************************/
					return(1);
				}
				else
				{
					/***************************************************************************
					* URGS! Da ist was schiefgelaufen, es wurde nix geändert!
					* DB::affected_rows() sollte bei einer sauberen WHERE-Klausel
					* hier aber etwas ungleich NULL zurückgeben! FEHLER!
					***************************************************************************/
					$log->add_to_log('tb_object::append_userlist', 'Error while creating userlist! DB::affected_rows() returned 0! Aborting!', 'error');
					$log->add_to_log('tb_object::append_userlist', 'The following SQL-Query was used:', 'error');
					$log->add_to_log('tb_object::append_userlist', $sql, 'error');
					return(0);
				}
			}
			else
			{
				/***************************************************************************
				* URGS! Keine ref_object_id!!!!!
				***************************************************************************/
				$log->add_to_log('tb_object::append_userlist', 'URGS! No ref_object_id given! Aborting!', 'error');
				return(0);
			}
		}
		else
		{
			/***************************************************************************
			* URGS! Keine Listenparameter übergeben..
			***************************************************************************/
			$log->add_to_log('tb_object::append_userlist', 'Whoups! No parameters given to ::append_userlist()! Aborting!');
			return(0);
		}
	}


	/*******************************************************************************
	* Created: 31.07.2003
	* Last edit: 01.08.2003
	*
	* function load_userlist($id)
	*
	* Funktion:
	*			Lädt eine Userliste die an dem tb_object mit der ID $id hängt!
	*			User die bereits in der aktiven Liste sind werden mit Name und
	*			Vorname und User-ID in $this->participating und User in der
	*			Warteschlange in $this->queue gespeichert!
	*			Zusätzlich speichert die Funktion in $this->num_participating
	*			und in $this->num_queue die Anzahl der jeweils eingetragenen
	*			User ab!
	*
	* Parameter:
	* - $id : ID des tb_objects an dem die Userliste hängt!
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function load_userlist($id)
	{
		/***************************************************************************
		* Alle wichtigen Objekte holen:
		***************************************************************************/
		global $log, $db, $session;

		$log->add_to_log('tb_helferliste2::load_userlist', 'load_userlist() called...', 'debug');

		/***************************************************************************
		* Zu erst laden wir mal die Eigenschaften der Helferliste an sich:
		***************************************************************************/
		$sql = '
				select
					*
				from
					' . TB_HELFERLISTE . '
				where
					ref_object_id = ' . $id . '
			';

		/***************************************************************************
		* Query ausführen und prüfen:
		***************************************************************************/
		$raw = $db->query($sql);
		if ($db->num_rows($raw))
		{
			/***************************************************************************
			* OK, Eigenschaften der Helferliste sind da, speichern in der
			* Session!
			***************************************************************************/
			$this->current_userlist = $db->fetch_array($raw);

			/***************************************************************************
			* Dann holen wir jetzt noch die Einträge in der Userliste
			***************************************************************************/
			$sql = '
					select
						' . TB_HELFERLISTENEINTRAG . '.*,
						' . TB_USER . '.name,
						' . TB_USER . '.vorname
					from
						' . TB_HELFERLISTENEINTRAG . ',
						' . TB_USER . '
					where
						ref_object_id = ' . $id . '
						and
						' . TB_USER . '.id = ' . TB_HELFERLISTENEINTRAG . '.ref_user_id
					order by
						' . TB_HELFERLISTENEINTRAG . '.flag_drin,
						' . TB_USER . '.name
				';

			$raw = $db->query($sql);
   			if ($count = $db->num_rows($raw))
			{

				// Arrays resetten, sonst wirds komisch....
				$this->participating = array();
				$this->queue = array();

				while ($current = $db->fetch_array($raw))
				{
					if ($current[ref_user_id] == $session->user_info('id'))
					{
						$this->user_in_list = 1;
					}

					if ($current[flag_drin])
					{
						$this->participating[] = $current;
					}
					else
					{
						$this->queue[] = $current;
					}
				}

				$this->num_participating = count($this->participating);
				$this->num_queue = count($this->queue);

			}
			else
			{
				$this->num_participating = 0;
				$this->num_queue = 0;
			}

			return(1);
		}
		else
		{
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 31.07.2003
	* Last edit: 02.08.2003
	*
	* function view_userlist_interface($id, $action = 0)
	*
	* Funktion:
	*			Das UI zu den Userlisten; zeigt eine Userliste an, bietet
	*			normalen Usern die Möglichkeit sich einzutragen, ermöglicht
	*			ROOT's oder dem Listenersteller das eintragen von Usern und
	*			verändern von Flags...
	*
	* Parameter:
	* - $id : ID des tb_objects an dem die Userliste hängt!
	* - $action : Auf welche action soll verwiesen werden?
	*
	* Bemerkungen:
	*			Gibt eine Interface::dialog_box mit width=100% zurück, einfach
	*			in eine Tabelle basteln und voilá... (o:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function view_userlist_interface($id, $action = 0)
	{
		global $log, $db, $page, $session;
		$log->add_to_log('tb_helferliste2::view_userliste_interface', 'view_userliste_interface() called!', 'debug');

		// Jetzt stellen wir erstmal fest wer wir überhaupt sind:
		$is_admin = 0;

		if ($this->current_userlist[ref_user_id] == $session->user_info('id'))
		{
			$is_admin = 1;
		}

		if ($session->user_info('rights') == ROOT)
		{
			$is_admin = 1;
		}

		if (!$action)
		{
			$action = $GLOBALS[action];
		}

		if ($this->load_userlist($id))
		{
			$log->add_to_log('tb_helferliste2::view_userliste_interface', 'Okilidokily, found a userlist!! Now checking for its type...', 'debug');
			// Ok, userliste gefunden...
			$message = '
						' . $this->statusmessage . '
						<table width=100% class=list_table border=0 cellpadding=0>';

			if ($this->current_userlist[flag_hidden])
			{
				$log->add_to_log('tb_helferliste2::view_userliste_interface', 'Now look at that, found flag_hidden = 1', 'debug');
				// Einheitenliste:

				$sql = '
					select
						' . TB_USER . '.ref_einheit_id
					from
						' . TB_USER . ',
						' . TB_HELFERLISTENEINTRAG . '
					where
						' . TB_HELFERLISTENEINTRAG . '.ref_object_id = ' . $id . '
						and
						' . TB_USER . '.id = ' . TB_HELFERLISTENEINTRAG . '.ref_user_id
					group by
						' . TB_USER . '.ref_einheit_id
					';

				$raw = $db->query($sql);
				if ($db->num_rows($raw))
				{
					$message .='
								<tr class=list_table_active >
									<td colspan=2>
										<b>Für Helfer folgender Einheiten wurde automatisch ein Dienst angesetzt:</b>
									</td>
								</tr>
								<tr>
									<td class=list_table_active align=right>
										Einheiten:
									</td>
									<td>
										<table width=100% align=center border=0 class=list_table>';

					while ($current = $db->fetch_array($raw))
					{
						$class = '';
						if ($current[ref_einheit_id] == $session->user_info('ref_einheit_id'))
						{
							$class = 'list_table_important';
						}

						$message .= '
											<tr class=' . $class . '>
												<td>
													<b>' . $GLOBALS[EINHEITEN][$current[ref_einheit_id]][name] . '</b>
												</td>
											</tr>';
					}

					$message .= '
										</table>
									</td>
								</tr>';

				}
				else
				{
					$log->add_to_log('tb_helferliste2::view_userliste_interface', 'Strange, found no userlist entries for id ' . $id . '! Here\'s my query: ', 'debug');
					$log->add_to_log('tb_helferliste2::view_userliste_interface', $sql, 'debug');
				}

			}
			else
			{
				$log->add_to_log('tb_helferliste2::view_userliste_interface', 'Now look at that, found flag_hidden = 0', 'debug');

// 				// Userliste:

				$message .='
							<tr>
								<td class=list_table_active align=right>
									eintragen:
								</td>
								<td>';

				/***************************************************************************
				* Ok, prüfen wir erstmal ob man sich überhaupt noch eintragen kann:
				***************************************************************************/
				if ($this->current_userlist[flag_open])
				{

					if ($is_admin)
					{
						$message .= '
									<table width=100% align=center border=0 class=list_table>';

						$message .= '
										<tr>
											<td>
												<form action="' . $GLOBALS[PHP_SELF] . '" method=get>
												<select name="ref_user_id[]" size=10 multiple>';
						$sql = '
								select
									' . TB_USER . '.id,
									' . TB_USER . '.name,
									' . TB_USER . '.vorname
								from
									' . TB_USER . '
								order by
									' . TB_USER . '.name
							';
						$raw = $db->query($sql);
						while ($temp = $db->fetch_array($raw))
						{
							$message .= '
													<option value="' . $temp[id] . '">' . $temp[name] . ', ' . $temp[vorname] . '</option>';
						}
						$message .= '
												</select>
												<input type=hidden name=action value=' . $action . '>
												<input type=hidden name=id value=' . $id . '>
												<input type=submit name=add_user value="Eintragen&gt;&gt;">
												';

						$message .= '
									</table>';
					}
					else
					{
						/***************************************************************************
						* Dann müssen wir prüfen ob der User bereits eingetragen ist oder nicht:
						***************************************************************************/
						if (!$this->user_in_list)
						{
							$message .= '
										<table width=100% align=center border=0 class=list_table>';

							/***************************************************************************
							* Ok, wir wollen uns also eintragen; Dazu überprüfen wir erstmal ob
							* ein disclaimer da ist:
							***************************************************************************/
							if ($this->current_userlist[disclaimer])
							{
								$message .= '
											<th>
												Vorraussetzungen:
											</th>
											<tr>
												<td class=list_table_important>
													<b>' . $this->current_userlist[disclaimer] . '</b>
												</td>
											</tr>
											<tr>
												<td>
													Ich habe den obenstehenden Text gelesen und erfülle die angegebenen Vorrausetzung(en):
												</td>
											</tr>
											<tr>
												<td align=center>
													<form action=' . $GLOBALS[PHP_SELF]. ' method=get>';
								if ($this->current_userlist[flag_comment])
								{
									$message .= '
														Kommentar: <input type=text size=30 maxlength=250 name=kommentar title="Hier kannst du ein paar persönliche Worte einfügen! (o:"><br>
										';
								}

								$message .= '
														<input type=hidden name=action value=' . $action . '>
														<input type=hidden name="ref_user_id[]" value=' . $session->user_info('id') . '>
														<input type=hidden name=id value=' . $id . '>
														<input type=submit name=add_user value="Ok und eintragen&gt;&gt;">
													</form>
												</td>
											</tr>
									';
							}
							else
							{
								$message .= '
												<tr>
													<td align=center>
														Mich hier eintragen:
													</td>
												</tr>
												<tr>
													<td align=center>
														<form action=' . $GLOBALS[PHP_SELF]. ' method=get>';
								if ($this->current_userlist[flag_comment])
								{
									$message .= '
														Kommentar: <input type=text size=30 maxlength=250 name=kommentar title="Hier kannst du ein paar persönliche Worte einfügen! (o:"><br>
										';
								}
								$message .= '
															<input type=hidden name=action value=' . $action . '>
															<input type=hidden name="ref_user_id[]" value="' . $session->user_info('id') . '">
															<input type=hidden name=id value=' . $id . '>
															<input type=submit name=add_user value="eintragen&gt;&gt;">
														</form>
													</td>
												</tr>
										';
							}

						$message .= '
										</table>
									</td>
								</tr>
							';
						}
						else
						{
							/***************************************************************************
							* Tja, wir sind schon eingetragen; dumm gelaufen (o;
							***************************************************************************/
							$message .= '
										<b>Du bist bereits eingetragen</b>
									</td>
								</tr>';
						}
					}
				}
				else
				{
					/***************************************************************************
					* Tja, Liste ist bereits zu... Zu spät!
					***************************************************************************/
					$message .= '
									<b>Liste ist bereits geschlossen!</b>
								</td>
							</tr>';
				}


				/***************************************************************************
				* Jetzt zeigen wir noch an wieviele Plätze es gibt, wieviele noch
				* frei sind und wieviele User in der Warteschlange sind:
				***************************************************************************/
				$message .='
							<tr>
								<td class=list_table_active align=right>
									Plätze:
								</td>
								<td>';


				$message .= '<b title="Dabei">' . ($this->current_userlist[slots] - $this->num_participating) . '</b> / ';
				if ($this->current_userlist[slots])
				{
					$message .= '<b>' . $this->current_userlist[slots] . '</b>';
				}
				else
				{
					$message .= '<b>unendlich</b>';
				}
				$message .= ' (<b class=list_table_disabled title="Warteliste">' . $this->num_queue . '</b>)';

				$message .= '
								</td>
							</tr>';


				/***************************************************************************
				* Jetzt kommt die eigentliche Userliste:
				***************************************************************************/
				$message .= '
							<tr>
								<td class=list_table_active align=right>
									Liste:
								</td>
								<td>';
				/***************************************************************************
				* Sind überhaupt schon Leute eingetragen??
				***************************************************************************/
				if ($this->num_participating or $this->num_queue)
				{
					/***************************************************************************
					* Jetzt kommt die Tabelle mit den Usern:
					***************************************************************************/

					$message .= '
									<table width=100% align=center border=0 class=list_table cellspacing=2 cellpaddin=0>';

					/***************************************************************************
					* Hier kommen die Tabellenheader:
					***************************************************************************/
					$message .= '
										<th class="list_table_active">Name</th>';

					/***************************************************************************
					* Wollen wir ein Kommentarfeld?
					***************************************************************************/
					if ($this->current_userlist[flag_comment])
					{
						$message .= '
										<th class="list_table_active">Kommentar</th>';
					}

					/***************************************************************************
					* Wollen wir eine Funktionsauswahl?
					***************************************************************************/
					if ($this->current_userlist[flag_funktion])
					{
						$message .= '
										<th class="list_table_active">Funktion</th>';
					}

					$message .= '
										<th class="list_table_active" width="100">Status</th>';


					/***************************************************************************
					* Jetzt kommen die eigentlichen Usereinträge; Zuerste die bereits
					* eingetragenen:
					***************************************************************************/
					while ($current = each($this->participating))
					{
						if ($current[value][ref_user_id] == $session->user_info('id'))
						{
							$class = 'list_table_important';
						}
						else
						{
							$class = 'list_table_less_important';
						}

						$message .= '
										<tr class="' . $class . '">
											<td>';
						if ($is_admin)
						{
							$message .= '
												<a href="' . $GLOBALS[PHP_SELF] . '?action=' . $action . '&remove_user=1&ref_user_id[]=' . $current[value][ref_user_id] . '&id=' . $id . '" title="User aus der Liste entfernen!">
													<b>' . $current[value][name] . ', ' . $current[value][vorname] . '</b>
												</a>';
						}
						else
						{
							$message .= '
												<b>' . $current[value][name] . ', ' . $current[value][vorname] . '</b>';
						}
						$message .= '
											</td>';
						// Wird ein Kommentarfeld gewünscht??
						if ($this->current_userlist[flag_comment])
						{
							$message .= '
											<td>
												' . $current[value][kommentar] . '
											</td>';
						}

						// Wird ein Funktionsfeld gewünscht??
						if ($this->current_userlist[flag_funktion])
						{
							if ($is_admin)
							{
								$message .= '
											<td align=center>
												<form action="' . $PHP_SELF . '" method="get">
													<select size=1 name=value>';
								reset($GLOBALS[DATE_FUNCTIONS]);
								while ($tmp = each($GLOBALS[DATE_FUNCTIONS]))
								{
									$insert = '';
									if ($tmp[key] == $current[value][funktion])
									{
										$insert = 'selected';
									}
									$message .= '
														<option value=' . $tmp[key] . ' ' . $insert . '>
															' . $tmp[value][name] . '
														</option>';
								}
								$message .= '

													</select>
													<input type=hidden name=action value="' . $action . '">
													<input type=hidden name=id value="' . $id . '">
													<input type=hidden name=change_id value="' . $current[value][id] . '">
													<input type=hidden name=change_flag value="funktion">
													<input type=submit name=submit value="Los&gt;&gt;">
												</form>
											</td>';
							}
							else
							{
								$message .= '
											<td align=center>
												<b>' . $GLOBALS[DATE_FUNCTIONS][$current[value][funktion]][name] . '</b>
											</td>';
							}
						}

						// Statusauswahl! Da wir in der Teilnehmenden Liste sind, sollte ein Klick hier den User
						// in die Warteschlange zurückversetzen:

						if ($is_admin)
						{
							$message .= '
												<td align=center>
													<a href="' . $GLOBALS[PHP_SELF] . '?action=' . $action . '&id=' . $id . '&change_id=' . $current[value][id] . '&change_flag=flag_drin&value=0" title="User in die Warteliste verschieben!"><b>dabei</b></a>
												</td>
											</tr>';

						}
						else
						{
							$message .= '
												<td align=center>
													<b>dabei</b>
												</td>
											</tr>';
						}

					}


					/***************************************************************************
					* Jetzt kommen die eigentlichen Usereinträge; Jetzt kommt
					* noch die Warteschlange
					***************************************************************************/
					while ($current = each($this->queue))
					{
						if ($current[value][ref_user_id] == $session->user_info('id'))
						{
							$class = 'list_table_important';
						}
						else
						{
							$class = 'list_table_inactive';
						}

						$message .= '
										<tr class="' . $class . '">
											<td>';
						if ($is_admin)
						{
							$message .= '
												<a href="' . $GLOBALS[PHP_SELF] . '?action=' . $action . '&remove_user=1&ref_user_id[]=' . $current[value][ref_user_id] . '&id=' . $id . '" title="User aus der Liste entfernen!">
													<b>' . $current[value][name] . ', ' . $current[value][vorname] . '</b>
												</a>';
						}
						else
						{
							$message .= '
												' . $current[value][name] . ', ' . $current[value][vorname];
						}
						$message .= '
											</td>';
						// Wird ein Kommentarfeld gewünscht??
						if ($this->current_userlist[flag_comment])
						{
							$message .= '
											<td>
												' . $current[value][kommentar] . '
											</td>';
						}

						// Wird ein Funktionsfeld gewünscht??
						if ($this->current_userlist[flag_funktion])
						{
							$message .= '
											<td align=center>
												-
											</td>';
						}

						// Statusauswahl! Da wir in der Teilnehmenden Liste sind, sollte ein Klick hier den User
						// in die Warteschlange zurückversetzen:

						if ($is_admin)
						{
							$message .= '
												<td align=center>
													<a href="' . $GLOBALS[PHP_SELF] . '?action=' . $action . '&id=' . $id . '&change_id=' . $current[value][id] . '&change_flag=flag_drin&value=1" title="User in aktive Liste aufnehmen!"><b>dazu</b></a>
												</td>
											</tr>';

						}
						else
						{
							$message .= '
												<td align=center>
													<b>wartet</b>
												</td>
											</tr>';
						}

					}



					$message .= '
									</table>';
				}
				else
				{
					$message .= '
									<b>Liste ist leer...</b>
						';
				}

				$message .= '
								</td>
							</tr>';

			}

			$message .= '
						</table>';

			$menu = array();
			$menu[0][text] = 'Liste editieren';
			$menu[0][link] = $GLOBALS[$PHP_SELF] . '?action=userlist_edit&id=' . $id;
			$menu[1][text] = 'Liste löschen';
			$menu[1][link] = $GLOBALS[$PHP_SELF] . '?action=userlist_delete&id=' . $id;

			return($page->dialog_box(0, $message, $menu, 0, '100%'));
		}
		else
		{
			return('');
		}

	}

	function change_flag($id, $flag, $value)
	{
		global $db;

		$entry[] = array(
				'name' => $flag,
				'value' => $value
			);

		$db->update_row($id, TB_HELFERLISTENEINTRAG, $entry);
	}


	/*******************************************************************************
	* Created: 31.07.2003
	* Last edit: 01.08.2003
	*
	* function insert_userlist_entry($id, $entries, $kommentar = '')
	*
	* Funktion:
	*			Fügt einen oder mehrere Einträge in eine Userlsite ein!
	*
	* Parameter:
	* - $id : ID des tb_objects an dem die Userliste hängt!
	* - $entries : Array der die neuen Einträge enthält!
	* - $kommentar : hier kann optional noch ein Kommentar abgegeben werden!
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function insert_userlist_entry($id, $entries, $kommentar = '')
	{
		global $db, $log;
		$log->add_to_log('tb_object::insert_userlist_entry', 'insert_userlist_entry() called...', 'debug');

		/***************************************************************************
		* Zuerst laden wir die Userliste an sich:
		***************************************************************************/
		if ($this->load_userlist($id))
		{
			$log->add_to_log('tb_object::insert_userlist_entry', 'Ok, got id, proceeding....', 'debug');
			/***************************************************************************
			* OK, Userliste geladen! Jetzt prüfen wir ob in die Liste überhaupt
			* etwas eingetragen werden darf!
			***************************************************************************/
			if (!$this->current_userlist[flag_open])
			{
				$this->statusmessage = '<p class=error>Whoups, die Liste ist bereits geschlossen!</p>';
				$log->add_to_log('tb_object::insert_userlist_entry', 'Whoups, found an already closed list... aborting!', 'debug');
				return(-1);
			}

			/***************************************************************************
			* Sind überhaupt noch Plätze frei? (slots = 0 für unendlich)
			***************************************************************************/
			if ($this->current_userlist[slots])			// Es gibt eine Beschränkung!
			{
				if ($this->current_userlist[number_of_entries] >= $this->current_userlist[slots])
				{
					$this->statusmessage = '<p class=error>Whoups, die Liste ist bereits geschlossen!</P>';
					return(0);
				}
			}

			$inserted = 0;

			switch($this->current_userlist[flag_hidden])
			{
				case '1':
					/***************************************************************************
					* Versteckte Userliste! Hier werden nur Einheiten eingetragen!
					***************************************************************************/

					// Erstmal die Tabelle locken!
					$sql = '
							lock
								tables
								' . TB_HELFERLISTENEINTRAG . '
						';
					$db->query($sql);

					while ($current = each($entries))
					{

						$sql = '
								insert into
									' . TB_HELFERLISTENEINTRAG . '
									(
										' . TB_HELFERLISTENEINTRAG . '.ref_user_id
									)
								select
									' . TB_USER . '.id
								from
									' . TB_USER . '
								where
									' . TB_USER . '.ref_einheit_id = ' . $current[value] . '
							';

						// echo nl2br($sql);

						$db->query($sql);
						if (!$db->affected_rows())
						{
							$log->add_to_log('tb_object::insert_userlist_entry', 'Strange, tried to insert an entry (einheit) in Userlist ' . $id . ', but affected_rows() returned 0!!', 'warning');
							$log->add_to_log('tb_object::insert_userlist_entry', 'Here is the query i used :', 'warning');
							$log->add_to_log('tb_object::insert_userlist_entry', $sql, 'warning');
						}
					}

					$sql = '
							update
								' . TB_HELFERLISTENEINTRAG . '
							set
								' . TB_HELFERLISTENEINTRAG . '.ref_object_id = ' . $this->current_userlist[ref_object_id] . '
							where
								' . TB_HELFERLISTENEINTRAG . '.ref_object_id = 0
						';
					$db->query($sql);

					// Tabellen wieder freigeben!
					$sql = '
							unlock
								tables
						';
					// echo $sql . '<br>';
					$db->query($sql);

					return(1);

					break;

				default:
					/***************************************************************************
					* Default: Normale Userliste
					***************************************************************************/
					$remaining_slots = ($this->current_userlist[slots] - $this->num_participating);

					reset($entries);
					while ($current = each($entries))
					{
						$flag_drin = 0;
						if ($this->current_userlist[flag_autojoin] and $this->current_userlist[flag_open])
						{
							if ($remaining_slots > 0)
							{
								$flag_drin = 1;
							}
						}

						// Da wir einfügen setzen wir mal alles auf 0!
						$sql = '
								insert into
									' . TB_HELFERLISTENEINTRAG . '
								(
										ref_object_id,
										ref_user_id,
										flag_dgl,
										flag_drin,
										kommentar,
										funktion
								)
								values
								(
									' . $id . ',
									' . $current[value] . ',
									0,
									' . $flag_drin . ',
									"' . htmlentities(trim($kommentar)) . '",
									0
								)
							';

						$db->query($sql);
						if ($db->affected_rows())
						{
							$remaining_slots--;
							$inserted++;
						}
						else
						{
							$log->add_to_log('tb_object::insert_userlist_entry', 'Strange, tried to insert an entry (user) in Userlist ' . $id . ', but affected_rows() returned 0!!', 'warning');
							$log->add_to_log('tb_object::insert_userlist_entry', 'Here is the query i used :', 'warning');
							$log->add_to_log('tb_object::insert_userlist_entry', $sql, 'warning');
						}
					}

					if ($inserted)
					{
						$this->statusmessage = '<p class=ok>OK, es wurden ' . $inserted . ' User in die Liste eingetragen!</p>';
					}
					else
					{
						$this->statusmessage = '<p class=warning>Hmmm, es wurden keine User eingetragen! Evtl. bereits alle eingetragen?</p>';
					}


			}
		}
		else
		{
			/***************************************************************************
			* URGS! Hierfür gibts gar keine Userliste...
			***************************************************************************/
			$log->add_to_log('tb_object::insert_userlist_entry', 'URGS! Tried to insert an entry into a non-existing userlist! (ID: ' . $id . ')!', 'error');
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 31.07.2003
	* Last edit: 01.08.2003
	*
	* function remove_userlist_entry($id, $entries)
	*
	* Funktion:
	*			Entfernt einen oder mehrere Einträge aus einer Userliste!
	*
	* Parameter:
	* - $id : ID des tb_objects an dem die Userliste hängt!
	* - $entries : Array der die zu löschenden ref_user_id's enthält!
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function remove_userlist_entry($id, $entries)
	{
		global $db, $log;
		$log->add_to_log('tb_object::remove_userlist_entry', 'remove_userlist_entry() called...', 'debug');

		if ($id)
		{
			if (count($entries))
			{
				reset($entries);
				$current = each($entries);
				$sql = '
						delete from
							' . TB_HELFERLISTENEINTRAG . '
						where
							ref_user_id = ' . $current[value];

				while ($current = each($entries))
				{
					$sql .= '
							or
							ref_user_id = ' . $current[value];
				}

				$db->query($sql);
				if ($db->affected_rows())
				{
					$this->statusmessage = '<p class=ok>User wurde aus der Liste entfernt!</p>';
				}

			}
			else
			{
				$log->add_to_log('tb_object::remove_userlist_entry', 'Urgs, got nothing to remove...', 'error');
				return(0);
			}
		}
		else
		{
			$log->add_to_log('tb_object::remove_userlist_entry', 'Urgs, called without an ID... ', 'error');
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 31.07.2003
	* Last edit: 01.08.2003
	*
	* function create_userlist_dialog($submit_name = 'submit', $action = 0)
	*
	* Funktion:
	*			Baut automatisch einen $fields-Array um damit eine Userliste
	*			anzulegen
	*
	* Parameter:
	* - $submit_name : Name des Submit-Buttons, default 'submit'
	* - $action : Default-action; holt sich per default das was in der
	*			dem aktuellen GLOBALS eintrag steht!
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function create_userlist_dialog($submit_name = 'submit', $action = 0, $id = 0)
	{
		global $db, $log;
		$field_no = 0;

		$fields[$field_no++] = array(
				'name' => 'CREATE_USERLIST_MODE',
				'type' => 'radio',
				'title' => 'Art der Userliste:',
				'important' => 1,
				'selections' => array(
						0 => array(
								'value' => 'userlist',
								'title' => 'Normale Userliste',
								'description' => 'Normale Userliste!'
							),
						1 => array(
								'value' => 'einheit',
								'title' => 'Einheitenliste',
								'description' => 'Einheitenliste!'
							)
					)
			);

		if ($GLOBALS[CREATE_USERLIST_MODE] == 'userlist')
		{
			$fields[$field_no++] = array(
					'name' => 'CREATE_USERLIST_COMMENT',
					'type' => 'checkbox',
					'title' => 'Kommentarfeld:',
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
									'value' => 'Sollen den Usern beim Eintragen ein Kommentarfeld zur Verfügung stehen?'
								)
						)
				);
			$fields[$field_no++] = array(
					'name' => 'CREATE_USERLIST_FUNKTION',
					'type' => 'checkbox',
					'title' => 'Funktionen:',
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
									'value' => 'Aktiviert die Funktionsauswahl für Userlisten! Damit ist eine Zuweisung (He, DGL, San...) mittels Dropdown möglich!'
								)
						)
				);
			$fields[$field_no++] = array(
					'name' => 'CREATE_USERLIST_AUTOJOIN',
					'type' => 'checkbox',
					'title' => 'Autojoin:',
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
									'value' => 'Aktiviert die Funktion Autojoin, welche User automatisch in die aktive Userliste einträgt sofern noch Plätze frei sind!!'
								)
						)
				);
			$fields[$field_no++] = array(
					'name' => 'CREATE_USERLIST_SLOTS',
					'type' => 'text',
					'title' => 'Anzahl Plätze:',
					'important' => 1,
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Bitte hier die Anzahl der gewünschten Plätze eintragen!'
								),
							1 => array(
									'name' => 'size',
									'value' => '5'
								)
						)
				);
			$fields[$field_no++] = array(
					'name' => 'CREATE_USERLIST_DISCLAIMER',
					'type' => 'textarea',
					'title' => 'Disclaimer:',
					'important' => 0,
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Hier eingetragenen Text muss der User per Checkbox bestätigen um sich einzutragen!'
								),
							1 => array(
									'name' => 'cols',
									'value' => '30'
								),
							2 => array(
									'name' => 'rows',
									'value' => '5'
								)
						)
				);

		}
		else if ($GLOBALS[CREATE_USERLIST_MODE] == 'einheit')
		{
			$fields[$field_no++] = array(
					'type' => 'separator',
					'value' => '<b>Achtung</b>, kann länger dauern!!!!!! Diese Funktion ist relativ Datenbankintensiv und kann zwischen 20 Sekunden und 1, 2 Minuten daueren! Bitte <b>nicht</b> Aktualisieren o.ä.!'
				);

			$fields[$field_no] = array(
					'name' => 'CREATE_USERLIST_EINHEIT[]',
					'type' => 'select',
					'title' => 'Einheiten',
					'important' => 1,
					'selections' => array(
						),
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Für welche Einheiten sollen Listen generiert werden? Mehrfachauswahl mittels STRG+Auswahl möglich!'
								),
							1 => array(
									'name' => 'size',
									'value' => '10'
								),
							2 => array(
									'name' => 'multiple',
									'value' => 'multiple'
								)
						)
				);

			$i = 0;
			while ($temp = each($GLOBALS[EINHEITEN]))
			{
				$fields[$field_no][selections][$i][value] = $temp[0];
				$fields[$field_no][selections][$i][title] = $temp[1][name];
				$i++;
			}
			$field_no++;

		}

		$fields[$field_no++] = array(
				'type' => 'hidden',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => $action,
								'name' => 'action'
							),
						1 => array(
								'value' => $GLOBALS[id],
								'name' => 'id'
							),
						2 => array(
								'value' => $GLOBALS[type],
								'name' => 'type'
							)

					)
			);

		$fields[$field_no] = array(
				'type' => 'buttons',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => $submit_name,
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
	* Created: 31.07.2003
	* Last edit: 01.08.2003
	*
	* function create_userlist()
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
	function create_userlist($id)
	{

		if ($GLOBALS[CREATE_USERLIST_MODE] == 'einheit')
		{
			$flag_hidden = 1;
		}
		else
		{
			$flag_hidden = 0;
		}

		$list_attribs = array(
    			'deadline' => $GLOBALS[CREATE_USERLIST_DEADLINE],
				'slots' => $GLOBALS[CREATE_USERLIST_SLOTS],
				'flag_hidden' => $flag_hidden,
				'flag_comment' => $GLOBALS[CREATE_USERLIST_COMMENT],
				'flag_autojoin' => $GLOBALS[CREATE_USERLIST_AUTOJOIN],
				'flag_funktion' => $GLOBALS[CREATE_USERLIST_FUNKTION],
				'disclaimer' => $GLOBALS[CREATE_USERLIST_DISCLAIMER],
				'deadline' => $GLOBALS[CREATE_USERLIST_DEADLINE]
			);

		if ($this->append_userlist($id, $list_attribs))
		{
			if ($GLOBALS[CREATE_USERLIST_EINHEIT])
			{
				if ($this->insert_userlist_entry($id, $GLOBALS[CREATE_USERLIST_EINHEIT]))
				{
					return(1);
				}
				else
				{
					return(0);
				}
			}
			else
			{
				return(1);
			}
		}
		else
		{
			return(0);
		}
	}

	/*******************************************************************************
	* Created: 01.08.2003
	* Last edit: 01.08.2003
	*
	* function create_userlist()
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
	function drop_userlist($id)
	{
		global $db, $log;

		$output = '<p><b>Userliste gefunden; lösche...</b></p>';

		/*******************************************************************************
		* Zuerst einmal alle Einträge in der Liste speichern:
		*******************************************************************************/
		$sql = '
				delete from
					' . TB_HELFERLISTENEINTRAG . '
				where
					ref_object_id = ' . $id . '
			';

		$db->query($sql);
		if ($db->affected_rows())
		{
			$output .= '<p class=warning>Es wurden ' . $db->affected_rows() . ' Einträge in der Liste gefunden; diese wurden entfernt!</p>';
		}
		else
		{
			$output .= '<p class=ok>Es wurden keine Einträge in dieser Liste gefunden!</p>';
		}

		/*******************************************************************************
		* Jetzt löschen wir die Liste an sich:
		*******************************************************************************/
		$sql = '
				delete from
					' . TB_HELFERLISTE . '
				where
					ref_object_id = ' . $id . '
			';

		$db->query($sql);
		if ($db->affected_rows())
		{
			$output .= '<p class=ok> Liste wurde erfolgreich gelöscht!</p>';
		}
		else
		{
			$output .= '<p class=error>Urgs, da ist was schiefgelaufen!</p>';
		}

		return($output);
	}

	function return_field($field)
	{
		return($this->current_userlist[$field]);
	}

	/*******************************************************************************
	* Created: 31.07.2003
	* Last edit: 01.08.2003
	*
	* function create_userlist_interface()
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
	function create_userlist_interface($id, $action, $type, $preset = 0)
	{
		global $log, $db, $page;

		require_once('inc/classes/class_form2.inc.php');

		$form = new Form2($GLOBALS[PHP_SELF], 'get', 'create_userlist', $action, $id);

		$fields = $this->create_userlist_dialog('abspeichern&gt;&gt;', $action);

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
			$title = 'Liste anlegen';
		}
		else
		{
			$width = '50%';
			if ($this->create_userlist($id))
			{
				$title = 'Liste angelegt';
				$message = '<p class=ok>Liste wurde angelegt!</p>';
				$menu = array();

				switch($type)
				{
					case 'report':
							$menu[0][text] = 'Zum Bericht';
							$menu[0][link] = $GLOBALS[PHP_SELF] . '?action=report_read&id=' . $id;
						break;
					case 'date':
							$menu[0][text] = 'Zum Termin';
							$menu[0][link] = $GLOBALS[PHP_SELF] . '?action=date_view&id=' . $id;
						break;

					default:
				}

				$form->form_shutdown();
			}
			else
			{
				$title = 'Fehler';
				$message = '<p class=error>Fehler beim anlegen der Userliste! (logfiles?)</p>';
			}
		}

		return($page->dialog_box($title, $message, 0, $menu, $width));
	}



}
?>
