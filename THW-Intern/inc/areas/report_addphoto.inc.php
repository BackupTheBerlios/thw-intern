<?php

	function datestring2unix($date)
	{
		$date_array = explode(".", $date);
		$timestamp = mktime(0, 0, 0, $date_array[1], $date_array[0], $date_array[2]);
		return $timestamp;
	}

	$page->title_bar();

	$menu = array();
	$menu[0][link] = $PHP_SELF . '?action=report';
	$menu[0][text] = 'Berichtübersicht';
	$menu[1][link] = $PHP_SELF . '?action=report_read&id=' . $id;
	$menu[1][text] = 'Zurück zum Bericht';

	if ($start_upload)
	{
		$bericht = new tb_bericht();
		$message = '
				Es wurden effektiv ' . $bericht->add_photos($id, 'new_image') . ' Bilder hochgeladen...
			';

		$menu[2][link] = $PHP_SELF . '?action=report_addphoto&id=' . $id;
		$menu[2][text] = 'Weitere Bilder hinzufügen';

		echo $page->dialog_box('Berichte - Bilder hinzufügen', $message, $menu, 0, '50%');
	}
	else if ($submit)
	{
		if (!$number_of_images)
		{
			$number_of_images = 1;
		}

		$message = 'Anzahl der Bilder : ' . $number_of_images . '
				<form action="' . $PHP_SELF . '" method="post" enctype="multipart/form-data">
					<table align=center border=0>
			';

		for ($i = 0; $i < $number_of_images; $i++)
		{
			$message .= '
						<tr>
							<td align=right>#' . ($i + 1) . '</td>
							<td><input type=file name="new_image[]"></td>
						</tr>
				';
		}

		$message .= '
						<tr>
							<td align=right colspan=2>
										<input type=hidden name=action value="report_addphoto">
										<input type=hidden name=id value="' . $id . '">
										<input type=submit name="start_upload" value="Weiter &gt;&gt;">
							</td>
						</tr>
					</table>
			';


		echo $page->dialog_box('Berichte - Bilder hinzufügen', $message, $menu, 0, '50%');
	}
	else
	{

		$message = '
						<form action="' . $PHP_SELF . '" method="get">
							<table align=center border=0>
								<tr>
									<td align=right>
										Anzahl der Bilder:
									</td>
									<td>
										<input type=text size=5 name=number_of_images value=1>
									</td>
								</tr>
								<tr>
									<td colspan=2 align=right>
										<input type=hidden name=action value="report_addphoto">
										<input type=hidden name=id value="' . $id . '">
										<input type=submit name=submit value="Weiter &gt;&gt;">
									</td>
								</tr>
							</table>
						</form>
			';

		echo $page->dialog_box('Berichte - Bilder hinzufügen', $message, $menu, 0, '50%');
	}

?>

