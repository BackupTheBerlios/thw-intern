<?php

class DateViewer
{
	var $id, $db;

	var $has_userlist, $hast_unit;

	var $heading, $comment, $begin, $end, $anzahl_helfer, $helfer, $einheit, $anzahl_eingetragene_helfer, $ort, $kleidung, $creator, $creator_name, $create_date, $type_name, $has_ort, $has_kleidung;

	function DateViewer($id, $db)
	{
		$this->id = $id;
		$this->db = $db;

		$this->get_info();
		$this->build_list();
	}

	function build_list()
	{
		$output =  '
				<table width=100% align=center border=0> ';

					$output .= $this->print_info();
					$output .= $this->print_comment();
					$output .= $this->print_date();
					$output .= $this->print_ort();
					$output .= $this->print_kleidung();
					$output .= $this->print_helfer();

					$output .= $this->print_unit();
					$output .= $this->print_create_info();
					$output .= $this->print_nav();

		$output .=  '
				</table>';

		$Box = new InfoBox('Dienst', $output, 0, '90%');

	}

	function print_info_row($title, $content)
	{
		$output =  '
				<tr class=bar>
					<td align=center>' . $title . '</td>
				</tr>
				<tr>
					<td align=center>
						<b>' . $content . '</b>
					</td>
				</tr>';

		return($output);
	}

	function print_info()
	{
		// echo $this->info . '<br>';
		$output = $this->type_name . ' : ' . $this->info;
		$output = $this->print_info_row('Kurzinfo', $output);
		return($output);
	}

	function print_create_info()
	{
		$output = 'Dieser Dienst wurde von ' . $this->creator_name . ' am ' . strftime('%e.%m.%Y', $this->create_date) . ' erstellt bzw. editiert!';
		$output = $this->print_info_row('Erstellt am von', $output);
		return($output);
	}

	function print_nav()
	{
		$options = '
				<table width=100% align=center>
					<tr>
						<td>
							<a href="' . $PHP_SELF . '?area=date_view&search=type_prev&time=' . $this->begin . '&type=' . $this->type . '&id=' . $this->id . '" class=blue>&lt;&lt; Voriger Dienst vom Typ <i>' . $this->type_name . '</i></a>
						</td>
						<td align=right>
							<a href="' . $PHP_SELF . '?area=date_view&search=type_next&time=' . $this->begin . '&type=' . $this->type . '&id=' . $this->id . '" class=blue> Nächster Dienst vom Typ <i>' . $this->type_name . '</i> &gt;&gt;</a>
						</td>
					</tr>
					<tr>
						<td>
							<a href="' . $PHP_SELF . '?area=date_view&search=date_prev&time=' . $this->begin . '&id=' . $this->id . '" class=blue>&lt;&lt; Voriger Dienst</a>
						</td>
						<td align=right>
							<a href="' . $PHP_SELF . '?area=date_view&search=date_next&time=' . $this->begin . '&id=' . $this->id . '" class=blue> Nächster Dienst &gt;&gt;</a>
						</td>
					</tr>
				</table>
				';
		$output = $this->print_info_row('Navigation', $options);
		return($output);
	}

	function print_comment()
	{
		// echo $this->comment . '<br>';
		$output = $this->print_info_row('Kommentar', nl2br($this->comment));
		return($output);
	}

	function print_date()
	{
		// echo strftime('%e.%m.%Y %H:%m', $this->begin) . ' - ' . strftime('%e.%m.%Y %H:%m', $this->end) . '<br>';
		$output = '
				<table align=center>
					<tr>
						<td>Von :</td><td><b>' . strftime('%e.%m.%Y %H:%M', $this->begin) . '</b></td>
					</tr>
					<tr>
						<td>Bis :</td><td><b>' . strftime('%e.%m.%Y %H:%M', $this->end) . '</b></td>
					</tr>
				</table>';
		$output = $this->print_info_row('Termin', $output);
		return($output);
	}

	function print_ort()
	{
		if ($this->has_ort)
		{
			$output = $this->print_info_row('Ort', $this->ort);
			return($output);
		}
	}

