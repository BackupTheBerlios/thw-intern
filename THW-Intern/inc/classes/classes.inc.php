<?php

class Interface
{
	var
		$stylesheet		// welches Stylesheet soll benutzt werden?
		;

	function Interface($stylesheet)
	{
		$this->stylesheet = $stylesheet;
	}

	function html_header()
	{
		echo '
		<html>
			<head>
				<title>THW-Intern: ' . OV_NAME . '</title>
				<link rel="stylesheet" type="text/css" href="etc/css/' . $this->stylesheet . '">
			</head>
			<body>
				<a name="top"></a>
		';
	}


	function title_bar($menu = 0, $title = 0)
	{
		global $session;
		if (is_object($session))
		{

			if ($GLOBALS[MAIN_MENU])
			{
				$menu_bar = '
									<tr class=main_menubar>
										<td>
				';
				for ($i = 0; $i < count($GLOBALS[MAIN_MENU]); $i++)
				{
					if ($i)
					{
						$menu_bar .= ' - ';
					}


					// strpos ( string haystack, string needle [, int offset])

					// if ($GLOBALS[action] == $GLOBALS[MAIN_MENU][$i][action])

					if ( !(strpos($GLOBALS[action], $GLOBALS[MAIN_MENU][$i][action]) === false) )
					{
						$class = 'class=active ';
						$current_main_action = explode('_', $GLOBALS[action]);
						$current_main_action = $current_main_action[0];
					}
					else
					{
						$class = '';
					}

					$menu_bar .= "
											<a href=" . $GLOBALS[MAIN_MENU][$i][link] . "?action=" . $GLOBALS[MAIN_MENU][$i][action] . " $class> " . $GLOBALS[MAIN_MENU][$i][text] . " </a>
						";

				}
				$menu_bar .= '
										</td>
										<td align=right>
											<a href="' . $PHP_SELF . '?pre_action=logoff" title="User abmelden (Bitte hier klicken sobald du fertig bist!!)!">&gt;' . $session->user_info('vorname') . ' ' . $session->user_info('name') . '&lt;</a>
										</td>
									</tr>
					';
			}

			if (count($GLOBALS[SUB_MENU][$current_main_action]))
			{
				$sub_menu_bar = '
									<tr class=sub_menubar>
										<td colspan=2>
					';
				for ($i = 0; $i < count($GLOBALS[SUB_MENU][$current_main_action]); $i++)
				{
					if ($GLOBALS[action] == $GLOBALS[SUB_MENU][$current_main_action][$i][action])
					{
						$class = ' class=active ';
					}
					else
					{
						$class = '';
					}
					if ($i)
					{
						$sub_menu_bar .= ' - ';
					}
					$sub_menu_bar .= "<a href='$GLOBALS[PHP_SELF]?action=" . $GLOBALS[SUB_MENU][$current_main_action][$i][action] . "' $class>" . $GLOBALS[SUB_MENU][$current_main_action][$i][text] . "</a>";
				}
				$sub_menu_bar .= '
										</td>
									</tr>
					';
			}
		}

		if (!$title)
		{
			$title = 'THW-Intern : ' . OV_NAME;
		}

		echo '
				<table width=98% align=center border=0 class=dialog_box_border cellspacing=1 cellpadding=0>
					<tr>
						<td>
							<table width=100% align=center cellspacing=0 cellpadding=2 border=0>
								<tr class=dialog_box_titlebar>
									<td align=center style="font-size: 11pt; font-weight: bold;" colspan=2>
										' . $title . '
									</td>
								</tr>
								' . $menu_bar . '
								' . $sub_menu_bar . '
							</table>
						</td>
					</tr>
				</table>
				<br>
			';
	}

