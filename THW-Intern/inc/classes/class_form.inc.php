<?php
/*******************************************************************************
* (c) 2003 Jakob K�lzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Last edit: 06.07.2003
*
* class Form
*
* Funktion:
*			Eine Klasse zum verwalten von Formularen!!
*			DIE MONSTERKLASSE!
*
*			Knee before me, as i am the Lord of the SOURCE! (o:
*
* Parameter:
*			Keine
*
* R�ckgabe:
* 			Keine
*
* Bemerkungen:
*
* TODO:
* - pre_process_form umschreiben!!!!! pre_process_form() bearbeitet momentan
*		$this->fields, sollte aber einen per Parameter �bergebenen bearbeiten
*		und auf $this->fields nur zur�ckfallen falls nichts anderes �bergeben...
* - Klasseninterne Variable, die alle Felder des Formulars enth�lt
* - passend dazu eine Funktion die Felder daraus zur�ckgibt (return_field())
* - eine Methode die den gesamten Array ausgibt, analog zu reread_form()
* - vielleicht eine Methode die es Erlaubt Formularfelder hinzuzuf�gen
* 		(insert_field($field, $position)??) zwecks variable Formulare
* - Methode: set_pre_process_error() um erneuten Formularaufbau zu erzwingen!
*
* DONE:
* - Doku f�r $fields-Array schreiben!!!!!!!!!!!!!!!!
*
*******************************************************************************/
class Form
{
	var
		$output,							// Hier speichern wir die Ausgabe bis sie ausgegeben wird
		$action,							// Welche Seite soll bei submit geladen werden?
		$method,						// Wie sollen die Daten zum Server geschafft werden?
 		$fields,							// Die eigentlichen Formulardaten
		$form_name,					// Name des Formulars f�r JS-Funktionen
		$submitted,					// Wurde das Formular schonmal abgesendet?
		$pre_process_error;		// Liegt ein Fehler vor? (Fehlender Eintrag in einem Feld)


