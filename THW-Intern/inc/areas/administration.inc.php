<?php

	$page->title_bar();

	$sql = '
			select
				count(*) as anzahl
			from
				' . TB_USER . '
			';

	$alle_user = $db->fetch_array($db->query($sql));

	$sql = '
			select
				count(*) as anzahl
			from
				' . TB_USER . '
			where
				flag_active > 0
			';
	$aktive_user = $db->fetch_array($db->query($sql));

	$sql = '
			select
				count(*) as anzahl
			from
				' . TB_USER . '
			where
				flag_online > 0
			';
	$online_user = $db->fetch_array($db->query($sql));


	$benutzerverwaltung = '
					<table width=100% align=center border=0>
						<tr>
							<td>
								Aktive User:
							</td>
							<td>
								<b>
									' . $aktive_user[anzahl] . '
								</b>
							</td>
						</tr>
						<tr>
							<td>
								Inaktive User (gelöscht):
							</td>
							<td>
								<b>
									' . ($alle_user[anzahl] - $aktive_user[anzahl]) . '
								</b>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<hr>
							</td>
						</tr>
						<tr>
							<td>
								User gesamt:
							</td>
							<td>	
								<b>
									' . $alle_user[anzahl] . '
								</b>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<br>
							</td>
						</tr>
						<tr>
							<td>
								User online:
							</td>
							<td>
								<b>
									' . $online_user[anzahl] . '
								</b>
							</td>
						</tr>
					</table>
		';

	$menu_user = array();
	$menu_user[0][link] = $PHP_SELF . '?action=administration_user';
	$menu_user[0][text] = 'Zur Benutzerverwaltung';


	$filesize = 0;
	$counter = 0;
	$handle = opendir('var/log');
	while (false !== ($file = readdir($handle)))
	{
		if ($file == '..')
		{
			$file = 0;
		}
		if ($file == '.')
		{
			$file = 0;
		}

		if ($file)
		{
			$filesize += filesize('var/log/' . $file);
			$counter++;
		}
	}
	$logmessage = 'Es sind <b>'  . $counter . '</b> Logfiles vorhanden, sie belegen momentan
			<b>' . $filesize . '</b> Bytes (= <b>' . ceil($filesize / 1024) . '</b> KB)!';
	$menu_log = array();
	$menu_log[0][link] = $PHP_SELF . '?action=administration_logviewer';
	$menu_log[0][text] = 'Logfiles lesen';


	$message = '
		<table width=96% align=center border=0 cellpadding=3>
			<tr>
				<td width=50% valign=top>
					' . $page->dialog_box('Benutzerverwaltung', $benutzerverwaltung, $menu_user, 0, '100%') . '
				</td>
				<td valign=top>
					' . $page->dialog_box('Logfiles', $logmessage, $menu_log, 0, '100%') . '
				</td>
			</tr>
		</table>
		';


	echo $page->dialog_box('Administration', $message, 0, 0, '');

?>
