<?php
/*******************************************************************************
* (c) 2003 Jakob K?lzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 23.05.2003
* Last edit: 28.07.2003
*
* setup.php
*
* Funktion:
*
* Bemerkungen:
*
* TODO:
*
* DONE:
* - Installation der Link-Tabellen einbauen!
*
*******************************************************************************/

	/***************************************************************************
	* Laden der Einstellungen
	***************************************************************************/
	require_once("etc/tables.inc.php");
	require_once("etc/names.inc.php");
	require_once("etc/database.inc.php");

	/***************************************************************************
	* Laden der wichtigsten Bibliotheken und Klassen
	***************************************************************************/
	require_once("inc/classes/classes.inc.php");
// 	require_once('inc/classes/class_lister.inc.php');
	require_once('inc/classes/class_log.inc.php');
// 	require_once('inc/classes/class_tb.inc.php');
	require_once('inc/classes/class_database.inc.php');
	require_once("inc/classes/tb_classes.inc.php");

	/***************************************************************************
	* Locale setzen, damit strftime deutsche Wochentage ausgibt!
	* FUNKTIONIERT NICHT!! BUG IN PHP??
	***************************************************************************/
	setlocale("LC_TIME", 'DE');

	/***************************************************************************
	* Log-Objekt
	***************************************************************************/
	$log = new Log();

	/***************************************************************************
	* Datenbankobjekt
	***************************************************************************/
	$db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$db->debug(0);

	$page = new Interface('default.css');
	$page->html_header();

	$top_menu = array();
	$top_menu[0][text] = '?bersicht';
	$page->title_bar($top_menu, 'THW-Intern: Setup');


		/***************************************************************************
		* Pr?fen ob die Tabellen evtl. schon bestehen
		***************************************************************************/

	$databases = array();
	$databases[tb_bericht] = '
			CREATE TABLE tb_bericht (
				id int(11) NOT NULL auto_increment,
				ref_object_id int(11) NOT NULL default \'0\',
				text text NOT NULL,
				titel varchar(250) NOT NULL default \'\',
				berichtart int(11) NOT NULL default \'0\',
				flag_freigegeben tinyint(4) NOT NULL default \'0\',
				PRIMARY KEY  (id),
				KEY ref_object_id (ref_object_id,berichtart,flag_freigegeben)
			) TYPE=MyISAM
			';

	$databases[tb_foren] = '
				CREATE TABLE tb_foren (
					id int(11) NOT NULL auto_increment,
					ref_object_id int(11) NOT NULL default \'0\',
					name varchar(250) NOT NULL default \'\',
					beschreibung tinytext NOT NULL,
					ref_user_id int(11) NOT NULL default \'0\',
					PRIMARY KEY  (id)
				) TYPE=MyISAM
			';

	$databases[tb_forenbeitrag] = '
				CREATE TABLE tb_forenbeitrag (
					id int(11) NOT NULL auto_increment,
					ref_user_id int(11) NOT NULL default \'0\',
					ref_foren_id int(11) NOT NULL default \'0\',
					ref_forenbeitrag_id int(11) NOT NULL default \'0\',
					titel varchar(250) NOT NULL default \'\',
					beitrag text NOT NULL,
					date_create datetime NOT NULL default \'0000-00-00 00:00:00\',
					date_lastviewed datetime NOT NULL default \'0000-00-00 00:00:00\',
					counter int(11) NOT NULL default \'0\',
					PRIMARY KEY  (id),
					KEY ref_foren_id (ref_foren_id,ref_forenbeitrag_id),
					KEY date_create (date_create,date_lastviewed)
				) TYPE=MyISAM
			';
	$databases[tb_messages] = '	
                	CREATE TABLE tb_messages (
                      id int(11) NOT NULL auto_increment,
                      tb_messages.from int(11) NOT NULL default \'0\',
                      tb_messages.to int(11) NOT NULL default \'0\',
                      subject varchar(200) NOT NULL default \'\',
                      message text NOT NULL,
                      type tinyint(4) NOT NULL default \'0\',
                      date timestamp(14) NOT NULL,
                      flags int(11) NOT NULL default \'0\',
                      PRIMARY KEY  (id),
                      KEY type (type),
                      KEY date (date),
                      KEY flags (flags)
                    ) TYPE=MyISAM
			';
	
	$databases[tb_guestbook] = ' 
				CREATE TABLE tb_guestbook (
  					id int(11) NOT NULL auto_increment,
  					name varchar(140) NOT NULL default \'anonym\',
  					date datetime NOT NULL default \'0000-00-00 00:00:00\',
  					message text NOT NULL,
  					email varchar(150) NOT NULL default \'\',
  					homepage varchar(150) NOT NULL default \'\',
  					ip varchar(15) NOT NULL default \'0\',
  					PRIMARY KEY  (name,date),
  					KEY id (id)
				) TYPE=MyISAM
			';			

	$databases[tb_helferliste] = '
				CREATE TABLE tb_helferliste (
					id int(11) NOT NULL auto_increment,
					ref_object_id int(11) NOT NULL default \'0\',
					deadline datetime NOT NULL default \'0000-00-00 00:00:00\',
					flag_open tinyint(4) NOT NULL default \'1\',
					disclaimer text NOT NULL,
					slots mediumint(9) NOT NULL default \'0\',
					flag_hidden tinyint(4) NOT NULL default \'0\',
					flag_comment tinyint(4) NOT NULL default \'0\',
					flag_funktion tinyint(4) NOT NULL default \'0\',
					flag_autojoin tinyint(4) NOT NULL default \'0\',
					PRIMARY KEY  (id,ref_object_id),
					KEY ref_object_id (ref_object_id,deadline,slots),
					KEY flag_hidden (flag_hidden)
				) TYPE=MyISAM
			';

	$databases[tb_helferlisteneintrag] = '
			CREATE TABLE tb_helferlisteneintrag (
				id int(11) NOT NULL auto_increment,
				ref_object_id int(11) NOT NULL default \'0\',
				ref_user_id int(11) NOT NULL default \'0\',
				flag_dgl tinyint(4) NOT NULL default \'0\',
				flag_drin tinyint(4) NOT NULL default \'1\',
				kommentar varchar(250) NOT NULL default \'\',
				funktion tinyint(4) NOT NULL default \'0\',
				PRIMARY KEY  (ref_object_id,ref_user_id,ref_einheit_id),
				KEY ref_helferliste_id (ref_object_id,ref_user_id,flag_drin),
				KEY id (id),
				KEY ref_einheit_id (ref_einheit_id)
			) TYPE=MyISAM
			';

	$databases[tb_links] = '
			CREATE TABLE tb_links (
				id int(11) NOT NULL auto_increment,
				ref_object_id int(11) NOT NULL default \'0\',
				description varchar(255) NOT NULL default \'\',
				counter int(11) NOT NULL default \'0\',
				uri varchar(255) NOT NULL default \'\',
				ref_category_id int(11) NOT NULL default \'1\',
				PRIMARY KEY  (id),
				UNIQUE KEY uri (uri),
				UNIQUE KEY ref_object_id (ref_object_id),
				KEY ref_category_id (ref_category_id)
			) TYPE=MyISAM
			';
			
	$databases[tb_object] = '
				CREATE TABLE tb_object (
					id int(11) NOT NULL auto_increment,
					ref_user_id int(11) NOT NULL default \'0\',
					date_lastchange timestamp(14) NOT NULL,
					date_create timestamp(14) NOT NULL,
					date_begin datetime NOT NULL default \'0000-00-00 00:00:00\',
					date_end datetime NOT NULL default \'0000-00-00 00:00:00\',
					flag_public tinyint(4) NOT NULL default \'0\',
					last_edited_by int(11) NOT NULL default \'0\',
					PRIMARY KEY  (id),
					KEY ref_user_id (ref_user_id,date_create,date_begin,date_end,flag_public),
					KEY date_lastchange (date_lastchange)
				) TYPE=MyISAM
			';

	$databases[tb_photos] = '
				CREATE TABLE tb_photos (
					id int(11) NOT NULL auto_increment,
					ref_bericht_id int(11) NOT NULL default \'0\',
					kommentar varchar(250) NOT NULL default \'\',
					priority tinyint(4) NOT NULL default \'0\',
					PRIMARY KEY  (id),
					KEY ref_bericht_id (ref_bericht_id),
					KEY priority (priority)
				) TYPE=MyISAM
			';
	$databases[tb_termin] = '
				CREATE TABLE tb_termin (
					id int(11) NOT NULL auto_increment,
					ref_object_id int(11) NOT NULL default \'0\',
					terminart int(11) NOT NULL default \'0\',
					kommentar text NOT NULL,
					flag_edit tinyint(4) NOT NULL default \'0\',
					flag_kueche tinyint(4) NOT NULL default \'0\',
					kleidung tinyint(4) NOT NULL default \'1\',
					PRIMARY KEY  (id),
					KEY ref_object_id (ref_object_id,terminart),
					KEY flag_kueche (flag_kueche)
				) TYPE=MyISAM
			';
	$databases[tb_user] = '
				CREATE TABLE tb_user (
					id int(11) NOT NULL auto_increment,
					name varchar(30) NOT NULL default \'\',
					vorname varchar(30) NOT NULL default \'\',
					password varchar(15) NOT NULL default \'\',
					email varchar(50) NOT NULL default \'\',
					last_action timestamp(14) NOT NULL,
					last_login timestamp(14) NOT NULL,
					ref_einheit_id int(11) NOT NULL default \'0\',
					flag_online tinyint(4) NOT NULL default \'0\',
					flag_active tinyint(4) NOT NULL default \'1\',
					geburtstag date NOT NULL default \'0000-00-00\',
					rang smallint(6) NOT NULL default \'1\',
					rights int(11) NOT NULL default \'0\',
					PRIMARY KEY  (id),
					KEY name (name,password,last_action,ref_einheit_id,flag_online)
				) TYPE=MyISAM
			';


	$message = '';
	switch($action)
	{
		case 'user_save':

			if ($pw)
			{
				if ($pw == $pw_wdh)
				{
					$sql = '
							insert into
								' . TB_USER . '
							(
								name,
								vorname,
								password,
								rights
							)
							values
							(
								"' . trim($name) . '",
								"' . trim($vorname) . '",
								"' . trim($pw) . '",
								' . ROOT . '
							)
						';
					$db->query($sql);
					if ($db->affected_rows())
					{
						$bottom_menu = array();
						$bottom_menu[0][text] = 'Zum Login&gt;&gt;';
						$bottom_menu[0][link] = 'login.php';

						echo $page->dialog_box('Root-User anlegen', 'Der User wurde angelegt!! Sie k?nnen sich jetzt mit dem Namen und dem Passwort des gerade angelgeten Users anmelden!', 0, $bottom_menu, '50%');
					}
					else
					{
						echo $page->dialog_box('Root-User anlegen', 'Whoups!! Da ist was schiefgelaufen!! Was nu?', 0, $bottom_menu, '50%');
					}
					break;
				}
				$message = '<b class=error>Die Passw?rter stimmen nicht ?berein!</b><br>';
			}
			else
			{
				$message = '<b class=error>Es wurde kein Passwort eingegeben!</b><br>';
			}


		case 'user_create':
				$bottom_menu = array();
				$bottom_menu[0][text] = '&lt;&lt;?bersicht';
				$bottom_menu[0][link] = $PHP_SELF;

				$message .= '
					Bitte hier Namen, Vornamen und das Passwort des Root-Users eingeben!! Weitere User können dann im internen Bereich in der Administration angelegt werden!

					<form action="' . $PHP_SELF . '" method=get>
						<table width=100% align=center border=0>
							<tr class=list_table_active>
								<td align=right>
									Name:
								</td>
								<td>
									<input type=text size=20 name=name value="' . $name . '">
								</td>
							</tr>
							<tr class=list_table_active>
								<td align=right>
									Vorname:
								</td>
								<td>
									<input type=text size=20 name=vorname value="' . $vorname . '">
								</td>
							</tr>
							<tr class=list_table_active>
								<td align=right>
									Passwort:
								</td>
								<td>
									<input type=password size=20 name=pw>
								</td>
							</tr>
							<tr class=list_table_active>
								<td align=right>
									Passwort wdh.:
								</td>
								<td>
									<input type=password size=20 name=pw_wdh>
								</td>
							</tr>
							<tr class=list_table_active>
								<td align=right colspan=2>
									<input type=hidden name="action" value="user_save">
									<input type=submit name=submit value="Los&gt;&gt;">
									<input type=reset>
								</td>
							</tr>
						</table>
					</form>
					';

				echo $page->dialog_box('Root-User anlegen', $message, 0, $bottom_menu, '50%');
			break;

		case 'confirm_all':
				$bottom_menu = array();
				$bottom_menu[0][text] = '&lt;&lt;URGS! NEIN!!';
				$bottom_menu[0][link] = $PHP_SELF;
				$bottom_menu[1][text] = 'OK, installieren&gt;&gt;';
				$bottom_menu[1][link] = $PHP_SELF . '?action=install&all=1';

				echo $page->dialog_box('Bestätigen', 'Sind Sie sicher das <b>ALLE</b> Tabellen erneut installiert werden sollen? Dieser Vorgang l?scht <b>ALLE</b> bis jetzt gespeicherten Daten!!', 0, $bottom_menu, '50%');
			break;

		case 'install':

				$message = 'Die Tabellen werden jetzt installiert:
							<table widtH=100% align=center border=0>';
				/***************************************************************************
				* Tabellen installieren:
				***************************************************************************/
				while ($current = each($databases))
				{
					$message .= '<tr class=list_table_active><td>' .  $current[key] . '</td><td>';
					$sql = 0;
					if (count($selection))
					{
						if (in_array($current[key], $selection))
						{
							$sql = 'DROP TABLE IF EXISTS ' . $current[key];
							$db->query($sql);
							$sql = $current[value];

							if ($db->query($sql) == FALSE)
							{
								echo nl2br($sql);
								$class = '<b class="error">error</b>';
							}
							else
							{
								$class = '<b class="ok">ok</b>';
							}
						}
						else
						{
							$class = '<b>skipped</b>';
						}
					}
					else if ($all)
					{
						$sql = 'DROP TABLE IF EXISTS ' . $current[key];
						$db->query($sql);
						$sql = $current[value];

						if ($db->query($sql) == FALSE)
						{
							$class = '<b class="error">error</b>';
						}
						else
						{
							$class = '<b class="ok">ok</b>';
						}
					}

     				$message .= $class . '</td></tr>';
				}

				$bottom_menu = array();
				$bottom_menu[0][text] = '?bersicht';
				$bottom_menu[0][link] = $PHP_SELF;
				$bottom_menu[1][text] = 'User anlegen&gt;&gt;';
				$bottom_menu[1][link] = $PHP_SELF . '?action=user_create';

				$message .= '
							</table>';
				echo $page->dialog_box('Tabellen installieren', $message, 0, $bottom_menu, '50%');
			break;

		case 'check_database':
				/***************************************************************************
				* Jetzt holen wir zu erst einmal alle vorhandenen Tabellen:
				***************************************************************************/
				$existing_tables = $db->list_tables(DB_DATABASE);
				$num_existing_tables = $db->num_rows($existing_tables);



				$message = '
						<table width=100% align=center border=0>
							<tr>
								<td width=50% valign=top>
									<table width=100% align=center border=0>
										<th>Vorhandene Tabellen</th>';

				$found = array();
				while ($current = $db->fetch_row($existing_tables))
				{
					if ($databases[$current[0]])
					{
						$class = 'list_table_less_important';
						$found[$current[0]] = 1;
					}
					else
					{
						$class = 'list_table_active';
					}
					$message .= '
										<tr class=' . $class . '>
											<td>
												' . $current[0] . '
											</td>
										</tr>';
				}

				$message .= '
									</table>
								</td>
								<td widtH=50% valign=top>
									<form action="' . $PHP_SELF . '" method=get>
									<table width=100% align=center border=0>
										<tr>
											<th>Ben?tigte Tabellen</th>';

				while ($current = each($databases))
				{

					if ($found[$current[key]])
					{
						$class = 'list_table_active';
					}
					else
					{
						$class = 'list_table_important';
					}
					$message .= '
										<tr class=' . $class . '>
											<td>
												<input type="checkbox" name="selection[]" value="' . $current[key] . '" title="Hier klicken um diese Datenbank zu installieren!">
												' . $current[key] . '
											</td>
										</tr>';
				}

				$message .= '
										<tr>
											<td>
												Bitte alle Tabellen ausw?hlen welche installiert werden sollen! Eine Installation ?berschreibt eine Tabelle vollst?ndig!!
											</td>
										</tr>
										<tr class="list_table_active">
											<td align="right">
												<input type=hidden name="action" value="install">
												<input type=submit name="submit" value="Los&gt;&gt;">
												<input type=reset>
											</td>
										</tr>
									</table>
									<form>
								</td>
							</tr>
						</table>';
				echo $page->dialog_box('Datenbank pr?fen', $message, 0, $bottom_menu, '70%');
			break;

		default:

			$message = 'Dieses Script installiert die f?r THW-Intern n?tigen Tabellen in der ausgew?hlten Datenbank:
				<table align=center border=0 class="list_table" width=50%>
					<tr class=list_table_active>
						<td>Host:</td><td><B>' . DB_HOST . '</b></td>
					</tr>
					<tr class=list_table_active>
						<td>User:</td><td><B>' . DB_USER . '</b></td>
					</tr>
					<tr class=list_table_active>
						<td>Datenbank:</td><td><B>' . DB_DATABASE . '</b></td>
					</tr>
				</table>
				<b>Bitte stellen Sie sicher dass dies die richtige Datenbank ist!!!<br></b>
				Klicken Sie auf "<b>Datenbank pr?fen</b>" um zu ?berpr?fen ob die Tabellen evtl. schon exisiteren oder
				auf "<b>Jetzt erstellen</b>" um die Datenbanken ohne R?cksicht auf Verluste zu erstellen!
				Mit "<b>Datenbank pr?fen</b>" erhalten Sie die M?glichkeit nur ausgew?hlte Datenbank neu zu erstellen!
			';
			$bottom_menu = array();
			$bottom_menu[0][text] = 'Datenbank pr?fen';
			$bottom_menu[0][link] = $PHP_SELF . '?action=check_database';
			$bottom_menu[1][text] = 'Jetzt installieren';
			$bottom_menu[1][link] = $PHP_SELF . '?action=confirm_all';

			echo $page->dialog_box('Info', $message, 0, $bottom_menu, '50%');
	}

	$page->html_footer();

	/***************************************************************************
	* Aufr?umen!!
	* Wird hoffentlich bald durch Destruktoren erledigt!
	***************************************************************************/
	$log->shutdown();
?>


