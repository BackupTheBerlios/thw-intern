<?php
	// Selbsterklärend...
	define('OV_NAME', 'München-West');

	// Welche include-Datei soll geladen werden wenn $action leer ist? (OHNE .inc.php!!!)
	define('DEFAULT_STARTPAGE', 'intern');

	// Die Fehlerseite falls der User eine geschützte Seite aufruft!
	define('ACCESS_DENIED', 'access_denied');

	// Wie lange darf ein User idle sein bevor ihn das System als offline markiert?
	// ACHTUNG :
	// Der user wird dann nur als offline markiert, seine session wird NICHT geschlossen!
	// Der Wert wird in Sekunden angegeben!
	define('MAX_IDLE_TIME', '3600');

	// Wie lange darf ein Eintrag in tb_object liegen bevor er gelöscht wird! (default : 1 Tag)
	define('MAX_UNFINISHED_TIME', '86400');


	// Wieviele Berichte werden auf der Berichtseite pro Spalte angezeigt?
	define('MAX_REPORTS_PER_COLUMN', '10');

	// Maximale Anzahl an News auf der Startseite
	define('MAX_NEWS_ON_STARTPAGE', '5');

	// Max. Anzahl an Terminen pro Box...
	define('MAX_DATES_PER_COLUMN', '10');


	// Wo werden Photos gespeichert?
	define('PHOTO_PATH', 'var/photos/');

	// Wieviele Photos sollen pro Bericht angezeigt werden??
	define('MAX_PHOTOS_PER_REPORT', 4);

	// Wieviele Photos sollen pro Gallerie-Seite angezeigt werden??
	// Sollte ein vielfaches von 3 sein, da immer 3 Bilder nebeneinander angezeigt werden
	define('PHOTOS_PER_GALLERY', 9);

	// Wieviele Berichte sollen pro Seite angezeigt werden (extern)
	define('REPORTS_PER_PAGE', 5);

	// Die verschiedenen Berichtarten :

	define('REPORTTYPE_EINSATZ', 1);
	define('REPORTTYPE_VERANST', 2);
	define('REPORTTYPE_KAMVER', 3);
	define('REPORTTYPE_UEBUNG', 4);
	define('REPORTTYPE_SONSTIG', 5);
	define('REPORTTYPE_AB', 6);
	define('REPORTTYPE_MOD', 7);
	define('REPORTTYPE_NEWS', 8);
	define('REPORTTYPE_DIARY', 9);

	$REPORT_TYPES = array();
	$REPORT_TYPES[REPORTTYPE_EINSATZ] = array(
			'name' => 'Einsatz',
			'public' => 1,
			'date' => 1
		);
	$REPORT_TYPES[REPORTTYPE_VERANST] = array(
			'name' => 'Veranstaltung',
			'public' => 1,
			'date' => 1
		);
	$REPORT_TYPES[REPORTTYPE_KAMVER] = array(
			'name' => 'kam. Veranstaltung',
			'public' => 1,
			'date' => 1,
			'userlist' => 1
		);
	$REPORT_TYPES[REPORTTYPE_UEBUNG] = array(
			'name' => 'Übung',
			'public' => 1,
			'date' => 1
		);
	$REPORT_TYPES[REPORTTYPE_SONSTIG] = array(
			'name' => 'Sonstiges',
			'public' => 1,
			'date' => 1,
			'userlist' => 1
		);
	$REPORT_TYPES[REPORTTYPE_AB] = array(
			'name' => 'Ausbildungsbeschreibung'
		);

	// Diese Typen sollten nicht per report_create erstellt werden können!!
	$REPORT_TYPES[REPORTTYPE_MOD] = array(
			'name' => 'Spruch/Bild des Monats'
		);
	$REPORT_TYPES[REPORTTYPE_NEWS] = array(
			'name' => 'News',
			'public' => 1
		);
	$REPORT_TYPES[REPORTTYPE_DIARY] = array(
			'name' => 'Tagebuch',
			'public' => 1
		);

	// Die verschiedenen Termintypen:
	// Ausbildungen:
	define('DATETYPE_GA', 1);
	define('DATETYPE_FA', 2);
	define('DATETYPE_BA', 3);
	define('DATETYPE_LG', 4);

	// Veranstaltungen
	define('DATETYPE_OV', 5);
	define('DATETYPE_KV', 6);

	// Dienste
	define('DATETYPE_THV', 7);
	define('DATETYPE_TD', 8);
	define('DATETYPE_ID', 9);
	define('DATETYPE_SP', 10);		// Straßensperre

	$DATE_TYPES = array();
	$DATE_TYPES[DATETYPE_GA] = array(
			'name' => 'Grundausbildung'
		);
	$DATE_TYPES[DATETYPE_FA] = array(
			'name' => 'Fachausbildung'
		);
	$DATE_TYPES[DATETYPE_BA] = array(
			'name' => 'Bereichsausbildung'
		);
	$DATE_TYPES[DATETYPE_LG] = array(
			'name' => 'Lehrgang'
		);
	$DATE_TYPES[DATETYPE_OV] = array(
			'name' => 'Öffentlichkeitsveranstaltung'
		);
	$DATE_TYPES[DATETYPE_KV] = array(
			'name' => 'kam. Veranstaltung'
		);
	$DATE_TYPES[DATETYPE_THV] = array(
			'name' => 'THV'
		);
	$DATE_TYPES[DATETYPE_TD] = array(
			'name' => 'Technischer Dienst'
		);
	$DATE_TYPES[DATETYPE_ID] = array(
			'name' => 'Innerer Dienst'
		);
	$DATE_TYPES[DATETYPE_SP] = array(
			'name' => 'Straßensperre'
		);


	// Dieser array enthält die Funktionen die einem User zugewiesen werden können
	// wenn flag_funktion gesetzt ist!
	$DATE_FUNCTIONS = array();
	$DATE_FUNCTIONS[0] = array(
			'name' => 'He'
		);
	$DATE_FUNCTIONS[] = array(
			'name' => 'DGL'
		);
	$DATE_FUNCTIONS[] = array(
			'name' => 'San'
		);
	$DATE_FUNCTIONS[] = array(
			'name' => 'KF'
		);
	$DATE_FUNCTIONS[] = array(
			'name' => 'SprFu'
		);
	$DATE_FUNCTIONS[] = array(
			'name' => 'AGT'
		);
	$DATE_FUNCTIONS[] = array(
			'name' => 'Masch'
		);


	// Die verschiedenen Primärfunktionen :

	define('FUNKTION_HAW', 1);
	define('FUNKTION_HE', 2);
	define('FUNKTION_TRUFU', 3);
	define('FUNKTION_GRUFU', 4);
	define('FUNKTION_ZTRUFU', 5);
	define('FUNKTION_ZUFU', 6);
	define('FUNKTION_VWHE', 7);
	define('FUNKTION_KUHE', 8);
	define('FUNKTION_KO', 9);
	define('FUNKTION_JB', 10);
	define('FUNKTION_AB', 11);
	define('FUNKTION_BOH', 12);
	define('FUNKTION_STVOB', 13);
	define('FUNKTION_OB', 14);

	// Hier werden die einzelnen Primärfunktionen gespeichert!
	$RANG = array();
	$RANG[FUNKTION_HAW] = array (
			'name' => 'Helferanw&auml;rter',
			'tz' => 1,
			'id' => FUNKTION_HAW
		);
	$RANG[FUNKTION_HE] = array (
			'name' => 'Helfer',
			'tz' => 1,
			'id' => FUNKTION_HE
		);
	$RANG[FUNKTION_TRUFU] = array (
			'name' => 'Truppf&uuml;hrer',
			'tz' => 1,
			'id' => FUNKTION_TRUFU
		);
	$RANG[FUNKTION_GRUFU] = array (
			'name' => 'Gruppenf&uuml;hrer',
			'tz' => 1,
			'id' => FUNKTION_GRUFU
		);
	$RANG[FUNKTION_ZTRUFU] = array (
			'name' => 'Zugtruppf&uuml;hrer',
			'tz' => 1,
			'id' => FUNKTION_ZTRUFU
		);
	$RANG[FUNKTION_ZUFU] = array (
			'name' => 'Zugf&uuml;hrer',
			'tz' => 1,
			'id' => FUNKTION_ZUFU
		);

	$RANG[FUNKTION_VWHE] = array (
			'name' => 'Verwaltungshelfer',
			'stab' => 1,
			'id' => FUNKTION_VWHE
		);
	$RANG[FUNKTION_KO] = array (
			'name' => 'Koch',
			'stab' => 1,
			'id' => FUNKTION_KO
		);
	$RANG[FUNKTION_JB] = array (
			'name' => 'Jugendbetreuer',
			'stab' => 1,
			'id' => FUNKTION_JB
		);
	$RANG[FUNKTION_KUHE] = array (
			'name' => 'Küchenhelfer',
			'stab' => 1,
			'id' => FUNKTION_KUHE
		);
	$RANG[FUNKTION_AB] = array (
			'name' => 'Ausbildungsbeauftragter',
			'stab' => 1,
			'id' => FUNKTION_AB
		);
	$RANG[FUNKTION_BOH] = array (
			'name' => 'BÖH',
			'stab' => 1,
			'id' => FUNKTION_BOH
		);
	$RANG[FUNKTION_STVOB] = array (
			'name' => 'stv. Ortsbeauftragter',
			'stab' => 1,
			'id' => FUNKTION_STVOB
		);
	$RANG[FUNKTION_OB] = array (
			'name' => 'Ortsbeauftragter',
			'stab' => 1,
			'id' => FUNKTION_OB
		);


	// Hier stehen erst mal alle Rechte die wir haben :

	// News anlegen und editieren!
	define('NEWS_EDIT', '1');
	// News löschen
	define('NEWS_DELETE', '2');

	// Berichte anlegen und editieren
	define('REPORT_EDIT', '4');
	// Berichte öffentlich freigeben
	define('REPORT_PUBLISH', '8');
	// Berichte löschen
	define('REPORT_DELETE', '16');

	// Termine anlegen und editieren
	define('DATE_EDIT', '32');
	// Termine löschen
	define('DATE_DELETE', '64');

	// Foren anlegen, verändern und löschen
	define('FORUM_ADMIN', '128');
	// Diskussionen im Forum administrieren
	define('DISCUSSION_ADMIN', '256');

	// Downloads hinzufügen, ändern und entfernen
	define('DOWNLOAD_ADMIN', '512');

	// Benutzer und Zugangsrechte verwalten
	define('USER_ADMIN', '1024');

	// Gästebuch administrieren = Beiträge löschen!
	define('GUESTBOOK_ADMIN', '2048');

	// Backups erstellen...
	define('BACKUP_ADMIN', '4096');

	// God... root... what's the difference?
	define('ROOT', '1073741823');

	// Und hier ist der Array in dem wir die Rechte speichern!
	$RIGHTS = array();
	$RIGHTS[0] = array(
			'action' => array(
				0 => 'date_create',
				1 => 'date_edit'
			),
			'right' => DATE_EDIT,
			'description' => 'Termine anlegen/editieren'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'date_delete'
			),
			'right' => DATE_DELETE,
			'description' => 'Termine löschen'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'report_create',
				1 => 'report_edit'
			),
			'right' => REPORT_EDIT,
			'description' => 'Berichte anlegen/editieren'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'report_publish'
			),
			'right' => REPORT_PUBLISH,
			'description' => 'Berichte freigeben'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'report_delete'
			),
			'right' => REPORT_DELETE,
			'description' => 'Berichte löschen'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'news_create',
				1 => 'news_edit'
			),
			'right' => NEWS_EDIT,
			'description' => 'News anlegen/editieren'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'news_delete'
			),
			'right' => NEWS_DELETE,
			'description' => 'News löschen'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'forum_create',
				1 => 'forum_edit',
				2 => 'forum_delete'
			),
			'right' => FORUM_ADMIN,
			'description' => 'Foren anlegen/editieren/löschen'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'forum_thread_delete',
				1 => 'forum_thread_edit',
				2 => 'forum_thread_message_edit',
				3 => 'forum_thread_message_delete'
			),
			'right' => DISCUSSION_ADMIN,
			'description' => 'Diskussionen editieren/löschen; Beiträge editieren/löschen'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'download_add',
				1 => 'download_edit',
				2 => 'download_remove'
			),
			'right' => DOWNLOAD_ADMIN,
			'description' => 'Downloads hinzufügen/editieren/entfernen'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'administration_user_create',
				1 => 'administration_user_edit',
				2 => 'administration_user_remove',
				3 => 'administration_user',
				4 => 'administration_rights',
				4 => 'administration_user_view'
			),
			'right' => USER_ADMIN,
			'description' => 'Benutzer anlegen/editieren/entfernen; Zugangsrechte zuweisen'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'administration_guestbook_admin'
			),
			'right' => GUESTBOOK_ADMIN,
			'description' => 'Gästebuch administrieren'
		);

	$RIGHTS[] = array(
			'action' => array(
				0 => 'god... root.. Whats the difference?'
			),
			'right' => ROOT,
			'description' => 'root'
		);







	// Hier speichern wir die Einheiten
	$EINHEITEN = array();
	$EINHEITEN[10] = array(
			'name' => '1. Technischer Zug',
			'id' => 10
		);
	$EINHEITEN[11] = array(
			'name' => 'Zugtrupp',
			'id' => 11
		);
	$EINHEITEN[12] = array(
			'name' => '1. Bergungsgruppe',
			'id' => 12
		);
	$EINHEITEN[13] = array(
			'name' => '2. Bergungsgruppe',
			'id' => 13
		);
	$EINHEITEN[14] = array(
			'name' => 'Fachgruppe W/P',
			'id' => 14
		);
	$EINHEITEN[20] = array(
			'name' => '2. Technischer Zug',
			'id' => 20
		);
	$EINHEITEN[21] = array(
			'name' => 'Zugtrupp',
			'id' => 21
		);
	$EINHEITEN[22] = array(
			'name' => '1. Bergungsgruppe',
			'id' => 22
		);
	$EINHEITEN[23] = array(
			'name' => '2. Bergungsgruppe',
			'id' => 23
		);

	$EINHEITEN[50] = array(
			'name' => 'Stab',
			'id' => 50
		);
	$EINHEITEN[60] = array(
			'name' => 'Grundausbildung',
			'id' => 60
		);
	$EINHEITEN[61] = array(
			'name' => 'GA 1',
			'id' => 61
		);
	$EINHEITEN[62] = array(
			'name' => 'GA 2',
			'id' => 62
		);
	$EINHEITEN[70] = array(
			'name' => 'Jugend',
			'id' => 70
		);
	$EINHEITEN[71] = array(
			'name' => 'JG 1',
			'id' => 71
		);
	$EINHEITEN[72] = array(
			'name' => 'JG 2',
			'id' => 72
		);
	$EINHEITEN[73] = array(
			'name' => 'JG 3',
			'id' => 73
		);

	$LINK_CATEGORIES = array();
	$LINK_CATEGORIES[1] = 'THW Ortsverbände';
	$LINK_CATEGORIES[] = 'THW GFB\'s';
	$LINK_CATEGORIES[] = 'THW allgemein';
	$LINK_CATEGORIES[255] = 'Testlinks';

?>