	function print_kleidung()
	{
		if ($this->has_kleidung)
		{
			$output = $this->print_info_row('Kleidung', $this->kleidung);
			return($output);
		}
	}

	function print_helfer()
	{
		if ($this->has_userlist)
		{
/*			$sql = 'select v_name, n_name, id from ' . DB_USERS . ' where '; */

			if ($this->userlist_has_comment)
			{
				if ($this->anzahl_eingetragene_helfer)
				{
					$user_list = '<table widtH=100% align=center>';
					while ($tmp = $this->db->fetch_array($this->helfer))
					{
						if (1)
						{
							$user_list .= '<tr><td><a href="' . $GLOBALS[$PHP_SELF] . '?area=date_view&id=' . $this->id . '&remove_user=' . $tmp[user_id] . '" class=blue>' . $tmp[v_name] . ' ' . $tmp[n_name] . '</a></td><td>' . $tmp[comment] . '</td></tr> ';
						}
						else
						{
							$user_list .= $tmp[v_name] . ' ' . $tmp[n_name] . ', ';
						}

						if ($tmp[user_id] == $GLOBALS[user_id])
						{
							$already_registered = 1;
						}
					}

					$user_list .= '</table>';
				}
			}
			else
			{
				if ($this->anzahl_eingetragene_helfer)
				{
					while ($tmp = $this->db->fetch_array($this->helfer))
					{
						if (1)
						{
							$user_list .= '<a href="' . $GLOBALS[$PHP_SELF] . '?area=date_view&id=' . $this->id . '&remove_user=' . $tmp[user_id] . '" class=blue>' . $tmp[v_name] . ' ' . $tmp[n_name] . '</a>, ';
						}
						else
						{
							$user_list .= $tmp[v_name] . ' ' . $tmp[n_name] . ', ';
						}

						if ($tmp[user_id] == $GLOBALS[user_id])
						{
							$already_registered = 1;
						}
					}
				}
			}

			if ($this->anzahl_eingetragene_helfer < $this->anzahl_helfer)		// Sind überhaupt noch Plätze frei?
			{
				if ($already_registered)
				{
					$temp = 'Du bist bereits eingetragen';
				}
				else
				{
					// $temp = '<a href="' . $GLOBALS[PHP_SELF] . '?area=date_view&id=' . $this->id . '&user_add=' . $GLOBALS[user_id] . '" class=blue>Mich hier eintragen</a>';
					if ($this->userlist_has_comment)
					{
						$temp = "<form action='$PHP_SELF' method=get><input type=hidden name=area value=date_view><input type=hidden name=id value=$this->id><input type=hidden name=user_add value=$GLOBALS[user_id]><input type=text size=10 maxlength=255 name=comment><input type=submit value='Mich hier eintragen'></form>";
					}
					else
					{
						$temp = '<a href="' . $GLOBALS[PHP_SELF] . '?area=date_view&id=' . $this->id . '&user_add=' . $GLOBALS[user_id] . '" class=blue>Mich hier eintragen</a>';
					}
				}
			}
			else
			{
				$temp = 'Liste ist voll!';
			}

			$output = '
					<table width=100% align=center>
						<tr>
							<td align=right width=50%>Benötigte Helfer :</td>
							<td><b>' . $this->anzahl_helfer . '</b></td>
						</tr>
						<tr>
							<td align=right width=50%>Eingetragene Helfer : </td>
							<td><b>' . $this->anzahl_eingetragene_helfer . '</b></td>
						</tr>
						<tr>
							<td align=right width=50%>Freie Plätze : </td>
							<td><b>' . ( $this->anzahl_helfer - $this->anzahl_eingetragene_helfer ) . '</b> &lt;- ' . $temp . '</td>
						</tr>
						<tr>
							<td align=right width=50%>Helfer : </td>
							<td>' . $user_list . '</td>
						</tr>';

			if (1)			// UNFINISHED
			{
				if ($this->anzahl_eingetragene_helfer < $this->anzahl_helfer)
				{
					$sql = "select id, n_name, v_name from " . DB_USERS . " order by n_name";
					$users_raw = $this->db->query($sql);

					$output .= '
							<tr>
								<td colspan=2 align=center>
									<form action="' . $PHP_SELF . '" method=get><input type=text size=10 name=comment maxlength=255>
										<select size=1 name=user_add>

									';

									while ($current = $this->db->fetch_array($users_raw))
									{
										$output .= '<option value=' . $current[id] . '>' . $current[n_name] . ' ' . $current[v_name] . '</option>';
									}

					$output .= '
										</select>
										<input type=hidden name=area value=date_view>
										<input type=hidden name=id value=' . $this->id . '>
										<input type=submit value=Eintragen>
									</form>
								</td>
							</tr>
							';
				}
			}

			$output .= '
					</table>';

			$output = $this->print_info_row('Helfer', $output);
			return($output);
		}
	}