	function html_footer($num_queries = 'n/a')
	{
		global $db, $session;

		if (is_object($session))
		{
			if ($session->user_info(name))
			{
				$insert = '<a href="' . $PHP_SELF . '?pre_action=logoff" title="User abmelden"><b>' . $session->user_info(vorname) . ' ' . $session->user_info(name) . '</b></a> | ';
			}
		}
		echo '
				<br>
				<table width=98% align=center border=0 class=dialog_box_border cellspacing=1 cellpadding=0>
					<tr>
						<td>
							<table width=100% align=center cellspacing=0 cellpadding=2>
								<tr class=dialog_box_content>
									<td align=center style="font-size: 9pt;">
										<b><a href="info.php">THW-Intern DEVEL</a></b> | ' . $insert . ' <b>' . $db->query_counter() . '</b> SQL-Statements queried!
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</body>
		</html>
		';
	}

	function dialog_box($title, $message, $menu, $bottom_menu, $width)
	{
		if ($menu)
		{
			$menu_bar = '
									<tr class=dialog_box_menubar>
										<td>
				';

			for ($i = 0; $i < count($menu); $i++)
			{
				if ($i)
				{
					$menu_bar .= ' - ';
				}
				$menu_bar .= '
											<a href="' . $menu[$i][link] . '" title="' . $menu[$i][title] . '">' . $menu[$i][text] . '</a>
						';
			}
			$menu_bar .= '
										</td>
									</tr>
				';
		}

		if ($bottom_menu)
		{
			$menu_bottom = '
									<tr class=dialog_box_bottommenu>
										<td>
				';

			for ($i = 0; $i < count($bottom_menu); $i++)
			{
				if ($i)
				{
					$menu_bottom .= ' - ';
				}
				$menu_bottom .= "<a href=" . $bottom_menu[$i] [link] . ">" . $bottom_menu[$i] [text] . "</a>";
			}
			$menu_bottom .= '
										</td>
									</tr>
				';
		}


		if ($message)
		{
			$content = '
								<tr class=dialog_box_message>
									<td>
										' . $message . '
									</td>
								</tr>
				';
		}

		if (!$width)
		{
			$width = '95%';
		}

		if ($title)
		{
			$title_bar = '
								<th class=dialog_box_titlebar>
									' . $title . '
								</th>
				';
		}
		else
		{
			$title_bar = '';
		}

		$output = '
				<table width=' . $width . ' align=center border=0 cellspacing=0 cellpadding=1 class=dialog_box_border>
					<tr>
						<td>
							<table width=100% align=center border=0 cellspacing=0 cellpadding=2 class=dialog_box_content>
								' . $title_bar . '
								' . $menu_bar . '
								' . $content . '
								' . $menu_bottom . '
							</table>
						</td>
					</tr>
				</table>
			';

		return($output);
	}

	// table_lister baut eine Tabelle nach den Vorgaben aus $table_attribs mit dem Inhalt aus $table_content
	// Struktur von $table_attribs : array (
	//			0 => array(
	//				name => 'width',
	//				value => '100%'
	//			)
	// )

	function table_lister($table_attribs, $table_content)
	{
		$output = '
					<table ';

		for ($i = 0; $i < count($table_attribs); $i++)
		{
			$output .= $table_attribs[$i][name] . '="' . $table_attribs[$i][value] . '"';
		}
		$output .= '>
			';

		for ($row = 0; $row < count($table_content); $row++)
		{
			$output .= '
						<tr>
				';

			$output .= '
						</tr>
				';
		}

		$output .= '
					</table>
			';
		return($output);
	}
}

class Rights
{
	function Rights()
	{
	}

	// User ein Recht hinzufügen
	function add_right($user_id, $right)
	{
		global $log, $db, $RIGHTS;
		$log->add_to_log('Rights::add_right', 'add_right called! parameters : user_id : ' . $user_id . ' $right : ' . $right);
		// Zuerst Userdaten holen
		$sql = '
				select
					rights
				from
				' . TB_USER . '
				where
					id = ' . $user_id . '
			';

		$userdaten = $db->query($sql);

		if ($userdaten = $db->fetch_array($userdaten))
		{
			if (is_int($right))
			{
				// $log->add_to_log('Rights::add_right', '$right is an integer!!');
			}
			else
			{
				// $log->add_to_log('Rights::add_right', 'Whoups, $right is not an integer!! converting...');
				$right = intval($right);
			}

			// Neue Userrechte mittels OR berechnen
			$userdaten[rights] = $userdaten[rights] | $right;

			// Neue Userrechte zurückschreiben
			$sql = '
					update
					' . TB_USER . '
					set
						rights = ' . $userdaten[rights] . '
					where
						id = ' . $user_id . '
				';

			$db->query($sql);
		}
		else
		{
			$log->add_to_log('Rights::add_right', 'Fatal error!! Couldnt load userdata!');
			die('Rights::add_right : Fehler : konnte Userdaten nicht laden!');
		}
	}

