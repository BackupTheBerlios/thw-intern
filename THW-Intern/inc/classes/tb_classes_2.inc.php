<?php

class tb_object
{
	var
		$current_object,
		$current_table,
		$tb_object_id
		;

	// Konstruktor...
	function tb_object()
	{
	}

	// Fügt einen Eintrag in die Tabelle tb_object ein!
	function insert_record($tb_object_data, $update_id = 0)
	{
		global $session, $db, $log;

		if (!$tb_object_data[flag_public])
		{
			$tb_object_data[flag_public] = 0;
		}

		if ($update_id)
		{
			$sql = '
					update
						' . TB_OBJECT . '
					set
						date_begin = ' . $tb_object_data[date_begin] . ',
						date_end = ' . $tb_object_data[date_end] . ',
						flag_public = ' . $tb_object_data[flag_public] . ',
						date_lastchange = NULL
					where
						id = ' . $update_id . '
				';
		}
		else
		{
			$sql = '
					insert into
						' . TB_OBJECT . '
					(
						ref_user_id,
						date_begin,
						date_end,
						flag_public,
						date_create
					)
					values
					(
						' . $session->user_info('id') . ',
						' . $tb_object_data[date_begin] . ',
						' . $tb_object_data[date_end] . ',
							' . $tb_object_data[flag_public] . ',
						now()
					)
				';
		}
		// echo $sql;

		$db->query($sql);

		if ($update_id)
		{
			return($update_id);
		}
		else
		{
			return($db->last_insert_id());
		}
	}

	// updated einen Eintrag!
	function update_record($id, $tb_object_data)
	{
		global $db, $log;

		if (!$tb_object_data[flag_public])
		{
			$tb_object_data[flag_public] = 0;
		}

		$sql = '
				update
					' . TB_OBJECT . '
				set
					date_begin = "' . $tb_object_data[date_begin] . '",
					date_end = "' . $tb_object_data[date_end] . '",
					flag_public = ' . $tb_object_data[flag_public] . '
				where
					id = ' . $id .'
			';
		$db->query($sql);
	}


	function delete_record($id)
	{
		global $db;

		$sql = '
				delete
				from
					' . TB_OBJECT . '
				where
				 	id = ' . $id . '
			';
		$db->query($sql);
	}
}

class tb_bericht
{
	var
		$current
		;

	function tb_bericht()
	{
	}

