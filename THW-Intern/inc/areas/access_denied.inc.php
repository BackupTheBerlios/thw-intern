<?php

	$page->title_bar();

	$message ='
		<p class=error> Whoups, da darfst du nicht rein... )-:</p>
		';
	$menu = array();
	$menu[0][text] = 'Naaaaa gut!';
	$menu[0][link] = $PHP_SELF;

	echo $page->dialog_box('Access denied!', $message, 0, $menu, '50%');
?>