	function print_unit_item($name, $rank)
	{
		for ($i = 0; $i < $rank; $i++)
		{
			$indent = $indent . '&nbsp;&nbsp;';
		}
		$output = "<tr><td>$indent $name</td></tr>";
		return($output);
	}


	function get_unit_tree($start_id)
	{

	}

	/*
		Rekursive Funktion, Zeichnet eine hierarchische Ansicht einer Einheit
	*/
	function print_unit_tree($start_id)
	{
		$sql = "select * from " . DB_UNITS . " where id=$start_id";
		$current = $this->db->fetch_array($this->db->query($sql));

		$output = $this->print_unit_item($current[name], $current[rank]);

		$sql = "select id from " . DB_UNITS . " where ref_id=$start_id order by rank";
		$tmp = $this->db->query($sql);
 		if ($this->db->num_rows($tmp))
		{
			while ($current  = $this->db->fetch_array($tmp))
			{
				$output .= $this->print_unit_tree($current[id]);
			}
		}
		return($output);
	}



	function print_unit()
	{
		if ($this->has_units)
		{
			$einheiten = explode('x', $this->einheit);

			$output = '<table align=center>';
			for ($i = 0; $i < count($einheiten); $i++)
			{
				$output .= $this->print_unit_tree($einheiten[$i]);
			}
			$output .= '</table>';

			$output = $this->print_info_row('Einheit', $output);
			return($output);
		}
	}