	// Einem User ein Recht entziehen!
	function remove_right($user_id, $right)
	{
		global $log, $db, $RIGHTS;

		$log->add_to_log('Rights::remove_right', 'remove_right called! parameters : user_id : ' . $user_id . ' $right : ' . $right);

		// Zuerst Userdaten holen
		$sql = '
				select
					rights
				from
				' . TB_USER . '
				where
					id = ' . $user_id . '
			';
		$userdaten = $db->query($sql);


		if ($userdaten = $db->fetch_array($userdaten))
		{
			// Neue Userrechte mittels AND NOT

			if (is_int($right))
			{
				// $log->add_to_log('Rights::remove_right', '$right is an integer!!');
			}
			else
			{
				// $log->add_to_log('Rights::remove_right', 'Whoups, $right is not an integer!! converting...');
				$right = intval($right);
			}

			$userdaten[rights] = ( $userdaten[rights] & (~ $right) );

			// $log->add_to_log('Rights::remove_right', '~$right : ' . (~ $right) . ' decbin(~$right) : ' . decbin(~ $right) );
			$log->add_to_log('Rights::remove_right', 'new userright : ' . decbin($userdaten[rights] & (~ $right)) );
			if (!decbin($userdaten[rights]))
			{
				$userdaten[rights] = 0;
			}

			// Neu Userrechte zurückschreiben
			$sql = '
					update
					' . TB_USER . '
					set
						rights = ' . $userdaten[rights] . '
					where
						id = ' . $user_id . '
				';

			$db->query($sql);
		}
		else
		{
			$log->add_to_log('Rights::remove_right', 'Fatal error!! Couldnt load userdata!');
			die('Rights::remove_right : Fehler : konnte Userdaten nicht laden!');
		}
	}

	// Gibt einen Array zurück, der alle Rechte enthält die der User hat!
	// Optional kann $rights auch leer sein, dann holt Rights::users_rights()
	// die Daten für den aktuell angemeldeten User [Session:user_info('id')]
	// direkt aus der DB!
	function users_rights($rights = 0)
	{
		global $log, $db, $RIGHTS, $session;

		if ($rights)
		{
			// Userdaten haben wir von außen bekommen.. also nix tun...
		}
		else
		{
			// Keine Userrechte übergeben, also holen wir sie uns aus der DB
			$sql = '
					select
						' . TB_USER . '.rights
					from
						' . TB_USER . '
					where
						' . TB_USER . '.id = ' . $session->user_info('id') . '
				';
			$raw = $db->query($sql);
			if ($db->num_rows($raw))
			{
				$rights = $db->fetch_array($raw);
				$rights = $rights[rights];
			}
			else
			{
				$log->add_to_log('Rights::users_rights', 'Fatal error!! Couldnt load userdata!');
				die('Rights::users_rights : Fatal error!! Couldnt load userdata!');
			}
		}

		// Prüfen ob die übergebenen Rechte auch in INT sind...
		if (is_int($rights))
		{
			$log->add_to_log('Rights::remove_right', '$right is an integer!!');
		}
		else
		{
			$log->add_to_log('Rights::remove_right', 'Whoups, $right is not an integer!! converting...');
			$rights = intval($rights);
		}

		// Den Array anlegen in dem wir die Rechte übergeben :
		$return = array();

		// Zu erst prüfen wir ob der User root-Rechte hat...
		$log->add_to_log('Rights::users_rights', 'Checking for root...');
		if ($rights == ROOT)
		{
			$log->add_to_log('Rights::users_rights', 'OK, user got root, returning...');
			// Dann können wir jetzt abbrechen...
			$return[0][right] = ROOT;
			$return[0][description] = 'root';
			return($return);
		}

		$log->add_to_log('Rights::users_rights', 'OK, user got NO root, commencing...');

		$counter = 0;
		// Jetzt prüfen wir jedes einzelne Recht gegen das übergebene...
		for ($i = 0; $i < count($RIGHTS); $i++)
		{
			if ($RIGHTS[$i][right] == ROOT)
			{
				// Gegen root-rechte muss nicht geprüft werden, da dieser AND immer positiv wäre!!
			}
			else
			{
				if ($rights & $RIGHTS[$i][right])
				{
					$log->add_to_log('Rights::users_rights', 'checking : ' . $rights . ' vs. ' . $RIGHTS[$i][right] . ' = ' . ($rights & $RIGHTS[$i][right]) . '...');
					$log->add_to_log('Rights::users_rights', 'Found a right the user got...');
					// Dieses Recht hat der User...
					$return[$counter][right] = $RIGHTS[$i][right];
					$return[$counter][description] = $RIGHTS[$i][description];

					$counter++;
				}
			}
		}

		// Und jetzt geben wir den Array noch zurück!
		return($return);
	}

