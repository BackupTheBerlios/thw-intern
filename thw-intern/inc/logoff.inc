<?php
			setcookie ("user_password", "", time() - 3600);
			setcookie ("user_id", "", time() - 3600);

			$LogoffSeite = new Page('Log off', '', 0, DEFAULT_STYLESHEET, $db, 0);
			$LogoffSeite->html_header();

			$options = array();
			$options[right][text] = 'Nochmal anmelden?';
			$options[right][link] = $PHP_SELF;

			$sql = "update " . DB_USERS . " set last_action=" . time() . ", last_login=" . time() . ", online=0 where id=$user_id";
			$db->query($sql);
			$box = new InfoBox('Abgemeldet', 'Du hast dich erfolgreich abgemeldet...<br>Login-Cookies wurden gelöscht!<br>', $options);
			session_destroy();

			$LogoffSeite->html_footer();
?>