	/*******************************************************************************
	* Last edit: 04.07.2003
	*
	* DOKU: $fields
	* 			Dieser Array beschreibt komplett alle Formularfelder
	*			des darzustellenden Formlars!
	*
	* Prototyp:
	* 		folgende Eigentschaften besitzt ein Eintrag im Array (allgemein):
	*			- type: gibt den Typ des Feldes an, m�glich sind alle
	*						in HTML definierten Formularfelder bis auf file
	*			- name: Name des Elementes; Bei Feldern die eine Mehrfach-
	*						auswahl gestatten sollen, sollte man "[]" Anh�ngen,
	*						damit PHP automatisch einen Array daraus generiert
	*						und pre_process_form automatisch den default-Wert
	*						richtig setzt!
	*			- important: Wenn dieses Flag gesetzt ist, muss es ausgef�llt
	*						werden, sonst hagelts Fehler...
	*			- description: Hier steht die Beschreibung welche in der linken
	*						Tabellenspalte steht!
	*			- title: Inhalt des title-Feldes im Formulartags; Damit lassen
	*						sich elegant kurze, weiterf�hrende Erkl�rungen
	*						einbauen!!!!!!!!!!! BITTE BENUTZEN! (o:
	* 			- js_functions: Enth�lt einen Array der jeweils den Event (onLoad,
	*						onClick....) und die entsprechende JS-Funktion
	*						enth�lt....
	*			- value: Enth�lt Vorbelegungen oder Auswahlm�glichkeiten
	*						bei select-, radio- oder checkbox-feldern
	*			- predefined: F�r select, radio oder Checkboxen: enth�lt die
	*						Felder die ausgew�hlt werden sollen!
	*			- size: Gr��e des Feldes; siehe unten!
	*			- status: enth�lt die Klasse die dem Beschreibungsfeld
	* 						zugewiesen werden soll!
	*						WIRD VOM SYSTEM GESETZT!
	*						Muss normalerweise vom USer nicht ge�ndert werden;
	*						enth�lt normalerweise "form_left"; bei Feldern die
	*						mit important = 1 markiert sind, wird standardm��ig
	*						"form_important" geladen!
	*						Nach einem Submit wird bei unausgef�llten Feldern
	*						mit important=1 ein "form_error", bei ausgef�llten
	*						Feldern, egal ob wichtig oder nicht, ein "form_ready"
	*
	*
	* BEISPIELE
	*		Hier gibt es jetzt Beispiele f�r ALLE bis jetzt implementierten
	*		Formularfeld-typen:

			$fields = array();

	*
	* Hier einfach mal ein Textfeld (analog dazu auch type=password):
			$fields[0] = array(
					'type' => 'text',							// Textfeld
					'name' => 'text_field',				// Feldname, Name der PHP-Variable
					'value' => 'Vorbelegung',			// Vorbelegung
					'title' => 'Bla Bla Bla',				// title-Text, erscheint in einem Popup
					'important' => '1',						// Muss dieses Feld ausgef�llt werden?
					'description' => 'Ein Textfeld',	// Beschreibung
					'size' => '15',								// Gr��e des Feldes, hier Breite

					// STILL UNDER DEVELOPMENT!!!!!!!!!!!!!! *URGSSSSSS*
					'js_functions' => array(				// Hier werden JS-Script aufrufe gespeichert!
							0 => array(
									'event' => 'onLoad',
									'function' => 'SomeJsFunction'
								),
							1 => array(
									'event' => 'onClick',
									'function' => 'SomeOtherJsFunction'
								)
						)
				);

	****************************************************************************
	* Radiobuttons:
			$fields[] = array(
					'type' => 'radio',						// Radiobuttons
					'name' => 'radiobuttons[]',		// Feldname, Name der PHP-Variable
																	// Die eckigen Klammern [] Sind wichtig,
																	// sonst macht PHP keinen Array draus!!
					'value' => array(			,			// Enth�lt die m�gliche Auswahl!
																		// In Value wird der Inhalt des Value-Feldes
																		// gespeichert, in Description der Text der
																		// Neben dem entsprechenden Radiobutton
																		// steht:
											0 => array(
															'value' => 'rb1',
															'description' => 'Radiobutton 1',
															'title' => 'N�here Beschreibung zu Radiobutton 1'
														),
											1 => array(
															'value' => 'rb2',
															'description' => 'Radiobutton 2',
															'title' => 'N�here Beschreibung zu Radiobutton 2'
														)
										),
					'important' => '1',						// Muss dieses Feld ausgef�llt werden?
					'description' => 'Radiobuttons'	// Beschreibung
					'predefined' => 'rb2'					// Welcher Radiobutton soll per default ausgew�hlt
																	// sein?? In diesem Beispiel w�re es "Radiobutton 2"

				);

	****************************************************************************
	* Select's (Dropdown und Auswahlfelder):
			$fields[] = array(
					'type' => 'select',						// Radiobuttons
					'name' => 'radiobuttons[]',		// Feldname, Name der PHP-Variable
																	// Die eckigen Klammern [] Sind wichtig,
																	// falls Mehrfachauswahl gew�nscht wird,
																	// sonst macht PHP keinen Array draus!!
					'multiple' => 'multiple',				// Mehrfachauswahl m�glich??
					'size' => 4,								// Gr��e des Feldes;
																	// size = 1 : Dropdown
																	// size > 1 : Auswahlfeld
					'value' => array(			,			// Enth�lt die m�gliche Auswahl!
																		// In Value wird der Inhalt des Value-Feldes
																		// gespeichert, in Description der Text der
																		// Neben dem entsprechenden Radiobutton
																		// steht:
											0 => array(
															'value' => 's1',
															'description' => 'Auswahl 1'
														),
											1 => array(
															'value' => 's2',
															'description' => 'Auswahl 2'

														)
										),
					'title' => 'Irgend ne Beschreibung',	// Popup
					'important' => '1',						// Muss dieses Feld ausgef�llt werden?
					'description' => 'Select'			// Beschreibung
					'predefined' => 's2'					// Welcher Eintrag soll per default ausgew�hlt
																	// sein?? In diesem Beispiel w�re es "Auswahl 2"

				);

	****************************************************************************
	* Textbox:
			$fields[] = array(
					'type' => 'textarea',						// Eine Textbox
					'name' => 'textbox'		,				// Feldname, Name der PHP-Variable
					'value' => 'Hier bitte einen kurzen Roman reinschreiben!',
																		// Vorbelegung!
					'description' => 'Eine Textbox	',	// Beschreibung
					'title' => 'Bla Bla Bla',					// Popup
					'important' => '1',							// Muss dieses Feld ausgef�llt werden?
					'size' => ' cols="60" rows="20"'	// Gr��e der Textbox!
																		// OK, OK, ich gebs zu, ist unelegant so,
																		// aber ich hatte keine Lust da nochmal
																		// extra nen Array zu basteln!
				);

	****************************************************************************
	* Checkbox:
			$fields[] = array(
					'type' => 'checkbox',						// Eine Checkbox
					'name' => 'chkbox'		,				// Feldname, Name der PHP-Variable
					'value' => '1',								// Wert der mit Submit bei ausgew�hlter
																		// Checkbox �bergeben wird!
					'description' => 'Bitte w�hlen:',	// Beschreibung
					'title' => 'Bla Bla Bla',					// Popup
					'important' => '1'							// Muss dieses Feld ausgef�llt werden?
				);

	****************************************************************************
	* Versteckte Felder:
			$fields[] = array(
					'type' => 'hidden',							// Hidden Fields

					'hidden_fields' => array(
								0 => array(
										'name' => 'hidden_field',
										'value' => 'versteckt'
									),
								1 => array(
										'name' => 'another_hidden_field',
										'value' => 'versteckter'
									)
							)
				);

	* Versteckte Felder sollten alle in einem Rutsch �bergeben werden, es macht
	* kaum Sinn f�r Jedes Feld einen eigenen Array-Eintrag aufzumachen!

	****************************************************************************
	* buttons
			$fields[] = array(
					'type' => 'button'							// Button
					'buttons' => array(
								0 => array(
										'type' => 'submit',		// submit-Button
										'name' => 'submit',
										'value' => 'Abschicken'
									),
								1 => array(
										'type' => 'reset',		// Reset-button
										'name' => '',
										'value' => 'Zur�cksetzen'
									)
							)
				);

	*******************************************************************************/


