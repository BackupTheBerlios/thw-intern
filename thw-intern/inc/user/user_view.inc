<?php

function unit_string($db, $id, $string)
{
	$sql = "select name, ref_id from " . DB_UNITS . " where id=$id";
	//  echo $sql;
	$current = $db->fetch_array($db->query($sql));

	$string = $current[name] . ', ' .  $string;

	if ($current[ref_id])
	{
		$string = unit_string($db, $current[ref_id], $string);
	}
	return($string);
}


			$User = new Page('User betrachten', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$User->html_header();

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=user";
			$menu[1][text] = 'Userverwaltung';
			$menu[2][link] = "$PHP_SELF?area=user_edit&id=$id";
			$menu[2][text] = 'Diesen Account editieren';
			$menu[3][link] = "$PHP_SELF?area=user_delete&id=$id";
			$menu[3][text] = 'User löschen';

			$User->pagetitle('User betrachten', $menu);
			echo '<br>';

			$sql = "select *, unix_timestamp(geburtstag) as birth from users where id=$id";

			$tmp = $db->query($sql);

			if ($db->num_rows($tmp))
			{
				echo '<table width=98% align=center>';
				$result = $db->fetch_array($tmp);
				echo "<tr><td width=30% align=right>Loginname : </td><td><b>$result[login_name]</td></b></tr>";
				echo "<tr><td align=right>Vorname : </td><td><b>$result[v_name]</td></b></tr>";
				echo "<tr><td align=right>Nachname : </td><td><b>$result[n_name]</td></b></tr>";
				echo "<tr><td align=right>Geburtstag : </td><td><b>";
					if ($result[birth] > 0)
					{
						echo strftime("%e.%m.%Y" ,$result[birth]);
					}
					echo  "</td></b></tr>";

				echo '<tr><td><br></td></tr>';

				echo "<tr><td align=right>Email : </td><td><a href='mailto:$result[email]' class=blue>$result[email]</a></td></tr>";
				echo "<tr><td align=right>Homepage : </td><td><a href='$result[homepage]' class=blue>$result[homepage]</a></td></tr>";

				if (!$result[icq])
				{
					$icq = '-';
				}
				else
				{
					$icq = $result[icq];
				}
				echo "<tr><td align=right>ICQ : </td><td><b>$icq</b></td></tr>";

				echo '<tr><td><br></td></tr>';

				$sql = "select name from " . DB_RANG . " where rang=$result[rang]";
				$rang = $db->fetch_array($db->query($sql));
				echo "<tr><td align=right>Rang : </td><td><b>$rang[name]</b></td></tr>";
				echo "<tr><td align=right>Funktion : </td><td><b></b></td></tr>";

				$einheit = unit_string($db, $result[einheit], $string);
				echo "<tr><td align=right>Einheit : </td><td><b>$einheit</b></td></tr>";

				echo '<tr><td><br></td></tr>';
				echo "<tr><td align=right>Last action : </td><td><b>" . strftime('%H:%M:%S', $result[last_action]) . "</b></td></tr>";
				echo '</table>';
			}
			else
			{
				$options = array();
				$options[right][text] = 'Zur Userverwaltung';
				$options[right][link] = "$PHP_SELF?area=user";
				$options[left][text] = 'Zurück zur Startseite';
				$options[left][link] = "$PHP_SELF";
				$error = new InfoBox('User nicht gefunden', 'Whoups, der User mit dieser ID wurde nicht gefunden... Datenbankfehler?', $options);
			}

			$User->html_footer();
?>