	// add_photos :
	// Wenn aufgerufen, durchsucht diese Funktion automatisch im $_FILES nach hochgeladenen Dateien
	// mit dem Namen $upload_name;
	// Die Bilder werden dann in das entsprechende Verzeichnis (PHOTO_PATH/$bericht_id/$photo_id)
	// kopiert! Anschließend wird noch ein Thumbnail angelegt und ebenfalls in das richtige Verzeichnis
	// (PHOTO_PATH/$bericht_id/$photo_id _thumb.jpg) kopiert!
	// Die Funktion gibt die Anzahl der erfolgreich eingetragenen Bilder zurück; 0 wenn keine Bilder hochgeladen
	// wurden
	function add_photos($bericht_id, $upload_name)
	{
		global $db, $log;
		$log->add_to_log('::add_photos', '::add_photos(id = ' . $bericht_id . ', upload_name = ' . $upload_name . ') called...');

		$log->add_to_log('::add_photos', 'Found ' . count($_FILES[$upload_name][name]) . ' uploaded files, checking for images...');
		// Zuerst mal prüfen ob überhaupt Dateien angekommen sind!
			$images_found = 0;
			$images_processed = 0;
			// Dazu gehen wir den übergebenen Array durch
			for ($i = 0; $i < count($_FILES[$upload_name][name]); $i++)
			{
				if ($_FILES[$upload_name][size][$i])
				{
					// Aha, hier haben wir ne hochgeladene Datei!

					// Dann prüfen wir ob das Ding ein Bild ist!
					$image_properties = @getimagesize($_FILES[$upload_name][tmp_name][$i]);
					if ($image_properties)
					{
						// Sehr schön, das scheint ein Bild zu sein!
						$images_found++;

						// Gut, wir haben also ein Bild!! Jetzt kopieren wirs erstmal in das
						// Dafür vorgesehen Verzeichnis!
							// Gibts das Verzeichnis überhaupt??
							$path = PHOTO_PATH . $bericht_id;
							if (!is_dir($path))
							{

								// Nö, gibts nicht! Also erstellen!
								// echo 'creating dir : ' . $path . '<br>';
								if (mkdir( $path , 0775) )
								{
									$log->add_to_log('tb_bericht::add_photos', 'Created dir (' . $path . ') to save uploaded photos in it!');
									$log->shutdown();
								}
								else
								{
									$log->add_to_log('tb_bericht::add_photos', 'Fatal error! Couldnt create dir (' . $path . ') to save uploaded photos in it!');
									$log->shutdown();
									die('Whoups!!! Konnte das Verzeichnis zum Speichern der Bilder nicht anlegen!');
								}
							}

							// Ok, das Verzeichnis exisitert jetzt sicher; Photo reinkopieren und umbenennen!
								// Dazu legen wir nen Eintrag in der Datenbank an und nehmen die zurückgegebene
								// ID als Dateiname!
								$sql = '
										insert into
										' . TB_PHOTOS . '
										(
											ref_bericht_id,
											kommentar,
											priority
										)
										values
										(
											' . $bericht_id . ',
											"",
											0
										)
									';
								$db->query($sql);
								$photo_id = $db->last_insert_id();

								if (copy($_FILES[$upload_name][tmp_name][$i], $path . '/' . $photo_id . '.jpg'))
								{
									// Ok, Das Bild ist da wo es hinsoll!
									// Dann können wir jetzt das Thumbnail erstellen!
										// Hierzu erstmal feststellen ob es Hoch- oder Querformat ist!
										if ($image_properties[0] < $image_properties[1])
										{
											// Ok, das scheint Hochformat zu sein...
											$thumb_width = 150;
											$thumb_height = 200;
										}
										else
										{
											// Und dieses Querformat...
											$thumb_width = 200;
											$thumb_height = 150;
										}

										$thumb = ImageCreate($thumb_width, $thumb_height);

										if ($thumb)
										{
											$sourcefile = ImageCreateFromJPEG(PHOTO_PATH . $bericht_id . '/' . $photo_id . '.jpg');

											// Ok, jetzt können wir das originalbild runterskalieren!
											if (ImageCopyResized($thumb, $sourcefile, 0, 0, 0, 0, $thumb_width, $thumb_height, $image_properties[0],$image_properties[1]))
											{
												// Ok, Thumbnail steht! jetzt müssen wirs nur noch abspeichern!
												if (ImagePNG($thumb, PHOTO_PATH . $bericht_id . '/' . $photo_id . '_thumb.jpg'))
												{
													// Geschafft!
													$images_processed++;
												}
												else
												{
													// Whoups, sowas sollte eigentlich nicht passieren... )o:
													$log->add_to_log('tb_bericht::add_photos', 'Fatal error! Couldnt save thumbnail for image "' . PHOTO_PATH . $bericht_id . '/' . $photo_id . '.jpg"! Cleaning up and aborting...!');
													$log->shutdown();
													// Das kopierte Bild noch löschen:
													unlink(PHOTO_PATH . $bericht_id . '/' . $photo_id . '.jpg');
													// Datenbankeintrag entfernen!
													$sql = '
															delete from
																' . TB_PHOTOS . '
															where
																id = ' . $photo_id . '
														';
													$db->query($sql);
													die('Whoups!!! Konnte Thumbnail nicht abspeichern... *urgs*');
												}
											}
											else
											{
												// Whoups, sowas sollte eigentlich nicht passieren... )o:
												$log->add_to_log('tb_bericht::add_photos', 'Fatal error! Couldnt create thumbnail (imagecopyresized) for image "' . PHOTO_PATH . $bericht_id . '/' . $photo_id . '.jpg"! Cleaning up and aborting...!');
												$log->shutdown();
												// Das kopierte Bild noch löschen:
												unlink(PHOTO_PATH . $bericht_id . '/' . $photo_id . '.jpg');
												$sql = '
														delete from
															' . TB_PHOTOS . '
														where
															id = ' . $photo_id . '
													';
												$db->query($sql);
												die('Whoups!!! Konnte kein Thumbnail erstellen (imagecopyresized)... *urgs*');
											}
										}
										else
										{
											// Whoups, sowas sollte eigentlich nicht passieren... )o:
											$log->add_to_log('tb_bericht::add_photos', 'Fatal error! Couldnt create thumbnail (image_create) for image "' . PHOTO_PATH . $bericht_id . '/' . $photo_id . '.jpg"! Cleaning up and aborting...!');
											$log->shutdown();
											// Das kopierte Bild noch löschen:
											unlink(PHOTO_PATH . $bericht_id . '/' . $photo_id . '.jpg');
											$sql = '
													delete from
														' . TB_PHOTOS . '
													where
														id = ' . $photo_id . '
												';
											$db->query($sql);
											die('Whoups!!! Konnte kein Thumbnail erstellen (image_create)... *urgs*');
										}
								}
								else
								{
									// Whoups, sowas sollte eigentlich nicht passieren... )o:
									$log->add_to_log('tb_bericht::add_photos', 'Fatal error! Couldnt copy file to dir (' . PHOTO_PATH . $bericht_id . '/)! Aborting...!');
									$log->shutdown();
									$sql = '
											delete from
												' . TB_PHOTOS . '
											where
												id = ' . $photo_id . '
										';
									$db->query($sql);
									die('Whoups!!! Konnte das Bild nicht speichern... *urgs*');
								}
					}
					else
					{
						// Hmm... wasndas?
					}
				}
			}

			if (!$images_found)
			{
				// Hmm.. Nix da!
				return(0);
			}

			return($images_processed);
	}

	function remove_photo($bericht_id, $photo_id)
	{
	}