	/*******************************************************************************
	* Last edit: 06.07.2003
	*
	* function Form($action, $method, $fields, $form_name), Konstruktor
	*
	* Funktion:
	* 			setzt alle wichtigen Variablen auf die �bergebenen Werte;
	* 			desweiteren �berpr�ft der Konstruktor ob evtl. in $LAST_FORM
	* 			schon Formulardaten vorliegen und l�dt diese ggf. wieder in
	*			das Objekt.
	*
	* Parameter:
	* - $action: welche Seite soll bei Submit geladen werden??
	* - $method: wie sollen die Daten zum Server geschaufelt werden?
	* 			(normalerweise "get", bei gro�en Datenmengen "post")
	* - $fields: der Array der die Formulareigenschaften und Aufbau enth�lt
	* 			F�r den Aufbau dieses Arrays siehe weiter unten!
	* - $submitted: Wurde das Formular schon mal abgesendet oder nicht?
	* 			Am besten einfach den Submit-button "submit" nennen und dort
	*			eintragen... (o:
	* - $form_name: der Name des Formulars f�r die JavaScript-Funktionen;
	* 			Ist per default "firstform"!
	*
	* R�ckgabe:
	* 			Keine
	*
	* Bemerkungen:
	* 			Hinweise zu Layout und Farbgebung bei build_form()!
	*
	*			Mit den neu implementierten Funktionen w�ren auch
	* 			sich erweiterndende Formulare denkbar, beispielsweise
	*			Auswahlfelder die erst nach Anw�hlen einer Checkbox
	*			erscheinen.... (Hierzu m�sste man nach einem pre_process_form
	*			die �bergebenen Werte auswerten, den Formulararray ausgeben,
	*			erweitern und per reread_form() wieder einlesen...
	*			Ain't this funky? (o:   )
	*
	*******************************************************************************/
	function Form($action,  $method,  $fields, $submitted = 0, $form_name = 'firstform')
	{
		$this->fields = $fields;
		$this->action = $action;
		$this->method = $method;
		$this->pre_process_error = 0;
		$this->form_name = $form_name;
		$this->submitted = $submitted;

		// Jetzt pr�fen wir ob evtl. noch alte Formulardaten anliegen...
		$LAST_FORM = '';
		global $LAST_FORM;
		$LAST_FORM = unserialize($LAST_FORM);
		if (is_array($LAST_FORM))
		{
			// Felder mit den aktuellen �berschreiben
			$this->fields = $LAST_FORM;
		}

		// Jetzt rufen wir erstmal pre_process_form() auf um das was wir haben zu pr�fen:
		$this->pre_process_form($this->fields);
	}


