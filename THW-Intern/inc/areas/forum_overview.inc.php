<?php

	require_once('inc/classes/class_forum.inc.php');
	require_once('inc/classes/class_form2.inc.php');

	$page->title_bar();

	$forum = new Forum();

	$filter = array(
			'compact' => 1,
//			'hide_old' => 10
		);

	if ($thread_id)
	{
		$filter[thread_id] = $thread_id;
	}

	echo $forum->load_messages($forum_id, $filter);



?>
