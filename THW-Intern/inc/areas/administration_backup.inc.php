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

	$message ='
		Wat tu ma da nur rein?
		';
	echo $page->dialog_box('Backups', $message, 0, 0, '50%');

?>
