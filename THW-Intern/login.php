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
	require_once('inc/classes/class_session.inc.php');

	$log = new Log();

	$db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$db->debug(0);

	$page = new Interface('default.css');

		if ($submit)
		{
			if ($name)
			{

				$sql = '
						select
							id,
							password
						from
							' . TB_USER . '
						where
							name = "' . $name . '"
					';
				$raw_data = $db->query($sql);

				if ($db->num_rows($raw_data))
				{
					$data = $db->fetch_array($raw_data);

					if ($password)
					{
						if ($password == $data[password])
						{
							// User erfolgreich angemeldet!
							$session = new Session();
							$user_data = $session->start_session($data[id]);
							session_register('user_data');

							// Rechtemanagement initialisieren!
							// $rights = new Rights();

							// Rechte des Users laden!
							// $ALL_RIGHTS = $rights->load_rights();
							// und in der Session speichern!
							// session_register('ALL_RIGHTS');

							$log->add_to_log('session', 'User ' . $name . ' logged in...');

							header("Location: intern.php");
						}
						else
						{
							// Falsches Passwort!!!!!!!!!
							$message = urlencode('Kombination aus Name und Passwort stimmt nicht!');
							header("Location: $PHP_SELF?message=$message&name=$name");
						}
					}
					else
					{
						// Kein Passwort eingegeben!
						$message = urlencode('Bitte auch ein Passwort eingeben!');
						header("Location: $PHP_SELF?message=$message&name=$name");
					}
				}
				else
				{
					// Unbekannter user!
					$message = urlencode('Whoups, diesen User gibt es nicht!');
					header("Location: $PHP_SELF?message=$message");
				}
			}
			else
			{
				// Submit gedrückt aber keinen Namen eingegeben.... ZONK!
				$message = urlencode('Bitte die Felder ausfüllen!');
				header("Location: $PHP_SELF?message=$message");
			}

		}
		else
		{
			$output ='
			  <script language="javascript" src="inc/javascript/jslib-forms.js"></script>
				<form action=' . $PHP_SELF . ' method=post name=firstform>
					<table width=100% align=center border=0>
						<tr>
							<td colspan=2 align=center class=error>
								' . $message . '
							</td>
						</tr>
						<tr>
							<td width=50% align=right>
								Name
							</td>
							<td>
								<input type=text name=name size=15 value="' . $name . '" onBlur="setAllcapchar(this,true,true,\'normal\',false)">
							</td>
						</tr>
						<tr>
							<td width=50% align=right>
								Passwort
							</td>
							<td>
								<input type=password name=password size=10>
							</td>
						</tr>
						<tr>
							<td align=center colspan=2>
								&nbsp;
								<input type=submit name=submit value="Anmelden">
							</td>
						</tr>
					</table>
				</form>
				<!-- JavaScript um Focus auf erstes Fomularelement zu setzen -->
				<script language="javascript">
					<!-- Dieser Kommentar verhindert das abspacken bei älteren Browsern
						firstform.elements[0].focus();
						firstform.elements[0].select();
					//-->
				</script>
				';
		}

	$menu = array();
	$menu[0][link] = 'forgotten.php';
	$menu[0][text] = 'Passwort vergessen?';
	$menu[1][link] = 'info.php';
	$menu[1][text] = 'Credits';

	$page->html_header();
	$page->title_bar();

	echo $page->dialog_box('Login', $output, $menu, 0,  '50%');

	$page->html_footer();

?>