	/*******************************************************************************
	* Last edit: 03.07.2003
	*
	* function function reread_form($fields)
	*
	* Funktion:
	* 			liest das Formularfeld nochmals ein; somit kann der Array
	* 			mit den Feldern nach pre_process_form() nochmals mit
	*			Userspezifischen (Fehler-)Meldungen versehen werden!
	*			Dann wird der Array wieder eingelesen und kann per build_form()
	*			der HTML-Code erstellt werden!
	*
	* Parameter:
	* - $fields: der Array der die Formulareigenschaften und Aufbau enth�lt
	* 			Hier kommen die ver�nderten Formulardaten
	*
	* R�ckgabe:
	* 			Keine
	*
	* Bemerkungen:
	*			Vorerst keine
	*
	*******************************************************************************/
	function reread_form($fields)
	{
		$this->fields = $fields;
	}


	/*******************************************************************************
	* Last edit: 03.07.2003
	* function filter_html($text)
	*
	* Funktion:
	* 			Ersetzt alle Umlaute, Anf�hrungszeichen etc. etc. durch
	* 			die entsprechenden HTML-Codes; filtert momentan auch
	*			HTML-Tags aus;
	*
	* Parameter:
	* - $text: der zu filternde Text
	*
	* R�ckgabe:
	* 			gibt den gefilterten String zur�ck
	*
	* Bemerkungen:
	*			Keine
	*
	* TODO:
	* - evtl. eigene Routine zum Ersetzen schreiben um <a href=""> Tags
	* 			zu erm�glichen!
	*
	*******************************************************************************/
	function filter_html($text)
	{
		return(htmlentities(stripslashes($text), ENT_QUOTES));
	}

	/*******************************************************************************
	* Last edit: 06.07.2003
	*
	* function reset_single_field($field_no, $fields = 0)
	*
	* Funktion:
	* 			Setzt den Default-Wert des angegebenen Feldes auf den Wert
	*			der im $GLOBALS-Array gespeichert ist!
	*
	* Parameter:
	* - $field_no: Welches Feld soll bearbeitet werden?
	* - $fields: Array der bearbeitet werden soll; Wenn NULL, dann wird
	* 			$this->fields bearbeitet!
	*
	* Bemerkungen:
	*			Keine
	*
	* TODO:
	*
	* DONE:
	* - bei select-Feldern nach Mehrfachauswahl pr�fen!!!!!!!!!!!
	* - BUG: einmal gesetzte Checkboxen lassen sich nicht mehr abw�hlen..??
	*
	*******************************************************************************/
	function reset_single_field($field_no, $fields = 0)
	{
		global $log;

		if (!$fields)
		{
			$fields = $this->fields;
		}

		switch($fields[$field_no][type])
		{
			case 'text':
			case 'textarea':
			case 'password':
					$fields[$field_no][value] = $this->filter_html($GLOBALS[$fields[$field_no][name]]);
				break;

			case 'radio':
					for ($j = 0; $j < count($fields[$field_no][value]); $j++)
					{
						// Ist der akutelle Radiobutton der mit dem ausgew�hlten Wert?
						if ($fields[$field_no][value][$j][value] == $GLOBALS[$fields[$field_no][name]])
						{
							$fields[$field_no][predefined] = $fields[$field_no][value][$j][value];
						}
					}
				break;

			case 'select':
					// Bei einem Select-Feld mit Mehrfachauswahl muss der Name
					// des Feldes mit [] abgeschlossen sein, damit PHP daraus
					// einen Array macht! Dieser wird wenn "multiple" �bergeben
					// wird hier durchgeloopt...

					if ($fields[$field_no][multiple])
					{
						// Mehrfachauswahl!!!

						$tmp = 0;

						// Jetzt gehen wir durch, alle vorgegebenen M�glichkeiten!
						$log->add_to_log('Form::reset_single_field', 'found ' . count( $fields[$field_no][value] ) . ' predefined entries!', 'debug');
						for ($j = 0; $j < count($fields[$field_no][value]); $j++)
						{
							$log->add_to_log('Form::reset_single_field', 'found ' . count( $GLOBALS[ rtrim(str_replace('[]', ' ', $fields[$field_no][name]))] ) . ' selected fields... ', 'debug');

							// Jetzt pr�fen wir den aktuellen Men�punkt gegen die Auswahl die der User getroffen hat!
							for ($k = 0; $k < count( $GLOBALS[ rtrim(str_replace('[]', ' ', $fields[$field_no][name]))] ); $k++)
							{
								if ($fields[$field_no][value][$j][value] == $GLOBALS[ rtrim(str_replace('[]', ' ', $fields[$field_no][name]))][$k])
								{
									// echo 'found one! (' . $j . ') <br>';
									// $this->fields[$field_no][predefined][$tmp] = $this->fields[$field_no][value][$j][value];

									if (!is_array($fields[$field_no][predefined]))
									{
									 	$fields[$field_no][predefined] = array();
									}
									if (!is_array($fields[$field_no][predefined][$tmp]))
									{
									 	$fields[$field_no][predefined][$tmp] = array();
									}

									$fields[$field_no][predefined][$tmp][value] = $fields[$field_no][value][$j][value];

									$tmp++;
								}
							}
						}
					}
					else
					{
						// Einfachauswahl!!
						for ($j = 0; $j < count($fields[$field_no][value]); $j++)
						{
							// Ist der akutelle Radiobutton der mit dem eingetragenen Wert?
							if ($fields[$field_no][value][$j][value] == $GLOBALS[$fields[$field_no][name]])
							{
								// Jetzt m�ssen wir predefined nur noch auf den richtigen Wert setzten.
								$fields[$field_no][predefined] = $fields[$field_no][value][$j][value];
							}
						}
					}
				break;

			case 'checkbox':
					$fields[$field_no][predefined] = 1;
				break;

			case 'file':
					// Hier mach ma vorerst nix!
					// Nicht implementiert!
				break;

		// ENDE switch($this->fields[$i][type])
		}

		return($fields);
	}


