<?php

function yesno($value)
{
	if ($value > 0)
	{
		return(' Ja ');
	}
	else
	{
		return(' Nein ');
	}
}

function reserve_id($db)
{

	$sql = "insert into " . DB_DATES . " (begin, end, unfinished) values
								(" . time() . ", " . time() . ", 1)";
	$db->query($sql);

	$tmp = $db->fetch_array($db->query("select last_insert_id() as last_id"));

	return($tmp[last_id]);
}

function date_string2unix($date, $time) 	// macht aus zwei Strings TT.MM.JJJJ und HH:MM einen Unix-Timestamp
{
	$date_array = explode(".", $date);
	$time_array = explode(":", $time);
	$timestamp = mktime($time_array[0], $time_array[1], 0, $date_array[1], $date_array[0], $date_array[2]);
	return $timestamp;
}


function print_unit_checkbox_item($name, $rank, $var_name, $id)
{
	for ($i = 0; $i < $rank; $i++)
	{
		$indent = $indent . '&nbsp;&nbsp;';
	}

	$output .= "<tr><td>$indent <input type=checkbox name=$var_name value=$id> $name</td></tr>\n";
	return($output);
}

function print_unit_checkbox($db, $start_id, $var_name)
{
	$sql = "select * from " . DB_UNITS . " where id=$start_id";
	$current = $db->fetch_array($db->query($sql));
	$output .= print_unit_checkbox_item($current[name], $current[rank], $var_name, $current[id]);

	$sql = "select id from " . DB_UNITS . " where ref_id=$start_id order by rank";
	$tmp = $db->query($sql);
	if ($db->num_rows($tmp))
	{
		while ($current  = $db->fetch_array($tmp))
		{
			$output .= print_unit_checkbox($db, $current[id], $var_name, $current[id]);
		}
	}

	return($output);
}

?>
