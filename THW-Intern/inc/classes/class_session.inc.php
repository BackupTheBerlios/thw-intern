<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 27.07.2003
* Last edit: 27.07.2003
*
* class Session
*
* Funktion:
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


class Session
{
	var
		$local_user_data,
		$is_logged_in;


	/*******************************************************************************
	* Last edit: 27.07.2003
	*
	* function Session()
	*
	* Funktion:
	* 			Konstruktor
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
	function Session()
	{
		global $log;
		// $log->add_to_log('Session::Session()', 'Constructor called!', 'debug');

		if (session_is_registered('USER_DATA'))
		{
			$log->add_to_log('Session::Session()', 'Found some userdata in the session; trying to restore it...', 'debug');
			if ($this->local_user_data = unserialize($GLOBALS[USER_DATA]))
			{
				$log->add_to_log('Session::Session()', '...done!', 'debug');
			}
		}

		$this->is_logged_in = 0;
	}

	/*******************************************************************************
	* Last edit: 27.07.2003
	*
	* function load_userdata($id)
	*
	* Funktion:
	* 			Lädt alle Infos über einen User aus der Datenbank!
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
	function load_userdata($id)
	{
		global $log, $db;
		// $log->add_to_log('Session::load_userdata()', 'load_userdata(' . $id . ') called!!', 'debug');

		$sql = '
				select
					id,
					name,
					vorname,
					password,
					last_login,
					rang,
					stylesheet,
					date_format(last_login, "%d.%m.%Y %H:%i") as last_login_readable,
					ref_einheit_id,
					rights
				from
					' . TB_USER . '
				where
					id = ' . $id .'
			';

		$user_rawdata = $db->query($sql);
		if ($db->num_rows($user_rawdata))
		{
			/*******************************************************************************
			* Ok, Userdaten gefunden; jetzt speichern wir sie in die Session:
			*******************************************************************************/
			// $user_rawdata = $db->fetch_array($user_rawdata);
			$user_data = $db->fetch_array($user_rawdata);

			$this->local_user_data = $user_data;