	/*******************************************************************************
	* Last edit: 06.07.2003
	*
	* function pre_process_form()
	*
	* Funktion:
	*			pre_process_form leistet, wie der Name sagt, die Vorarbeit f�r
	*			build_form() indem es alle Formularfelder �berpr�ft!
	*			Felder die das Erste mal von pre_process_form() bearbeitet
	*			werden ($fields[$i][processed] = 0) werden als bearbeitet
	*			markiert ($fields[$i][processed] = 1) und einigen
	*			einfachen Tests unterzogen. Desweiteren werden bei diesem Ersten
	*			Durchlauf die Standardklassen gesetzt. Danach wird der gesamte
	*			Formular-Array in der Session gespeichert
	* 			Bearbeitete Felder werden dann auf Inhalt gepr�ft und in
	*			Abh�ngigkeit von "importance" weiter bearbeitet bzw. Fehler
	*			ausgegeben!
	*
	* Parameter:
	* - $fields: Die Felder die bearbeitet werden sollen; Wenn keine �bergeben
	*			werden, wird $this->fields bearbeitet!
	*
	* Bemerkungen:
	*			Keine
	*
	* TODO:
	* - Destruktor, um $LAST_FORM wieder aus der Session zu l�schen!
	* - file-upload-formulare einbauen!!!
	* - Hier und da das Array-Handling etwas tunen;
	* - Fehlerresistenter machen!
	*
	* DONE:
	* - bei select-Feldern nach Mehrfachauswahl pr�fen!!!!!!!!!!!
	*
	*******************************************************************************/
	function pre_process_form($fields = 0)
	{
		if (!$fields)
		{
			$fields = $this->fields;
		}
		// Zuerst pr�fen wir mal ob es sich bei $fields �berhaupt um einen Array handelt...
		if (is_array($fields))
		{
			// Ok, ein Array liegt vor...

			// Jetzt gehen wir die Felder durch:
			for ($i = 0; $i < count($fields); $i++)
			{
				// Ist dieses Feld schonmal bearbeitet worden und ist dieses Formular schonmal abgeschickt worden?
				// Andernfalls macht diese Pr�fung keinen Sinn!
				if ($fields[$i][processed] AND $this->submitted)
				{
					// OK, hier liegt ein bereits bearbeitetes Feld vor!!!!

								// Ist der Inhalt wichtig?
								if ($fields[$i][important])
								{
									// Liegt �berhaupt Inhalt vor?
									if ($GLOBALS[$fields[$i][name]])
									{
										// Ja, Feld hat Inhalt. Juhu!

										// Das Feld als fertig markieren!
										$fields[$i][status] = 'form_ready';

										// Jetzt m�ssen die default-Werte rausgeworfen werden und durch die neuen Werte
										// ersetzt werden!
										$this->reset_single_field($i);

									}
									else
									{
										// Whoups, nix eingegeben!! Error!
										switch($fields[$i][type])
										{
											case 'checkbox':
											case 'radio':
													$fields[$i][predefined] = 0;
												break;

											case 'text':
											case 'password':
													$fields[$i][value] = '';
												break;
											default:
												$fields[$i][error] = 'Bitte ausf�llen!';
										}
										$fields[$i][status] = 'form_error';
										$this->pre_process_error++;
									}
								}
								else
								{
									// echo '#' . $i . ' found an normal field! (' . $GLOBALS[$this->fields[$i][name]] . ')<br>';


									// Liegt �berhaupt inhalt vor?
									// Das rtrim(str_replace())-Monster ist n�tig um eckige Klammern aus dem
									// Variablennamen rauszufiltern, da sonst die if-condition nicht greift... *urgs*
									if ($GLOBALS[ rtrim(str_replace('[]', ' ', $fields[$i][name]))])
									{
										// echo '#' . $i . ' field got content : <i>' . $GLOBALS[$this->fields[$i][name]] . '</i><br>';
										$fields[$i][status] = 'form_ready';

										// Jetzt m�ssen die default-Werte rausgeworfen werden und durch die neuen Werte ersetzt werden!
										$this->reset_single_field($i);

									}
									// Nein, Es wurde nichts eingetragen oder ausgew�hlt!
									else
									{
										// echo '#' . $i . ' found an empty field! (' . $GLOBALS[$this->fields[$i][name]] . ') [' . $this->fields[$i][name] . ']<br>';
										// Whoups, nix eingegeben!!

										// Bei Checkboxen muss separat auf 0 gesetzt werden!
										if ($fields[$i][type] == 'checkbox')
										{
											$fields[$i][predefined] = 0;
										}

										$fields[$i][status] = 'form_left';
									}

								}
				}
				// ENDE if ($this->fields[$i][processed])
				else
				{
								// Feld wurde hiermit das erste mal bearbeitet!
								$fields[$i][processed] = 1;

								// pre_process_error setzten wir sicherheitshalber mal auf NULL
								$this->pre_process_error = 0;

								// Standardklasse setzen!
								$fields[$i][status] = 'form_left';

								// Ist das Feld important?? Dann wollen wir es doch ein bisschen hervorheben!
								if ($fields[$i][important])
								{
									$fields[$i][status] = 'form_important';
								}

								// Pr�fen ob bei Radiobuttons ein 'predefined' vorhanden ist
								if ($fields[$i][type] == 'radio')
								{
									// predefined wenn nicht vorhanden auf 0 setzen! ???
									if (!$fields[$i][predefined])
									{
										$fields[$i][predefined] = 0;
									}
								}

								// Pr�fen ob bei Select ein 'predefined' vorhanden ist
								if ($fields[$i][type] == 'select')
								{
									// predefined wenn nicht vorhanden auf den ersten Eintrag setzen!
									if (!$fields[$i][predefined])
									{
										$fields[$i][predefined] = $fields[$i][value][0][value];
									}
								}

				// ENDE if ($this->fields[$i][processed]) ... else
				}
			}

			return($fields);
		}
		else
		{
			// Whoups, $this->fields ist kein Array!! Da stimmt was nicht! Wir brechen
			// Vorsichtshalber mal ab!
			return(-1);
		}

// Ausgelagert nach Konstruktor!!!
/*		// Jetzt m�ssen wir die Formulardaten noch speichern, damit wir nachher
		// pr�fen k�nnen ob alles da ist....
		$LAST_FORM = '';
		global $LAST_FORM;
		$LAST_FORM = serialize($fields);

		session_register('LAST_FORM');*/
	}