	function load_tb_bericht($id, $public = 0)
	{
		global $db, $log;
		$sql = '
				select
					' . TB_BERICHT . '.*,
					' . TB_BERICHT . '.id as tb_bericht_id,
					' . TB_OBJECT . '.*,
					date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y %H:%i") as date_create,
					date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y") as date_begin,
					date_format(' . TB_OBJECT . '.date_end, "%d.%m.%Y") as date_end,
					' . TB_USER . '.name,
					' . TB_USER . '.vorname
				from
					' . TB_BERICHT . ',
					' . TB_OBJECT . ',
					' . TB_USER . '
				where
					' . TB_OBJECT . '.id = ' . $id . '
					and
					' . TB_OBJECT . '.id = ' . TB_BERICHT . '.ref_object_id
					and
					' . TB_USER . '.id = ' . TB_OBJECT . '.ref_user_id
			';

		// echo $sql . '<br>';

		$raw = $db->query($sql);

		if ($db->num_rows($raw))
		{
			$bericht = $db->fetch_array($raw);
			$this->current = $bericht;

			if ($public and ($this->current[flag_freigegeben] == 0))
			{
				$this->current = 0;
				return(0);
			}

			return(1);
		}
		else
		{
			die('tb_bericht::load_tb_bericht(): Fataler Fehler! Konnte Bericht nicht laden!');
		}
	}

	function publish_tb_bericht($id, $publish)
	{
		global $db, $log;

		$sql = '
				update
					' . TB_BERICHT . '
				set
					flag_freigegeben = ' . $publish . '
				where
					ref_object_id = ' . $id . '
			';
		$db->query($sql);
		$log->add_to_log('tb_bericht::publish_tb_bericht', 'report #' . $id . 's flag_freigegeben has been set to ' . $publish . '!');
	}

	function return_field($field)
	{
		global $log;
		// $log->add_to_log('tb_bericht::return_field', 'called to return field "' . $field . '" with content : "' . $this->current[$field] . '"');
		return($this->current[$field]);
	}

	function add_tb_bericht($data)
	{
		global $db, $page;
		$object = new tb_object();

		$object_record = array(
				'date_begin' => strftime('%Y%m%d', $data[date_begin]),
				'date_end' => strftime('%Y%m%d', $data[date_end]),
				'flag_public' => $data[flag_public]
			);
		$object_id = $object->insert_record($object_record);

		$sql = '
				insert into
					' . TB_BERICHT . '
					(
						ref_object_id,
						text,
						titel,
						berichtart,
						flag_freigegeben
					)
					values
					(
						' . $object_id . ',
						"' . htmlentities(stripslashes(trim($data[text]))) . '",
						"' . htmlentities(stripslashes(trim($data[titel]))) . '",
						' . $data[berichtart] . ',
						0
					)
			';

		// echo $sql . '<br>';
		$db->query($sql);

		if ($db->affected_rows())
		{
			return($object_id);
		}
		else
		{
			echo '<p class=red>Whoups! Beim anlegen des tb_bericht-Eintrages ist etwas schiefgelaufen! *urgs* <br>Räume auf...</p>';
			$object->delete_record($object_id);
		}
	}

	function update_tb_bericht($bericht_id, $object_id, $data)
	{
		global $db, $log;

		$tb_object_data = array(
				'date_begin' => $data[date_begin],
				'date_end' => $data[date_end],
				'flag_public' => $data[flag_public]
			);

		// Zuerst einmal den tb_object-Eintrag updaten
		$object = new tb_object();
		$object->update_record($object_id, $tb_object_data);

		// Und jetzt den tb_bericht-Eintrag aktualisieren!
		$sql = '
				update
					' . TB_BERICHT . '
				set
					text = "' . htmlentities(stripslashes($data[text])) .'",
					titel = "' . htmlentities(stripslashes($data[titel])) .'",
					berichtart = ' . $data[berichtart] . '
				where
					id = '. $bericht_id . '
			';

		$db->query($sql);
	}

	function delete_tb_bericht($bericht_id, $object_id)
	{
		global $db;
		$object = new tb_object();
		$object->delete_record($object_id);

		$sql = '
				delete
				from
					' . TB_BERICHT . '
				where
					ref_object_id = ' . $object_id . '
			';

		// echo $sql;

		$db->query($sql);
	}
}


class tb_helferliste
{
	var
		$current_list,
		$user_list
		;

	function tb_helferliste()
	{
	}

	// Fügt der Liste einen Helfer hinzu
	function add_user($user_id, $listen_id, $comment = '')
	{
		global $db;

		// Zuerst mal holen wir die Listendaten
		$sql = '
				select
					id
				from
					' . TB_HELFERLISTE . '
				where
					ref_object_id = ' . $listen_id . '
			';

		// echo $sql . '<br>';

		$current = $db->fetch_array($db->query($sql));

		// Ist die Liste überhaupt noch offen?
		// if ($current[flag_open]) // Momentan noch daktiviert weil is nich!
		if (1)
		{
			$sql = '
					insert
					into
						' . TB_HELFERLISTENEINTRAG . '
					(
						ref_user_id,
						ref_helferliste_id,
						flag_dgl,
						flag_drin,
						kommentar
					)
					values
					(
						' . $user_id . ',
						' . $current[id] . ',
						0,
						0,
						"' . $comment . '"
					)
				';
			// echo $sql . '<br>';

			$db->query($sql);

		}
		else
		{
			// Liste ist bereits geschlossen, es kann kein User mehr eingefügt werden!
			return(0);
		}
	}

