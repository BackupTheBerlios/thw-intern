<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 23.07.2003
* Last edit: 23.07.2003
*
* class Lister
*
* Funktion:
*
* Parameter:
*			Keine
*
* Rückgabe:
* 			Keine
*
* Bemerkungen:
*
* TODO:
*
* DONE:
*
*******************************************************************************/

class Lister
{
	var
			$temp
		;

	/*******************************************************************************
	* Last edit: 23.07.2003
	*
	* function Lister()
	*
	* Funktion:
	*			Konstruktor
	*
	* Parameter:
	*
	* Rückgabe:
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function Lister()
	{
	}

	/*******************************************************************************
	* Last edit: 23.07.2003
	*
	* function Lister()
	*
	* Funktion:
	*			Konstruktor
	*
	* Parameter:
	*
	* Rückgabe:
	*
	* Bemerkungen:
	*
	* TODO:
	*
	* DONE:
	*
	*******************************************************************************/
	function build_table($table_structure, $last_login)
	{
		$current_date = strftime('%Y%m%d%H%M%S');
		$output = '';

		if (count($table_structure))
		{
			/***************************************************************************
			* Die Tabelle an sich bauen!! Tabelleninfos stehen in Eintrag 0 von
			* $table_structure
			***************************************************************************/
			reset($table_structure);
			$tmp = each($table_structure);
			$output = '
							<TABLE ' . $this->little_helper($tmp[value]) . '>';

			/***************************************************************************
			* Jetzt bauen wir die TR's, TH's und die TD's
			***************************************************************************/
			while($current = each($table_structure))
			{
				switch($current[type])
				{
					/***************************************************************************
					* Tableheader:
					***************************************************************************/
					case 'th':
							if (count($current[value]))
							{
								while($tmp = each($current[value]))
								{
									$output .= $this->build_th($tmp);
								}
							}
						break;

					/***************************************************************************
					* Normale Reihe
					***************************************************************************/
					default:
							if (count($current[value][fields]))
							{
								$output .= '
								<TR>';
								while($tmp = each($current[value]))
								{
									$output .= $this->build_td($tmp);
								}
								$output .= '
								</TR>';
							}
				}
			}

			$output .= '
							</TABLE>';

			return($output);

		}
		else
		{
			return(0);
		}
	}

	function build_th($data)
	{
		$output = '
									<TH ' . $this->little_helper($data[params]) . '>' . $data[contents] . '</TH>';
		return($output);
	}

	function build_td($data)
	{
		$output = '
										<TD ' . $this->little_helper($data[params]) . '>' . $data[contents] . '</TD>';
		return($output);
	}


	function little_helper($data)
	{
		$output = '';

		while ($current = each($data))
		{
			$output .= $current[key] . '="' . $current[value] . '" ';
		}

		return($output);
	}

}

?>
