<?php

	require_once('inc/classes/class_form.inc.php');

	$page->title_bar();

	// Hat der User ein Recht ausgewählt ?
	if ($auswahl)
	{

		// Wollen wir einen User hinzufügen??
		if ($add_user)
		{
			$rights->add_right($user, $auswahl);
		}

		// User entfernen??
		if ($remove_user)
		{
			$rights->remove_right($user, $auswahl);
		}


		$fields[1] = array (
				'type' => 'select',
				'description' => 'Zugewiesene User: ',
				'name' => 'user',
				'size' => '6',
				'value' => array()
					);

		if ($auswahl == ROOT)
		{
			// Alle User einlesen die das Recht besitzen
			$sql = '
					select
						' . TB_USER . '.id,
						' . TB_USER . '.name,
						' . TB_USER . '.vorname
					from
						' . TB_USER . '
					where
						' . TB_USER . '.rights = ' . $auswahl . '
					order by ' . TB_USER . '.name
					';
		}
		else
		{
			// Alle User einlesen die das Recht besitzen
			$sql = '
					select
						' . TB_USER . '.id,
						' . TB_USER . '.name,
						' . TB_USER . '.vorname
					from
						' . TB_USER . '
					where
						' . TB_USER . '.rights & ' . $auswahl . '
					order by ' . TB_USER . '.name
					';
		}

		$raw = $db->query($sql);

		if ($db->num_rows($raw))
		{
			$i = 0;
			while ($current = $db->fetch_array($raw))
			{
				$fields[1][value][$i][description] = $current[name] . ', ' . $current[vorname];
				$fields[1][value][$i][value] = $current[id];
				$i++;
			}
		}

		$fields[] = array (
				'type' => 'hidden',
				'hidden_fields' => array(
						'0' => array(
								'value' => 'administration_rights',
								'name' => 'action'
							),
						'1' => array(
								'value' => $auswahl,
								'name' => 'auswahl'
							)

					)
			);

		$fields[] = array (
				'type' => 'submit',
				'value' => 'User dieses Recht entziehen',
				'name' => 'remove_user',
				'important' => '0',
			);


		$form = new Form($PHP_SELF, 'get', $fields);
		$LAST_FORM = $form->pre_process_form();

		$zugewiesen = $form->build_form();


		$output = '
			<table width=96% align=center border=0 cellpadding=3>
				<tr>
					<td width=50% valign=top>
						' . $page->dialog_box('Zugewiesene User', $zugewiesen, 0, 0, '100%') . '
					</td>
					<td valign=top>';

		$fields = array();
		$fields[1] = array (
				'type' => 'select',
				'description' => 'Alle User: ',
				'name' => 'user',
				'size' => '6',
				'value' => array()
					);

		$sql = '
				select
					' . TB_USER . '.id,
					' . TB_USER . '.name,
					' . TB_USER . '.vorname
				from
					' . TB_USER . '
				order by ' . TB_USER . '.name
				';

		$raw = $db->query($sql);
		if ($db->num_rows($raw))
		{
			$i = 0;
			while ($current = $db->fetch_array($raw))
			{
				$fields[1][value][$i][description] = $current[name] . ', ' . $current[vorname];
				$fields[1][value][$i][value] = $current[id];
				$i++;
			}
		}

		$fields[] = array (
				'type' => 'hidden',
				'hidden_fields' => array(
						'0' => array(
								'value' => 'administration_rights',
								'name' => 'action'
							),
						'1' => array(
								'value' => $auswahl,
								'name' => 'auswahl'
							)

					)
			);

		$fields[] = array (
				'type' => 'submit',
				'value' => 'User dieses Recht gewähren',
				'name' => 'add_user',
				'important' => '0',
			);


		$form = new Form($PHP_SELF, 'get', $fields);
		$LAST_FORM = $form->pre_process_form();

		$nicht_zugewiesen = $form->build_form();


		$output .=  '
						' . $page->dialog_box('Userliste', $nicht_zugewiesen, 0, 0, '100%') . '
					</td>
				</tr>
			</table>
			';

		for ($i = 0; $i < count($RIGHTS); $i++)
		{
			for ($j = 0; $j < count($RIGHTS[$i][action]); $j++)
			{
				if ($RIGHTS[$i][right] == $auswahl)
				{
					$tmp .= $RIGHTS[$i][action][$j] . ', ';
				}
			}
		}

		$output = ' <p>Aktuelle Berechtigung : <b>' . $tmp . '</b> </p> ' . $output;

		$menu = array();
		$menu[0][link] = $PHP_SELF . '?action=' . $action;
		$menu[0][text] = 'Alle Berechtigungen';

		echo $page->dialog_box('Berechtigung bearbeiten', $output, $menu, 0, '96%');

	}
	else
	{
		$fields = array();

		$fields[0] = array (
				'type' => 'select',
				'description' => 'Vorhandene Rechte :',
				'name' => 'auswahl',
				'size' => '15',
				'value' => array()
					);

		for ($i = 0; $i < count($RIGHTS); $i++)
		{
 			$fields[0][value][$i][value] = $RIGHTS[$i][right];
			$fields[0][value][$i][description] .= $RIGHTS[$i][description];
		}

		$fields[] = array (
				'type' => 'hidden',
				'hidden_fields' => array(
						'0' => array(
								'value' => 'administration_rights',
								'name' => 'action'
							)
					)
			);

		$fields[] = array (
				'type' => 'submit',
				'value' => 'Bearbeiten',
				'name' => 'submit',
				'important' => '0',
			);



		$form = new Form($PHP_SELF, 'get', $fields);
		$LAST_FORM = $form->pre_process_form();

		$message = $form->build_form();

		echo $page->dialog_box('Zugangsrechte', $message, 0, 0, '50%');
	}

?>