	/*******************************************************************************
	* Last edit: 04.07.2003
	*
	* function is_pre_process_error()
	*
	* Funktion:
	* Gibt die Variable $this->pre_process_error zur�ck
	*
	* Parameter:
	*
	* R�ckgabe:
	*			$this->pre_process_error
	*
	* Bemerkungen:
	*
	* TODO:
	*
	*******************************************************************************/
	function is_pre_process_error()
	{
		return($this->pre_process_error);
	}

	/*******************************************************************************
	* Last edit: 04.07.2003
	*
	* function build_form()
	*
	* Funktion:
	* 			Geht das in $this->fields �bergebene Formular Schritt f�r
	* 			Schritt durch und �bergibt die einzelnenen Reihen an
	*			build_row()
	*
	* Parameter:
	*
	* R�ckgabe:
	*			Gibt das Komplette Formular eingebettet in eine Tabelle zur�ck!
	*
	* Bemerkungen:
	*
	* TODO:
	* - JS-Funktionen integrieren!! *URGS*
	* - Doku zu den CSS-Klassen schreiben?? Oder hat der Rollo das schon??
	*
	*******************************************************************************/
	function build_form()
	{

		$this->output = '
					<form action="' . $this->action . '" method="' . $this->method . '" name="' . $this->form_name . '">
						<table width=100% align=center border=0 cellspacing=1 cellpadding=1>

			';
		for ($i = 0; $i < count($this->fields); $i++)
		{
			$this->output .= $this->build_row($this->fields[$i]);
		}

		$this->output .= '
						</table>
					</form>

					<!-- JavaScript um Focus auf das erste Formularelement zu setzen
					//   Dabei muss das erste Formular einer Seite den Namen "firstform" haben,
					//   alle anderen Formulare bekommen einen anderen Namen.
					//-->
					<script language="javascript">
						<!-- Dieser Kommentar verhindert das abspacken bei �lteren Browsern
							firstform.elements[0].focus();
							if (firstform.elements[0].type == "text") {
							  firstform.elements[0].select();
							}
						//-->
					</script>

			';

		return($this->output);
	}


