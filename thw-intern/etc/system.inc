<?php

	// Datenbank
	define('DB_HOST', 'localhost');
	// define('DB_HOST', 'mysql1.kontent.de');
	define('DB_USER', 'org89271');
	define('DB_PASSWORD', '3dsmax');
	define('DB_DATABASE', 'org89271');

	// Wie heißen die einzelnen Tabellen?
	define('DB_PERMISSIONS', 'permissions_new');				// Zugangsberechtigungen
	define('DB_USERS', 'users');								// Die einzelnen Useraccounts
	define('DB_MOTM', 'motm');									// Objekt des Monats
	define('DB_NEWS', 'new_news');							// News
	define('DB_FOREN', 'foren');								// Die einzelnen Foren
	define('DB_FORUM', 'forum_postings_new');					// Die Forenbeiträge
	define('DB_DATETYPES', 'date_types');						// die verschiedenen Typen eines Termins
	define('DB_DATES', 'dates_new');							// Die einzelnen Termine
	define('DB_REPORTS', 'reports');							// Berichte
	define('DB_REPORT_TYPES', 'report_types');				// Berichttypen
	define('DB_PHOTOS', 'photos');								// Die Photodatenbank; die eigentlichen Photos
																// werden NICHT hier gespeichert, sondern in
																// var/photos/$ID/
	define('DB_RANG', 'rang');									// Die einzelnen Ränge
	define('DB_UNITS', 'einheiten');								// Die Einheiten
	define('DB_USERLISTS', 'helferliste');						// Die Userlisten die bei den Terminen benutzt werden!

	// Create Mode
	define('FILES_OWNER', 'jakob');							// Gibt an mit welchem owner die Verzeichnisrechte
																// gesetzt werden sollen

	// Standard-Stylesheet
	define('DEFAULT_STYLESHEET', 'etc/style.css');

	// Name des OV's
	define('OV_NAME', 'München-West auf TNT');
//	define('OV_NAME', 'München-West');

	// Anzahl von verschiedenen Sachen pro Seite
	define('NEWS_PER_PAGE', '3');							// Gibt an wieviel News auf der Starseite stehen sollen
	define('REPORTS_PER_COLUMN', '5');						// Wieviele Berichte pro Kolumne?
	define('PHOTOS_PER_REPORT', '4');						// Wieviele Photos pro Bericht auf einmal sichtbar?
	
	// Speicherorte
	define('PHOTO_LOCATION', 'var/photos/');					// Wo werden die Photos gespeichert
	define('BACKUP_LOCATION', 'var/backup/');					// Wo werden die Backups gespeichert

	// E-Mail
	define('CONTACT_ADMIN', 'Jakob@Tarnkappenbaum.org');		// Hier steht die E-Mail-Adresse an die alle Anfragen nach neuen
																	// Accounts oder vergessenen Passwörtern gehen
																	
	// Timeout
	define('TIMEOUT', '600');									// Timeout bis ein User automatisch abgemeldet wird [sec]
?>