	// Überprüfen ob ein User ein Recht hat!
	function check_right($user_id, $action)
	{

		global $log, $db, $RIGHTS;

		$log->add_to_log('Rights::check_right', 'given parameters : user_id : ' . $user_id . ' action : ' . $action);
		// Durch den Array gehen und prüfen ob überhaupt ein Recht für die gegebene Action existiert!

		for ($i = 0; $i < count($RIGHTS); $i++)
		{
			for ($j = 0; $j < count($RIGHTS[$i][action]); $j++)
			{
				// $log->add_to_log('Rights::check_right', 'comparing : ' . $action . ' vs. ' . $RIGHTS[$i][action][$j]);
				if ($action == $RIGHTS[$i][action][$j])
				{
					// Gegebene Action gefunden => Es gibt ein Recht auf die Action!

					// Zuerst Userdaten holen
					$sql = '
							select
								rights
							from
							' . TB_USER . '
							where
								id = ' . $user_id . '
						';
					
					$userdaten = $db->query($sql);

					if ($userdaten = $db->fetch_array($userdaten))
					{
						if (is_int($userdaten[rights]))
						{
							// $log->add_to_log('Rights::remove_right', '$userdaten[rights] is an integer!!');
						}
						else
						{
							// $log->add_to_log('Rights::remove_right', 'Whoups, $userdaten[rights] is not an integer!! converting...');
							$userdaten[rights] = intval($userdaten[rights]);
						}

						// Ist der User root???
						if ($userdaten[rights] == ROOT)
						{
							$log->add_to_log('Rights::check_right', 'Wow, we got a root-user here!');
							return(1);
						}

						// $log->add_to_log('Rights::check_right', 'ANDing ' . decbin($userdaten[rights]) . ' vs. ' . decbin($RIGHTS[$i][right]) . ' = ' . decbin( $userdaten[rights] & $RIGHTS[$i][right] ) );

						// Hat der User das Recht??
						if ( $userdaten[rights] & $RIGHTS[$i][right])
						{
							$log->add_to_log('Rights::check_right', 'User got right for ' . $action);
							// User hat das Recht!
							return(1);
						}
						else
						{
							$log->add_to_log('Rights::check_right', 'Whoups, user got no right for ' . $action . '!');
							// Nix da, draußen bleiben!
							return(0);
						}
					}
					else
					{
						$log->add_to_log('Rights::check_right', 'Fatal error!! Couldnt load userdata!');
						die('Rights::check_right : Fehler : konnte Userdaten nicht laden!');
					}
				}
			}
		}
		// Hopala, kein Recht gefunden!!! Zugriff gewährt!
		return(1);
	}
}


?>
