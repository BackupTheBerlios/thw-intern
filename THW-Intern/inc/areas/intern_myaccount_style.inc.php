<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 24.07.2003
* Last edit: 24.07.2003
*
* intern_myaccount_style.inc.php
*
* Funktion:
*			Erlaubt es dem User aus verschiedenen Stylesheets zu wählen!
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


	if ($change_style)
	{
		$sql = '
				update
					' . TB_USER . '
				set
					stylesheet = "' . $change_style . '"
				where
					id = ' . $session->user_info('id') . '
			';
		$db->query($sql);
		$session->load_userdata($session->user_info('id'));
	}

	$title = 'Style wählen';
	$message = 'Dies sind die möglichen Stylesheets:
					<table width=100% align=center class=list_table>';

	if ($handle = opendir('etc/css'))
	{

		/* This is the correct way to loop over the directory. */
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
				if ($file == $session->user_info('stylesheet'))
				{
					$class = 'list_table_less_important';
				}
				else if ((!$session->user_info('stylesheet')) and ($file == 'default.css'))
				{
					$class = 'list_table_less_important';
				}
				else
				{
					$class = 'list_table_active';
				}

				$message .= '
							<tr class=' . $class . '>
								<td>
									<a href="' . $PHP_SELF . '?action=intern_myaccount_style&change_style=' . $file . '&id=' . $id . '" title="Diesen Style auswählen!">' . $file . '</a>
								</td>
							</tr>';
			}
		}
	}
	$message .= '
					</table>
					<p><b>Hinweis:</b> Die Änderungen werden erst nach einem weiterem Neuladen der Seite wirksam!';

	echo $page->dialog_box($title, $message, $menu, 0, '50%');

?>
