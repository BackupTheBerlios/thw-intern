<?php
/*******************************************************************************
* (c) 2003 Jakob Külzer, Jakob@TarnkappenBaum.org
* visit http://www.TarnkappenBaum.org for more information
* Created: 04.09.2003
* Last edit: 08.09.2003
*
* migrate.php
*
* Funktion:
*
* Bemerkungen:
* 		EIN KOTZBROCKEN!
*
* TODO:
*
* DONE:
*
*******************************************************************************/

	/***************************************************************************
	* Laden der Einstellungen
	***************************************************************************/
	require_once("etc/tables.inc.php");
	require_once("etc/names.inc.php");
	require_once("etc/database.inc.php");

	/***************************************************************************
	* Laden der wichtigsten Bibliotheken und Klassen
	***************************************************************************/
	require_once("inc/classes/classes.inc.php");
	require_once('inc/classes/class_log.inc.php');
 	require_once('inc/classes/class_tb.inc.php');
	require_once('inc/classes/class_database.inc.php');
	require_once("inc/classes/tb_classes.inc.php");
	require_once('inc/classes/class_form2.inc.php');

	/***************************************************************************
	* Alte Tabellennamen laden:
	***************************************************************************/
	define('TB_USERS_SOURCE', 'users');
	define('TB_EINHEITEN_SOURCE', 'einheiten');
	



	/***************************************************************************
	* Locale setzen, damit strftime deutsche Wochentage ausgibt!
	* FUNKTIONIERT NICHT!! BUG IN PHP??
	***************************************************************************/
	setlocale("LC_TIME", 'DE');

	/***************************************************************************
	* Log-Objekt
	***************************************************************************/
	$log = new Log();

	/***************************************************************************
	* Datenbankobjekt
	***************************************************************************/
	$db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$db->debug(0);

	session_start();

	$page = new Interface('default.css');
	$page->html_header();
	$page->title_bar();
	
	switch($action)
	{
		case 'preferences':
				// Hier stellen wir ein welchen Datenbankserver wir benutzen und von wo zum
				// Beispiel die Photos geladen werden sollen!
				
				$tmp = 0;
				
				$form = new Form2($PHP_SELF, 'get');
				
				$fields = array();

        		$fields[$tmp++] = array(
        				'type' => 'separator',
        				'value' => '<b>Quellserver:</b>'
        			);
                $fields[$tmp++] = array(
                		'name' => 'sql_server',
                		'type' => 'text',
                		'title' => 'SQL-Server:',
                		'important' => 1,
                		'attribs' => array(
                				0 => array(
                						'name' => 'title',
                						'value' => 'Hier den Quell-SQL-Server eingeben von dem die Daten bezogen werden sollen!'
                					),
                				1 => array(
                						'name' => 'size',
                						'value' => '20'
                					)
                			)
                	);
                $fields[$tmp++] = array(
                		'name' => 'sql_user',
                		'type' => 'text',
                		'title' => 'SQL-User:',
                		'important' => 1,
                		'attribs' => array(
                				0 => array(
                						'name' => 'title',
                						'value' => 'Hier den Usernamen für den Quell-SQL-Server eingeben von dem die Daten bezogen werden sollen!'
                					),
                				1 => array(
                						'name' => 'size',
                						'value' => '20'
                					)
                			)
                	);
                $fields[$tmp++] = array(
                		'name' => 'sql_database',
                		'type' => 'text',
                		'title' => 'SQL-Datenbank:',
                		'important' => 1,
                		'attribs' => array(
                				0 => array(
                						'name' => 'title',
                						'value' => 'Hier die Datenbank für den Quell-SQL-Server eingeben von dem die Daten bezogen werden sollen!'
                					),
                				1 => array(
                						'name' => 'size',
                						'value' => '20'
                					)
                			)
                	);
                $fields[$tmp++] = array(
                		'name' => 'sql_password',
                		'type' => 'password',
                		'title' => 'SQL-Password:',
                		'important' => 1,
                		'attribs' => array(
                				0 => array(
                						'name' => 'title',
                						'value' => 'Hier das Password für den Quell-SQL-Server eingeben von dem die Daten bezogen werden sollen!'
                					),
                				1 => array(
                						'name' => 'size',
                						'value' => '20'
                					)
                			)
                	);
        		$fields[$tmp++] = array(
        				'type' => 'separator',
        				'value' => '<b>Zielserver</b> (aus etc/database.inc.php):<br>
							Server : <b>' . DB_HOST . ' </b><br>
							User: <b>' . DB_USER . '</b><br>
							Datenbank: <b>' . DB_DATABASE . '</b>' 
        			);
        		$fields[$tmp++] = array(
        				'type' => 'hidden',
        				'important' => 0,
        				'selections' => array(
        						0 => array(
        								'value' => 'preferences',
        								'name' => 'action'
        							)
        					)
        			);
        		$fields[$tmp++] = array(
        				'type' => 'buttons',
        				'important' => 0,
        				'selections' => array(
        						0 => array(
        								'value' => 'speichern&gt;&gt;',
        								'type' => 'submit',
        								'name' => 'submit'
        							),
        						1 => array(
        								'value' => 'Reset',
        								'type' => 'reset',
        								'name' => 'reset'
        							)
        					)
        			);
        			
        		if ($sql_server)
        		{
        			$presets = array();
        			$presets[sql_server] = $sql_server;
        			$presets[sql_user] = $sql_user;
        			$presets[sql_database] = $sql_database;
        			$presets[sql_password] = $sql_password;
        			$form->load_presets($presets);
        		}

				$form->load_form($fields);
				$form->precheck_form();

				if (! $GLOBALS[submit])
				{
					$form->set_precheck_error();
				}
				
				if ($form->is_form_error())
				{
					$message = $form->build_form();
				}
				else
				{
					$message = 'Daten werden in der Session gespeichert...';
					session_register('sql_server', 'sql_user', 'sql_database', 'sql_password');
				}

				$menu = array();
				$menu[0][text] = 'Übersicht';
				$menu[0][link] = $PHP_SELF;
				
				echo $page->dialog_box('Einstellungen', $message, $menu, 0, '50%');
			break;
		case 'photos':
			break;
			
		case 'migrate_users':
				// Userdatenbank migrieren:

				// SQL Server eingetragen?
				if ($sql_server AND $sql_database)
				{								
    				// Verbindung zur alten Datenbank aufbauen:
					$db_source = new Database($sql_server, $sql_user, $sql_password, $sql_database);
					if ($db_source)
					{        
        				// Erstmal alle User aus der Datenbank laden:
        				$sql = '
							select 
								n_name, 
								v_name,
								id
							from
								' . TB_USERS_SOURCE . '
							order by n_name
								';
								
						$raw = $db_source->query($sql);
						if ($db_source->num_rows($raw))
						{
            				// Zuerst w\x{00E4}hlen wir alle User aus die nicht migriert werden sollen:
            				$tmp = 0;
            				
            				$form = new Form2($PHP_SELF, 'get');
            				
            				$fields = array();
            
                    		$fields[$tmp++] = array(
                    				'type' => 'separator',
                    				'value' => 'In <b>' . $sql_user . '@' . $sql_server . ':' . $sql_database . '</b> eingetragene User (bitte die User markieren die NICHT migriert werden sollen!):'
             
                    			);

							while ($current = $db_source->fetch_array($raw))
							{
                
                        		$fields[$tmp++] = array(
                        				'name' => 'users[]',
                        				'type' => 'checkbox',
                        				'title' => $current[n_name] . ' ' . $current[v_name] . ':',
                        				'important' => 0,
                        				'selections' => array(
                        						0 => array(
                        								'name' => 'value',
                        								'value' => $current[id]
                        							)
                        					),
                        				'attribs' => array(
                        						0 => array(
                        								'name' => 'title',
                        								'value' => 'Soll dieser User NICHT migriert werden?'
                        							)                        							
                        					)
                        			);
							}
            
                    		$fields[$tmp++] = array(
                    				'type' => 'hidden',
                    				'important' => 0,
                    				'selections' => array(
                    						0 => array(
                    								'value' => 'migrate_users',
                    								'name' => 'action'
                    							)
                    					)
                    			);
                    		$fields[$tmp++] = array(
                    				'type' => 'buttons',
                    				'important' => 0,
                    				'selections' => array(
                    						0 => array(
                    								'value' => 'weiter&gt;&gt;',
                    								'type' => 'submit',
                    								'name' => 'submit'
                    							),
                    						1 => array(
                    								'value' => 'Reset',
                    								'type' => 'reset',
                    								'name' => 'reset'
                    							)
                    					)
                    			);
                    			
                    			
            				$form->load_form($fields);
            				$form->precheck_form();
            
            				if (! $GLOBALS[submit])
            				{
            					$form->set_precheck_error();
            				}
            				
            				if ($form->is_form_error())
            				{
            					$message = $form->build_form();
            				}
            				else
            				{
            					$message = '<p>Die User wurden ausgewählt! </p>';
            					$bottom_menu = array();
            					$bottom_menu[0][text] = 'Weiter&gt;&gt;';
            					$bottom_menu[0][link] = $PHP_SELF . '?action=unit_mapping';
            					$form->form_shutdown();
            					
            					$users = serialize($GLOBALS[users]);
            					session_register('users');            					
            				}
							
						}
						else
						{
							$message = '<p class=error>Seltsam, in der Quelldatenbank <b>' . TB_USERS_SOURCE . '</b> wurden keine User gefunden...</p>';
						}
					}
					else
					{
						$message = '<p class=error>Whoups, Verbindung zum Quellserver ist fehlgeschlagen!</p>';
					}					
				}
				else
				{
					$message = '<p class=error>Fehler, es muss zu erst ein Quellserver eingetragen werden!</p>';
				}		
				
				$menu = array();
				$menu[0][text] = 'Übersicht';
				$menu[0][link] = $PHP_SELF;		
				echo $page->dialog_box('Userdatenbank migrieren', $message, $menu, $bottom_menu, '50%');
			break;
			
		case 'unit_mapping':

				$db_source = new Database($sql_server, $sql_user, $sql_password, $sql_database);
								
				$menu = array();
				$menu[0][text] = 'Übersicht';
				$menu[0][link] = $PHP_SELF;		

				if ($GLOBALS[users])
				{
					// So, zuerst einmal holen wir alle Einheiten aus der alten Userdatenbank!
					$sql = '
							select
								id,
								name
							from 
								' . TB_EINHEITEN_SOURCE . '
							order by
								unit,
								rank,
								ref_id								
								';
								
					$raw = $db_source->query($sql);
					
					if ($db_source->num_rows($raw))
					{
        				$tmp = 0;
        				
        				$form = new Form2($PHP_SELF, 'get');
        				
        				$fields = array();
        
                		$fields[$tmp++] = array(
                				'type' => 'separator',
                				'value' => '<b>Einheitenmapping:</b><br>Links sind die alten Einheiten und rechts die neuen Einheiten! Mehrfachzuweisungen sind möglich! '         
                			);

						while ($current = $db_source->fetch_array($raw))
						{
                    		$fields[$tmp] = array(
                    				'name' => 'alte_einheit_' . $current[id],
                    				'type' => 'select',
                    				'title' => $current[name] . ': ',
                    				'important' => 1,
                    				'selections' => array(
                    					),
                    				'attribs' => array(
                    						0 => array(
                    								'name' => 'title',
                    								'value' => ''
                    							),
                    						1 => array(
                    								'name' => 'size',
                    								'value' => '1'
                    							)
                    					)
                    			);                    
                    			
                			reset($GLOBALS[EINHEITEN]);
                			$i = 0;
                
                			while ($temp = each($GLOBALS[EINHEITEN]))                    			
                			{
                				$fields[$tmp][selections][$i][title] = $temp[value][name];
                				$fields[$tmp][selections][$i][value] = $temp[key];
                				$i++;
                			}
                			
                			$tmp++;            
						}
        
                		$fields[$tmp++] = array(
                				'type' => 'hidden',
                				'important' => 0,
                				'selections' => array(
                						0 => array(
                								'value' => 'unit_mapping',
                								'name' => 'action'
                							)
                					)
                			);
                		$fields[$tmp++] = array(
                				'type' => 'buttons',
                				'important' => 0,
                				'selections' => array(
                						0 => array(
                								'value' => 'weiter&gt;&gt;',
                								'type' => 'submit',
                								'name' => 'submit'
                							),
                						1 => array(
                								'value' => 'Reset',
                								'type' => 'reset',
                								'name' => 'reset'
                							)
                					)
                			);
                			
                			
        				$form->load_form($fields);
        				$form->precheck_form();
        
        				if (! $GLOBALS[submit])
        				{
        					$form->set_precheck_error();
        				}
        				
        				if ($form->is_form_error())
        				{
        					$message = $form->build_form();
        				}
        				else
        				{
        					$message = '<p>Baue Mapping-Tabelle...</p>';

        					$sql = '
        							select
        								id
        							from 
        								' . TB_EINHEITEN_SOURCE . '
        							order by
        								unit,
        								rank,
        								ref_id								
        								';
        								
        								
        					$raw = $db_source->query($sql);

        					$mapping = array();
    						while ($current = $db_source->fetch_array($raw))
    						{
    							$message .= 'generiere Eintrag für <b>' . $current[id] . '</b>: mapping auf <b>' . $GLOBALS['alte_einheit_' . $current[id]] . '</b><br>';
    							$mapping[$current[id]] = $GLOBALS['alte_einheit_' . $current[id]];
    						}
    						
    						if (count($mapping) > 0)
    						{
    							$message .= '<p class=ok>OK, <b>' . count($mapping) . '</b> Einträge!</p>';
    						}
    						else
    						{
    							$message .= '<p class=error>Keine Einträge? Fehler!</p>';
    						}
    						
    						session_register('mapping');
        					
        					$bottom_menu = array();
        					$bottom_menu[0][text] = 'Weiter&gt;&gt;';
        					$bottom_menu[0][link] = $PHP_SELF . '?action=copy_users';
        					$form->form_shutdown();
        				}

					}
					
				}
				else
				{
					$message = '<p class=error>Whoups, es wurden keine User gefunden... macht nicht viel Sinn ohne User weiterzuarbeiten...</p>';
				} 
				
				echo $page->dialog_box('Userdatenbank migrieren', $message, $menu, $bottom_menu, '50%');
			break;
			
		case 'copy_users':
		
			$db->debug(1);
				// Jetzt kopieren wir die User aus der alten Datenbank in die neue Datenbank!
				$db_source = new Database($sql_server, $sql_user, $sql_password, $sql_database);
								
				$menu = array();
				$menu[0][text] = 'Übersicht';
				$menu[0][link] = $PHP_SELF;		
					
				// Schritt 1:
				// Datenbank locken und eine ID holen um sie einzufügen:

				$sql = ' 
						lock table ' . TB_USER;
				$db->query($sql);
				
				// Sollte doch eigentlich die letzte (=höchste) ID ausspucken!?!?
				$sql = '
						select
							id
						from
							' . TB_USER . '
						order by 
							id desc
						limit 1';

				$raw = $db->query($sql);
				$insert_id = $db->fetch_array($raw);
				$insert_id = $insert_id[id]++;

				// Schritt 2: 
				// User aus der Quelldatenbank holen:
				$sql = '
					select						
						n_name,
						v_name,
						einheit,
						rang,
						email,
						homepage,
						geburtstag,
						password
					from 
						' . TB_USERS_SOURCE . '
					';				
				$raw = $db_source->query($sql);
				if ($db_source->num_rows($raw))
				{
					$current = $db_source->fetch_array($raw);
						if (! $GLOBALS[mapping][$current[einheit]])
						{
							$temp = 0;
						}
						else
						{
							$temp = $GLOBALS[mapping][$current[einheit]];
						}
					$sql = ' 
						insert into  
							' . TB_USER . '
						VALUES
							(' . $insert_id++ . ', "' . $current[n_name] . '", "' . $current[v_name] . '", "' . $current[password] . '", "' . $current[email] . '", 0, 0, ' . $temp . ', 0, 0, ' . $current[geburtstag] . ', ' . $current[rang] . ', 0, "")';
						echo $sql . '<br>';
					$db->query($sql);
						
					while ($current = $db_source->fetch_array($raw))
					{
						if (! $GLOBALS[mapping][$current[einheit]])
						{
							$temp = 0;
						}
						else
						{
							$temp = $GLOBALS[mapping][$current[einheit]];
						}
    					$sql = ' 
    						insert into  
    							' . TB_USER . '
    						VALUES
    							(' . $insert_id++ . ', "' . $current[n_name] . '", "' . $current[v_name] . '", "' . $current[password] . '", "' . $current[email] . '", 0, 0, ' . $temp . ', 0, 0, ' . $current[geburtstag] . ', ' . $current[rang] . ', 0, "")';
						echo $sql . '<br>';    							
    					$db->query($sql);						
					}
					$db->query($sql);					
					
					$message = 'User werden jetzt übertragen; Dieser Vorgang kann einige Zeit dauern!<br>kopiere...';
				
					$sql = 
						'unlock tables';
					$db->query($sql);
				}
				else
				{
					$message = '<p class=error>Whoups, es wurden keine User gefunden... macht nicht viel Sinn ohne User weiterzuarbeiten...</p>';
				}	

				echo $page->dialog_box('Userdatenbank migrieren', $message, $menu, $bottom_menu, '50%');
			break;
		
		default:
		
				
				$menu = array();
				$menu[0][text] = 'Einstellungen';
				$menu[0][link] = $PHP_SELF . '?action=preferences';
				$menu[1][text] = 'Userdatenbank';
				$menu[1][link] = $PHP_SELF . '?action=migrate_users';
				$message = 
					'<p>Willkommen beim Migrations-Script!!</p>';
				echo $page->dialog_box('Migration', $message, $menu, 0, '50%');
	}

	$page->html_footer();

	/***************************************************************************
	* Aufräumen!!
	* Wird hoffentlich bald durch Destruktoren erledigt!
	***************************************************************************/
	$log->shutdown();
?>


