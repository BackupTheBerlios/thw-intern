<?php

	$page->title_bar();


		if ($add_user)
		{
			$rights->add_right($auswahl, $right);
		}

		if ($remove_user)
		{
			$rights->remove_right($auswahl, $right);
		}

		$sql = '
				select
					' . TB_USER . '.*,
					date_format(' . TB_USER . '.geburtstag, "%d.%m.%Y") as geburtstag,
					date_format(' . TB_USER . '.last_action, "%d.%m.%Y %T") as last_action
				from
					' . TB_USER . '
				where
					' . TB_USER . '.id = ' . $auswahl . '
			';

		$current = $db->fetch_array($db->query($sql));

		if ($current[geburtstag] == '00.00.0000')
		{
			$current[geburtstag] = 'n/a';
		}

		$menu = array();
		$menu[0][text] = 'Zurück zur Benutzerverwaltung';
		$menu[0][link] = $PHP_SELF . '?action=administration_user';
		$menu[1][text] = 'Neuen User anlegen';
		$menu[1][link] = $PHP_SELF . '?action=administration_user_create';
		$menu[2][text] = 'User editieren';
		$menu[2][link] = $PHP_SELF . '?action=administration_user_edit&id=' . $auswahl . ' ';
		$menu[3][text] = 'User löschen';
		$menu[3][link] = $PHP_SELF . '?action=administration_user_delete&id=' . $auswahl . ' ';

		$message = '
			<table width=100% align=center border=0 cellspacing=1 cellpadding=1>
				<tr>
					<td align=right>
						Rechte zuweisen:
					</td>
					<td>
						<form action=' . $PHP_SELF . ' method=get>
							<select name=right size=1>
					';

			for ($i = 0; $i < count($RIGHTS); $i++)
			{
				$message .= '
								<option value=' . $RIGHTS[$i][right] . '>' . $RIGHTS[$i][description] . '</option>
					';
			}

			$message .= '
							</select>
							<input type=hidden name=action value="administration_user_view">
							<input type=hidden name=auswahl value=' . $auswahl . '>
							<input type=submit name=add_user value="Hinzufügen">
						</form>
					</td>
				</tr>
				';

		if ($current[rights])
		{
			$message .= '
				<tr>
					<td align=right>
						Zugewiesene Rechte:
					</td>
					<td>
						<form action=' . $PHP_SELF . ' method=get>
							<select name=right size=5>
					';

				if ($current[rights] == ROOT)
				{
							$message .= '
									<option value=' . ROOT . '>root</option>
								';
				}
				else
				{
					$user_rights = $rights->users_rights($current[rights]);

					for ($i = 0; $i < count($user_rights); $i++)
					{
							$message .= '
										<option value=' . $user_rights[$i][right] . '>' . $user_rights[$i][description] . '</option>
								';
					}
				}

			$message .= '


							</select>
							<input type=hidden name=action value="administration_user_view">
							<input type=hidden name=auswahl value=' . $auswahl . '>
							<input type=submit name=remove_user value="Recht entfernen">
						</form>

					</td>
				</tr>
				';
		}

		$message .= '
			</table>
			';




		require_once('inc/classes/class_user.inc.php');

		$temp = new User();
		echo '<table width=50% align=center border=0><tr><td>' . $temp->view_user_interface($auswahl, 0, $menu) . '</td></tr></table>';


		echo $page->dialog_box('Benutzerverwaltung - Rechte', $message, 0, 0, '50%');
?>
