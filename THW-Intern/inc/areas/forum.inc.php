<?php

	$page->title_bar();

	// Erstmal die Foren holen...
	$sql = '
			select
				' . TB_FOREN . '.name as forum_name,
				' . TB_FOREN . '.beschreibung,
				' . TB_FOREN . '.id,
				' . TB_USER . '.name,
				' . TB_USER . '.email,
				' . TB_USER . '.vorname
			from
				' . TB_FOREN . ',
				' . TB_USER . '
			where
				' . TB_USER . '.id = ' . TB_FOREN . '.ref_user_id
		';

	$raw = $db->query($sql);
	if ($db->num_rows($raw))
	{
		// ERstmal Anzahl der neuen Beiträge holen...
			$sql = '
					select
						count(*),
						ref_foren_id
					from
						' . TB_BEITRAEGE . '
					where
						date_format(date_create, "%Y%m%d%H%i%s") > ' . $session->user_info('last_login') . '
					group by
						ref_foren_id
				';

			// echo nl2br($sql) . '<br>';

			$counter_raw = $db->query($sql);
			if ($db->num_rows($counter_raw))
			{
				$counter_new = array();
				while ($tmp = $db->fetch_array($counter_raw))
				{
					$counter_new[$tmp[1]] = $tmp[0];
				}
			}

		// Anzahl aller Beiträge
			$sql = '
					select
						count(*),
						ref_foren_id
					from
						' . TB_BEITRAEGE . '
					group by
						ref_foren_id
				';

			// echo nl2br($sql) . '<br>';

			$counter_raw = $db->query($sql);
			if ($db->num_rows($counter_raw))
			{
				$counter = array();
				while ($tmp = $db->fetch_array($counter_raw))
				{
					$counter[$tmp[1]] = $tmp[0];
				}
			}


			$message ='
						<table width=100% align=center border=0 class=list_table cellspacing=4>
							<th>Forum</th><th colspan="2">Beiträge</th><th>Beschreibung</th><th>Admin</th>
				';
			while ($tmp = $db->fetch_array($raw))
			{
				$message .= '
							<tr class="list_table_active">
								<td>
									<a href="' . $PHP_SELF . '?action=forum_overview&forum_id=' . $tmp[id] . '" title="Dieses Forum öffnen">' . $tmp[forum_name] . '</a>
								</td>
								<td title="Neue Beiträge seit <b>' . $session->user_info('last_login_readable') . '</b>" align="center"  class="list_table_important">
									<b>' . $counter_new[$tmp[id]] . '</b>
								</td>
								<td title="Beiträge gesamt"  align="center">
									<b>' . (0 + $counter[$tmp[id]]) . '</b>
								</td>
								<td>
									' . $tmp[beschreibung] . '
								</td>
								<td align=center>
									<a href="mailto:' . $tmp[email] . '" title="Dem Forenadmin eine Mail schreiben!">' . $tmp[name] . ',&nbsp;' . $tmp[vorname] . '</a>
								</td>
							</tr>
					';
			}

			$message .= '
						</table>
				';

	}
	else
	{
		$message = 'Whoups, keine Foren gefunden! Es wurden anscheinend noch keine angelegt...';
	}

	echo $page->dialog_box('Foren', $message, $menu, $menu2, '90%');

?>
