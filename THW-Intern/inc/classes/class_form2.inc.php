<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 06.07.2003
* Last edit: 11.07.2003
*
* class Form2
*
* Funktion:
*			Eine Klasse zum verwalten von Formularen!!
*
* Parameter:
*			Keine
*
* Rückgabe:
* 			Keine
*
* Bemerkungen:
*
* TODO:
*
* DONE:
*
*******************************************************************************/

class Form2
{
	var
			$presets,									// Hier werden die Vorgaben gespeichert!
			$fields,										// Das eigentliche Formular
			$already_processed,					// Jedes bearbeitete Feld wird
															// hier mit important gespeichert!
			$submitted_data,						// Hier werden alle übergebenen Daten
															// gespeichert!
			$precheck_error,						// Liegt ein Fehler (unvollständiges Feld) vor?
			$form_action,								// Wohin sollen die Daten gehen?
			$form_method,							// Wie sollen sie da hinkommen?
			$form_name								// Wie heißt das Formular? (Für JScript)
		;

	/*******************************************************************************
	* Last edit: 08.07.2003
	*
	* Form2($action, $method, $form_name = 'FIRSTFORM')
	*
	* Funktion:
	*			Konstruktor! Liest alle Übergebenen Daten in das Objekt ein.
	*			Dann sucht ::Form2() nach Formulardaten eines Formulars mit dem
	*			Name $form_name; Wenn vorhanden, werden diese eingelesen und
	*			stehen dann in $this->already_processed zur Verfügung. Wenn
	*			Formulardaten anliegen, prüft die Funktion weiter ob evtl. per
	*			submit Daten übergeben wurden und liest diese ins Objekt ein!
	*
	* Parameter:
	* - $action: Wohin mit den Submit-Daten?
	* - $method: Wie dahin?
	* - $form_name: Wie soll das Formular heißen? Wichtig für JScript und
	*			das wiedereinlesen der Daten!!!
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	* - Irgendwie eine Möglichkeit schaffen das Formular komplett zurückzusetzen
	*
	* DONE:
	*
	*******************************************************************************/
	function Form2($action, $method, $form_name = 'FIRSTFORM')
	{
		global $log;
		$log->add_to_log('Form2::Form2()', 'Form2::Form2() constructor called!', 'debug');

		$this->form_action = $action;
		$this->form_method = $method;
		$this->form_name = strtoupper($form_name);

		$this->precheck_error = 0;

		// Überprüfen ob dieses Formular bereits bearbeitet worden ist:
		if (session_is_registered($this->form_name))
		{
			// Ok, dieses Formular wurde bereits bearbeitet! Formulardaten
			// wiedereinlesen:
			$this->already_processed = unserialize($GLOBALS[$this->form_name]);
		}

		$this->submitted_data = array();

		// Damit können wir jetzt die per submit übergebenen Daten einlesen:
		// Hierzu gehen wir alle Einträge durch...
		if (is_array($this->already_processed))
		{
			while ($current = each($this->already_processed))
			{
				if ($GLOBALS[$current[key]])
				{
					$this->submitted_data[$current[key]] = $GLOBALS[$current[key]];
				}
			}
		}
		else
		{
			// *hmm* Ok, wohl noch keine Einträge übergeben...
			$this->already_processed = array();
		}

	}