	// Entfernt einen User
	function remove_user($user_id, $list_id)
	{
		global $db;
		// ARGH .. das lässt sich mittels join nicht machen.. *GRRR* §$%§$%

		$sql = '
				select
					' . TB_HELFERLISTENEINTRAG . '.id
				from
					' . TB_HELFERLISTENEINTRAG . ',
					' . TB_HELFERLISTE . '
				where
					' . TB_HELFERLISTENEINTRAG . '.ref_user_id = ' . $user_id . '
					and
					' . TB_HELFERLISTE . '.ref_object_id = ' . $list_id . '
					and
					' . TB_HELFERLISTENEINTRAG . '.ref_helferliste_id = ' . TB_HELFERLISTE . '.id
			';
		// echo $sql . '<br>';
		$tmp = $db->fetch_array($db->query($sql));

		$sql = '
				delete from
					' . TB_HELFERLISTENEINTRAG . '
				where
					id = ' . $tmp[id] . '
			';
		$db->query($sql);
		// echo $sql . '<br>';
	}

	// Ändert die Attribute eines Users
	function change_user($user_data)
	{
		global $db;

		// ARGH!! CRAP!! Wieder kein Join möglich!!!! Daher 2 Queries!!
		$sql = '
				select
					id
				from
					' . TB_HELFERLISTE . '
				where
					ref_object_id = ' . $user_data[list_id] . '
			';

		$tmp = $db->fetch_array($db->query($sql));

		$sql = '
				update
					' . TB_HELFERLISTENEINTRAG . '
				set
					' . $user_data[field] . ' = ' . $user_data[value] . '
				where
					ref_user_id = ' . $user_data[id] . '
					and
					ref_helferliste_id = ' . $tmp[id] . '
			';
		// echo $sql . '<br>';

		$db->query($sql);
	}

	// Erstellt eine Liste
	function create_list($append_id, $list_attribs)
	{
		global $db, $log;

		// Wollen wir eine hidden oder eine normale Liste??
		if ($list_attribs[flag_hidden])
		{
			// Erstmal den Eintrag in der Helferliste anlegen...

			$sql = '
					insert
					into
						' . TB_HELFERLISTE . '
					(
						ref_object_id,
						deadline,
						flag_open,
						flag_public,
						disclaimer,
						slots,
						flag_hidden,
						flag_comment,
						flag_dgl
					)
					values
					(
						' . $append_id . ',
						' . strftime('%Y%m%d') . ',
						0,
						0,
						"",
						0,
						1,
						"",
						0
					)
				';
			// echo $sql . '<br>';

			$db->query($sql);

			$last_id = $db->last_insert_id();

			// Erstmal die Tabelle locken!
			$sql = '
					lock
						tables
						' . TB_HELFERLISTENEINTRAG . '
				';
			$db->query($sql);
			// echo $sql . '<br>';

			// So, jetzt müssen wie die User eintragen die in den entsprechenden Einheiten sind...
			// Weird, quick and dirty, evtl. neu schreiben...
			for ($i = 0; $i < count($list_attribs[einheiten]); $i++)
			{
				// Also, alle User mit der entsprechenden Einheit in die Helferliste schmeißen!
				$sql = '
						insert into
							' . TB_HELFERLISTENEINTRAG . '
							(
								' . TB_HELFERLISTENEINTRAG . '.ref_user_id
							)
						select
							' . TB_USER . '.id
						from
							' . TB_USER . '
						where
							' . TB_USER . '.ref_einheit_id = ' . $list_attribs[einheiten][$i] . '

					';
				// echo $sql . '<br>';

				$db->query($sql);
			}

			// So, die User sind alle eingetragen, jetzt müssen wir nur noch die entsprechenden ref_id's umsetzen!

			$sql = '
					update
						' . TB_HELFERLISTENEINTRAG . '
					set
						' . TB_HELFERLISTENEINTRAG . '.ref_helferliste_id = ' . $last_id . '
					where
						' . TB_HELFERLISTENEINTRAG . '.ref_helferliste_id = 0
				';
			$db->query($sql);
			// echo $sql . '<br>';

			// Tabellen wieder freigeben!
			$sql = '
					unlock
						tables
				';
			// echo $sql . '<br>';
			$db->query($sql);
		}
		else
		{
			$sql = '
					insert
					into
						' . TB_HELFERLISTE . '
					(
						ref_object_id,
						deadline,
						flag_open,
						flag_public,
						disclaimer,
						slots,
						flag_hidden,
						flag_comment,
						flag_funktion
					)
					values
					(
						' . $append_id . ',
						' . strftime('%Y%m%d') . ',
						' . $list_attribs[flag_open] . ',
						' . $list_attribs[flag_public] . ',
						"' . $list_attribs[disclaimer] . '",
						' . $list_attribs[slots] . ',
						' . $list_attribs[flag_hidden] . ',
						' . $list_attribs[flag_comment] . ',
						' . $list_attribs[flag_funktion] . '
					)
				';
		}

		// echo $sql;


		// $db->debug(1);
		$db->query($sql);
		// $db->debug(0);
	}

