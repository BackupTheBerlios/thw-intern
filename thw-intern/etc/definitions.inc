<?php

	$report_types[1] = array(
			'name' => 'Einsatz'
		);

	$report_types[] = array(
			'name' => 'Ausbildung'
		);

	$report_types[] = array(
			'name' => 'Veranstaltung'
		);

	$report_types[] = array(
			'name' => 'THV'
		);

	$report_types[] = array(
			'name' => 'Jugend'
		);

	$report_types[] = array(
			'name' => 'Freizeit'
		);

	$report_types[] = array(
			'name' => 'Sonstiges'
		);

// 	$report_types[] = array(
// 			'name' => 'Spruch des Monats'
// 		);

// 	Beispiel für eine Termin-typ-definition :
// 	$date_types[] = array(
// 			'name' => 'XYZ',		// Was als Name angezeigt wird
// 						// Hier kommen Angaben zum Erstellen
// 			'has_unit' => 0,			// Einheitenliste automatisch setzen?
// 			'has_userlist' => 0,		// Userliste			""			"" 	?
// 			'helfer_preset' => 0,	// Voreinzustellende Userzahl
// 			'userlist_has_comment' => 0		// Automatisch Extrafelder?
// 		);


	$date_types[] = array(
			'name' => 'Dienst',
			'has_unit' => 0,
			'has_userlist' => 0,
			'helfer_preset' => 0,
			'userlist_has_comment' => 0
		);

	$date_types[] = array(
			'name' => 'Ausbildung',
			'has_unit' => 1,
			'has_userlist' => 0,
			'helfer_preset' => 0,
			'userlist_has_comment' => 0
		);
	$date_types[] = array(
			'name' => 'THV-Dienst',
			'has_unit' => 0,
			'has_userlist' => 1,
			'helfer_preset' => 6,
			'userlist_has_comment' => 1
		);

	$date_types[] = array(
			'name' => 'Grundausbildung',
			'has_unit' => 0,
			'has_userlist' => 0,
			'helfer_preset' => 0,
			'userlist_has_comment' => 0
		);

	$date_types[] = array(
			'name' => 'Straßensperre',
			'has_unit' => 0,
			'has_userlist' => 1,
			'helfer_preset' => 15,
			'userlist_has_comment' => 0
		);

	$date_types[] = array(
			'name' => 'Stab',
			'has_unit' => 0,
			'has_userlist' => 0,
			'helfer_preset' => 0,
			'userlist_has_comment' => 0
		);

	$date_types[] = array(
			'name' => 'Küchendienst',
			'has_unit' => 0,
			'has_userlist' => 0,
			'helfer_preset' => 0,
			'userlist_has_comment' => 0
		);

	$date_types[] = array(
			'name' => 'Sonstiges',
			'has_unit' => 0,
			'has_userlist' => 0,
			'helfer_preset' => 0,
			'userlist_has_comment' => 0
		);

	$date_types[] = array(
			'name' => 'Lehrgang',
			'has_unit' => 0,
			'has_userlist' => 1,
			'helfer_preset' => 0,
			'userlist_has_comment' => 0
		);


?>
