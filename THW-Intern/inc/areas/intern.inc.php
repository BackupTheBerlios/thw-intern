<?php

	$page->title_bar();

	$session->remove_idle_user();


	$menu = array();
	$menu[0][link] = $PHP_SELF;
	$menu[0][text] = 'Startseite';
	$menu[1][link] = $PHP_SELF . '?action=formtest';
	$menu[1][text] = 'Formulartest';



	/***********************************
	* Willkommensnachricht!
	***********************************/
	// Wieviele neue News?
	$sql = '
			select
				count(*) as count
			from
				' . TB_BERICHT . ',
				' . TB_OBJECT . '
			where
				berichtart = ' . REPORTTYPE_NEWS. '
				and
				date_create > ' . $session->user_info('last_login') . '
				and
				' . TB_OBJECT . '.id = ref_object_id
		';
	$raw = $db->query($sql);
	$new_news = $db->fetch_array($raw);

	// Wieviele User sind online
	$sql = '
			select
				count(*) as count,
				flag_online
			from
				' . TB_USER . '
			group by
				flag_online
		';

	$raw = $db->query($sql);
	$user_gesamt = $db->fetch_array($raw);
	$user_online = $db->fetch_array($raw);

	$welcome_message ='
			Hallo <b>' . $session->user_info('vorname') . ' ' . $session->user_info('name') . '</b>!
			Dein letzter Login war am <b>' . $session->user_info('last_login_readable') . '</b>! Von
			<b>' . ($user_gesamt[count] + $user_online[count]) . '</b> Usern sind <b>' . $user_online[count] . '</b> online!
			Seit deinem letzten Login wurden <b>' . $new_news[count] . '</b> neue News gepostet!
		';

	/***********************************
	* News
	***********************************/

	$sql = '
			select
				' . TB_BERICHT . '.id,
				' . TB_OBJECT . '.id as ref_object_id,
				MID(' . TB_BERICHT . '.text, 1, 200) as text,
				titel,
				name,
				vorname,
				date_format(date_create, "%d.%m.%Y %H:%i") as date_create,
				flag_public
			from
				' . TB_BERICHT . ',
				' . TB_USER . ',
				' . TB_OBJECT . '
			where
				berichtart = ' . REPORTTYPE_NEWS . '
				and
				' . TB_OBJECT . '.id = ref_object_id
				and
				' . TB_USER . '.id = ' . TB_OBJECT . '.ref_user_id
			order by
				' . TB_OBJECT . '.date_create desc
			limit
				' . MAX_NEWS_ON_STARTPAGE . '
		';

	$news_raw = $db->query($sql);

	if ($db->num_rows($news_raw))
	{
		$news_message = '
			<table width=100% align=center border=0>
			';
		while ($current = $db->fetch_array($news_raw))
		{
			if ($current[flag_public])
			{
				$class = 'form_important';
			}
			else
			{
				$class = 'form_default';
			}

			$news_message .= '
				<tr class="' . $class. '">
					<td class=small align=left>
						<b>' . $current[date_create] . '</b>
					</td>
					<td class=small>
						' . $current[vorname] . ' ' . $current[name] . '
					</td>
				</tr>
				<tr>
					<td class=' . $class . ' colspan=2>
						<h3 align=left style="margin-bottom: 0pt;">' . $current[titel] . '</h3>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 10pt;" colspan=2>
							' . nl2br($current[text]) . '... [<a href="' . $PHP_SELF. '?action=report_read&id=' . $current[ref_object_id] . '">mehr</a>]
						</a>
					</td>
				</tr>
				';
		}

		$news_message .= '
				<tr>
					<td colspan=2 class="small">
						<b class=form_left>Blau</b> hinterlegte News sind intern, <b class=form_important>rötlich</b> hervorgehobene sind öffentlich!
					</td>
				</tr>
			</table>
			';
	}
	else
	{
		$news_message = 'Keine News vorhanden... )o:';
	}



	$mom = '';


// 	require_once('inc/classes/class_forum.inc.php');
// 	$forum = new Forum();
// 		$filter = array(
// 			'compact' => 1,
// 			'hide_old' => 0
// 		);
//

	$report_lister = new tb_bericht2();
	/***********************************
	* Ausgabe!
	***********************************/
	echo '
		<table width=96% align=center border=0 cellpadding=3>
			<tr>
				<td width=50% valign=top>
					' . $page->dialog_box('Willkommen', $welcome_message, 0, 0, '100%') . '
					<br>
					' . $page->dialog_box('Liebling des Monats', $mom, 0, 0, '100%') . '
					<br>
					' . $report_lister->list_tb_bericht_interface('Tagebuch', 'diary') . '
					<br>

				</td>
				<td valign=top>
					' . $page->dialog_box('News', $news_message , 0, 0, '100%') . '
				</td>
			</tr>
		</table>';
?>