	// Entfernt eine Liste
	function drop_list($list_id)
	{
		global $db;
		// Zuerst einmal alle Einträge die in dieser Userliste hängen entfernen!
		$sql = '
				delete from
					' . TB_HELFERLISTENEINTRAG . '
				where
					' . TB_HELFERLISTENEINTRAG . '.ref_helferliste_id = ' . $list_id . '
			';

		// echo $sql . '<br>';

		$db->query($sql);

		$sql = '
				delete from
					' . TB_HELFERLISTE . '
				where
					' . TB_HELFERLISTE . '.ref_object_id = ' . $list_id . '
			';

		// echo $sql . '<br>';

		$db->query($sql);
	}

	// Gibt ein Feld aus den Eigenschaften einer Liste zurück
	function return_list_attrib($field)
	{
		return($this->current_list[$field]);
	}

	function update_list($data)
	{
		$sql = '
				update
					' . TB_HELFERLISTE . '
				set
					deadline = ' . strftime('%Y%m%d') . ',
					flag_hidden = ' . $data[flag_hidden] . ',
					flag_open = ' . $data[flag_open] . ',
					flag_comment = ' . $data[flag_comment] . ',
					slots = ' . $data[slots] . ',
					disclaimer = "' . $data[disclaimer] . '"
				where
					id = ' . $data[list_id] . '
			';
		echo $sql;
	}


	function return_user_list()
	{
		return($this->user_list);
	}

	// Lädt eine Userliste
	// gibt 1 zurück wenn eine Userliste gefunden wurde und 0 wenn keine vorhanden!
	function load_userlist($ref_id)
	{
		global $log, $db;
		$sql = '
				select
					' . TB_HELFERLISTE  . '.*,
					date_format(' . TB_HELFERLISTE  . '.deadline, "%d.%m.%Y %H:%i") as deadline_readable
				from
				 	' . TB_HELFERLISTE  . '
				where
					' . TB_HELFERLISTE  . '.ref_object_id = ' . $ref_id . '
			';

// 		echo $sql . '<br>';
		$raw = $db->query($sql);
		if ($db->num_rows($raw))
		{
			$current = $db->fetch_array($raw);

			$this->current_list = array(
					'id' => $current[id],
					'ref_object_id' => $current[ref_object_id],
					'deadline' => $current[deadline],
					'deadline_readable' => $current[deadline_readable],
					'flag_open' => $current[flag_open],
					'flag_public' => $current[flag_public],
					'disclaimer' => $current[disclaimer],
					'slots' => $current[slots],
					'flag_hidden' => $current[flag_hidden],
					'flag_comment' => $current[flag_comment],
					'flag_dgl' => $current[flag_dgl],
					'flag_funktion' => $current[flag_funktion]
				);

			// Jetzt suchen wir noch alle User raus, die eingetragen sind!
			$sql = '
					select
						' . TB_HELFERLISTENEINTRAG . '.ref_user_id,
						' . TB_HELFERLISTENEINTRAG . '.flag_dgl,
						' . TB_HELFERLISTENEINTRAG . '.flag_drin,
						' . TB_HELFERLISTENEINTRAG . '.funktion,
						' . TB_HELFERLISTENEINTRAG . '.kommentar,
						' . TB_USER . '.name,
						' . TB_USER . '.vorname
					from
						' . TB_HELFERLISTENEINTRAG . ',
						' . TB_USER . '
					where
						' . TB_HELFERLISTENEINTRAG . '.ref_helferliste_id = ' . $this->current_list[id] . '
						and
						' . TB_USER . '.id = ' . TB_HELFERLISTENEINTRAG . '.ref_user_id
					order by
						' . TB_HELFERLISTENEINTRAG . '.flag_drin desc,
						' . TB_USER . '.name
				';

			//echo $sql . '<br>';
			$raw = $db->query($sql);

			if ($db->num_rows($raw))
			{
// 				$sql = '
// 						select
// 							count(*) as count
// 						from
// 							' . TB_HELFERLISTENEINTRAG . '
// 						where
// 							' . TB_HELFERLISTENEINTRAG . '.ref_helferliste_id = ' . $this->current_list[id] . '
// 							and
// 							' . TB_HELFERLISTENEINTRAG . '.flag_drin = 1
// 					';
//
// 				// echo $sql . '<br>';
//
// 				$eingetragene_helfer = $db->fetch_array($db->query($sql));
// 				$eingetragene_helfer = $eingetragene_helfer[count];
//
// 				$this->current_list[anzahl_eingetragene_helfer] = $eingetragene_helfer;
//
				$this->user_list = array();
				$i = 0;

				while ($current = $db->fetch_array($raw))
				{
					$this->user_list[$i][name] = $current[name];
					$this->user_list[$i][vorname] = $current[vorname];
					$this->user_list[$i][ref_user_id] = $current[ref_user_id];
					$this->user_list[$i][kommentar] = $current[kommentar];
					$this->user_list[$i][flag_dgl] = $current[flag_dgl];
					$this->user_list[$i][flag_drin] = $current[flag_drin];
					$this->user_list[$i][funktion] = $current[funktion];

					$i++;
				}
			}
// 			else
// 			{
// 				$this->current_list[anzahl_eingetragene_helfer] = 0;
// 			}

			return(1);
		}
		else
		{
			return(0);
		}
	}