	function get_info()
	{
		if ($this->id)
		{
			// Daten zum eigentlichen Dienst holen
			$sql = "select *, unix_timestamp(created) as new_date from " . DB_DATES . " where id = $this->id";
			$current = $this->db->fetch_array($this->db->query($sql));

			// Überprüfen ob wir eine Userliste haben
			if ($current[anzahl_helfer])
			{
				$this->has_userlist = 1;
				$this->anzahl_helfer = $current[anzahl_helfer];
				
				$this->userlist_has_comment = $current[userlist_has_comment];

				// Prüfen wieviele sich bereits eingetragen haben
				$sql = "select count(*) as count from " . DB_USERLISTS . " where ref_id = $this->id";
				$count = $this->db->fetch_array($this->db->query($sql));

				// Wenn eingetragen User dann Daten holen!
				if ($count[count])
				{
					$sql = "select user_id, comment, v_name, n_name from " . DB_USERLISTS . ", " . DB_USERS . " where ref_id = $this->id and " . DB_USERS . ".id = user_id";
					$this->helfer = $this->db->query($sql);
				}

				$this->anzahl_eingetragene_helfer = $count[count];
			}
			else
			{
				$this->has_userlist = 0;
			}

			$this->info = $current[info];
			$this->comment = $current[comment];
			$this->begin = $current[begin];
			$this->end = $current[end];
			$this->creator = $current[creator];
			$this->create_date = $current[new_date];
			$this->type = $current[type];
			$sql = "select name from " . DB_DATETYPES . " where id = $this->type";
			$types = $this->db->fetch_array($this->db->query($sql));
			$this->type_name = $types[name];

			$sql = "select n_name, v_name from " . DB_USERS . " where id=$this->creator";
			$tmp = $this->db->fetch_array($this->db->query($sql));
			$this->creator_name = $tmp[v_name] . ' ' . $tmp[n_name];

			if ($this->kleidung)
			{
				$this->has_kleidung = 1;
				$this->kleidung = $current[kleidung];
			}

			if ($current[ort])
			{
				$this->has_ort = 1;
				$this->ort = $current[ort];
			}

			// Überprüfen ob wir eine Userliste haben

			// Brauchen wir eine Einheitenübersicht?
			if ($current[einheit])
			{
				$this->has_units = 1;
				$this->einheit = $current[einheit];
			}
			else
			{
				$this->has_units = 0;
			}

		}
	}

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Class Page
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class Page
{
	var $db;
	var $title, $stylesheet, $area;
	var $user_id;
	var $db;
	var $allowed, $admin;

	function Page($title, $area, $user_id, $stylesheet, $db, $requires_logged_in = 1, $is_pre_allowed = 0)
	{
		$this->is_pre_allowed = $is_pre_allowed;
		$this->user_id = $user_id;
		$this->db = $db;

		$this->allowed = 0;
		$this->admin = 0;

		$this->self = $GLOBALS[PHP_SELF];

		$this->stylesheet = $stylesheet;

		$this->title = $title;

		$this->area = $area;

		if ($requires_logged_in)
		{
			if ($this->is_logged_in($this->user_id))
			{

				if ($this->is_allowed() or $this->is_pre_allowed)
				{
				}
				else
				{
					$this->html_header();
					$this->not_allowed();
					$this->html_footer();
					exit;
				}
			}
			else
			{
				$this->allowed = 1;
				header("Location: $GLOBALS[PHP_SELF]?area=login");
				exit;
			}
		}

	}

	function user_link($id)
	{
		$sql = 'select v_name, n_name from ' . DB_USERS . ' where id=' . $id;
		$user = $this->db->fetch_array($this->db->query($sql));
		$output = $user[v_name] . '&nbsp;' . $user[n_name];

		return($output);
	}

	function not_allowed()
	{
		echo '<br>';
		$output = 'Sorry, du darfst hier nicht rein!';
		$menu = array();
		$menu[right][text] = "Startseite";
		$menu[right][link] = $GLOBALS[PHP_SELF];

		$Box = new InfoBox('Zugang verweigert', $output, $menu);
	}

	function html_header()
	{
		echo '	<html>
					<head>
						<link rel="stylesheet" type="text/css" href="' . $this->stylesheet . '">
						<title>' . $this->title . '</title>
					</head>
					<body class=BACKGROUND>
						<table width=98% align=center class=' . IB_BORDER . ' cellspacing=0 cellpadding=1>
							<tr>
								<td>
									<table width=100% align=center cellspacing=0 cellpadding=2>
										<tr class=bar>
											<td style="font-size: 13pt;" align=center colspan=2><a name=top></a> THW-Intern : ' . OV_NAME . '
											</td>
										</tr>';
										if ($this->is_logged_in($this->user_id))
										{
												echo '<tr class=' . IB_BACKGROUND . '>
													<td style="font-size: 9pt;" align=right>';
										if ($this->admin)
										{
											echo '<a href="' . $PHP_SELF . '?area=admin_permissions_add&new_area=' . $this->area . '">Neue Permission</a>';
										}
												echo '
													</td>
													<td style="font-size: 9pt;" align=right width=5%>
														<a href="' . $PHP_SELF.'?area=user_edit&id=' . $this->user_id .'" class=blue>' . $GLOBALS[user_v_name] . '&nbsp;' . $GLOBALS[user_n_name] .'</a>&nbsp;<a href="' . $PHP_SELF.'?area=logoff" class=red>abmelden</a>
													</td>
												</tr>';
										}
									echo '</table>
								</td>
							</tr>
						</table>
						<table width=98% align=center border=0 cellspacing=4 cellpadding=0>
							<tr>
								<td align=center valign=top>';
	}

	function html_header_plain()
	{
		echo '	<html>
					<head>
						<link rel="stylesheet" type="text/css" href="' . $this->stylesheet . '">
						<title>' . $this->title . '</title>
					</head>
					<body class=BACKGROUND>
						<table width=98% align=center border=0 cellspacing=4 cellpadding=0>
							<tr>
								<td align=center valign=top>';
	}


	function html_footer()
	{

		echo '
								</td>
							</tr>
						</table>
				<br>
				<table width=98% align=center border=0 class=IB_BORDER cellspacing=1 cellpadding=0>
					<tr>
						<td>
							<table width=100% align=center cellspacing=0 cellpadding=2>
								<tr>
									<td align=center class=bar style="font-size: 9pt;">
										THW-Intern Beta [obj 0.2]
									</td>
								</tr>
								<tr class=IB_BACKGROUND>
									<td align=center style="font-size: 9pt;">
										written by <a href="mailto:Jakob@TarnkappenBaum.org" class=blue>Jakob Külzer</a> | visit <a href="http://www.tarnkappenbaum.org/" class=blue>www.TarnkappenBaum.org</a> | <b>' . $this->db->sql_counter() . '</b> SQL-Statements queried!
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

	function html_footer_plain()
	{

		echo '
								</td>
							</tr>
						</table>
			</body>
		</html>
				';
	}


	function html_filter($text)
	{
		$output = htmlentities($text);
		return($output);
	}

	function menubar($menu)
	{
					for ($i = 0; $i < count($menu); $i++)
					{
						if ($i)
						{
							echo ' - ';
						}
						echo "<a href=" . $menu[$i] [link] . " class=blue>" . $menu[$i] [text] . "</a> ";

					}
	}

	function pagetitle($title, $menubar)
	{
		echo '
			<table width=95% align=center border=0 class=IB_BORDER cellspacing=1 cellpadding=0>
				<tr>
					<td>
						<table width=100% align=center cellspacing=0 cellpadding=2 class=IB_BACKGROUND>
							<tr>
								<td class=bar align=center>
								' . $title . '
							</td>
							</tr>
					';
					if ($menubar)
					{
						echo '		<tr>
										<td style="font-size: 10pt;">';
						$this->menubar($menubar);
						echo '			</td>
									</tr>';
					}
		echo '
						</table>
					</td>
				</tr>
			</table>';
	}

	function is_logged_in($user_id = 0)
	{
		// Überprüfen ob wir Login-Cookies haben
		if ($_COOKIE[user_id])
		{
			// echo 'Found login cookie!!<br>';
			// echo 'user_id (cookie) : ' . $_COOKIE[user_id] . '<br>';

			if ($_COOKIE[user_password])
			{
				// echo 'Found password cookie! comparing cookie with database...<br>';

				$sql = "select v_name, n_name, password, last_login from " . DB_USERS . " where id=$_COOKIE[user_id]";
				// echo 'sql : ' . $sql . '<br>';

				if ($current_user = $this->db->query($sql))		// gibts den user?
				{
					$current_user = $this->db->fetch_array($current_user);

					if (md5($current_user[password]) == urldecode($_COOKIE[user_password] ))
					{
						// echo 'user_password (cookie) : ' . urldecode($_COOKIE[user_password] ). '<br>';
						// echo 'user_password (db) : ' . md5($current_user[password]). '<br>';
						// echo 'login OK!<br>';
						return(1);
					}
				}
				else		// User existiert nicht!
				{
					return(0);
				}
			}
		}
		else if (!$user_id)		{	$user_id = $this->user_id;	}

		if ($user_id)
		{
			$hash = md5($GLOBALS[user_n_name] . $GLOBALS[user_password] . $GLOBALS[REMOTE_ADDR]);

			if ($hash != $GLOBALS[user_session_id])
			{
				return(0);
			}
			else
			{
				return(1);
			}
		}
		else		// Wir haben keine User-Id, also nicht angemeldet!
		{
			return(0);
		}
	}

	// Prüft ob der angegebene User (id) in der von $sql angegebenen Menge ist
	// gibt -1 zurück falls KEINE Permission
	//    "   0 zurück falls User in der Permission sind, der User (id) aber nicht gefunden wurde!
	//    "   1 zurück falls der User in der Permission gefunden wurde!!
	function check_for_user($sql, $id = 0)
	{
		if (!$id)
		{
			$id = $this->user_id;
		}

		// echo 'querying : ' . $sql . '<br>';
		$tmp = $this->db->query($sql);

		if ($this->db->num_rows($tmp))
		{
			// Wir benutzen keine while schleife, hier gilt first hit, only hit!!!

			$current = $this->db->fetch_array($tmp);

			if ($current[users])
			{
				$users = explode('#', $current[users]);

				for ($i = 0; $i < count($users); $i++)
				{
					if ($users[$i] == $id)
					{
						// echo 'found permission and youre in it!<br>';
						return(1);
						break;
					}
				}
			}
			else
			{
				return(0);
				echo 'found permission but no users!<br>';
			}
		}
		else
		{
			return(-1);
			echo 'there seems to be no permission!<br>';
		}

		return(0);

	}

	function admin()
	{
		return($this->admin);
	}

	function allowed()
	{
		return($this->allowed);
	}

	function is_allowed($area = 0)
	{
		if (!$area)
		{
			$area = $this->area;
		}

		// Überprüfen ob wir Admin sind :
		$sql = "select id, users from " . DB_PERMISSIONS . " where area = '*'";

		if ($this->check_for_user($sql) == 1)			// Wir sind Admin (o:
		{
			$this->admin = 1;
			$this->allowed = 1;
			// echo 'Youre Admin! breaking off! <br>';
			return(1);
			break;
		}
		else
		{			// Kein Admin
			// echo 'Youre NOT Admin! <br>';

			$sql = "select id, users from " . DB_PERMISSIONS . " where area = '$area'";

			$temp = $this->check_for_user($sql);

			if ($temp == -1)			// -1 : Keine Permission  -> Keine Beschränkung
			{
				// echo 'Youre allowed because this is a public area! breaking off! <br>';
				$this->allowed = 1;
				return(1);
				break;
			}
			else if ($temp == 1)		// Zugelassen!
			{
				// echo 'Youre allowed because youre Area-Admin! breaking off! <br>';
				$this->allowed = 1;
				$this->admin = 1;
				// echo 'Youre Area-Admin! breaking off! <br>';
				return(1);
				break;
			}

			// Überprüfen ob eine Permission-Klasse vorliegt.
			// Dazu aufsplitten in mögliche permissions :
			$tmp = explode('_', $area);

			$sql = "select id, users from " . DB_PERMISSIONS . " where area='" . ($tmp[0]) . "*'";

			// echo 'checking permission(class): ' . $sql . '<br>';

			$temp = $this->check_for_user($sql);

			if ($temp == 1)		// Zugelassen!
			{
				// echo 'Youre allowed because youre Area-Admin! breaking off! <br>';
				$this->allowed = 1;
				$this->admin = 1;
				// echo 'Youre Area-Admin! breaking off! <br>';
				return(1);
			}


			return(0);
		}
	}

}

class Database
{
	var $host, $user, $password, $database, $type, $db;

	function Database($host = 0, $user = 0, $password = 0, $database = 0, $type = 'mysql')
	{
		$this->sql_counter = 0;
		$this->type = $type;

		if ($host)		{	$this->host = $host;	}	else	{	$this->host = DB_HOST;	}
		if ($user)		{	$this->user = $user;	}	else	{	$this->user = DB_USER;	}
		if ($password)		{	$this->password = $password;	}	else	{	$this->password = DB_PASSWORD;	}
		if ($database)		{	$this->database = $database;	}	else	{	$this->database = DB_DATABASE;	}

/*
		echo $this->host . '<br>';
		echo $this->user . '<br>';
		echo $this->password . '<br>';
		echo $this->database . '<br>';
		echo $this->type . '<br>';
*/

		$this->connect_db();
	}

	function sql_counter()
	{
		return($GLOBALS[query_counter]);
	}

	function fetch_array($raw)
	{
		if ($raw)
		{
			switch($this->type)
			{
				case 'mysql':
					return(mysql_fetch_array($raw));
					break;
				default:
					die('Sorry, you selected an unsupported databasesystem.');
			}
		}
		else
		{
			return(0);
		}
	}

	function query($sql)
	{
		$GLOBALS[query_counter]++;
		
		if ($sql)
		{
			switch($this->type)
			{
				case 'mysql':
					if ($tmp = mysql_query($sql))
					{
						return($tmp);
					}
					else
					{
						return(0);
					}
					break;
				default:
					die('Sorry, you selected an unsupported databasesystem.');
			}
		}
		else
		{
			return(0);
		}
	}

	function num_rows($tmp)
	{
		switch($this->type)
		{
			case 'mysql':
				if ($tmp)
				{
					return(mysql_num_rows($tmp));
				}
				else
				{
					return(0);
				}
				break;
			default:
				die('Sorry, you selected an unsupported databasesystem.');
		}
	}

	function server_info()
	{
		echo mysql_get_host_info();
	}

	function connect_db()
	{
		switch($this->type)
		{
			case 'mysql':
				if ($this->db = mysql_pconnect($this->host, $this->user, $this->password))
				{
					if (mysql_select_db($this->database, $this->db))
					{
						return(1);
					}
					else
					{
						die(mysql_error());
					}
				}
				else
				{
					die(mysql_error());
				}
				break;
			default:
				die('Sorry, you selected an unsupported databasesystem.');
		}
	}
}

class Infobox
{
	var $title, $message, $width, $class;
	var $options;

	function Infobox($title, $message, $options, $width = 0, $class = '')
	{
		$this->title = $title;
		$this->message = $message;
		$this->options = $options;
		$this->width = $width;
		$this->class = $class;

		$this->frame();
	}

	function buttons()
	{
		echo '		<tr>
						<td width=20>&nbsp;</td>
						<td align=left><a href="' . $this->options[left][link] . '" class=blue>' . $this->options[left][text] . '</a></td>
						<td align=right><a href="' . $this->options[right][link] . '" class=blue>' . $this->options[right][text] . '</a></td>
						<td width=20>&nbsp;</td>
					<tr>';
	}

	function content()
	{
		if ($this->class)
		{
			$insert_string2 = $this->class;
		}
		else
		{
			$insert_string2 = IB_TITLEBAR;
		}
		echo '		<tr class=' . IB_TITLEBAR . '>
						<td class=' .  $insert_string2 . ' colspan=4>' . $this->title . '</td>
					</tr>
					<tr>
						<td width=20>&nbsp;</td>
						<td>' . $this->message . '</td>
						<td width=20>&nbsp;</td>
					</tr>
					';

		$this->buttons();
	}

	function frame()
	{
		if ($this->width)
		{
			$insert_string = 'width=' . $this->width;
		}
		else
		{
			$insert_string = '';
		}

		echo '
				<table align=center border=0 cellspacing=0 cellpadding=1 class=' . IB_BORDER . ' ' . $insert_string . '>
					<tr>
						<td>
							<table width=100% align=center cellspacing=0 cellpadding=2 class=' . IB_BACKGROUND. '>
							';

			$this->content();

		echo '				</table>
						</td>
					</tr>
				</table>
				<br>
				';
	}
}

class Column extends Infobox
{
	function menubar($menu)
	{
		$output = '';
					for ($i = 0; $i < count($menu); $i++)
					{
						if ($i)
						{
							$output .= ' - ';
						}
						$output .= "<a href=" . $menu[$i] [link] . " class=blue>" . $menu[$i] [text] . "</a> ";

					}

		return($output);
	}

	function content()
	{
		echo '		<tr>
						<td class=' . IB_TITLEBAR . '>' . $this->title . '</td>
					</tr>';
			if ($this->options)
			{
				echo '<tr><td>' . $this->menubar($this->options) . '</td></tr>';
			}
		echo '		<tr class=' . IB_BACKGROUND . '>
						<td>' . $this->message . '</td>
					</tr>
					';

		// $this->buttons(); Wir brauchen keine Buttons
	}
}

?>
