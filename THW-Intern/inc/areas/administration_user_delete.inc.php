<?php

// 	$menu = array();
// 	$menu[0][text] = 'Überischt';
// 	$menu[0][action] = 'administration';
// 	$menu[1][text] = 'Benutzerverwaltung';
// 	$menu[1][action] = 'administration_user';
// 	$menu[2][text] = 'Foren';
// 	$menu[2][action] = 'administration_foren';
// 	$menu[3][text] = 'Gästebuch';
// 	$menu[3][action] = 'administration_guestbook';
// 	$menu[4][text] = 'Backup';
// 	$menu[4][action] = 'administration_backup';
// 	$menu[5][text] = 'Zugangsrechte';
// 	$menu[5][action] = 'administration_rights';

	$page->title_bar();


		$sql = '
				select
					' . TB_USER . '.name,
					' . TB_USER . '.vorname
				from
					' . TB_USER . '
				where
					' . TB_USER . '.id = ' . $id . '
			';
		$current = $db->fetch_array($db->query($sql));

		if ($confirmed and $id)
		{
			$menu = array();
			$menu[0][text] = 'Zurück zur Benutzerverwaltung';
			$menu[0][link] = $PHP_SELF . '?action=administration_user';
			$menu[1][text] = 'Neuen User anlegen';
			$menu[1][link] = $PHP_SELF . '?action=administration_user_create';

			$menu2 = array();
			$menu2[0][text] = 'OK';
			$menu2[0][link] = $PHP_SELF . '?action=administration_user';

			// Alle Zugangsberechtigungen die dieser User hatte entfernen!
			$sql = '
					delete
					from 
						' . TB_REF_USER_RECHTE . '
					where 
						ref_user_id = ' . $id . '
				';
				
			$db->query($sql);

			// Und jetzt den User an sich löschen!
			$sql = '
					delete
					from
						' . TB_USER . '
					where
						id = ' . $id . '
				';

			$db->query($sql);

			$message = '
					<p>Der User <b>' . $current[vorname] . ' ' . $current[name] . '</b> wird jetzt gelöscht!!!</p>

					<p class=small><b>Hinweis :</b> <br>
						Zu Testzwecken werden die User vorerst noch komplett gelöscht!!!!
					</p>
				';

			echo $page->dialog_box('Benutzerverwaltung - User betrachten', $message, $menu, $menu2, '50%');
		}
		else
		{
			$menu = array();
			$menu[0][text] = 'Zurück zur Benutzerverwaltung';
			$menu[0][link] = $PHP_SELF . '?action=administration_user';
			$menu[1][text] = 'Neuen User anlegen';
			$menu[1][link] = $PHP_SELF . '?action=administration_user_create';

			$menu2 = array();
			$menu2[0][text] = 'Nein, User beibehalten';
			$menu2[0][link] = $PHP_SELF . '?action=administration_user_view&auswahl=' . $id;
			$menu2[1][text] = 'Ja, User löschen';
			$menu2[1][link] = $PHP_SELF . '?action=administration_user_delete&id=' . $id . '&confirmed=1';

			$message = '
					<p>Soll der User <b>' . $current[vorname] . ' ' . $current[name] . '</b> wirklich gelöscht werden??</p>

					<p class=small><b>Hinweis :</b> <br>
						Der User wird nicht sofort gelöscht, er wird vorerst nur als <b>inaktiv</b> markiert! Komplett
						aus der Datenbank wird er erst später gelöscht!
					</p>
				';

			echo $page->dialog_box('Benutzerverwaltung - User betrachten', $message, $menu, $menu2, '50%');
		}
?>