	function userlist_interface($ref_id, $return_id, $current_user_id, $is_user_admin)
	{
		global $db, $session;
		// Jetzt prüfen wir erstmal ob überhaupt eine Userliste da ist;
		// Wenn ja, dann laden wir sie auch gleich!
		if ($this->load_userlist($ref_id))
		{

			// Jetzt  prüfen wir ob das Ding eine versteckte oder sichtbare Liste ist
			if ($this->current_list[flag_hidden])
			{
				// Versteckte Liste!
				// Jetzt versuchen wir mal die Einheiten rauszuparsen die eingetragen wurden!!!

				$sql = '
					select
						' . TB_USER . '.ref_einheit_id
					from
						' . TB_USER . ',
						' . TB_HELFERLISTENEINTRAG . '
					where
						' . TB_HELFERLISTENEINTRAG . '.ref_helferliste_id = ' . $this->current_list[id] . '
						and
						' . TB_USER . '.id = ' . TB_HELFERLISTENEINTRAG . '.ref_user_id
					group by
						' . TB_USER . '.ref_einheit_id
					';

				// echo $sql;
				$raw = $db->query($sql);

				$temp = '
						<table width=100% align=center border=0 cellspacing=0 cellspacing=>
							<tr>
								<td class=form_left width=30%>
									Eingetragene Einheiten:
								</td>
								<td>
					';

				for ($i = 0; $i < $db->num_rows($raw); $i++)
				{
					$current = $db->fetch_array($raw);
					$temp .= $GLOBALS[EINHEITEN][(floor (( $current[ref_einheit_id] / 10 )) * 10)][name];
					$temp .= '  ';
					$temp .= $GLOBALS[EINHEITEN][$current[ref_einheit_id]][name];
					$temp .= '<br>';
				}

				$temp .= '
								</td>
							</tr>
						</table>
					';

				$output = $temp;
			}
			else
			{
				$aktive_eintraege = 0;
				$list_full = 0;
				$current_user_eingetragen = 0;
				// Ok, eine ganz normale Liste...

				// Output aufbauen...
				$output = '
							<table width="100%" align="center" border="0" class="form" cellspacing="2" cellpadding="0">
								<tr>
									<td colspan=2 class=form_default>
										<b>Liste</b>
									</td>
								</tr>
					';

				// Wenn bereits User eingetragen sind, dann gehen wir die Liste mal durch und
				// prüfen auch gleich ob der akutelle User drinnen ist...

				if (count($this->user_list))
				{
					$output .= '
								<tr>
									<td class="form_default">
										eingetragen:
									</td>
									<td>
										<table width="100%" align="center" border="0">
						';

					// Jetzt schauen wir erstmal welche Felder wir überhaupt brauchen!
					// Namen brauchen wir auf jeden Fall
					$output .= '
											<th>Name</th>
						';
					// brauchen wir das Kommentarfeld?
					if ($this->current_list[flag_comment])
					{
						$output .= '
											<th>Kommentar</th>
							';
					}
					// brauchen wir ein Funktionsfeld (dropdown)
					if ($this->current_list[flag_funktion])
					{
						$output .= '
											<th>Funktion</th>
							';
					}

					// Und das status-Feld sollte auch dabei sein!
					$output .= '
											<th>Status</th>
						';

					// So, jetzt gehen wir alle Einträge durch!

					for ($i = 0; $i < count($this->user_list); $i++)
					{
						if ($this->user_list[$i][flag_drin])
						{
							$aktive_eintraege++;

							if ($this->current_list[slots])
							{
								if (($this->current_list[slots] - $aktive_eintraege ) == 0)
								{
									$list_full = 1;
								}

							}

						}

						// prüfen ob der User (session) evtl. schon eingetragen ist
						if ($this->user_list[$i][ref_user_id] == $session->user_info('id'))
						{
							$current_user_eingetragen = 1;
						}

						// Prüfen ob der aktive User drin ist, oder nicht!
						if ($this->user_list[$i][flag_drin])
						{
							$class = 'form_ready';
						}
						else
						{
							$class = 'form_inactive';
						}

						$output .= '
										<tr class=' . $class . '>
											<td >
								';

						// Ist der User Admin?? Wenn ja, dann darf er User rauswerfen!
						if ($is_user_admin)
						{
							$output .= '
												<a href="' . $GLOBALS[PHP_SELF] . '?action=' . $GLOBALS[action] . '&id=' . $return_id . '&remove_user=' . $this->user_list[$i][ref_user_id] . '&list_id=' . $this->current_list[id] . '" title="Diesen User komplett entfernen!">
													' . $this->user_list[$i][name] . ', ' . $this->user_list[$i][vorname] . '
												</a>
								';
						}
						else
						{
							// Andernfalls darf er nur anguggen!
							$output .= '
												' . $this->user_list[$i][name] . ', ' . $this->user_list[$i][vorname] . '
								';
						}

						$output .= '
											</td>
								';


						// So, prüfen ob wir die folgenden Felder überhaupt brauchen....

						// brauchen wir das Kommentarfeld?
						if ($this->current_list[flag_comment])
						{
							$output .= '
											<td >
												' . $this->user_list[$i][kommentar] . '
											</td>
								';
						}

						// brauchen wir ein Funktionsfeld (dropdown)
						if ($this->current_list[flag_funktion])
						{
							if ($this->user_list[$i][flag_drin])
							{
								if ($is_user_admin)
								{
									$output .= '
													<td align="center" valign="center">
														<form action="' . $PHP_SELF . '" method="get">
															<input type=hidden name=action value=' . $GLOBALS[action] . '>
															<input type=hidden name=id value=' . $return_id . '>
															<input type=hidden name=change_funktion value=' . $this->user_list[$i][ref_user_id] . '>

															<select size=1 name=funktion>
										';

									for ($j = 0; $j < count($GLOBALS[DATE_FUNCTIONS]); $j++)
									{
										$temp = '';
										if ($j == $this->user_list[$i][funktion])
										{
											$temp = ' selected ';
										}

										$output .= '
																<option value=' . $j . ' ' . $temp . '>
																	' . $GLOBALS[DATE_FUNCTIONS][$j][name] . '
																</option>
											';
									}

									$output .= '
															</select>
															&nbsp;
															<input type=submit name=submit value="Go">
														</form>
													</td>
										';

								}
								else
								{
									$output .= '
													<td >
														' . $GLOBALS[DATE_FUNCTIONS][$this->user_list[$i][funktion]][name] . '
													</td>
										';
								}
							}
							else
							{
								$output .= '
													<td></td>
									';
							}
						}

						$output .= '
												<td >
							';
						// Jetzt kommt noch das Status-Feld!!!
						if ($is_user_admin)
						{
							if ($this->user_list[$i][flag_drin])
							{
								$output .= '
													<a href="' . $GLOBALS[PHP_SELF] . '?action=' . $GLOBALS[action] . '&id=' . $return_id . '&change_drin=' . $this->user_list[$i][ref_user_id] . '&list_id=' . $this->current_list[id] . '&flag_drin=0" title="Diesen User in die Warteliste stellen!">dabei</a>
										';
							}
							else
							{
								if ($list_full)
								{
									$output .= '
													wartet
											';
								}
								else
								{
									$output .= '
													<a href="' . $GLOBALS[PHP_SELF] . '?action=' . $GLOBALS[action] . '&id=' . $return_id . '&change_drin=' . $this->user_list[$i][ref_user_id] . '&list_id=' . $this->current_list[id] . '&flag_drin=1" title="Diesen User aufnehmen">wartet</a>
											';
								}
							}
						}
						else
						{
							if ($this->user_list[$i][flag_drin])
							{
								$output .= 'dabei';
							}
							else
							{
								$output .= 'wartet';
							}
						}

						$output .= '
												</td>
											</tr>

							';

					}
								$output .= '
										</table>
									</td>
								</tr>
							';


					}
// ENDE FOR-SCHLEIFE


					$output .= '
								<tr>
									<td class="form_default" width="30%">
										freie/vorgesehene Plätze:
									</td>
									<td>
							';

					// Wenn slots = 0 dann keine Einschränkung!
					if ($this->current_list[slots])
					{
						$output .= '
										<b>' . ( $this->current_list[slots] - $aktive_eintraege ) . '/' . $this->current_list[slots] . '</b>
							';

						if (($this->current_list[slots] - $this->current_list[anzahl_eingetragene_helfer]) == 0)
						{
							$list_full = 1;
						}

					}
					else
					{
						$output .= '
										<b>Keine Einschränkung!</b>
							';
					}
						$output .= '
									</td>
								</tr>
								<tr>
									<td class="form_default">
										User eintragen:
									</td>
									<td>
						';

					// Ist der User berechtigt andere USer hinzuzufügen?
					if ($is_user_admin)
					{
						// Ein Admin darf andere User hinzufügen
						$output .= '
										<form action="' . $GLOBALS[PHP_SELF] . '" method=get>
											<select size=1 name=auswahl>
							';

						// Alle User holen...
						$sql = '
								select
									' . TB_USER . '.id,
									' . TB_USER . '.name,
									' . TB_USER . '.vorname
								from
									' . TB_USER . '
								order by
									' . TB_USER . '.name
							';

						$raw = $db->query($sql);
						while ($temp = $db->fetch_array($raw))
						{
							$output .= '
											<option value=' . $temp[id] . '>
												' . $temp[name] . ', ' . $temp[vorname] . '
											</option>
								';
						}

						$output .= '
										</select>
							';

						if ($this->current_list[flag_comment])
						{
							$output .= '
										<input type=text size=20 name=comment>
								';
						}

						$output .= '
										<input type=hidden name=action value=' . $GLOBALS[action] . '>
										<input type=hidden name=id value=' . $return_id . '>
										<input type=hidden name=add_user value=1>
										<input type=submit name=submit value="User eintragen">
									</form>
							';

					}
					else
					{
						// Normale User dürfen nur sich selbst hinzufügen!
						// Und das aber nur wenn sie noch nicht drinnen sind!
						if ($current_user_eingetragen)
						{
							$output .= '
									<b>Du bist bereits eingetragen!</b>
								';
						}
						else
						{
							$output .= '
									<form action=' . $GLOBALS[PHP_SELF] . ' method=get>
										<input type=hidden name=action value=' . $GLOBALS[action] . '>
										<input type=hidden name=auswahl value=' . $session->user_info('id') . '>
										<input type=hidden name=id value=' . $return_id . '>
										<input type=hidden name=add_user value=1>
										<input type=submit value="Mich eintragen">
									</form>
								';
						}

 					}

					$output .= '
								</td>
							</tr>
						';

				$output .= '
												<tr>
													<td colspan=4>
														<b>ToDo:</b><br>
              - in ::add_user() prüfen ob Liste voll ist...
													</td>
												</tr>
											</table>
					';

			}

			return($output);

		}
		else
		{
			return(0);
		}
	}
}

