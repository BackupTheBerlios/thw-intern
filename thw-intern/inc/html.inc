<?php

class HTML
{
	function Table ( $width, $border, $data, $rows, $cols )
	{
		$output =
			"<table width=$width border=$border>";
			
			for ( $y = 0; $y < $rows; $y++)
			{
				$output .=
					"<tr>";

				for ( $x = 0; $x < $cols; $x++ )
				{
					$output .= "<td> $data[x][y] </td>";
				}

				$output .=
					"</tr>";
			}

		$output .=
			"</table>";

		return ( );
	}
}

?>