	/*******************************************************************************
	* Last edit: 04.07.2003
	*
	* function build_row($current)
	*
	* Funktion:
	* 			Baut den HTML-Code f�r den von pre_process_form() bearbeiteten
	*			$this->fields-Array; Wird von build_form() aufgerufen!
	*
	* Parameter:
	* - $current: Von build_form �bergebener Array, der das aktuelle Element
	*			des Formulars darstellt! Dieser Array wird in HTML-Code
	*			umgesetzt und zur�ckgegeben
	*
	* R�ckgabe:
	*			Der umgesetzte HTML-Code
	*
	* Bemerkungen:
	*
	* TODO:
	*
	*******************************************************************************/
	function build_row($current)
	{
		$output = '
							<tr>
			';

		switch($current[type])
		{
			case 'radio':
					$output .= '
								<td width="40%" class="' . $current[status] . '">
									' . $current[description] . '
								</td>

								<td>
									<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
						';

						// Jetzt gehen wir einfach mal alle Radiobuttons durch!
						for ($i = 0; $i < count($current[value]); $i++)
						{
							// Pr�fen ob der aktulle Radiobutton ausgew�hlt sein soll...
							if ($current[value][$i][value] == $current[predefined])
							{
								$insert = ' checked ';
							}
							else
							{
								$insert = ' ';
							}

							// ... Und output bauen
							$output .= '
										<tr>
											<td width="10">
												<input type="radio" name="' . $current[name] . '" value="' . $current[value][$i][value] . '" ' . $insert . '  title="' . $current[value][$i][title] . '">
											</td>
											<td>
												' . $current[value][$i][description] . '
											</td>
										</tr>
								';
						}

					$output .= '
									</table>
								</td>
						';
				break;

			case 'select':
					$output .= '
								<td width="40%" class="' . $current[status] . '">
									' . $current[description] . '
								</td>

								<td>
									<select size="' . $current[size] . '" name="' . $current[name] . '" ' . $current[multiple] . '  title="' . $current[title] . '">
						';

						// Haben wir eine Mehrfachauswahl? Wenn ja, dann ist predefined ein Array, welcher anders behandelt werden muss!
						if ($current[multiple])
						{
							// WIr haben eine MEhrfachauswahl!!

							// Jetzt gehen wir erstmal alle Punkte der Auswahl durch...
							for ($i = 0; $i < count($current[value]); $i++)
							{
								// ...und pr�fen sie dann gegen den predefined-array!
								for ($j = 0; $j < count($current[predefined]); $j++)
								{
									// Ist der aktuelle Wert gleich einem aus dem value-Array?
									if ($current[value][$i][value]  == $current[predefined][$j][value])
									{
										// vorgew�hlter Eintrag!
										$insert = ' selected ';
										break;
									}
									else
									{
										$insert = ' ';
									}
								}

								// Ausgabe bauen:
								$output .= '
											<option value="' . $current[value][$i][value] . '" ' . $insert . '>' . $current[value][$i][description] . '</option>
									';
							}
						}
						else
						{
							// Einfachauswahl, es kann nur ein Eintrag ausgew�hlt werden!

							for ($i = 0; $i < count($current[value]); $i++)
							{
								if ($current[value][$i][value] == $current[predefined])
								{
									$insert = ' selected ';
								}
								else
								{
									$insert = ' ';
								}

								$output .= '
											<option value="' . $current[value][$i][value] . '" ' . $insert . '>' . $current[value][$i][description] . '</option>
									';

							}
						}

					$output .= '
									</select>
								</td>
						';
				break;


			case 'button':
					$output .= '
								<td colspan="2" class="' . $current[status] . '" align="center">';

						for ($i = 0; $i < count($current[buttons]); $i++)
						{
							$output .= '
									<input type="' . $current[buttons][$i][type] . '" name="' . $current[buttons][$i][name] . '" value="' . $current[buttons][$i][value] . '">
								';
						}

					$output .= '
								</td>	';
				break;

			case 'reset':
			case 'submit':
					$output .= '
								<td colspan="2" class="' . $current[status] . '" align="center">
									<input type="submit" name="' . $current[name] . '" value="' . $current[value] . '">
									<input type="reset" value="Felder zur�cksetzen" onClick="formreset(firstform)">
								</td>
						';
				break;

			case 'checkbox':
					$insert = '';
					if ($current[predefined])
					{
						$insert = ' checked ';
					}
					$output .= '
								<td width="40%" class="' . $current[status] . '">
         								' . $current[description] . '
								</td>
								<td>
									<input type="' . $current[type] . '" name="' . $current[name] . '" value="' . $current[value] . '" ' . $insert . ' title="' . $current[title] . '">
								</td>
						';
				break;

			case 'password':
			case 'text':
					$output .= '
								<td width="40%" class="' . $current[status] . '">
									' . $current[description] . '
								</td>
								<td>
									<input type="' . $current[type] . '" name="' . $current[name] . '" value="' . $current[value] . '" size="' . $current[size] . '" title="' . $current[title] . '">
								</td>
						';
				break;

			case 'hidden':
					$output .= '
									<td colspan="2">
						';
					for ($i = 0; $i < count($current[hidden_fields]); $i++)
					{
						$output .= '
										<input type="hidden" name="' . $current[hidden_fields][$i][name] . '" value="' . $current[hidden_fields][$i][value] . '">
							';
					}
					$output .= '
									</td>
						';
				break;

			case 'textarea':
					$output .= '
								<td width="40%" class="' . $current[status] . '">
									' . $current[description] . '
								</td>
								<td>
									<textarea ' . $current[size] . ' name="' . $current[name] . '"  title="' . $current[title] . '">' . $current[value] . '</textarea>
								</td>
						';
				break;

		}

		$output .= '
							</tr>
			';

		return($output);
	}

