<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 24.07.2003
* Last edit: 24.07.2003
*
* administration_user_edit.inc.php
*
* Funktion:
*
* Bemerkungen:
*
* TODO:
*
* DONE:
*
*******************************************************************************/

	require_once('inc/classes/class_form2.inc.php');
	require_once('inc/classes/class_user.inc.php');

	$page->title_bar();

	$user = new User();

	$menu = array();
	$menu[0][text] = 'Account editieren';
	$menu[0][link] = $PHP_SELF . '?action=intern_myaccount_edit&id=' . $session->user_info('id');
	$menu[1][text] = 'Style ändern';
	$menu[1][link] = $PHP_SELF . '?action=intern_myaccount_style&id=' . $session->user_info('id');

	echo '
			<table width="50%" align="center" border="0">
				<tr>
					<td>
						' . $user->view_user_interface($session->user_info('id'), 1, $menu, 'Mein Account') . '
					</td>
				</tr>
			</table>';

?>
