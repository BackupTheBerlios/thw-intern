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

	$output[id] = $start_id;
	$output[creator] = $data[owner];
	$output[v_name] = $data[v_name];
	$output[n_name] = $data[n_name];
	if ($data[counter])
	{
		$output[counter] = $data[counter];
	}
	else
	{
		$output[counter] = '';
	}
	$output[date] = $data[date];

	if(strlen($data[heading]) > (40 - $depth))
	{
		$output[heading] = substr($data[heading], 0, (40 - $depth)) . '...';
	}
	else
	{
		$output[heading] = $data[heading];
	}

	$output[replies] = 0;

	$output[indent] = str_repeat(' &nbsp; &nbsp; ', $depth);

	return($output);
}

function thread_to_array($db, $start_id, $forum_id, $last_login, $depth = -1)
{
	$output = array();

	// Depth erhöhen, hiermit wird nachher der Einrück-Abstand berechnet!
	$depth++;

	// Zuerst den Startbeitrag mit usernamen Raussuchen!
	$sql = "select heading, owner, unix_timestamp(date) as date, unix_timestamp(last_viewed) as last_viewed, counter, v_name, n_name from " . DB_FORUM . ", " . DB_USERS . " where " . DB_FORUM . ".id=$start_id and unfinished=0 and forum_id=$forum_id and " . DB_USERS . ".id=owner";
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

function count_replies($db, $start_id, $last_login)
{
	$sql = "select ";
}


			$ForumView = new Page('Forum', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$ForumView->html_header();

			$news = $db->query($sql);

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';
			$menu[1][link] = "$PHP_SELF?area=forum_overview";
			$menu[1][text] = 'Forenübersicht';
			$menu[2][link] = "$PHP_SELF?area=forum_createmessage&ref_id=0&id=$id";
			$menu[2][text] = 'Neue Diskussion';


			$sql = "select forum_name from " . DB_FOREN . " where forum_id=$id";

			$forum_raw = $db->fetch_array($db->query($sql));

			$ForumView->pagetitle('Forum : <i>' . $forum_raw[forum_name] . '</i>', $menu);

			echo '<br>
					<table width=95% align=center>';

				// $sort_hide_older_than = $user_last_login;
				$sort_hide_older_than = 0;
				$sql = "select id from " . DB_FORUM .  " where ref_id=0 and forum_id=$id and unfinished=0 order by date desc";

				$all_postings = $db->query($sql);

				if ($db->num_rows($all_postings))
				{

					echo '<th class=bar width=10>Zugriffe</th><th width=10 class=bar>Antworten</th><th class=bar>Betreff</th><th class=bar width=10>Datum</th><th class=bar width=10>Von</th><th class=bar width=10>Antwort</th>';

					while ($current = $db->fetch_array($all_postings))
					{
						$expand_this = 0;
						if($expand == $current[id])
						{
							$expand_this = $expand;
						}

						$output = thread_to_array($db, $current[id], $id, $user_last_login);

						for ($i = 0; $i < count($output); $i++)
						{
							if (!$output[$i][replies])
							{
								$output[$i][replies] = '';
							}
							
							if ($output[$i][flag_new])
							{
								$class = 'red';
							}
							else
							{
								$class = 'blue';
							}

							echo '
									<tr>
										<td style="font-size: 9pt;" align=center>' . $output[$i][counter] . '</td>
										<td style="font-size: 9pt;" align=center>' . $output[$i][replies] . '</td>
										<td style="font-size: 9pt;">' . $output[$i][indent] . '<a href="' . $PHP_SELF . '?area=forum_read&id=' . $output[$i][id] . '&forum_id=' . $id . '#' . $output[$i][id] . '" class=' . $class . '>' . $output[$i][heading] . '</td>
										<td style="font-size: 9pt;">' . strftime('%e.%m.%Y %H:%M', $output[$i][date]) . '</td>
										<td style="font-size: 9pt;"><a href="' . $PHP_SELF . '?area=user_view&id=' . $output[$i][creator] . '" class=blue>' . $output[$i][v_name] . ' ' . $output[$i][n_name] . '</a></td>
										<td style="font-size: 9pt;" align=center><a href="' . $PHP_SELF . '?area=forum_createmessage&ref_id=' . $output[$i][id] . '&id=' . $id . '" class=blue>Los</a></td>
									</tR>';
						}
					}

				}



				echo '</table>';


			$ForumView->html_footer();
?>
