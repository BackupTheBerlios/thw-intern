<?php

	$page->title_bar();


	$menu = array();
	$menu[0][link] = $PHP_SELF . '?action=news';
	$menu[0][text] = 'Übersicht';
	$menu[1][link] = $PHP_SELF . '?action=news_edit&id=' . $id;
	$menu[1][text] = 'News editieren';
	$menu[2][link] = $PHP_SELF . '?action=report_publish&id=' . $id;
	$menu[2][text] = 'News freigeben/zurückziehen';
	$menu[3][link] = $PHP_SELF . '?action=news_delete&id=' . $id;
	$menu[3][text] = 'News löschen';

	$bericht = new tb_bericht();
	$bericht->load_tb_bericht($id);

	$message = '
									<table width=100% align=center border=0>
										<tr>
											<td>
												<h2>' . $bericht->return_field('titel') . '</h2>
											</td>
										</tr>
										<tr>
											<td>
												<p>' . nl2br($bericht->return_field('text')) . '</p>
											</td>
										</tr>
										<tr>
											<td align=right class=small>
												erstellt von <b>' . $bericht->return_field('vorname') . ' ' . $bericht->return_field('name') . '</b> am ' . nl2br($bericht->return_field('date_create')) . '
											</td>
										</tr>
									</table>
		';

	echo $page->dialog_box('News lesen', $message, $menu, 0, '');

?>

