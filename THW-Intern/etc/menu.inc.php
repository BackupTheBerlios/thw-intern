<?php

	// MAIN_MENU :
	// Hier wird das Hauptmenü, welches standartmäßig von interface::title_bar() ausgegeben wird,
	// gespeichert!
	$MAIN_MENU = array();

	$MAIN_MENU[0][link] = $PHP_SELF;
	$MAIN_MENU[0][text] = 'I n t e r n';
	$MAIN_MENU[0][action] = 'intern';

	$MAIN_MENU[1][link] = $PHP_SELF;
	$MAIN_MENU[1][text] = 'Berichte';
	$MAIN_MENU[1][action] = 'report';

	$MAIN_MENU[2][link] = $PHP_SELF;
	$MAIN_MENU[2][text] = 'Termine';
	$MAIN_MENU[2][action] = 'date';

	$MAIN_MENU[3][link] = $PHP_SELF;
	$MAIN_MENU[3][text] = 'Ausbildungen';
	$MAIN_MENU[3][action] = 'training';

	$MAIN_MENU[4][link] = $PHP_SELF;
	$MAIN_MENU[4][text] = 'Forum';
	$MAIN_MENU[4][action] = 'forum';

	$MAIN_MENU[5][link] = $PHP_SELF;
	$MAIN_MENU[5][text] = 'Administration';
	$MAIN_MENU[5][action] = 'administration';

	// $SUB_MENU : Das entsprechende Untermenü
	$SUB_MENU = array(
			'intern' => array(
					0 => array(
							'text' => 'Startseite',
							'action' => 'intern'
						),
					1 => array(
							'text' => 'Mein Account',
							'action' => 'intern_myaccount',
							'title' => 'Hier kannst du persönliche Einstellungen vornehmen!'
						),
					2 => array(
							'text' => 'Downloads',
							'action' => '',
							'title' => 'Hier gibts diverse Formulare u. ä. zum Downloaden!'
						),
					3 => array(
							'text' => 'Links',
							'action' => 'intern_link',
							'title' => 'Hier gehts zur OV-Internen Linkliste!'
						)
				),
			'news' => array(
					0 => array(
							'text' => 'Newsübersicht',
							'action' => 'news'
						),
					1 => array(
							'text' => 'News anlegen',
							'action' => 'news_create'
								)
				),
			'report' => array(
					0 => array(
							'text' => 'Berichtübersicht',
							'action' => 'report'
						),
					1 => array(
							'text' => 'Komplette Liste',
							'action' => 'report_completelist'
						),
					2 => array(
							'text' => 'Bericht anlegen',
							'action' => 'report_create'
						)
				),
			'date' => array(
					0 => array(
							'text' => 'Terminübersicht',
							'action' => 'date'
						),
					1 => array(
							'text' => 'komplette Liste',
							'action' => 'date_completelist'
						),
					2 => array(
							'text' => 'Termin anlegen',
							'action' => 'date_create'
						)
				),
			'training' => array(
					0 => array(
							'text' => 'Übersicht',
							'action' => 'training'
						),
					1 => array(
							'text' => 'Neuen Termin anlegen',
							'action' => 'date_create'
						),
					2 => array(
							'text' => 'Neuen Bericht anlegen',
							'action' => 'report_create'
						)
				),
			'forum' => array(
					0 => array(
							'text' => 'Forenübersicht',
							'action' => 'forum'
						)
				),
			'downloads' => array(
				),
			'administration' => array(
					0 => array(
							'text' => 'Übersicht',
							'action' => 'administration'
						),
					1 => array(
							'text' => 'Benutzerverwaltung',
							'action' => 'administration_user'
						),
					2 => array(
							'text' => 'Foren',
							'action' => 'administration_forum'
						),
					3 => array(
							'text' => 'Gästebuch',
							'action' => 'administration_guestbook'
						),
					4 => array(
							'text' => 'Zugangsrechte',
							'action' => 'administration_rights'
						),
					5 => array(
							'text' => 'Backup',
							'action' => 'administration_backup'
						),
					6 => array(
							'text' => 'Logfiles',
							'action' => 'administration_logviewer'
						)
				)
		);

?>
