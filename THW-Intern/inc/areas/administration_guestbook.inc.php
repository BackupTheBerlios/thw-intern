<?php

	require_once('inc/classes/class_guestbook.inc.php');
	
	$page->title_bar();

	
	$guestbook = new Guestbook();
		
	if ($remove_entry)
	{
		$message = '<b class=important>Eintrag l&ouml;schen!</b>';
		if ($guestbook->remove_entry($id))
		{
			$message = '<br><b class=ok>Eintrag gel&ouml;scht!</b>';
		}
		else
		{
			$message = '<br><b class=error>Eintrag nicht gel&ouml;scht!</b>';
		}
	}
	
	$message .= $guestbook->show_entries_interface(1);

	echo $page->dialog_box('G&auml;stebuch administrieren', $message, 0, 0, '80%');

?>
