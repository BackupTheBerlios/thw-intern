<?php


function thread_row_to_array($data, $last_login, $start_id, $depth)
{
	$output = array();

	// Überprüfen ob der Beitrag neu ist!
	if ($data[date] > $last_login)		// Neu!
	{
		$output[flag_new] = '1';
	}
	else		// bereits gesehen...
	{
		$output[flag_new] = '0';
	}

	$output[message] = $data[message];
	$output[id] = $start_id;
	$output[creator] = $data[owner];
	$output[v_name] = $data[v_name];
	$output[n_name] = $data[n_name];
	$output[counter] = $data[counter];
	$output[date] = $data[date];
	$output[heading] = $data[heading];


	$output[replies] = 0;

	$output[indent] = $depth;

	return($output);
}

function thread_to_array($db, $start_id, $forum_id, $last_login, $depth = -1)
{
	$output = array();

	// Depth erhöhen, hiermit wird nachher der Einrück-Abstand berechnet!
	$depth++;

	// Zuerst den Startbeitrag mit usernamen Raussuchen!
	$sql = "select message, heading, owner, unix_timestamp(date) as date, unix_timestamp(last_viewed) as last_viewed, counter, v_name, n_name from " . DB_FORUM . ", " . DB_USERS . " where " . DB_FORUM . ".id=$start_id and unfinished=0 and forum_id=$forum_id and " . DB_USERS . ".id=owner";
	// echo $sql . '<br>';
	$output[0] = thread_row_to_array($db->fetch_array($db->query($sql)), $last_login, $start_id, $depth);

	// Mögliche Antworten suchen ...
	$sql = "select id from " . DB_FORUM . " where ref_id=$start_id and forum_id=$forum_id and unfinished=0";
	// echo $sql . '<br>';
	$raw = $db->query($sql);
	if ($db->num_rows($raw))		// ... und bearbeiten!
	{
		// Jede einzelne Nachricht bearbeiten!
		while ($current = $db->fetch_array($raw))
		{
			$output = array_merge($output, thread_to_array($db, $current[id], $forum_id, $last_login, $depth));
		}
	}

	return($output);
}

function reverse_walk($db, $start_id)
{
	if ($start_id)
	{
		$sql = "select ref_id from " . DB_FORUM . " where id=$start_id";
		$current = $db->fetch_array($db->query($sql));

		if ($current[ref_id])
		{
			$output = reverse_walk($db, $current[ref_id]);
		}
		else
		{
			return($start_id);
			break;
		}
	}
	return ($output);
}

			$ForumView = new Page('Forum', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$ForumView->html_header();

			$news = $db->query($sql);

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=forum_overview";
			$menu[1][text] = 'Forenübersicht';
			$menu[2][link] = "$PHP_SELF?area=forum_view&id=$forum_id";
			$menu[2][text] = 'Zurück zum Forum';
			$menu[3][link] = "$PHP_SELF?area=forum_createmessage&ref_id=0&id=$forum_id";
			$menu[3][text] = 'Neue Diskussion';


			$sql = "update " . DB_FORUM . " set counter=counter+1 where id=$id";
			$db->query($sql);

			$ForumView->pagetitle('Beitrag lesen', $menu);


			echo '<br>
					';

						$output = thread_to_array($db, reverse_walk($db, $id), $forum_id, $user_last_login);


						for ($i = 0; $i < count($output); $i++)
						{
							$temp = nl2br($output[$i][message]);

							if ($output[$i][flag_new])
							{
								$style = 'red';
							}
							else
							{
								$style = '';
							}

							$message = '
									<table width=100% align=center border=0 cellspacing=0 cellpadding=0>
										<tr>
											<td valign=top width=200>Von : <a href="' . $PHP_SELF . '?area=user_view&id=' . $output[$i][creator] . '" class=blue><b>' .
												$output[$i][v_name] . '&nbsp;' . $output[$i][n_name] . '

													</b></a><br>
													Am&nbsp;:&nbsp;' . strftime('%e.%m.%Y&nbsp;%H:%M', $output[$i][date]) . '<br>
													<a href="' . $PHP_SELF . '?area=forum_createmessage&ref_id=' . $output[$i][id] . '&id=' . $forum_id . '" class=blue><b>&gt;&gt; antworten</b></a><br>
													<a href="' . $PHP_SELF . '?area=forum_read&id=' . $id . '&forum_id=' . $forum_id . '#top" class=blue><b>&Lambda; nach oben</b></a>
											</td>
											<td width=' . ($output[$i][depth] * 3) . ' bgcolor=#F2F5FF>&nbsp;
											</td>
											<td valign=top align=left bgcolor=#F2F5FF>
												' . $temp . '

											</td>
										</tr>';

							$message .= '
									</table>';
							$box = new InfoBox('<a name="' . $output[$i][id] . '"></a><b class=' . $style . '>' . $output[$i][heading] . '</b>', $message, 0, '95%');
						}


			$ForumView->html_footer();
?>