			return(1);
		}
		else
		{
			$log->add_to_log('Session::load_userdata()', 'FATAL ERROR! Couldnt load userdata for id ' . $user_id, 'error');
			$log->add_to_log('Session::load_userdata()', 'Here\'s the query i used: ', 'error');
			$log->add_to_log('Session::load_userdata()', $sql, 'error');
			return(0);
		}
	}

	/*******************************************************************************
	* Last edit: 27.07.2003
	*
	* function start_session($user_id)
	*
	* Funktion:
	*
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
	function start_session($user_id)
	{
		global $log, $db;
		$log->add_to_log('Session::start_session()', 'start_session(' . $user_id . ') called!', 'debug');

		/*******************************************************************************
		* Cookies setzen damit wir wissen wer wir sind:
		*******************************************************************************/
		if (!setcookie('THW_USERID', $user_id))
		{
			$log->add_to_log('Session::start_session()', 'Uhm, probably not a problem; setcookie returne false for cookie THW_USERID!', 'debug');
		}

		/*******************************************************************************
		* Session starten:
		*******************************************************************************/
		session_name("THW_SID");
		session_start();

		$log->add_to_log('Session::start_session()', 'Started session for user ' . $user_id . ' with SID: ' . session_id(), 'debug');

		/*******************************************************************************
		* Jetzt schauen wir ersteinmal in der DB wer wir sind:
		*******************************************************************************/

		$this->load_userdata($user_id);

		/*******************************************************************************
		* So, dann können wir jetzt auch das Passwort speichern:
		*******************************************************************************/


		if (!setcookie('THW_PASS', $this->local_user_data[password]))
		{
			$log->add_to_log('Session::start_session()', 'Uhm, probably not a problem; setcookie returne false for cookie THW_PASS!', 'debug');
		}


		/*******************************************************************************
		* Und jetzt müssen wir den User noch als angemeldet markieren:
		*******************************************************************************/
		$sql = '
				update
					' . TB_USER . '
				set
					last_login = now(),
					last_action = now(),
					flag_online = 1
				where
					id = ' . $user_id . '
			';

		$db->query($sql);

		if ($db->affected_rows())
		{
			$this->is_logged_in = 1;

			$log->add_to_log('Session::start_session()', 'Starting new session for user ' . $this->local_user_data[id] . ': ' . $this->local_user_data[vorname] . ' ' . $this->local_user_data[name] . ', cookies sent!', 'session');

			/*******************************************************************************
			* So, jetzt speichern wir die User-Infos noch in der Session:
			*******************************************************************************/
			global $USER_DATA;
			$USER_DATA = serialize($this->local_user_data);
			if (session_register('USER_DATA'))
			{
				$log->add_to_log('Session::start_session()', 'Ok, userdata saved in session!!', 'session');
			}

			return(1);
		}
		else
		{
			return(0);
		}
	}



	/*******************************************************************************
	* Last edit: 27.07.2003
	*
	* function start_session($user_id)
	*
	* Funktion:
	*
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
	function remove_idle_user()
	{
		global $log, $db;
		$sql = '
				update
					' . TB_USER . '
				set
					flag_online = 0,
					last_action = 0
				where
					( last_action + INTERVAL ' . MAX_IDLE_TIME . ' SECOND ) < now()
					and
					flag_online = 1
			';
		$db->query($sql);

		if ($db->affected_rows())
		{
			$log->add_to_log('Session::remove_idle_user()', 'Marking ' . $db->affected_rows() . ' idle user(s) as offline... ', 'session');
		}
	}

	// check_session
	// Überprüft ob noch eine gültige Session vorliegt und ob der User damit noch
	// eingeloggt ist!
	function check_session()
	{
		global $log, $db;
		// $log->add_to_log('Session::check_session()', 'check_session() called!', 'debug');

		/*******************************************************************************
		* Prüfen ob wir überhaupt ne User-ID haben:
		*******************************************************************************/
		if ($_COOKIE[THW_USERID])
		{
			// $log->add_to_log('Session::check_session()', 'Found a valid user-id: ' . $_COOKIE[THW_USERID] . '!', 'debug');
			/*******************************************************************************
			* Dann prüfen wir ob der Webserver unsere Logindaten evtl.
			* schon wieder rausgeworfen hat:
			*******************************************************************************/
			if ($this->local_user_data[name])
			{

			}
			else
			{
				/*******************************************************************************
				* Gut, der Webserver hat die Sessiondaten gekillt!! Also
				* lesen wir die Userdaten wieder ein:
				*******************************************************************************/
				$this->load_userdata($_COOKIE[THW_USERID]);
			}

			/*******************************************************************************
			* Jetzt prüfen wir das Passwort!
			*******************************************************************************/
			// $log->add_to_log('Session::check_session()', 'comparing passwords (cookie vs. session): ' . $_COOKIE[THW_PASS] . ' vs. ' . $this->local_user_data[password]  , 'debug');
			if ($GLOBALS[_COOKIE][THW_PASS] == $this->local_user_data[password])
			{
				session_name('THW_SID');
				session_start();

				$sql = '
						update
							' . TB_USER . '
						set
							last_action = now(),
							flag_online = 1
						where
							id = ' . $this->local_user_data[id] . '
					';

				$db->query($sql);
				return(1);
			}
			else
			{
				$log->add_to_log('Session::check_session()', 'Urgs! Cookie password isn\'t similar to the one in the session!!', 'debug');
				$this->is_logged_in = 0;
				return(0);
			}
		}
		else
		{
			$this->is_logged_in = 0;
			$log->add_to_log('Session::check_session()', 'Found no user id in cookies!', 'debug');
			return(0);
		}
	}


	// close_session:
	// Terminiert die session, löscht alle gespeicherten Variablen und markiert den User
	// in der Datenbank als offline!
	function close_session()
	{
		global $log, $db;
		setcookie('THW_USERID');
		setcookie('THW_PASS');

		$sql = '
				update
					' . TB_USER . '
				set
					last_action = 0,
					flag_online = 0
				where
					id = ' . $this->local_user_data[id] . '
			';

		$db->query($sql);

		session_destroy();
		$log->add_to_log('Session::close_session()', 'User ' . $this->local_user_data[vorname] . ' ' . $this->local_user_data[name] . ' logged off! Good bye!', 'session');
	}

	// user_info:
	// Gibt ein Feld aus dem Userdatenarray zurück
	function user_info($field)
	{
		return($this->local_user_data[$field]);
	}
}


?>
