<?php

	$page->title_bar();


	$temp = new tb_bericht2();

	if (!$offset)
	{
		$offset = 0;
	}

		$sql = '
				select
					' . TB_BERICHT . '.*,
					' . TB_OBJECT . '.*,
					date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y") as date_begin
				from
					' . TB_BERICHT . ',
					' . TB_OBJECT . '
				where
					' . TB_OBJECT . '.flag_public = 0
					and
					' . TB_OBJECT . '.id = ' . TB_BERICHT . '.ref_object_id
					and
					' . TB_BERICHT . '.berichtart < 5
				order by
					' . TB_OBJECT . '.date_begin desc
				limit
					' . $offset . ', ' . MAX_REPORTS_PER_COLUMN . '
			';

		// echo $sql . '<br>';

	$raw = $db->query($sql);
	if ($db->num_rows($raw))
	{
		$private_reports = '
					<table width=100% align=center border=0 class=list_table>
						<th>Datum</th><th>Typ</th><th>Titel</th>
			';

		while ($current = $db->fetch_array($raw))
		{
			$private_reports .= '
					<tr class=list_table_active>
						<td width=10%>
							' . $current[date_begin] . '
						</td>
						<td width=10%>
							' . $REPORT_TYPES[$current[berichtart]][name] . '
						</td>
						<td>
							<a href="' . $PHP_SELF . '?action=report_read&id=' . $current[ref_object_id] . '">
								' . $current[titel] . '
							</a>
						</td>
					</tr>
					';

		}
		$private_reports .= '
				</table>
			';
	}
	else
	{
		$private_reports = 'Keine Berichte gefunden... )o:';
	}



	// Alle öffentlichen Berichte holen!
		$sql = '
				select
					' . TB_BERICHT . '.*,
					' . TB_OBJECT . '.*,
					date_format(' . TB_OBJECT . '.date_begin, "%d.%m.%Y") as date_begin
				from
					' . TB_BERICHT . ',
					' . TB_OBJECT . '
				where
					' . TB_OBJECT . '.flag_public = 1
					and
					' . TB_OBJECT . '.id = ' . TB_BERICHT . '.ref_object_id
					and
					' . TB_BERICHT . '.berichtart < 5
				order by
					' . TB_OBJECT . '.date_begin desc
				limit
					' . $offset . ', ' . MAX_REPORTS_PER_COLUMN . '
			';

		// echo $sql . '<br>';

	$raw = $db->query($sql);
	if ($db->num_rows($raw))
	{
		// Darf der User Berichte freigeben??
		if ( $rights->check_right($session->user_info('id'), 'report_publish') or ($session->user_info('rights') == ROOT) )
		{
			$report_publisher = 1;
		}

		$public_reports = '
					<table width=100% align=center border=0 class=list_table>
						<th>Datum</th><th>Typ</th><th>Titel</th>
			';
		if ($report_publisher)
		{
			$public_reports .= '
						<th>Freig.</th>
				';
		}

		while ($current = $db->fetch_array($raw))
		{
			if ($current[flag_public] and $current[flag_freigegeben])
			{
				$class = 'list_table_active';
				$public = 'Ja';
			}
			else
			{
				$class = 'list_table_inactive';
				$public = 'Nein';
			}
			$public_reports .= '
					<tr class="' . $class . '">
						<td width=10%>
							' . $current[date_begin] . '
						</td>
						<td width=10%>
							' . $REPORT_TYPES[$current[berichtart]][name] . '
						</td>
						<td>
							<a href="' . $PHP_SELF . '?action=report_read&id=' . $current[ref_object_id] . '">
								' . $current[titel] . '
							</a>
						</td>
					';
			if ($report_publisher)
			{
				$public_reports .= '
						<td>
							<a href="' . $PHP_SELF . '?action=report_publish&id=' . $current[ref_object_id] . '" title="Hier klicken um den Bericht freizugeben oder wieder zurückzunehmen">
								' . $public . '
							</a>
						</td>
					';
			}

			$public_reports .= '
					</tr>
					';
		}
		$public_reports .= '
					<tr>
						<td colspan=5>
							<p class=small><b>Legende: </b><br>
							<b class=list_table_inactive>Grau</b> hinterlegte Reihen sind Berichte die öffentlich
							eingetragen, aber noch nicht freigegeben wurden! Alle anderen Berichte sind öffentlich!
						</td>
					</tr>
				</table>
			';
	}
	else
	{
		$public_reports = 'Whoups, keine Berichte gefunden!';
	}

	$help = '
		Auf dieser Seite werden jeweils die letzten 10 öffentlichen und 10 internen Berichte und die letzten Einträge aus dem
		Tagebuch angezeigt. Um ältere Berichte einzusehen einfach in die <a href="?action=report_completelist">komplette Liste</a> schauen!
	';

	$overviews = '
			<ul>
				<li><a href="?action=report_completelist&sort_by_type=' . REPORTTYPE_NEWS . '">News</a> - Hier können alle News eingesehen werden!</li>
				<li><a href="?action=report_completelist&sort_by_type=' . REPORTTYPE_EINSATZ . '">Einsätze</a> - Hier werden alle Einsatzberichte gesammelt!</li>
			</ul>
		';

	echo '
		<table width=96% align=center border=0 cellpadding=3>
			<tr>
				<td width=50% valign=top>
					' . $page->dialog_box('Hilfe', $help, 0, 0, '100%') . '
					<br>
					' . $page->dialog_box('Übersichten', $overviews, 0, 0, '100%') . '
					<br>
					' . $page->dialog_box('Öffentliche Berichte', $public_reports, 0, 0, '100%') . '
				</td>
				<td valign=top>
					' . $page->dialog_box('Interne Berichte (OVCOM)', $private_reports, 0, 0, '100%') . '
					<br>
					' . $temp->list_tb_bericht_interface('Tagebuch', 'diary') . '
				</td>
			</tr>
		</table>
		';

?>