class tb_termin
{
	var
			$current_date
		;
	function tb_termin()
	{
	}

	function add_tb_termin($date_attribs, $update_ref_object_id = 0)
	{
		global $db, $log;
		$object = new tb_object();

		$object_record = array(
				'date_begin' => strftime('%Y%m%d%H%M%S', $date_attribs[date_begin]),
				'date_end' => strftime('%Y%m%d%H%M%S', $date_attribs[date_end]),
// 				'flag_public' => $data[flag_public]
				'flag_public' => 0			// Termine sind immer öffentlich??
			);

		$object_id = $object->insert_record($object_record, $update_ref_object_id);


		if ($update_ref_object_id)
		{
			$sql = '
					update
						' . TB_TERMIN . '
					set

							terminart = ' . $date_attribs[terminart] . ',
							kommentar = "' . $date_attribs[kommentar] . '",
							flag_edit = ' . $date_attribs[flag_edit] . ',
							flag_kueche = ' . $date_attribs[flag_kueche] . '
					where
							ref_object_id = ' . $update_ref_object_id . '
				';
		}
		else
		{
			$sql = '
					insert into
						' . TB_TERMIN . '
						(
							ref_object_id,
							terminart,
							kommentar,
							flag_edit,
							flag_kueche
						)
					values
						(
							' . $object_id . ',
							' . $date_attribs[terminart] . ',
							"' . $date_attribs[kommentar] . '",
							' . $date_attribs[flag_edit] . ',
							' . $date_attribs[flag_kueche] . '
						)
				';
		}

		// echo $sql;

		$db->query($sql);

		return($object_id);
	}