	/*******************************************************************************
	* Last edit: 06.07.2003
	*
	* function load_form($fields)
	*
	* Funktion:
	*			Lädt den Formulararray in das Objekt
	*
	* Parameter:
	* - $fields: Der Array der das Formular an sich beschreibt!
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function load_form($fields)
	{
		$this->fields = $fields;
	}


	/*******************************************************************************
	* Last edit: 06.07.2003
	*
	* function load_presets()
	*
	* Funktion:
	*			Lädt einen Array mit presets in das Objekt
	*
	* Parameter:
	*			Keine
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function load_presets($presets)
	{
		$this->presets = $presets;
	}


	/*******************************************************************************
	* Last edit: 08.07.2003
	*
	* function precheck_form()
	*
	* Funktion:
	*
	* Parameter:
	*			Keine
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function precheck_form()
	{
		global $log;
		// $log->add_to_log('::precheck_form()', 'Form2::precheck_form() called!', 'debug');


		$new_fields = 0;

		$this->precheck_error = 0;

		// Liegt überhaupt ein Array vor??
		if (count($this->fields))
		{
			$log->add_to_log('Form2::precheck_form()', 'Found ' . count($this->fields) . ' entries in form!', 'debug');

			// Jetzt gehen wir den Array Element für Element durch:
			for ($i = 0; $i < count ($this->fields); $i++)
			{
				// Prüfen ob dieses Feld bereits durch diese Funktion gelaufen
				// ist indem wir nach einem passendem Eintrag in
				// $this->already_processed suchen!

				// rtrim(str_replace('[]', ' ', $fields[$i][name]))
				$temp = rtrim(str_replace('[]', '', $this->fields[$i][name]));

				if ($this->already_processed[$temp][processed])
				{
					// Ein bearbeitetes Feld:
					// $log->add_to_log('::precheck_form()', 'Found a already processed form!', 'debug');

					// prüfen ob das Feld wichtig ist und ob etwas eingetragen worden ist:
					if ($this->already_processed[$temp][important])
					{
						if ($this->submitted_data[$temp])
						{
							// Ok, bei diesem Feld liegt Inhalt vor!!!

							// Feld als OK markieren
							$this->fields[$i][css_class] = 'form_ready';
						}
						else
						{
							// *urgs* hier liegt kein Inhalt vor...

							// Feld als fehlerhaft markieren!!!
							$this->fields[$i][css_class] = 'form_error';
							$this->precheck_error++;
						}
					}
					else
					{
						// Ein unwichtiges Feld:
						if ($this->submitted_data[$temp])
						{
							// Ok, bei diesem Feld liegt Inhalt vor!!!

							// Feld als OK markieren
							$this->fields[$i][css_class] = 'form_ready';
						}
						else
						{
							// hier liegt kein Inhalt vor... Naja..
							$this->fields[$i][css_class] = 'form_default';
						}

					}

				}
				else
				{
					// Ok, hier haben wir ein Feld das noch nicht bearbeitet wurde:
					// $log->add_to_log('::precheck_form()', 'Found a NOT processed form!', 'debug');

					$new_fields++;

					// Jetzt setzen wir noch die Standard-CSS-Klasse:
					if ($this->fields[$i][important])
					{
						$this->fields[$i][css_class] = 'form_important';
					}
					else
					{
						$this->fields[$i][css_class] = 'form_default';
					}
				}

				// nehmen wir das Feld mal als bearbeitet auf:

				// if (($this->fields[$i][type] != 'buttons') or ($this->fields[$i][type] != 'hidden'))
				if ($this->fields[$i][type] != 'hidden')
				{
					$this->alreay_processed[$temp] = array(
							'important' => $this->fields[$i][important],
							'processed' => 1,
							'name' => $this->fields[$i][name]
						);
				}
			}
		}
		else
		{
			// *urgs* kein Array vorhanden???
			die('*urgs* Fehler in ::precheck_form(), kein Formulararray vorhanden! Breche ab...');
		}

		if ($new_fields)
		{
			// Ok, es liegen neue Felder vor!
			// Jetzt setzen wir Sicherheitshalber mal $this->precheck_error,
			// damit das neue Feld auch auf jeden Fall angezeigt wird!
			$this->precheck_error++;
		}

		// Jetzt speichern wir noch wichtige Formulardaten in der Session:
  		$temp = $this->form_name;
  		global $$temp;
		$$temp= serialize($this->alreay_processed);
		session_register($this->form_name);
	}


	/*******************************************************************************
	* Last edit: 08.07.2003
	*
	* function build_form()
	*
	* Funktion:
	*
	* Parameter:
	*			Keine
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function build_form()
	{
		global $log;
		// $log->add_to_log('::build_form()', 'Form2::build_form() called!', 'debug');

		$output = '';

		// Liegt überhaupt ein Array vor??
		 if (count($this->fields))
		 {
			$output = '
						<script language="javascript" src="inc/javascript/jslib-forms.js"></script>
						<form action="' . $this->form_action . '" method="' . $this->form_method . '" name="' . $this->form_name . '" OnLoad="setFirstfocus(' . $this->form_name . ')">
							<table width="100%" align="center" border="0">';

			// Jetzt gehen wir einfach jeden Eintrag durch und bauen in
		 	for ($i = 0; $i < count($this->fields); $i++)
			{
				if ($this->submitted_data[rtrim(str_replace('[]', '', $this->fields[$i][name]))])
				{
					$output .= $this->build_code($this->fields[$i], $this->submitted_data[rtrim(str_replace('[]', '', $this->fields[$i][name]))]);
				}
				else
				{
					$output .= $this->build_code($this->fields[$i], $this->presets[rtrim(str_replace('[]', '', $this->fields[$i][name]))]);
				}
			}

			$output .= '
							</table>
						</form>
						<script language="javascript">setFirstfocus(' . $this->form_name . ')</script>';
		 }
		 else
		 {
		 	die('*urgs* Fehler in ::build_form(), $this->fields enthält keine Elemente!! Breche ab!!');
		 }

		 return($output);
	}

	/*******************************************************************************
	* Last edit: 07.07.2003
	*
	* function little_helper($data)
	*
	* Funktion:
	*
	* Parameter:
	*			Keine
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function little_helper($data)
	{
		$output = '';

		for ($i = 0; $i < count($data); $i++)
		{
			$output .= $data[$i][name] . '="' . $data[$i][value] . '" ';
		}

		return($output);
	}

	/*******************************************************************************
	* Last edit: 07.07.2003
	*
	* function build_code()
	*
	* Funktion:
	*
	* Parameter:
	*			Keine
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function build_code($current, $preset)
	{
		global $log;
		// $log->add_to_log('::build_code()', 'Form2::build_code() called!', 'debug');

		switch($current[type])
		{
			case 'text':
			case 'password':
			case 'radio':
			case 'checkbox':
			case 'select':
			case 'textarea':
				$output = '
									<tr>
										<td width="40%" class="' . $current[css_class]. '">
											' . $current[title]. '
										</td>
										<td>';
				break;

			case 'buttons':
				$output = '
									<tr>
										<td width="40%" class="form_default" colspan=2 align=right>';
				break;

			case 'hidden':
				$output = '
									<tr>
										<td colspan="2">';
				break;

			case 'separator':
				$output = '
									<tr>
										<td colspan="2" class="form_default">';
				break;

			default:
		}

		// Jetzt kommt der Typspezifische Teil:
		switch($current[type])
		{
			case 'text':
			case 'password':
					$output .= '
											<input type="' . $current[type] . '" name="' . $current[name] . '" value="' . $preset . '" ' . $this->little_helper($current[attribs]) . ' >';
				break;

			case 'radio':
					$output .= '
											<table width="100%" align="center" border="0">';

					for ($i = 0; $i < count($current[selections]); $i++)
					{
						// Das ausgewählte Element, falls vorhanden, wird noch etwas hervorgehoben:
						$selected = '';
						$class = '';
						if ($current[selections][$i][value] == $preset)
						{
							$class = 'class="' . $current[css_class] . '"';
							$selected = ' checked ';
						}

						$output .= '
												<tr>
													<td ' . $class . ' align=left>
														<div align=left><input type="radio" name="' . $current[name] . '" value="' . $current[selections][$i][value] . '" ' . $selected . ' ' . $this->little_helper($current[attribs]) . ' title="' . $current[selections][$i][description] . '"> ' . $current[selections][$i][title] . '</div>
													</td>
												</tr>';
					}
					$output .= '
											</table>';
				break;

			case 'checkbox':
					if ($preset)
					{
						$selected = ' checked ';
					}

					$output .= '
											<input type="' . $current[type] . '" name="' . $current[name] . '" ' . $selected . ' ' . $this->little_helper($current[selections]) . ' ' . $this->little_helper($current[attribs]) . '>';
				break;

			case 'select':
					$output .= '
											<select name="' . $current[name] . '" ' . $this->little_helper($current[attribs]) . '>';

					for ($i = 0; $i < count($current[selections]); $i++)
					{
						for ($j = 0; $j < count($preset); $j++)
						{
							if ($preset[$j] == $current[selections][$i][value])
							{
								$selected = ' selected ';
								break;
							}
							else
							{
								$selected = '';
							}
						}

						$output .= '
												<option value="' . $current[selections][$i][value] . '" ' . $selected . '>' . $current[selections][$i][title] . '</option>';
					}

					$output .= '
											</select>';
				break;

			case 'textarea':
					$output .= '
											<textarea name="' . $current[name] . '" ' . $this->little_helper($current[attribs]) . '>' . $preset . '</textarea>';
				break;

			case 'hidden':
					for ($i = 0; $i < count($current[selections]); $i++)
					{
						$output .= '
      										<input type="hidden" name="' . $current[selections][$i][name] . '"  value="' . $current[selections][$i][value] . '">
											';
					}
				break;

			case 'buttons':
					for ($i = 0; $i < count($current[selections]); $i++)
					{
						$output .= '
      										<input type="' . $current[selections][$i][type] . '"  name="' . $current[selections][$i][name] . '"  value="' . $current[selections][$i][value] . '">
											';
					}
				break;

			case 'separator':
					$output .= '<div align="center">' . $current[value] . '</div>';
				break;

			default:
				$log->add_to_log('::build_code()', 'Requested an unsupported type "' . $current[type] . '"! What now?? *urgs*', 'debug');
		}

		$output .= '
										</td>
									</tr>';

		return($output);
	}


	/*******************************************************************************
	* Last edit: 06.07.2003
	*
	* function is_form_error()
	*
	* Funktion:
	*
	* Parameter:
	*			Keine
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function is_form_error()
	{
		return($this->precheck_error);
	}

	/*******************************************************************************
	* Last edit: 06.07.2003
	*
	* function form_shutdown()
	*
	* Funktion:
	*
	* Parameter:
	*			Keine
	*
	* Rückgabe:
	* 			Keine
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
 	function form_shutdown()
	{
		session_unregister($this->form_name);
	}

	/*******************************************************************************
	* Last edit: 08.07.2003
	*
	* function return_submitted_field($field)
	*
	* Funktion:
	*
	* Parameter:
	* - $field: Gibt einen per Submit übergebenen Wert zurück, falls
	*			vorhanden!
	*
	* Rückgabe:
	* 			Feld oder NULL
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
 	function return_submitted_field($field)
	{
		return($this->submitted_data[$field]);
	}


	/*******************************************************************************
	* Last edit: 08.07.2003
	*
	* function set_precheck_error()
	*
	* Funktion:
	*			Setzt precheck_error und erzwingt damit ein erneutes Anzeigen
	*			des Formulars
	*
	* Parameter:
	*
	* Rückgabe:
	* 			Feld oder NULL
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
 	function set_precheck_error()
	{
		$this->precheck_error++;
	}


	/*******************************************************************************
	* Last edit: 11.07.2003
	*
	* function return_preset_field($field)
	*
	* Funktion:
	*			Gibt ein Feld aus dem preset-Array zurück
	*
	* Parameter:
	* - $field: Welches Feld?
	*
	* Rückgabe:
	* 			Feld oder NULL
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
 	function return_preset_field($field)
	{
		return($this->presets[$field]);
	}
}

?>
