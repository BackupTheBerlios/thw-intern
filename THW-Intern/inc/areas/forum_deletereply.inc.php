<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 14.07.2003
* Last edit: 15.07.2003
*
* forum_deletereply.inc.php
*
* Funktion:
*			Kümmert sich um das Löschen von Beiträgen und Diskussionen...
*
* Bemerkungen:
*
* TODO:
*
* DONE:
* - einzelne Beiträge löschen
* - ganze Diskussionen löschen, prüfen auf $thread_id
*
*******************************************************************************/

	$page->title_bar();


	// Muss überhaupt schon was getan werden??
	if ($confirmed)
	{

		if ($thread_id)
		{
			$sql = '
					delete from
						' . TB_BEITRAEGE . '
					where
						ref_forenbeitrag_id = ' . $thread_id . '
						or
						id = ' . $thread_id . '
				';

			$db->query($sql);
			if ($db->affected_rows())
			{
				// Ok, Datensätze gelöscht!

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[0][text] = 'Zum Forum';

				$message = '
						Die Diskussion wurde gelöscht! (Insgesamt wurden ' . $db->affected_rows() . ' Datensätze gelöscht!)
					';

				echo $page->dialog_box('Diskussion gelöscht', $message, 0, $menu, '50%');
			}
			else
			{
				// Whoups, keine veränderten Datensätze; Darf nicht sein
				// bei einer konkreten WHERE-klausel... FEHLER!

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[0][text] = 'Zum Forum';

				$message = '
						*urgs* Beim löschen ist ein Fehler aufgetreten! (DB::affected_rows(): ' . $db->affected_rows() . ')
					';

				echo $page->dialog_box('Diskussion löschen - Fehler', $message, 0, $menu, '50%');
			}
		}
		else
		{
			// Nur eine einzelne Nachricht löschen!
			$sql = '
					delete from
						' . TB_BEITRAEGE . '
					where
						id = ' . $message_id . '
				';

			$db->query($sql);
			if ($db->affected_rows())
			{
				// Ok, Datensätze gelöscht!

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[0][text] = 'Zum Forum';

				$message = '
						Der Beitrag wurde gelöscht!
					';

				echo $page->dialog_box('Beitrag gelöscht', $message, 0, $menu, '50%');
			}
			else
			{
				// Whoups, keine veränderten Datensätze; Darf nicht sein
				// bei einer konkreten WHERE-klausel... FEHLER!

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[0][text] = 'Zum Forum';

				$message = '
						*urgs* Beim Löschen ist ein Fehler aufgetreten! (DB::affected_rows(): ' . $db->affected_rows() . ')
					';

				echo $page->dialog_box('Beitrag löschen - Fehler', $message, 0, $menu, '50%');
			}
		}

	}
	else
	{
		if ($thread_id)
		{
			// Uuhooh, wir haben eine Thread-ID bekommen; d.h. wir wollen eine
			// ganze Diskussion löschen!! *urgs*

			// Erstmal ein paar Infos einholen:
			$sql = '
				select
					titel
				from
					' . TB_BEITRAEGE . '
				where
					id = ' . $thread_id;
			$info = $db->query($sql);
			if ($db->num_rows($info))
			{
				$info = $db->fetch_array($info);
				// Ok, anzeigen...
				$message = 'Soll die Diskussion "<b>' . $info[titel] . '</b>" und alle sich darauf beziehenden Antworten wirklich gelöscht werden?';

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_deletereply&confirmed=1&forum_id=' . $forum_id . '&thread_id=' . $thread_id;
				$menu[0][text] = 'Ja, löschen';
				$menu[1][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[1][text] = 'Nein, nicht löschen';
				$menu[2][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id . '&thread_id=' . $thread_id;
				$menu[2][text] = 'Zur Diskussion';

				echo $page->dialog_box('Diskussion löschen', $message, 0, $menu, '50%');
			}
			else
			{
				// *urgs* Nix gefunden, also kann auch nix gelöscht werden...
				echo $page->dialog_box('Diskussion löschen - Fehler', '*urgs* Die Diskussion (ID: ' . $thread_id . ') konnte nichte gefunden werden... breche ab!', 0, 0, '50%');
			}
		}
		else
		{
			// Erstmal ein paar Infos einholen:
			$sql = '
				select
					titel
				from
					' . TB_BEITRAEGE . '
				where
					id = ' . $message_id;
			$info = $db->query($sql);
			if ($db->num_rows($info))
			{
				$info = $db->fetch_array($info);
				// Ok, anzeigen...
				$message = 'Soll der Beitrag "<b>' . $info[titel] . '</b>" wirklich gelöscht werden?';

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_deletereply&confirmed=1&forum_id=' . $forum_id . '&message_id=' . $message_id;
				$menu[0][text] = 'Ja, löschen';
				$menu[1][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[1][text] = 'Nein, nicht löschen';

				echo $page->dialog_box('Beitrag löschen', $message, 0, $menu, '50%');
			}
			else
			{
				// *urgs* Nix gefunden, also kann auch nix gelöscht werden...
				echo $page->dialog_box('Beitrag löschen - Fehler', '*urgs* Der Beitrag (ID: ' . $message_id . ') konnte nichte gefunden werden... breche ab!', 0, 0, '50%');
			}
		}

		$menu = array();
		$menu[0][link] = $PHP_SELF . '?action=news_delete&confirmed=1&id=' . $id;
		$menu[0][text] = 'Ja, löschen';
		$menu[1][link] = $PHP_SELF . '?action=news';
		$menu[1][text] = 'Nein, nicht löschen';
		$menu[2][link] = $PHP_SELF . '?action=news_read&id=' . $id;
		$menu[2][text] = 'Den Beitrag lesen';

	}

?>

