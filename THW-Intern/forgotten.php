<?php
	/***************************************************************************
	* Laden der Einstellungen
	***************************************************************************/
	require_once("etc/tables.inc.php");
	require_once("etc/names.inc.php");
	require_once("etc/menu.inc.php");
	require_once("etc/database.inc.php");

	/***************************************************************************
	* Laden der wichtigsten Bibliotheken und Klassen
	***************************************************************************/
	require_once("inc/classes/classes.inc.php");
	require_once('inc/classes/class_log.inc.php');
	require_once('inc/classes/class_database.inc.php');
// 	require_once('inc/classes/class_session.inc.php');

	$log = new Log();

	$db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$db->debug(0);

	$page = new Interface('default.css');
	$page->html_header();
	$page->title_bar();

	if ($submit and $name)
	{
		// Zuerst prüfen wir ob es diesen User überhaupt gibt:
		$sql = '
				select
					*
				from
					' . TB_USER . '
				where
					name = "' . $name . '"
			';
		$current = $db->query($sql);

		if ($db->num_rows($current))
		{
			$current = $db->fetch_array($current);
			// Ok, diesen User gibt es! Dann schauen wir jetzt ob er ein Passwort hat!
			if ($current[password])
			{
				if ($current[email])
				{
					$menu = array();
					$menu[0][link] = 'login.php';
					$menu[0][text] = 'Zurück zum Login';
					$menu[1][link] = 'info.php';
					$menu[1][text] = 'Credits';

					$output = 'Ok, der User <b>' . $name . '</b> wurde gefunden; dein Passwort wird an <b>' . $current[email]  . '</b> geschickt!!';


							$email_message = '
Hallo ' . $current[vorname] . ' ' . $current[name] . ',
hier ist dein Login für den Internen Bereich vom THW ' . OV_NAME . '.
Login : ' . $current[name] . '
Passwort : ' . $current[password] .'

Viel Spaß,
der Admin
';
					if (mail ($current[email], 'Dein Passwort', $email_message))
					{
						$output .= '<p class=ok>Mail wurde erfolgreich verschickt!</p>';
					}
					else
					{
						$output .= '<p class=error>Urgs, beim versenden der Mail ist ein Fehler
						aufgetreten!! Bitte kontaktier einen Admin!!</p>';
					}
					echo $page->dialog_box('Fehler', $output, $menu, 0,  '50%');
				}
				else
				{
					$menu = array();
					$menu[0][link] = 'login.php';
					$menu[0][text] = 'Zurück zum Login';
					$menu[1][link] = 'info.php';
					$menu[1][text] = 'Credits';

					$output = 'Ok, der User <b>' . $name . '</b> wurde gefunden, aber es wurde keine E-Mail-Adresse hinterlegt!
								Bitte kontaktiere einen Admin!';

					$bottom_menu = array();
					$bottom_menu[0][link] = $PHP_SELF;
					$bottom_menu[0][text] = '&lt;&lt;Nochmal versuchen';

					echo $page->dialog_box('Fehler', $output, $menu, $bottom_menu,  '50%');
				}
			}
			else
			{
				$menu = array();
				$menu[0][link] = 'login.php';
				$menu[0][text] = 'Zurück zum Login';
				$menu[1][link] = 'info.php';
				$menu[1][text] = 'Credits';

				$output = 'Ok, der User <b>' . $name . '</b> wurde gefunden, aber er hat kein Passwort!
							Bitte kontaktiere einen Admin!';

				$bottom_menu = array();
				$bottom_menu[0][link] = $PHP_SELF;
				$bottom_menu[0][text] = '&lt;&lt;Nochmal versuchen';

				echo $page->dialog_box('Fehler', $output, $menu, $bottom_menu,  '50%');
			}
		}
		else
		{
			$menu = array();
			$menu[0][link] = 'login.php';
			$menu[0][text] = 'Zurück zum Login';
			$menu[1][link] = 'info.php';
			$menu[1][text] = 'Credits';

			$output = 'Whoups! Dieser User existiert in der Datenbank nicht! Evtl. hast du
					deinen Namen falsch geschrieben. Möglicherweise wurde auch noch kein
					Account für dich angelegt!';

			$bottom_menu = array();
			$bottom_menu[0][link] = $PHP_SELF;
			$bottom_menu[0][text] = '&lt;&lt;Nochmal versuchen';

			echo $page->dialog_box('Fehler', $output, $menu, $bottom_menu,  '50%');
		}
	}
	else
	{

		$menu = array();
		$menu[0][link] = 'login.php';
		$menu[0][text] = 'Zurück zum Login';
		$menu[1][link] = 'info.php';
		$menu[1][text] = 'Credits';

		$output = 'Passwort vergessen? Kein Problem, gib hier deinen
		Login-Namen (üblicherweise dein Nachname) ein und drücke auf "Weiter"!

		<script language="javascript" src="inc/javascript/jslib-forms.js"></script>
		<form action=' . $PHP_SELF . ' method=get>
			<table width=50% align=center class=list_table>
				<tr>
					<td class=list_table_active align=center>
						<b>Name:</b>
					</td>
				</tr>
				<tr>
					<td class=list_table_active align=center>
						<input type=text size=20 name=name onBlur=setAllcapchar(this,true,true)>
					</td>
				</tr>
				<tr>
					<td class=list_table_active align=right>
						<input type=submit name=submit value="Weiter &gt;&gt;">
					</td>
				</tr>
			</table>
		</form>
		</p>

		';

		echo $page->dialog_box('Passwort vergessen?', $output, $menu, 0,  '50%');
	}

	$page->html_footer();

?>
