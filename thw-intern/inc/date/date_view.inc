<?php

			$DateView = new Page('Termin', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$DateView->html_header();

			if ($user_add)
			{
					// Zuerst überprüfen ob der User bereits eingetragen ist!
					$sql = "select user_id from " . DB_USERLISTS . " where ref_id = $id and user_id = $user_add";
					echo 'prüfe ob user schon eingetragen ist : ' . $sql . '<br>';
					if ($db->num_rows($db->query($sql)))
					{		// User ist bereits eingetragen
						echo '<p>Whoups, der User ist bereits eingetragen!</p>';
					}
					else	// User ist noch nicht eingetragen, also weiter
					{
						$sql = "insert into " . DB_USERLISTS . " (ref_id, user_id, comment) values ($id, $user_add, '$comment')";
						echo 'user wird eingetragen : ' . $sql . '<br>';
						$db->query($sql);
					}
			}

			if ($remove_user)
			{
					$sql = "delete from " . DB_USERLISTS . " where ref_id = $id and user_id = $remove_user";
					$db->query($sql);
			}

			switch($search)
			{
				case 'date_prev':
					$sql = "select id from " . DB_DATES . " where begin < $time and unfinished = 0 order by begin desc limit 1";

					$tmp = $db->query($sql);

					if ($db->num_rows($tmp))
					{
						$tmp = $db->fetch_array($tmp);
						$id = $tmp[id];
					}
					break;

				case 'date_next':
					$sql = "select id from " . DB_DATES . " where begin > $time and unfinished = 0 order by begin desc limit 1";

					$tmp = $db->query($sql);

					if ($db->num_rows($tmp))
					{
						$tmp = $db->fetch_array($tmp);
						$id = $tmp[id];
					}
					break;

				case 'type_next':
					$sql = "select id from " . DB_DATES . " where begin > $time and unfinished = 0 and type = $type order by begin desc limit 1";

					$tmp = $db->query($sql);

					if ($db->num_rows($tmp))
					{
						$tmp = $db->fetch_array($tmp);
						$id = $tmp[id];
					}
					break;
				case 'type_prev':
					$sql = "select id from " . DB_DATES . " where begin < $time and unfinished = 0 and type = $type order by begin desc limit 1";

					$tmp = $db->query($sql);

					if ($db->num_rows($tmp))
					{
						$tmp = $db->fetch_array($tmp);
						$id = $tmp[id];
					}
					break;

			}

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=date_overview";
			$menu[1][text] = 'Terminübersicht';
			$menu[2][link] = "$PHP_SELF?area=date_create";
			$menu[2][text] = 'Neuen Termin anlegen';
			$menu[3][link] = "$PHP_SELF?area=date_edit&id=$id";
			$menu[3][text] = 'Diesen Termin editieren';
			$menu[4][link] = "$PHP_SELF?area=date_delete&id=$id";
			$menu[4][text] = 'Diesen Termin löschen';


			$DateView->pagetitle('Termin', $menu);
			echo '<br>';

			$viewer = new DateViewer($id, $db);
			$DateView->html_footer();
?>
