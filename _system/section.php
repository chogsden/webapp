<?PHP

	// Code to generate / remove application sections:
	//  -- run in root directory of web app from command line.

	if(isset($argv[0])) {

		// Require global config file and core functions:
		require_once('app/config/global.php'); 
		require_once('app/core/functions.php');

		$GLOBALS['debug'] = true;

		// Set core variables from user request:
		$uri = array($argv[0], $argv[1], $argv[2]);
		$command = $uri[1];
		$section = $uri[2];
		$GLOBALS['model'] = $section;

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
		$routes = translateRoutes();

		// Set procedure and default error logs:
		$procedure_elements = array(
			'mvc_status',
			'write_routes',
			'db_action1',
			'db_action2',
			'write_htaccess',
		);

		// Check MySQL config is set:
		if(	empty($db_config['db_server']) OR
			empty($db_config['db_user']) OR
			empty($db_config['db_pass']) OR
			empty($db_config['db']))
		{
			$mysql = false;
			unset($procedure_elements[2]);
			unset($procedure_elements[3]);
		} else {
			$mysql = true;
		}

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
											array(
												'mode'	=> 'CREATE',
												'fields'=> array(
													'title' => 'varchar(255) NOT NULL DEFAULT \'\'',
													'content' => 'varchar(255) NOT NULL DEFAULT \'\'')),
											)),
								array(	'function' => 'mysqlQuery', 
										'args' => array(
											$db_config,
											array(
												'mode'	=> 'DELETE'),
											))),
			'db_action2'	=>	array(
								array(	'function' => 'mysqlQuery',
										'args' => array(
											$db_config,
											array(
												'mode'	=>	'INSERT',
												'fields'=>	array(
													'title' => '"'.ucfirst(preg_replace('@_@', ' ', $section)).'"',
													'content' => '"'.preg_replace('@_@', ' ', $section).' content"')),
											)),
								array(	'function' => 'mysqlQuery',
										'args' => array(
											$db_config, 
											array(
												'mode'	=> 'DROP'),
											))),
								
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
//				echo($process.chr(10));
//				if($process == 'mvc_status') {
				$run_process = call_user_func_array($procedure[$process][$element]['function'], $procedure[$process][$element]['args']);
//				print_r($run_process);
				$procedure_report[$process] = $run_process['result']['response'];
//				die();
//			}
			}
			return $procedure_report;
		}

		// Set echo response:
		$response = '';
		$procedure_report = array();

		// Validate user request:
		if(isset($section) AND in_array($command, $command_routines) == true) {

			// Build list of procedures:

			foreach($procedure_elements as $procedure_type) {
				$procedure_report[$procedure_type] = array('response' => false);
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
						if($status == false) {
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