<?php

	$page->title_bar();

		$sql = '
				select
					' . TB_BERICHT . '.*,
					' . TB_OBJECT . '.*,
					date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y %H:%i") as date_create
				from
					' . TB_BERICHT . ',
					' . TB_OBJECT . '
				where
					' . TB_OBJECT . '.flag_public = 0
					and
					 ' . TB_BERICHT . '.ref_object_id = ' . TB_OBJECT . '.id
					and
					' . TB_BERICHT . '.berichtart = ' . REPORTTYPE_NEWS . '
				order by
					' . TB_OBJECT . '.date_create desc
				limit
					' . MAX_REPORTS_PER_COLUMN . '
			';

		// echo $sql . '<br>';

	$raw = $db->query($sql);

	$private_news = '
				<table width=100% align=center border=0 class=list_table>
					<th>Datum</th><th>Titel</th>
		';

	while ($current = $db->fetch_array($raw))
	{
		$private_news .= '
				<tr class=list_table_active>
					<td width=30%>
						' . $current[date_create] . '
					</td>
					<td>
						<a href="' . $PHP_SELF . '?action=news_read&id=' . $current[ref_object_id] . '">
							' . $current[titel] . '
						</a>
					</td>
				</tr>
				';

	}
	$private_news .= '
			</table>
		';


	$sql = '
			select
				' . TB_BERICHT . '.*,
				' . TB_BERICHT . '.id as news_id,
				' . TB_OBJECT . '.*,
				date_format(' . TB_OBJECT . '.date_create, "%d.%m.%Y %H:%i") as date_create
			from
				' . TB_BERICHT . ',
				' . TB_OBJECT . '
			where
				' . TB_OBJECT . '.flag_public = 1
				and
				' . TB_OBJECT . '.id = ' . TB_BERICHT . '.ref_object_id
				and
				' . TB_BERICHT . '.berichtart =  ' . REPORTTYPE_NEWS . '
			order by
				' . TB_OBJECT . '.date_create desc
		';

	$raw = $db->query($sql);

	// Darf der User Berichte freigeben??
	if ( $rights->check_right($session->user_info('id'), 'report_publish') or ($session->user_info('rights') == ROOT) )
	{
		$report_publisher = 1;
	}

	$public_news = '
				<table width=100% align=center border=0 class=list_table>
					<th>Datum</th><th>Titel</th>
		';
	if ($report_publisher)
	{
		$public_news .= '
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
		$public_news .= '
				<tr class="' . $class . '">
					<td width=30%>
						' . $current[date_create] . '
					</td>
					<td>
						<a href="' . $PHP_SELF . '?action=news_read&id=' . $current[ref_object_id] . '">
							' . $current[titel] . '
						</a>
					</td>
				';
		if ($report_publisher)
		{
			$public_news .= '
					<td width=10%>
						<a href="' . $PHP_SELF . '?action=report_publish&id=' . $current[ref_object_id] . '" title="Hier klicken um den Beitrag freizugeben oder wieder zurückzunehmen">
							' . $public . '
						</a>
					</td>
				';
		}

		$public_news .= '
				</tr>
				';

	}
	$public_news .= '
				<tr>
					<td colspan=5>
						<p class=small><b>Legende: </b><br>
						<b class=list_table_inactive>Grau</b> hinterlegte Reihen sind News die öffentlich
						eingetragen, aber noch nicht freigegeben wurden! Alle anderen News sind öffentlich!
					</td>
				</tr>
			</table>
		';


	echo '
		<table width=96% align=center border=0 cellpadding=3>
			<tr>
				<td width=50% valign=top>
					' . $page->dialog_box('Öffentliche News', $public_news, 0, 0, '100%') . '
				</td>
				<td valign=top>
					' . $page->dialog_box('Interne News', $private_news , 0, 0, '100%') . '
				</td>
			</tr>
		</table>
		';

?>