	function remove_tb_termin($object_id)
	{
		global $db;
		$object = new tb_object();
		$object->delete_record($object_id);

		$sql = '
				delete
				from
					' . TB_TERMIN . '
				where
					ref_object_id = ' . $object_id . '
			';

		// echo $sql;
		$db->query($sql);

	}

	function update_tb_termin()
	{
	}

	function load_tb_termin($ref_object_id)
	{
		global $db, $log;

		$sql = '
				select
					' . TB_TERMIN . '.*,
					date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y %H:%i") as date_begin_readable,
					date_format(' . TB_OBJECT . '.date_end, "%d.%m.%Y %H:%i") as date_end_readable,
					date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y %H:%i") as date_create_readable,
					date_format(' . TB_OBJECT . '.date_lastchange, "%d.%m.%Y %H:%i") as date_lastchange_readable,
					' . TB_OBJECT . '.date_lastchange,
					' . TB_OBJECT . '.date_create,
					' . TB_OBJECT . '.ref_user_id,
					' . TB_USER . '.name,
					' . TB_USER . '.vorname
				from
					' . TB_TERMIN . ',
					' . TB_OBJECT . ',
					' . TB_USER . '
				where
					' . TB_TERMIN . '.ref_object_id = ' . $ref_object_id . '
					and
					' . TB_OBJECT . '.id = ' . TB_TERMIN . '.ref_object_id
					and
					' . TB_USER . '.id = ' . TB_OBJECT . '.ref_user_id
			';

		// echo $sql;

		$raw = $db->query($sql);
		if ($db->num_rows($raw))
		{
			$this->current_date = $db->fetch_array($raw);
			return(1);
		}
		else
		{
			$log->add_to_log('tb_termin::load_tb_termin', 'Fatal error! Could not load data!');
			die('Whoups, konnte Termindaten nicht laden!!!');
		}
	}

	function return_field($field)
	{
		return($this->current_date[$field]);
	}
}

?>
