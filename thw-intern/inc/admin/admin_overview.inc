<?php

			$AdminOverview = new Page('Administration', $area, $GLOBALS[user_id], DEFAULT_STYLESHEET, $db, 1);
			$AdminOverview->html_header();

			$news = $db->query($sql);

			$menu = array();
			$menu[0][link] = "$PHP_SELF";
			$menu[0][text] = 'Startseite';

			$AdminOverview->pagetitle('Administration', $menu);
			echo '<br>';

			echo '
					<table width=95% align=center>
						<tr>
							<td width=50% valign=top>';
							
						$menu = array();
						$menu[0][link] = "$PHP_SELF?area=admin_backup";
						$menu[0][text] = 'Backup erstellen';
						$output = 'Hier können verschiedene Backups erstellt werden.';
						$Box = new Column('Backup', $output, $menu, '99%');

						$menu[0][link] = "$PHP_SELF?area=admin_permissions";
						$menu[0][text] = 'Zugangsrechte bearbeiten';
						$output = 'Hier können Zugangsrechte betrachtet, editiert, erstellt und gelöscht werden!';
						$Box->Column('Zugangsrechte', $output, $menu, '99%');

					echo '
							</td>
							<td valign=top>';
						$menu[0][link] = "$PHP_SELF?area=admin_units";
						$menu[0][text] = 'Einheiten bearbeiten';
						$output = 'Hier können die Einheiten betrachtet, editiert, erstellt und gelöscht werden!';
						$Box->Column('Einheiten', $output, $menu, '99%');

						$menu[0][link] = "$PHP_SELF?area=admin_guestbook";
						$menu[0][text] = 'Gästebuch administrieren';
						$output = 'Hier wird das Gästebuch administriert';
						$Box->Column('Gästebuch', $output, $menu, '99%');

					echo '	</td>
						</tr>';

			echo '</table>';

			$AdminOverview->html_footer();
?>
