<?php

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
		$temp = '';
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

class NOInfobox
{
	var $title, $message, $width, $class;
	var $options;

	function Infobox()
	{
	}

	function Run ($title, $message, $options, $width = 0, $class = '' )
	{
		$this->title = $title;
		$this->message = $message;
		$this->options = $options;
		$this->width = $width;
		$this->class = $class;

		return ( $this->frame() );

	}

	function buttons()
	{
		return '
					<tr>
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
		$output = '	
					<tr class=' . IB_TITLEBAR . '>
						<td class=' .  $insert_string2 . ' colspan=4>' . $this->title . '</td>
					</tr>
					<tr>
						<td width=20>&nbsp;</td>
						<td>' . $this->message . '</td>
						<td width=20>&nbsp;</td>
					</tr>
					';

		$output .= $this->buttons();

		return ( $output );
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

		$output = '
				<table align=center border=0 cellspacing=0 cellpadding=1 class=' . IB_BORDER . ' ' . $insert_string . '>
					<tr>
						<td>
							<table width=100% align=center cellspacing=0 cellpadding=2 class=' . IB_BACKGROUND. '>
							';

			$output .= $this->content();

		$output .= '				</table>
						</td>
					</tr>
				</table>
				<br>
				';
		return ( $output );
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
