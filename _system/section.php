<?PHP

	// Code to generate / remove application sections:
	//  -- run in root directory of web app from command line.

	if(isset($argv[0])) {

		// Require global config file and core functions:
		require_once('app/config/global.php'); 
		require_once('app/core/functions.php');

		// Set core variables from user request:
		$uri = array($argv[0], $argv[1], $argv[2]);
		$command = $uri[1];
		$section = $uri[2];

		// Check MySQL config is set:
		if(	empty($db_config['db_server']) OR
			empty($db_config['db_user']) OR
			empty($db_config['db_pass']) OR
			empty($db_config['db']))
		{
			$mysql = false;
		} else {
			$mysql = true;
		}

		// Set allowed commands:
		$command_routines = array('create', 'delete');

		// Set MVC naming protocol:
		$mvc_elements = array('controllers', 'models', 'views');

		// Set file paths:
		$app_paths = array(
			'config'		=>	'app/config/global.php',
			'routes'		=>	'app/config/routes.json',
			'functions'		=>	'app/core/functions.php',
			'db_sql'		=>	'db/migrate.php',
			'htaccess_rules'=>	'.htaccess'
		);

		// Build Routes from app/config/routes.json
		$routes = json_decode(file_get_contents($app_paths['routes']), true);

		// Set procedure and default error logs:
		$procedure_elements = array(
			'mvc_status',
			'write_routes',
			'db_action1',
			'db_action2',
			'write_htaccess',
		);

		// Declare functions to load for each procedure:
		$procedure = array(
			
			// Create / Remove MVC files:
			'mvc_status' 	=>	array(
								array(	'function' => 'createMVC', 
										'args' => array($mvc_elements, $section, $mysql)),
								array(	'function' => 'removeMVC', 
										'args' => array($mvc_elements, $section))),

			// Add / Remove section to app/config/routes.json:
			'write_routes'	=>	array(
								array(	'function' => 'updateRoutes',
										'args' => array($config, $app_paths, $routes, $section)),
								array(	'function' => 'resetRoutes',
										'args' => array($app_paths, $routes, $section))),

			// MySQL actions - Add / Delete default section table, add / delete content row:
			'db_action1'	=>	array(
								array(	'function' => 'mysqlQuery', 
										'args' => array(
											$db_config, 
											'CREATE',
											'`'.$section.'` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`'.$section.'` varchar(255) NOT NULL DEFAULT \'\',`created_at` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8',
											false,
											false,
											false,
											false,
											false,
											false,
											false)),
								array(	'function' => 'mysqlQuery', 
										'args' => array($db_config, 'DELETE', false, $section,  false, false, false, false, false, false))),

			'db_action2'	=>	array(
								array(	'function' => 'mysqlQuery',
										'args' => array(
											$db_config, 
											'INSERT',
											'(id,'.$section.',created_at) VALUES("1","'.preg_replace('@_@', ' ', $section).' content text",CURRENT_TIMESTAMP())',
											$section,
											false,
											false,
											false,
											'id', 
											false,
											false)),
								array(	'function' => 'mysqlQuery',
										'args' => array($db_config, 'DROP', false, $section,  false, false, false, false, false, false))),
								
			// Add / Remove section to .htaccess:
			'write_htaccess'=>	array(
								array(	'function' => 'updateHTaccessRules',
										'args' => array($app_paths, $section, 'include')),
								array(	'function' => 'updateHTaccessRules',
										'args' => array($app_paths, $section, 'remove')))
		);
		

		// Function to call each process according to section procedure:
		function runProcess($procedure_report, $procedure, $element, $mysql) {
			foreach($procedure_report as $process => $status) {
//				echo($process);
				$procedure_report[$process] = call_user_func_array($procedure[$process][$element]['function'], $procedure[$process][$element]['args']);
			}
			return $procedure_report;
		}

		// Set echo response:
		$response = '';

		// Validate user request:
		if(isset($section) AND in_array($command, $command_routines) == true) {

			// Build list of procedures:
			foreach($procedure_elements as $procedure_type) {
				if(substr($procedure_type, 0, -1) == 'db_action' AND $mysql == false) {
				} else {
					$procedure_report[$procedure_type] = array('response' => false);
				}
			}

			// On command CREATE:
			if($command == 'create') {

				// Check to see if section already exists:
				if(!isset($routes[$section])) {

					// If not, run create section procedure and generate reports:
					$procedure_report = runProcess($procedure_report, $procedure, 0, $mysql);
					$response = chr(10).'NEW SECTION '.$section.' GENERATED.'.chr(10).chr(10);

					// On completion of procedure, check reports for process failure:
					foreach($procedure_report as $process => $status) {
						
						// If a process(s) failed, roll back procedure:
						if($status['response'] == false) {
							$procedure_report = runProcess($procedure_report, $procedure, 1, $mysql);
							$response = chr(10).'ERROR --- SOMETHING WENT WRONG, SECTION NOT GENERATED!'.chr(10).chr(10);
							break;
						}
					}
				} else {	
					$response = chr(10).'ERROR --- SECTION ALREADY GENERATED!'.chr(10).chr(10);
				}
			}

			// On command DELETE:
			if($command == 'delete') {

				// Check to see that section exists:
				if(isset($routes[$section])) {

					// If so, run remove section procedure: 
					$procedure_report = runProcess($procedure_report, $procedure, 1, $mysql);
					$response = chr(10).'SECTION '.$section.' REMOVED.'.chr(10).chr(10);
				} else {
					$response = chr(10).'ERROR --- SECTION DOES NOT EXIST!'.chr(10).chr(10);
				}
			}
		}

		// Report activity and show procedure log:
		echo($response);
		echo(chr(10).'Report...'.chr(10).chr(10)); print_r($procedure_report);
	}

?>