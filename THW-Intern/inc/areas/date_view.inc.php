<?php

	$page->title_bar();


	$termin = new tb_termin();
	if ($termin->load_tb_termin($id))
	{


		if ($termin->return_field('flag_kueche'))
		{
			$temp = 'ja';
		}
		else
		{
			$temp = 'nein';
		}

		$message = '
				<table width=100% align=center border=0>
					<tr>
						<td width=30% class=list_table_active>
							Terminart:
						</td>
						<td>
							<b>' . $DATE_TYPES[$termin->return_field('terminart')][name] . '</b>
						</td>
					</tr>
					<tr>
						<td width=30% class=list_table_active>
							Datum:
						</td>
						<td>
							<b>' . $termin->return_field('date_begin_readable') . '</b> - <b>' . $termin->return_field('date_end_readable') . '</b>
						</td>
					</tr>
					<tr>
						<td width=30% class=list_table_active>
							Kommentar:
						</td>
						<td>
							' . nl2br($termin->return_field('kommentar')) . '
						</td>
					</tr>
					<tr>
						<td width=30% class=list_table_active>
							Küchenrelevant:
						</td>
						<td>
							' . $temp . '
						</td>
					</tr>
					<tr>
						<td width=30% class=list_table_active>
							Erstellt:
						</td>
						<td>
							von <b>' . $termin->return_field('vorname') . ' ' . $termin->return_field('name') . '</b>, am <b>' . $termin->return_field('date_create_readable') . '</b>
						</td>
					</tr>
					';

		if ($termin->return_field('date_create') != $termin->return_field('date_lastchange'))
		{
			$message .= '
					<tr>
						<td class=list_table_active>
							editiert:
						</td>
						<td>
							<b>' . $termin->return_field('date_lastchange_readable') . '</b>
						</td>
					</tr>
				';
		}

		// Sind wir berechtigt bei diesem Dienst User hinzuzufügen, zu ändern oder zu entfernen?
		if ( ($termin->return_field('ref_user_id') == $session->user_info('id')) or ($session->user_info('rights') == ROOT))
		{
			$is_user_admin = 1;
		}

		$message .= '

				</table>
			';
	}
	else
	{
		die('Whoups! Irgendwas ist beim laden der Termindaten schiefgelaufen!');
	}

	require_once('inc/classes/class_helferliste.inc.php');

	$new_list = new tb_helferliste2($id);
	$temp = $new_list->view_userlist_interface($id);

	$menu = array();
	$menu[0][link] = $PHP_SELF . '?action=date_edit&id=' . $id;
	$menu[0][text] = 'Termin editieren';
	$menu[1][link] = $PHP_SELF . '?action=date_delete&id=' . $id;
	$menu[1][text] = 'Termin löschen';

	if (!$temp)
	{
		$menu[2][link] = $PHP_SELF . '?action=userlist_create&type=date&id=' . $id;
		$menu[2][text] = 'Userliste anhängen';
	}

	echo $page->dialog_box('Termin betrachten', $message, $menu, 0, '70%');

 	echo '<br><table width=70% align=center border=0><tr><td>' . $temp . '</td></tr></table>';

?>

