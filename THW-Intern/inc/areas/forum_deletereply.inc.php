<?php
/*******************************************************************************
* (c) 2003 Jakob K�lzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 14.07.2003
* Last edit: 15.07.2003
*
* forum_deletereply.inc.php
*
* Funktion:
*			K�mmert sich um das L�schen von Beitr�gen und Diskussionen...
*
* Bemerkungen:
*
* TODO:
*
* DONE:
* - einzelne Beitr�ge l�schen
* - ganze Diskussionen l�schen, pr�fen auf $thread_id
*
*******************************************************************************/

	$page->title_bar();


	// Muss �berhaupt schon was getan werden??
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
				// Ok, Datens�tze gel�scht!

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[0][text] = 'Zum Forum';

				$message = '
						Die Diskussion wurde gel�scht! (Insgesamt wurden ' . $db->affected_rows() . ' Datens�tze gel�scht!)
					';

				echo $page->dialog_box('Diskussion gel�scht', $message, 0, $menu, '50%');
			}
			else
			{
				// Whoups, keine ver�nderten Datens�tze; Darf nicht sein
				// bei einer konkreten WHERE-klausel... FEHLER!

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[0][text] = 'Zum Forum';

				$message = '
						*urgs* Beim l�schen ist ein Fehler aufgetreten! (DB::affected_rows(): ' . $db->affected_rows() . ')
					';

				echo $page->dialog_box('Diskussion l�schen - Fehler', $message, 0, $menu, '50%');
			}
		}
		else
		{
			// Nur eine einzelne Nachricht l�schen!
			$sql = '
					delete from
						' . TB_BEITRAEGE . '
					where
						id = ' . $message_id . '
				';

			$db->query($sql);
			if ($db->affected_rows())
			{
				// Ok, Datens�tze gel�scht!

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[0][text] = 'Zum Forum';

				$message = '
						Der Beitrag wurde gel�scht!
					';

				echo $page->dialog_box('Beitrag gel�scht', $message, 0, $menu, '50%');
			}
			else
			{
				// Whoups, keine ver�nderten Datens�tze; Darf nicht sein
				// bei einer konkreten WHERE-klausel... FEHLER!

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[0][text] = 'Zum Forum';

				$message = '
						*urgs* Beim L�schen ist ein Fehler aufgetreten! (DB::affected_rows(): ' . $db->affected_rows() . ')
					';

				echo $page->dialog_box('Beitrag l�schen - Fehler', $message, 0, $menu, '50%');
			}
		}

	}
	else
	{
		if ($thread_id)
		{
			// Uuhooh, wir haben eine Thread-ID bekommen; d.h. wir wollen eine
			// ganze Diskussion l�schen!! *urgs*

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
				$message = 'Soll die Diskussion "<b>' . $info[titel] . '</b>" und alle sich darauf beziehenden Antworten wirklich gel�scht werden?';

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_deletereply&confirmed=1&forum_id=' . $forum_id . '&thread_id=' . $thread_id;
				$menu[0][text] = 'Ja, l�schen';
				$menu[1][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[1][text] = 'Nein, nicht l�schen';
				$menu[2][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id . '&thread_id=' . $thread_id;
				$menu[2][text] = 'Zur Diskussion';

				echo $page->dialog_box('Diskussion l�schen', $message, 0, $menu, '50%');
			}
			else
			{
				// *urgs* Nix gefunden, also kann auch nix gel�scht werden...
				echo $page->dialog_box('Diskussion l�schen - Fehler', '*urgs* Die Diskussion (ID: ' . $thread_id . ') konnte nichte gefunden werden... breche ab!', 0, 0, '50%');
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
				$message = 'Soll der Beitrag "<b>' . $info[titel] . '</b>" wirklich gel�scht werden?';

				$menu = array();
				$menu[0][link] = $PHP_SELF . '?action=forum_deletereply&confirmed=1&forum_id=' . $forum_id . '&message_id=' . $message_id;
				$menu[0][text] = 'Ja, l�schen';
				$menu[1][link] = $PHP_SELF . '?action=forum_overview&forum_id=' . $forum_id;
				$menu[1][text] = 'Nein, nicht l�schen';

				echo $page->dialog_box('Beitrag l�schen', $message, 0, $menu, '50%');
			}
			else
			{
				// *urgs* Nix gefunden, also kann auch nix gel�scht werden...
				echo $page->dialog_box('Beitrag l�schen - Fehler', '*urgs* Der Beitrag (ID: ' . $message_id . ') konnte nichte gefunden werden... breche ab!', 0, 0, '50%');
			}
		}

		$menu = array();
		$menu[0][link] = $PHP_SELF . '?action=news_delete&confirmed=1&id=' . $id;
		$menu[0][text] = 'Ja, l�schen';
		$menu[1][link] = $PHP_SELF . '?action=news';
		$menu[1][text] = 'Nein, nicht l�schen';
		$menu[2][link] = $PHP_SELF . '?action=news_read&id=' . $id;
		$menu[2][text] = 'Den Beitrag lesen';

	}

?>

