<?php

	require('etc/system.inc');			// Systemweite Einstellungen laden
	require('etc/database.inc');			// Wo ist die Datenbank, wie ist der Login dazu?
	require('etc/definitions.inc');			// Definitionen laden
	require('inc/classes.inc');			// OOP-Klassen laden
	require('inc/functions.inc');			// diverse Funktionen

	// Datenbankobjekt anlegen!
	$db = new Database();

	// Deutsches Layout für Datumsangaben
	setlocale (LC_ALL, 'de_DE');

	// Query-counter, wie viele SQL-Queries?
	$GLOBALS[query_count] = 0;

	session_name('THW-INTERN');
	session_start();

	if ($user_id)
	{
		// Den User updaten, sodass er nicht per timeout herausfliegt!
		$sql = "update " . DB_USERS . " set online = 1, last_action = " . time() . " where id = $user_id";
		$db->query($sql);
	}

	switch($area)
	{
		// Ein user möchte sich anmelden!
		case 'auth':

				if ($login)		// Der user hat etwas eingegeben -> überprüfen
				{
					if ($password)		// Hat er ein passwort eingegeben?
					{
						$sql = "select v_name, n_name, id, password, last_login from " . DB_USERS . " where login_name='$login'";

						if ($user_raw = $db->query($sql))		// Überprüfen ob es so einen User überhaupt gibt!
						{		// Jepp, diesen User gibt es!
							if (mysql_num_rows($user_raw) > 1)		// Mehrere User mit dem gleichen Login...
							{
								echo 'sorry, multiple choice not implemented yet!';
							}
							else		// Nur 1 User mit diesem Login
							{
								$current_user = mysql_fetch_array($user_raw);

								if ($current_user[password] == $password)		// Passwort überprüfen
								{
									setcookie("user_id", $current_user[id]);
									setcookie("user_password", urlencode(md5($current_user[password])));

									session_name('THW-INTERN');
									session_start();

									$user_id = $current_user[id];
									$user_v_name = $current_user[v_name];
									$user_n_name = $current_user[n_name];
									$user_last_login = $current_user[last_login];
									$user_password = md5($current_user[password]);
									$user_session_id = md5($user_n_name . $user_password . $REMOTE_ADDR);

									$sql = "update " . DB_USERS . " set last_login=" . time() . ", online=1 where id=$user_id";
									$db->query($sql);

									session_register('user_id', 'user_v_name', 'user_n_name', 'user_password', 'user_session_id', 'user_last_login');

									header("Location: $GLOBALS[PHP_SELF]");
									exit;
								}
								else		// Existierender Login aber falschen Passwort
								{
									$message = urlencode('Falsches Passwort! (<a href=index.php?area=forgotten_pass&id=' . $current_user[id] . ' class=blue>Passwort vergessen?</a>)');
									header("Location: $GLOBALS[PHP_SELF]?area=login&login=$login&message=$message");
									exit;
								}
							}
						}
						else			// Ungültiger User
						{
							$message = urlencode('Dieser User existiert nicht...');
							header("Location: $GLOBALS[PHP_SELF]?area=login&login=$login&message=$message");
							exit;
						}
					}
					else		// Der user hat einen usernamen eingegeben, aber kein Passwort
					{
						$message = urlencode('Bitte noch das Passwort eingeben!');
						header("Location: $GLOBALS[PHP_SELF]?area=login&login=$login&message=$message");
						exit;
					}
				}
				else		// Der user hat auf anmelden geklickt, aber nichts eingegeben!
				{
					$message = urlencode('Du mußt schon was eingeben...');
					header("Location: $GLOBALS[PHP_SELF]?area=login&login=$login&message=$message");
					exit;
				}

			break;

		case 'forgotten_pass':		// Ein User der sein Passwort zugestellt haben möchte!

			$LoginSeite = new Page('Vergessenes Passwort', '', 0, DEFAULT_STYLESHEET, $db, 0);

			$LoginSeite->html_header();

				// Zuerst mal die Userdaten holen!
				$sql = "select v_name, n_name, email, login_name, password from " . DB_USERS . " where id=$id";
				$current = $db->query($sql);

				if ($db->num_rows($current))
				{
					$current = $db->fetch_array($current);

					echo '<br><br>';

					$message = '<p>Hallo <b>' . $current[v_name] . ' ' . $current[n_name] . '</b>, du hast dein Passwort vergessen.';

					if ($current[email])
					{
						$message .= '<br><br>Dein Passwort wird jetzt an deine E-Mail Adresse gesandt (<b>' . $current[email] . '</b>).
							Sollte dies nicht deine E-Mail Adresse sein, so wende dich möglichst rasch an einen
							<a href="mailto:' . CONTACT_ADMIN . '?subject=Account sperren" class=blue>Administrator</a>.';

							$email_message = '
Hallo ' . $current[v_name] . ' ' . $current[n_name] . ',
hier ist dein Login für den Internen Bereich vom THW ' . OV_NAME . '.
Login : ' . $current[login_name] . '
Passwort : ' . $current[password] .'

Viel Spaß,
Der Admin
';
							if (mail ($current[email], 'Dein Passwort', $email_message))
							{
								$message .= '<br><br>Mail wurde erfolgreich verschickt!';
							}

							$menu[right][text] = 'Zurück zum Login';
							$menu[right][link] = "$PHP_SELF";
					}
					else
					{
						$message .= '<br><br>Leider hast du <b>keine</b> E-Mail Adresse in deiner Userkonfiguration angegeben,
							daher kann dir dein Passwort nicht zugestellt werden. Bitte wende ich an einen
							<a href="mailto:' . CONTACT_ADMIN . '?subject=Passwort vergessen" class=blue>Administrator</a>.';
					}
				}
				else
				{
					$message = '<p>Whoups, es wurde eine ungültige UserID übergeben...</p>';
				}

				$box = new Infobox('Passwort zustellen', $message, $menu, '50%');

			$LoginSeite->html_footer();

			break;
		case 'logoff':
			require('inc/logoff.inc');
			break;
		case 'login':

			$LoginSeite = new Page('Login', '', 0, DEFAULT_STYLESHEET, $db, 0);

			$LoginSeite->html_header();

			echo '<br><br>';
			if ($message)
			{
				$message = urldecode($message);
			}
			else
			{
				$message = 'Du bist nicht angemeldet!';
			}
			$box = new Infobox('Nicht angemeldet', $message . '<br><form action=' . $PHP_SELF . ' method=post>
										<table width=100% align=center border=0>
											<tr>
												<td align=right>Login</td><td><input type=text name=login value=' . $login . '></td>
											</tr>
											<tr>
												<td align=right>Password</td><td><input type=password name=password></td>
											</tr>
											<tr>
												<td align=center colspan=2>
													<input type=hidden name=area value=auth>
													<input type=submit value="Los">
												</td>
											</tr>
										</table>
									</form>'
											, 0);

			$LoginSeite->html_footer();
			break;

		/*
			Berichte
		*/
		case 'report':
		case 'report_overview':
			$area = 'report_overview';
			require('inc/report/report_overview.inc');
			break;
		case 'report_read':
			require('inc/report/report_read.inc');
			break;
		case 'report_add':
			require('inc/report/report_add.inc');
			break;
		case 'report_delete':
			require('inc/report/report_delete.inc');
			break;
		case 'report_edit':
			require('inc/report/report_edit.inc');
			break;

		/*
			Photos
		*/
		case 'photo':
		case 'photo_overview':
			$area = 'photo_overview';
			require('inc/photo/photo_overview.inc');
			break;
		case 'photo_viewcatalog':
			require('inc/photo/photo_viewcatalog.inc');
			break;
		case 'photo_view':
			require('inc/photo/photo_view.inc');
			break;
		case 'photo_editcatalog':
			require('inc/photo/photo_editcatalog.inc');
			break;
		case 'photo_delete':
			require('inc/photo/photo_delete.inc');
			break;

		/*
			Spruch/Bild des Monats
		*/
		case 'motm':
		case 'motm_overview':
			$area = 'motm_overview';
			require('inc/motm/motm_overview.inc');
			break;
		case 'motm_add':
			require('inc/motm/motm_add.inc');
			break;
		case 'motm_delete':
			require('inc/motm/motm_delete.inc');
			break;

		/*
			Terminverwaltung
		*/
		case 'date':
		case 'date_overview':
			$area = 'date_overview';
			require('inc/date/date_overview.inc');
			break;
		case 'date_view':
			require('inc/date/date_view.inc');
			break;
		case 'date_create':
			require('inc/date/date_create.inc');
			break;
		case 'date_list':
			echo 'Sorry, area date_list is not implemented yet...';
			break;
		case 'date_delete':
			require('inc/date/date_delete.inc');
			break;
		case 'date_edit':
			require('inc/date/date_edit.inc');
			break;

		/*
			Die Administration
		*/
		case 'admin':
		case 'admin_overview':
			$area = 'admin_overview';
			require('inc/admin/admin_overview.inc');
			break;

		case 'admin_backup':
			require('inc/admin/admin_backup.inc');
			break;
		case 'admin_permissions':
			require('inc/admin/admin_permissions.inc');
			break;
		case 'admin_permissions_add':
			require('inc/admin/admin_permissions_add.inc');
			break;
		case 'admin_permissions_edit':
			require('inc/admin/admin_permissions_edit.inc');
			break;
		case 'admin_permissions_delete':
			require('inc/admin/admin_permissions_delete.inc');
			break;
		case 'admin_units':
			echo 'Sorry, area admin_units is not implemented yet...';
			break;
		case 'admin_guestbook':
			echo 'Sorry, area admin_guestbook is not implemented yet...';
			break;

		/*
			Forum
		*/
		case 'forum':
		case 'forum_overview':
			$area = 'forum_overview';
			require('inc/forum/forum_overview.inc');
			break;
		case 'forum_view':
			require('inc/forum/forum_view.inc');
			break;
		case 'forum_read':
			require('inc/forum/forum_read.inc');
			break;
		case 'forum_createforum':
			require('inc/forum/forum_createforum.inc');
			break;
		case 'forum_createmessage':
			require('inc/forum/forum_createmessage.inc');
			break;

		/*
			Hier ist alles was mit usern zusammenhängt
		*/
		case 'user':
		case 'user_overview':
			$area = 'user_overview';
			require('inc/user/user_overview.inc');
			break;
		case 'user_view':
			require('inc/user/user_view.inc');
			break;
		case 'user_create':
			require('inc/user/user_create.inc');
			break;
		case 'user_edit':
			require('inc/user/user_edit.inc');
			break;
		case 'user_delete':
			require('inc/user/user_delete.inc');
			break;


		/*
			Hier sind die cases für Alles was mit News zusammenhängt
		*/
		case 'news':
		case 'news_archive':
			$area = 'news_archive';
			require('inc/news/news_archive.inc');
			break;
		case 'news_read':
			require('inc/news/news_read.inc');
			break;
		case 'news_add':
			require('inc/news/news_add.inc');
			break;
		case 'news_delete':
			require('inc/news/news_delete.inc');
			break;
		case 'news_edit':
			require('inc/news/news_edit.inc');
			break;

		/*
			Hier ist die Startseite
		*/
		default:
			$StartSeite = new Page('Interner Bereich - Übersicht', '', $user_id, DEFAULT_STYLESHEET, $db, 1);

			$StartSeite->html_header();

							// Zuerst alle User herausfiltern die noch als online vermerkt sind, aber sich seit dem Timeout nicht mehr gerührt haben!
							$sql = "update " . DB_USERS . " set online = 0, last_login = last_action where online=1 and (last_action + " . TIMEOUT . ") < " . time() . "";
							$db->query($sql);

							// User herausfiltern die online sind!
							$sql = "select count(*) as count from " . DB_USERS . " where online=1";
							$online = $db->fetch_array($db->query($sql));

							// News rausfiltern die seit dem letzten Login erstellt wurden
							$sql = "select count(*) as count from " . DB_NEWS . " where unfinished=0 and unix_timestamp(date) > $user_last_login";
							$new_news = $db->fetch_array($db->query($sql));

							// Neue Forenbeiträge herausfiltern
							$sql = "select count(*) as count from " . DB_FORUM . " where unfinished=0 and unix_timestamp(date) > $user_last_login";
							$new_postings = $db->fetch_array($db->query($sql));

							// Willkommensnachricht erstellen
							$message = 'Hallo ' . $user_v_name . ' ' . $user_n_name . '! Dein letzter Login war am
									<b>' . strftime('%e.%m.%Y', $user_last_login) . '</B> um <b>' . strftime('%H:%M', $user_last_login) . '</b>.
									Es sind momentan <b>' . $online[count]. '</b> User online. Seit deinem letztem Login wurden
									<b>' . $new_news[count] . '</b> News gepostet! Es wurden <b>' . $new_postings[count] . '</b>
									neue Beiträge im Forum gepostet.';

							$box = new Column('Willkommen im Internen Bereich', $message, 0, '99%');

							$message = '
												<table width=100% align=center border=0 cellspacing=2 cellpadding=3>
													<tr bgcolor=#F2F5FF>
														<td>
															<a href=' . $PHP_SELF . '?area=news_archive class=blue>News</a>
														</td>
														<td>
															Hier werden News gesammelt und verwaltet.
														</td>
													</tr>
													<tr>
														<td>
															<a href=' . $PHP_SELF . '?area=date_overview class=blue>Terminverwaltung</a>
														</td>
														<td>
															Terminverwaltung
														</td>
													</tr>
													<tr bgcolor=#F2F5FF>
														<td>
															<a href=' . $PHP_SELF . '?area=user class=blue>Userverwaltung</a>
														</td>
														<td>
															Die Userverwaltung<br>
														</td>
													</tr>
													<tr>
														<td>
															<a href=' . $PHP_SELF . '?area=forum_overview class=blue>Forum</a>
														</td>
														<td>
															Zum Forum
														</td>
													</tr>
													<tr bgcolor=#F2F5FF>
														<td>
															<a href=' . $PHP_SELF . '?area=report_overview class=blue>Berichte</a>
														</td>
														<td>
															Hier werden Berichte verwaltet und gelesen.
														</td>
													</tr>
													<tr>
														<td>
															<a href=' . $PHP_SELF . '?area=photo_overview class=blue>Photogalerie</a>
														</td>
														<td>
															Hier werden Photos verwaltet und betrachtet.
														</td>
													</tr>
													<tr bgcolor=#F2F5FF>
														<td>
															<a href=' . $PHP_SELF . '?area=motm class=blue>Spruch des Monats</a>
														</td>
														<td>
															Sprüche des Monats werden hier gesammelt und verwaltet
														</td>
													</tr>
													<tr>
														<td>
															<a href=' . $PHP_SELF . '?area=admin_overview class=blue>Administration</a>
														</td>
														<td>
															Administration
														</td>
													</tr>
												</table>';
							$box->Column('Bereiche', $message, 0, '99%');

			echo '			</td>
							<td align=center valign=top>';

							// Erst mal schauen ob überhaupt was für diesen Monat vorliegt...
							$sql = "select * from " . DB_REPORTS . " where type >= 256 and begin >= " . mktime(0, 0, 0, strftime('%m'), 0, strftime('%Y')) . " and begin < " . mktime(0, 0, 0, (strftime('%m') + 1), 0, strftime('%Y')) . " order by begin, create_date desc";

							$message = '
									<table width=100% align=center border=0 cellspacing=1 cellpadding=1>
									';
							$sql = "select id, heading, message, creator, unix_timestamp(date) as date from " . DB_NEWS . " where unfinished=0 order by date desc limit " . NEWS_PER_PAGE;
							$tmp = $db->query($sql);
							while ($news = $db->fetch_array($tmp))
							{
								if (strlen($news[message]) > 150)
								{
									$text = substr($news[message], 0, 150);
									$text .= '... [<a href=' . $PHP_SELF . '?area=news_read&read=' . $news[id] . ' class=blue>mehr</a>]';
								}
								else
								{
									$text = $news[message];
								}
								$message .= '
										<tr>
											<td bgcolor=#F2F5FF>
												<a href=' . $PHP_SELF . '?area=news_read&read=' . $news[id] . ' class=blue>
													<h3>
														' . $news[heading] . '
													</h3>
												</a>
											</td>
										</tr>
										<tr>
											<td>
												<i>'. strftime('%e.%m.%Y %H:%M', $news[date]) . ', von ' . $StartSeite->user_link($news[creator]) . ' : </i><br>' . $text . '<br>
											</td>
										</tr>';

							}

							$message .= '
									</table>
									';

							$menu = array();
							$menu[0][text] = 'Archiv';
							$menu[0][link] = "$PHP_SELF?area=news_archive";
							$menu[1][text] = 'News hinzufügen';
							$menu[1][link] = "$PHP_SELF?area=news_add";

							// $short_info = new Infobox('Spruch des Monats', 0, 0, '100%');
							$box = new Column('News', $message, $menu, '99%');

			$StartSeite->html_footer();
	}

?>

