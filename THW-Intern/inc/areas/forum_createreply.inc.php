<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 14.07.2003
* Last edit: 15.07.2003
*
* forum_createreply.inc.php
*
* Funktion:
*			Kümmert sich um das Erstellen von von Nachrichten und Antworten...
*
* Bemerkungen:
*
* TODO:
*
* DONE:
* - neue Diskussionen erstellen!
* - Antworten erstellen
* - Beim Erstellen einer Antwort quoten oder zumindest den Beitrag auf den man
*			antwortet anzeigen...
* - Editieren von Beiträge: Laden der Werte und DB aktualisieren...
* - Bei einer neuen Diskussion ref_forenbeitrag_id = 0 überbrücken und auf
* 			last_insert_id setzen...
*
*******************************************************************************/

	require_once('inc/classes/class_form2.inc.php');

	$form = new Form2($PHP_SELF, 'post', 'form_createreply_dialog');

	$page->title_bar();

	// OK, wir wollen einen Beitrag erstellen; Zuerst prüfen wir ob wir auf
	// einen Vorhandenen Beitrag antworten oder ob wir eine neue Diskussion
	// anfangen oder ob wir einen bereits vorhandenen Beitrag editieren...

	if ($ref_forenbeitrag_id)
	{
		// Ok, wir erstellen einen Antwort...
		// TODO:
		// - Text zum zitieren holen??


		$sql = '
				select
					' . TB_BEITRAEGE . '.titel,
					' . TB_BEITRAEGE . '.beitrag,
					' . TB_USER . '.name,
					' . TB_USER . '.vorname
				from
					' . TB_BEITRAEGE . ',
					' . TB_USER . '
				where
					' . TB_BEITRAEGE . '.id = ' . $reply_id . '
					and
					' . TB_USER . '.id = ' . TB_BEITRAEGE . '.ref_user_id
			';

		$raw = $db->fetch_array($db->query($sql));

		$title = 'Antworten';

		$fields = array();

		$fields[0] = array(
				'type' => 'separator',
				'value' => '
						<b>Antworten auf: <i>' . $raw[titel] . '</i></b>
					'
			);

		$fields[] = array(
				'type' => 'separator',
				'value' => '
						<p align="left"><i>' . nl2br($raw[beitrag]) . '</i></p>
					'
			);
		$fields[] = array(
				'type' => 'separator',
				'value' => '
						von <b>' . $raw[vorname] . ' ' . $raw[name] . '</b>
					'
			);
		$fields[] = array(
				'name' => 'titel',
				'type' => 'text',
				'title' => 'Titel:',
				'important' => 1,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Hier bitte einen <b>markanten, beschreibendenen</b> Titel eingeben'
							),
						1 => array(
								'name' => 'size',
								'value' => '20'
							)
					)
			);
		$fields[] = array(
				'name' => 'beitrag',
				'type' => 'textarea',
				'title' => 'Beitrag:',
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
								'value' => '15'
							)
					)
			);
		$fields[] = array(
				'type' => 'hidden',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'forum_createreply',
								'name' => 'action'
							),
						1 => array(
								'value' => $forum_id,
								'name' => 'forum_id'
							),
						2 => array(
								'value' => $ref_forenbeitrag_id,
								'name' => 'ref_forenbeitrag_id'
							),
						3 => array(
								'value' => $reply_id,
								'name' => 'reply_id'
							)
					)
			);
		$fields[] = array(
				'type' => 'buttons',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'Los&gt;&gt;',
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


		$presets = array();
		$presets[titel] = $raw[titel];

		$form->load_presets($presets);
	}
	else if ($message_id)
	{
		// Wir editieren einen bestehenden Beitrag
		// TODO:
		// - evtl. unten anfügen: "Dieser Beitrag wurde von XYZ am ZYX editiert..."

		// Zuersteinmal die entsprechenden Daten holen:
		$sql = '
				select
					titel,
					beitrag
				from
					' . TB_BEITRAEGE . '
				where
					id = ' . $message_id . '
			';
		// echo nl2br($sql);

		$raw = $db->query($sql);
		if ($db->num_rows($raw))
		{
			$raw = $db->fetch_array($raw);

			$fields = array();

			$fields[0] = array(
					'name' => 'titel',
					'type' => 'text',
					'title' => 'Titel:',
					'important' => 1,
					'attribs' => array(
							0 => array(
									'name' => 'title',
									'value' => 'Hier bitte einen <b>markanten, beschreibendenen</b> Titel eingeben'
								),
							1 => array(
									'name' => 'size',
									'value' => '20'
								)
						)
				);
			$fields[] = array(
					'name' => 'beitrag',
					'type' => 'textarea',
					'title' => 'Beitrag:',
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
									'value' => '15'
								)
						)
				);
			$fields[] = array(
					'type' => 'hidden',
					'important' => 0,
					'selections' => array(
							0 => array(
									'value' => 'forum_createreply',
									'name' => 'action'
								),
							1 => array(
									'value' => $forum_id,
									'name' => 'forum_id'
								),
							2 => array(
									'value' => $message_id,
									'name' => 'message_id'
								)
						)
				);
			$fields[] = array(
					'type' => 'buttons',
					'important' => 0,
					'selections' => array(
							0 => array(
									'value' => 'Speichern&gt;&gt;',
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

			$presets = array();
			$presets[titel] = $raw[titel];
			$presets[beitrag] = $raw[beitrag];

			// Presets an das Formular übergeben!
			$form->load_presets($presets);
		}
		else
		{
			// *urgs* Fehler beim laden...
			die('*urgs* Fehler beim laden der Beitragdaten... breche ab...');
		}
	}
	else
	{
		// Nix ausgewählt, also wollen wir wohl eine neue Diskussion anfangen...
		$title = 'Neue Diskussion';

		$fields = array();

		$fields[0] = array(
				'name' => 'titel',
				'type' => 'text',
				'title' => 'Titel:',
				'important' => 1,
				'attribs' => array(
						0 => array(
								'name' => 'title',
								'value' => 'Hier bitte einen <b>markanten, beschreibendenen</b> Titel eingeben'
							),
						1 => array(
								'name' => 'size',
								'value' => '20'
							)
					)
			);
		$fields[] = array(
				'name' => 'beitrag',
				'type' => 'textarea',
				'title' => 'Beitrag:',
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
								'value' => '15'
							)
					)
			);
		$fields[] = array(
				'type' => 'hidden',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'forum_createreply',
								'name' => 'action'
							),
						1 => array(
								'value' => $forum_id,
								'name' => 'forum_id'
							),
						2 => array(
								'value' => $ref_forenbeitrag_id,
								'name' => 'ref_forenbeitrag_id'
							)
					)
			);
		$fields[] = array(
				'type' => 'buttons',
				'important' => 0,
				'selections' => array(
						0 => array(
								'value' => 'Los&gt;&gt;',
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
	}


	$form->load_form($fields);		// Formular laden
 	$form->precheck_form();

	if ($form->is_form_error())
	{
		$message = $form->build_form();
	}
	else
	{
		// Fertig!!! Daten eintragen!!!!!

		if ($message_id)
		{
			// Titel und Beitrag zuschneiden!
			// Nachricht anfügen!!
			$sql = '
					update
						' . TB_BEITRAEGE . '
					set
						beitrag = "' . (htmlentities(trim($beitrag)) . ' \n \nDieser Beitrag wurde von <b>' . $session->user_info('vorname') . ' ' . $session->user_info('name') . '</b> am <b>' . strftime("%d.%m.%Y</b> um <b>%H:%M") . '</b> editiert!' ) . '",
						titel = "' . htmlentities(trim($titel)) . '"
					where
						id = ' . $message_id . '
				';
			 // echo nl2br($sql);

			$db->query($sql);
			if ($db->affected_rows())
			{
				$title = 'Beitrag editiert';
				$message = 'Die Änderungen wurden gespeichert!';
				$width = '50%';
			}
			else
			{
				// Urgs, da ist was schiefgelaufen...
				$title = 'Beitrag editieren - Fehler';
				$message = '*urgs* Beim abspeichern des Beitrages ist was schiefgelaufen (DB::affected_rows() = 0)! Die Änderungen wurden <b>NICHT</b> gespeichert!';
				$width = '50%';
			}
		}
		else
		{
			// Wir erstellen eine komplett neue Diskussion:

			if (!$ref_forenbeitrag_id)
			{
				$ref_forenbeitrag_id = 0;
			}

			$thread_id = $ref_forenbeitrag_id;

			$sql = '
					insert
					into
						' . TB_BEITRAEGE . '
					(
						ref_user_id,
						ref_foren_id,
						ref_forenbeitrag_id,
						titel,
						beitrag,
						date_create,
						date_lastviewed,
						counter
					)
					values
					(
						' . $session->user_info('id') . ',
						' . $forum_id . ',
						' . $ref_forenbeitrag_id . ',
						"' . trim(htmlentities($titel)) . '",
						"' . trim(htmlentities($beitrag)) . '",
						"' . strftime("%Y-%m-%d %H:%M:%S") . '",
						"' . strftime("%Y-%m-%d %H:%M:%S") . '",
						1
					)
				';

			// echo nl2br($sql);
			$db->query($sql);
			if ($db->affected_rows())
			{
				$last_id = $db->last_insert_id();
				if (!$thread_id)
				{
					$thread_id = $last_id;
				}

				$message = 'Beitrag wurde gespeichert!';
				$bottom_menu = array();
				$bottom_menu[0][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id . '&thread_id=' . $thread_id;
				$bottom_menu[0][text] = 'Zur Diskussion';
				$bottom_menu[1][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id . '&thread_id=' . $thread_id . '&message_id=' . $last_id . '#message' . $last_id;
				$bottom_menu[1][text] = 'Zum Beitrag';

			}
			else
			{
				$message = '<p class="error">*urgs* Beim Eintragen ist etwas schiefgelaufen!!! (DB::affected_rows() = 0) </p>';
			}
		}

		$form->form_shutdown();
	}



	$width = '70%';

	$menu = array();
	$menu[0][link] = $PHP_SELF . '?action=forum';
	$menu[0][text] = 'Forenübersicht';
	$menu[1][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
	$menu[1][text] = 'Zurück zum Forum';
	if ($ref_forenbeitrag_id)
	{
		$menu[2][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id . '&thread_id=' . $ref_forenbeitrag_id;
		$menu[2][text] = 'Zurück zur Diskussion';
	}

	echo $page->dialog_box($title, $message, $menu, $bottom_menu, $width);

?>