	/*******************************************************************************
	* Last edit: 03.07.2003
	*
	* function shutdown()
	*
	* Funktion:
	* 			Gibt belegte Resourcen frei...
	*
	* Parameter:
	*
	* R�ckgabe:
	*
	* Bemerkungen:
	*
	* TODO:
	* - evtl. durch Konstruktor ersetzen
	*
	*******************************************************************************/
	function shutdown()
	{
		global $LAST_FORM;
		session_unregister('LAST_FORM');
	}

	/*******************************************************************************
	* Last edit: 06.07.2003
	*
	* insert_field($field_to_insert, $position)
	*
	* Funktion:
	* 			F�gt nachtr�glich noch ein Formularfeld ein und setzt
	*			automatisch einen pre_process_error, so dass eine Wiederanzeige
	*			vom Formular erzwungen wird!
	*
	* Parameter:
	* - $field_to_insert: Beschreibt das Feld das eingef�gt werden soll, siehe oben!
	* - $position: an welcher Stelle soll das Feld eingef�gt werden, Z�hlung
	*			beginnt bei 0!
	*
	* R�ckgabe:
	*
	* Bemerkungen:
	*
	* TODO:
	*
	*******************************************************************************/
	function insert_field($field_to_insert, $position)
	{
		// Zuerstmal �berpr�fen wir das was wir da kriegen:
		if (count($field_to_insert))
		{

		}
		else
		{
			// *urgs* scheint kein Array zu sein, Abbruch!
			return(-1);
		}
	}
}
?>
