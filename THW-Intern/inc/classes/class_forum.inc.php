<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 11.07.2003
* Last edit: 12.07.2003
*
* class Forum
*
* Funktion:
*			Eine einfache Forumklasse!
*
* Parameter:
*			Keine
*
* Rückgabe:
* 			Keine
*
* Bemerkungen:
*
* TODO:
* - neue Beiträge auch in der thread_id ansicht hervorheben
* - Counter einbauen!
*
* DONE:
*
*******************************************************************************/
class Forum
{
	var
			$flag_debug,
			$discussion
		;

	/*******************************************************************************
	* Last edit: 12.07.2003
	*
	* function Forum()
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
	function Forum()
	{
		$this->flag_debug = 0;
	}


	/*******************************************************************************
	* Last edit: 12.07.2003
	*
	* function set_debug($level)
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
	function set_debug($level)
	{
		$this->flag_debug = $level;
	}


	/*******************************************************************************
	* Last edit: 12.07.2003
	*
	* function create_forum_dialog()
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
	function edit_forum_dialog()
	{
	}

	/*******************************************************************************
	* Last edit: 12.07.2003
	*
	* function save_forum()
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
	function save_forum()
	{
	}

	/*******************************************************************************
	* Last edit: 12.07.2003
	*
	* function delete_forum()
	*
	* Funktion:
	*			Löscht ein Forum
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
	function delete_forum()
	{
	}

	/*******************************************************************************
	* Last edit: 12.07.2003
	*
	* function view_forum()
	*
	* Funktion:
	* 			Zeigt ein ausgewähltes Forum an
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
	function view_forum($forum_id, $filter_options = 0)
	{
	}

	/*******************************************************************************
	* Last edit: 13.07.2003
	*
	* function view_forum_overview()
	*
	* Funktion:
	* 			Holt Forenbeiträge, entweder nur Header oder komplett!
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
	function load_messages($forum_id, $filter_options)
	{
		global $log, $db, $page, $session;

		// Erstmal Infos zu dem aktuellen Forum holen
			$sql = '
					select
						' . TB_FOREN . '.name,
						' . TB_FOREN . '.beschreibung,
						' . TB_FOREN . '.ref_user_id,
						' . TB_USER . '.name as admin_name,
						' . TB_USER . '.vorname as admin_vorname,
						' . TB_USER . '.email
					from
						' . TB_FOREN . ',
						' . TB_USER . '
					where
						' . TB_FOREN . '.id = ' . $forum_id . '
						and
							' . TB_USER . '.id = ' . TB_FOREN . '.ref_user_id
				';

			if ($this->flag_debug) $log->add_to_log('Form::view_forum()', 'SQL: ' . $sql, 'debug');

			$forum_info = $db->query($sql);
			if ($db->num_rows($forum_info))
			{
				$forum_info = $db->fetch_array($forum_info);
			}

		// Dann brauchen wir die entsprechenden Forenbeiträge!
		// Dazu gehen wir $filter_options durch und schauen was für
		// Beiträge wir überhaupt wollen!
		// Mögliche Filteroptionen:
		// - compact: Nur die Diskussionsüberschriften (ref_forenbeitrag_id = 0)
		// - hide_old: Beiträge älter als hide_old ausblenden (in Tagen??)
		// - hide_inactive: Inaktive Diskussionen ausblenden!


		$sql = '
				select
					' . TB_BEITRAEGE . '.titel,';
		if ($filter_options[thread_id])
		{
			$sql .= '
					' . TB_BEITRAEGE . '.beitrag,';
		}
		$sql .= '
					' . TB_BEITRAEGE . '.id,
					date_format(' . TB_BEITRAEGE . '.date_create, "%d.%m.%Y %H:%i") as date_create_readable,
					date_format(' . TB_BEITRAEGE . '.date_lastviewed, "%d.%m.%Y %H:%i") as date_lastviewed_readable,
					date_format(' . TB_BEITRAEGE . '.date_create, "%Y%m%d%H%i%s") as date_create_timestamp,
					' . TB_BEITRAEGE . '.counter,
					' . TB_BEITRAEGE . '.ref_forenbeitrag_id,
					' . TB_BEITRAEGE . '.ref_user_id,
					' . TB_USER . '.name,
					' . TB_USER . '.vorname
				from
					' . TB_BEITRAEGE . ',
					' . TB_USER . '
				where
					' . TB_BEITRAEGE . '.ref_foren_id = ' . $forum_id . '
					and
					' . TB_USER . '.id = ' . TB_BEITRAEGE . '.ref_user_id';

		if ($filter_options[thread_id])
		{
			// compact setzen wir auf 0, da wirs ja eigentlich nciht bruachen wenn
			// wir eine einzelne Diskussion betrachten!
			$filter_options[compact] = 0;
			$sql .= '
					and
					(
					' . TB_BEITRAEGE . '.ref_forenbeitrag_id = ' . $filter_options[thread_id] . '
					or
					' . TB_BEITRAEGE . '.id = ' . $filter_options[thread_id] . '
					)';
		}
		else if ($filter_options[compact])
		{
			$sql .= '
					and
					' . TB_BEITRAEGE . '.ref_forenbeitrag_id = 0';
		}

		if ($filter_options[hide_old])
		{
			$sql .= '
					and
					' . TB_BEITRAEGE . '.date_create > "' . strftime("%Y-%m-%d %H:%M:%S", (time() - ($filter_options[hide_old] * 86400) )) . '"';
		}

		if ($filter_options[thread_id])
		{
			$sql .= '
				order by
					' . TB_BEITRAEGE . '.date_create
				';
		}
		else
		{
			$sql .= '
				order by
					' . TB_BEITRAEGE . '.date_create desc
				';
		}

		// echo nl2br($sql) . '<br>';

		if ($this->flag_debug) $log->add_to_log('Form::view_forum()', 'SQL: ' . $sql, 'debug');

		$raw = $db->query($sql);

		if ($db->num_rows($raw))
		{
			if ($filter_options[thread_id])
			{
				// Wir lesen einen vorhandenen Beitrag!! aktualiesieren wir mal den counter!
				$sql = '
						update
							' . TB_BEITRAEGE . '
						set
							counter = counter + 1
						where
							id = ' . $filter_options[thread_id] . '
					';
				$db->query($sql);
			} else if ($filter_options[compact])
			{
				// kompakte Ansicht, d.h. wir sollten mal rausfiltern wo wieviele Beiträge sind!

				// Erstmal die gesamtzahl der Beiträge
				$sql = '
						select
							count(*) as neu,
							ref_forenbeitrag_id
						from
							' . TB_BEITRAEGE . '
						where
							ref_foren_id = ' . $forum_id . '
							and
							ref_forenbeitrag_id != 0
						group by
							ref_forenbeitrag_id
					';

				// echo nl2br($sql);

				$counter_raw = $db->query($sql);
				if ($db->num_rows($counter_raw))
				{
					$counter = array();
					while ($tmp = $db->fetch_array($counter_raw))
					{
						$counter[$tmp[1]] = $tmp[0];
					}
				}

				// Und jetzt die Anzahl aller neuen Beiträge!
				$sql = '
						select
							count(*) as neu,
							ref_forenbeitrag_id
						from
							' . TB_BEITRAEGE . '
						where
							ref_foren_id = ' . $forum_id . '
							and
							ref_forenbeitrag_id != 0
							and
							date_format(date_create, "%Y%m%d%H%i%s") > ' . $session->user_info('last_login') . '
						group by
							ref_forenbeitrag_id
					';

				// echo nl2br($sql);

				$counter_raw = $db->query($sql);
				if ($db->num_rows($counter_raw))
				{
					$counter_new = array();
					while ($tmp = $db->fetch_array($counter_raw))
					{
						$counter_new[$tmp[1]] = $tmp[0];
					}
				}

			}

			// Jetzt schauen wir noch ob wir nicht einen Forenadmin bzw. einen root-user haben:
			if (($session->user_info('rights') == ROOT) OR ($forum_info[ref_user_id] == $session->user_info('id')))
			{
				$user_is_admin = 1;
			}
			else
			{
				$user_is_admin = 0;
			}

			$first_run = 1;
			while ($current = $db->fetch_array($raw))
			{
				if ($first_run)
				{
					if ($filter_options[thread_id])
					{
						$message = '
							<h2 style="margin-bottom: 10pt;">' . $forum_info[name] . ': <i>' . $current[titel] . '</i></h2>
								<table width="100%" align="center" border="0" class="list_table">';
					}
					else
					{
									$message = '
							<h2 style="margin-bottom: 10pt;">' . $forum_info[name] . '</h2>
								<table width="100%" align="center" border="0" class="list_table">
									<th>Thema</th><th>Von</th><th width=15%>Datum</th><th width="10">Zugriffe</th><th width="10" colspan="2">Antworten</th>';
					}

					$first_run = 0;
				}

				if (($current[date_create_timestamp] > $session->user_info('last_login')) or ($counter_new[$current[id]]) )
				{
					$class = 'list_table_important';
				}
				else
				{
					$class = 'list_table_active';
				}

				if ($filter_options['compact'])
				{
					if ($user_is_admin)
					{
						$insert = '
								<td width=10>
									<a href="' . $GLOBALS[PHP_SELF]. '?action=forum_deletereply&forum_id=' . $forum_id . '&thread_id=' . $current[id] . '" title="Diese Diskussion und alle Antworten löschen"><b class=error>X</b></a>
								</td>';

					}

					// Kompakt-ansicht, also nur die Header von den einzelnen Diskussionen
					$message .= '
							<tr class="' . $class . '">
								<td>
									<a href="' . $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id . '&thread_id=' . $current[id] . '">
										' . $current[titel] . '
									</a>
								</td>
								<td>
									' . $current[vorname] . ' ' . $current[name] . '
								</td>
								<td align="center">
									' . $current[date_create_readable] . '
								</td>
								<td align="center">
									<b>' . $current[counter] . '</b>
								</td>
								<td align="center" width="5%" title="Neue Beiträge seit <b>' . $session->user_info('last_login_readable') . '</b>">
									<b>' . $counter_new[$current[id]] . '</b>
								</td>
								<td align="center" width="5%"  title="Beiträge gesamt">
									<b>' . $counter[$current[id]] . '</b>
								</td>
								' . $insert . '
							</tr>';
				}
				else if ($filter_options[thread_id])
				{
					// Thread-ID gegeben, d.h. wir wollen wohl eine einzelne Diskussion anschauen...
					// Der übergebene MySQL-Result-Set dürfte jetzt nur die Nachrichten enthalten die
					// Zu dieser Diskussion gehören... oder nciht?

					// Jetzt prüfen wir ob wir der Ersteller der aktuellen Nachricht sind:
					if ($current[ref_user_id] == $session->user_info('id'))
					{
						$user_is_owner = 1;
					}
					else
					{
						$user_is_owner = 0;
					}

					if ($user_is_admin or $user_is_owner)
					{
						// Jetzt müssen wir noch prüfen ob wir einen
						// Diskussionsanfang oder eine normale Nachricht
						// haben:
						if ($current[ref_forenbeitrag_id])
						{
							// Ok, sieht nach einer ganz normalen Nachricht aus!
							$insert = '
									<a href="' . $GLOBALS[PHP_SELF]. '?action=forum_deletereply&forum_id=' . $forum_id . '&message_id=' . $current[id] . '" title="Diesen Beitrag löschen">löschen</a> - ';
						}
						else
						{
							// *urgs* keine ref_id, also scheinbar ein
							// Diskussionstart!! => Diskussion löschen!

							// Dies kann aber nur ein Forenadmin oder ein root-User:
							if ($user_is_admin)
							{
								$insert = '
									<a href="' . $GLOBALS[PHP_SELF]. '?action=forum_deletereply&forum_id=' . $forum_id . '&thread_id=' . $current[id] . '" title="Diese Diskussion und alle Antworten löschen"><b class="error">löschen</b></a> - ';
							}
							else
							{
								$insert = '
									<b class="disabled" title="Diesen Beitrag darfst du nicht löschen da eine Diskussion mit ihm verknüpft ist! Kontaktier bitte den Forenadmin!">löschen</b> - ';
							}
						}

						// Sind wir der Eigentümer der Nachricht??
						if ($user_is_owner)
						{
							$insert .= '
									<a href="' . $GLOBALS[PHP_SELF]. '?action=forum_createreply&forum_id=' . $forum_id . '&message_id=' . $current[id] . '" title="Diesen Beitrag editieren">editieren</a> - ';
						}
					}
					else
					{
						$insert = '';
					}

					$message .= '
							<tr class="' . $class . '">
								<td colspan=2>
										<a name="message' . $current[id] . '"></a><b>' . $current[titel] . '</b>
								</td>
							</tr>
							<tr>
								<td class="' . $class . '" width="20%" valign="top" rowspan="2">
									Von:&nbsp;<b>' . $current[vorname] . ' ' . $current[name] . '</b><br>
									Am:&nbsp;<b>' . $current[date_create_readable] . '</b>
								</td>
								<td valign="top" style="font-size: 12pt;">
									' . nl2br($current[beitrag]) . '
								</td>
							</tr>
							<tr>
								<td align="right" class="' . $class . '" >
									' . $insert . ' <a href="' . $PHP_SELF . '?action=forum_createreply&reply_id=' . $current[id] . '&ref_forenbeitrag_id=' . $filter_options[thread_id] . '&forum_id=' . $forum_id . '">antworten</a> - <a href="#top">nach oben</a>
								</td>
							</tr>
							<tr>
								<td colspan=2>
									&nbsp;
								</td>
							</tr>';
				}
			}

			$message .= '
						</table>';

		}
		else
		{
			$message = 'Whoups, keine Beträge.. )o:';
		}

		$menu = array();
		if ($filter_options[thread_id])
		{
			$menu[0][text] = 'Zurück zum Forum';
			$menu[0][title] = 'Hier gehts zurück zum Forum';
			$menu[0][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
			$menu[1][text] = 'antworten';
			$menu[1][link] = $PHP_SELF . '?action=forum_createreply&ref_forenbeitrag_id=' . $filter_options[thread_id] . '&forum_id=' . $forum_id;
			$menu[1][title] = 'Hier kannst du der Diskussion einen Beitrag hinzufügen';
			$menu[2][text] = 'Forenadmin kontaktieren';
			$menu[2][title] = 'Dem Admin (<b>' . $forum_info[vorname] . ' ' . $forum_info[name] . '</b>) dieses Forums eine Mail schreiben!';
			$menu[2][link] = 'mailto:' . $forum_info[email];

			if ($user_is_admin)
			{
				$menu[3][text] = 'Diskussion löschen';
				$menu[3][title] = 'Diese Diskussion und alle Antworten löschen!';
				$menu[3][link] = $GLOBALS[PHP_SELF] . '?action=forum_deletereply&forum_id=' . $forum_id . '&thread_id=' . $filter_options[thread_id];
			}
		}
		else
		{
			$menu[0][text] = 'Neue Diskussion';
			$menu[0][link] = $PHP_SELF . '?action=forum_createreply&ref_forenbeitrag_id=0&forum_id=' . $forum_id;
			$menu[0][title] = 'Hier kannst du eine neue Diskussion starten';
			$menu[1][text] = 'Forenadmin kontaktieren';
			$menu[1][title] = 'Dem Admin (<b>' . $forum_info[vorname] . ' ' . $forum_info[name] . '</b>) dieses Forums eine Mail schreiben!';
			$menu[1][link] = 'mailto:' . $forum_info[email];
		}

		$output = $page->dialog_box(0, $page->dialog_box(0, $message, 0, 0, '100%'), $menu, $menu2, '96%');;

		return($output);

	}


	/*******************************************************************************
	* Last edit: 12.07.2003
	*
	* function view_forum_overview()
	*
	* Funktion:
	* 			Zeigt eine Übersicht über alle vorhandenen _Foren an
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
	function view_forum_overview()
	{
	}

}
?>
