<?php

	$menu = array();
	$menu[0][link] = $PHP_SELF;
	$menu[0][text] = 'Download hinzufügen';
	$page->title_bar($menu);

	$message ='
		Hier kommen mal die Downloads...
		';
	echo $page->dialog_box('Downloads', $message, 0, 0, '50%');

?>