<?php

	$page->title_bar();

	$menu = array();
	$menu[0][link] = $PHP_SELF . '?action=administration_forum';
	$menu[0][text] = 'Forenadmin.';
	$menu[1][link] = $PHP_SELF . '?action=administration_forum&forum_create=1';
	$menu[1][text] = 'Neues Forum anlegen';

	/***************************************************************************
	* Wir wollen ein neues Forum anlegen, hier ist der dialog dazu...
	***************************************************************************/
	if ($forum_create)
	{
		$titel = 'Foren administrieren - Forum anlegen';

		$message = '
				<form action="' . $PHP_SELF . '" method=get>
					<table align=center border=0 class=list_table width=100%>
						<tr>
							<td  class=list_table_important align=right>
								Name des Forums:
							</td>
							<td>
								<input type=text name=forum_name size=20 value="' . $forum_name . '">
							</td>
						</tr>
						<tr>
							<td  class=list_table_active align=right>
								Beschreibung:
							</td>
							<td>
								<textarea cols=30 rows=3 name="forum_description">' . $forum_description . '</textarea>
							</td>
						</tr>
						<tr>
							<td  class=list_table_important align=right>
								Administrator:
							</td>
							<td>
								<select size=1 name=forum_admin>
					';
		$sql = '
				select
					id,
					name,
					vorname
				from
					' . TB_USER . '
				order by
					name
			';
		$raw_users = $db->query($sql);

		if (!$db->num_rows($raw_users))
		{
			die('Datenbankfehler! Konnte keine User in der Userdatenbank finden, breche ab... *urgs*');
		}
		while ($tmp = $db->fetch_array($raw_users))
		{
			$message .= '
									<option value="' . $tmp[id] . '">' . $tmp[name] . ' ' . $tmp[vorname] . '</option>';
		}

		$message .= '
								</select>
							</td>
						</tr>
						<tr class=list_table_active>
							<td colspan=2 align=right>
								<input type=hidden name=action value="' . $action . '">
								<input type=hidden name=forum_add value="1">
								<input type=reset value="Felder zurücksetzen">
								<input type=submit name=forum_add value="Forum anlegen&gt;&gt;">
							</td>
						</tr>
					</table>
				</form>

			';
	}
	/***************************************************************************
	* Jetzt speichern wir ein neues Forum ab...
	***************************************************************************/
	else if ($forum_add)
	{
		$sql = '
				insert into
					' . TB_FOREN . '
				(
					ref_user_id,
					name,
					beschreibung
				)
				values
				(
					' . $forum_admin . ',
					"' . htmlentities($forum_name) . '",
					"' . htmlentities($forum_description) . '"
				)
			';
		// echo $sql;
		$db->query($sql);
	}

	/***************************************************************************
	* Nix ausgewählt, also einfach mal die Übersicht...
	***************************************************************************/
	else
	{

		$sql = '
				select
					' . TB_FOREN . '.name as forum_name,
					' . TB_FOREN . '.beschreibung,
					' . TB_FOREN . '.id,
					' . TB_USER . '.name,
					' . TB_USER . '.vorname
				from
					' . TB_FOREN . ',
					' . TB_USER . '
				where
					' . TB_USER . '.id = ' . TB_FOREN . '.ref_user_id
			';

		// echo $sql;

		$raw = $db->query($sql);
		if ($db->num_rows($raw))
		{
			$message ='
					Vorhandene Foren:
						<table width=100% align=center border=0 class=list_table cellspacing=4>
							<th>Forum</th><th>Beschreibung</th><th>Admin</th><th width=10%>Aktionen</th>
				';
			while ($tmp = $db->fetch_array($raw))
			{
				$message .= '
							<tr class=list_table_active>
								<td>
									' . $tmp[forum_name] . '
								</td>
								<td>
									' . $tmp[beschreibung] . '
								</td>
								<td>
									' . $tmp[name] . ', ' . $tmp[vorname] . '
								</td>
								<td align=center>
									<a href="" title="Hier klicken um das Forum ' . $tmp[forum_name] . ' zu löschen!"><b  class=error>X</b></a>
									<a href="" title="Hier klicken um das Forum ' . $tmp[forum_name] . ' zu editieren!"><b  class=important style="font-weight: bold;">E</b></a>
								</td>
							</tr>
					';
			}

			$message .= '
						</table>
				';
		}
		else
		{
			$message = '
					Momentan keine Foren vorhanden...
				';
		}

		$titel = 'Foren administrieren';
	}

	echo $page->dialog_box($titel, $message, $menu, 0, '70%');

?>
