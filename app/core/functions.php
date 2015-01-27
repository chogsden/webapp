<?PHP 

	// Function to return client device type:
	function clientDevice($mobile_agents, $user_agent) {
		$client_device = 'desktop';
		if(preg_match('/'.implode('|', $mobile_agents).'/', $user_agent)) {
			$client_device = 'mobile';
		}
		return $client_device;
	}

	// Function to set Application request parameters:
	function clientRequest($config) {

		global $argv;

		$routes = translateRoutes();
		//	print_r($routes);

		// Set application request parameters...

		// If access over http:
		if(!isset($_SERVER['argv'])) {
			// Set default PHP Server request property if empty:
			if(!isset($_SERVER["REQUEST_URI"])) {
				$_SERVER["REQUEST_URI"] = '/'.$config['root_dir'].'home';
			}
			$GLOBALS['controller'] = 'application';
			$GLOBALS['debug'] = false;

		// if access from command line:
		} elseif(isset($_SERVER['argv'])) {
			$output_format = '';
			if(isset($_SERVER['argv'][3])) {
				$output_format =  preg_replace('@(\w)+=@', '', $_SERVER['argv'][3]);
			}
			$_SERVER["HTTP_USER_AGENT"] = '';
			$GLOBALS['controller'] = preg_replace('@(\w)+=@', '', $_SERVER['argv'][1]);
			$request = trim(preg_replace('@(page=)|((\w)+=)@', '', $_SERVER['argv'][2]), '/');
			$_SERVER["REQUEST_URI"] = 'commandline::'.$config['root_dir'].$request.'/'.$output_format;
			$GLOBALS['debug'] = true;
			$config['domain'] = '';
		}

		// Set application uri request parameters:
		$request_parameters = clientRequestValidation($config, $routes);
		return $request_parameters;

	}

	function translateRoutes() {
		return json_decode(file_get_contents('app/config/routes.json'), true);
	}

	// Function to validate client URL request parameters - used for controlling access and link navigation throughout the app:
	function clientRequestValidation($config, $routes) {
		
		$app_view = 'shared/_404';
		$route_request = '_null';
		$route_view = 'shared/_null';
		$route_name = '';
		$app_request = array();
		$output_format = $config['allowed_output_formats']['application'];

//		print_r($_SERVER);
		$uri = explode('/', trim($_SERVER["REQUEST_URI"], '/') );
		array_shift($uri);
//		print_r($uri);
		if(empty($uri)) {
			$client_request = '/';
		} else {
			$client_request = array_shift($uri);
		}
		if(array_key_exists($client_request, $routes)) {
			$route_request = $routes[$client_request]['request'];
			if(!empty($routes[$client_request]['referer'])) {
				$route_request = $routes[$client_request]['referer'];
			}
			$app_request = array($route_request);
			$app_view = 'application';
			$route_view = $route_request;
			if(!empty($routes[$client_request]['navbar'])) {
				$route_name = $routes[$client_request]['navbar']['name'];
			}
//			print_r($uri);
			for($i=0; $i<count($uri); $i++) {
				if(	!empty($uri[$i]) AND
					isset($config['url_validation_rules']) AND
					preg_match('@'.implode('|', $config['url_validation_rules']).'@', $uri[$i]) == true) {
					if(in_array($uri[$i], $config['allowed_output_formats'])) {
						$app_view = array_search($uri[$i], $config['allowed_output_formats']);
						$route_view = 'shared/_null';
						$route_name = '';
						$output_format = $uri[$i];
					} else {
						$app_request[] = $uri[$i];
					}
				} else {
					$app_view = 'shared/_404';
					$route_request = '_null';
					$route_view = 'shared/_null';
					$route_name = '';
					break;
				}
			}
//			print_r($app_request);
		} else {
			$client_request = '_null';
		}
		return declareRequestParameters($app_request, $config['domain'].$config['root_dir'].implode('/', $app_request).'/', $_SERVER["REQUEST_URI"], $client_request, $route_request, $route_view, $route_name, $output_format);

	}

	// Function to set core App declarations:
	function declareRequestParameters($app_request, $this_url, $request_uri, $client_request, $route_request, $route_view, $route_name, $output_format) {

		$request_parameters = array(	
			// Request properties to application:
			'app_request'	=>	$app_request,

			// Application view:
//			'app_view'		=>	$app_view,

			// Client URI request elements:
			'this_url'		=>	$this_url,

			// Server request:
			'request_uri'	=>	$request_uri,

			// Client route request:
			'client_request'=>	$client_request,

			// Section route requested:
			'route_request'	=>	$route_request,

			// Section route view
			'route_view'	=>	$route_view,

			// Section route name
			'route_name'	=>	$route_name,

			// Format of application ouput:
			'output_format'	=>	$output_format,
		);
//		print_r($request_parameters);
		return $request_parameters;

	}

	// Function to call MVC module:
	function loadMVC($type, $module) {
		$module = preg_replace('@_null/item@', '_null', $module);
		$source = $module;
		// Set global controller assertion:
		switch($type) {
			case 'controller':
				$GLOBALS['controller'] = $module;
				break;
			case 'model':
				$GLOBALS['model'] = $module;
				break;
		}
		if($type == 'model') {
//			echo('controller = '.$controller);
		}
		// Prepend $module with view folder:
		if($type == 'view') {
			$source = $module.'/'.$module;
			// Ignore the following view paths: encounters/x.php, x/item.php or shared/x.php:
			preg_replace_callback(
				'@(item|shared|template[0-9])@',
				function($matches) use (&$source, &$module) {
//					print_r($matches);
					$source = $module;
				},
				$module
			);
		}
		// Output loaded module to screen:
		if($GLOBALS['debug'] == true) {
			echo(chr(10).'Loading '.$type.' '.$source.chr(10));
		}
		return 'app/'.$type.'s/'.$source.'.php';
	}

	function echoContent($content) {
		if($GLOBALS['debug'] == true) {
			echo(chr(10).
			'CONTENT====================================================================================='.
			chr(10));
			print_r($content);
			echo(chr(10).
			'============================================================================================'.
			chr(10));
		}
	}

	function setModelParameters($search) {
		$criteria = array('item_id', 'condition', 'limit', 'order', 'mode', 'fields');
		foreach($criteria as $clause) {
			if(!isset($search[$clause])) {
				$search[$clause] = false;
			}
		}
		return $search;
	}

	// Function to query MySQL database and return result:
	function mysqlQuery($db_config, $request) {
		$mysql = mysqlAccess($db_config);
		$start_time = getMicrotime();
//		$show_time = true;

		if(!isset($request['mode'])) {
			$request['mode'] = 'SELECT';
		}
		$request['model'] = $GLOBALS['model'];
		if($GLOBALS['debug'] == true) {
//			print_r($request);
		}

		$db_result = buildQuery($mysql, $request, $start_time);

		// Return MySQL query result and query:
		return array('result' => $db_result['result'], 'query' => $db_result['query']);
		
		// Close MySQL connection:
		$mysql->close();
	}

	// Function to access MySQL database:
	function mysqlAccess($db_config) {
		$mysql = new mysqli($db_config['db_server'], $db_config['db_user'], $db_config['db_pass'], $db_config['db']);
		// Check for MySQL connection: 
		if ($mysql->connect_error) {
		    printf("Connect to MySQL database failed: %s\n", mysqli_connect_error());
		    exit();
		}
		return($mysql);
	}

	// MySQL Query generator:
	function buildQuery($mysql, $request, $start_time) {

		$query = array();
		$result = false;
		$response = '';

		switch($request['mode']) {

			case 'SELECT':
			case 'SELECT COUNT':
				$query = array(
					 'SELECT DISTINCT SQL_CALC_FOUND_ROWS '.sqlReturn($request).' FROM '.sqlRoute($request),
					sqlCondition($request),
					sqlLimit($request).' '.sqlOrder($request)
				);
				break;
		
			case 'INSERT':
				$query = array(
					'INSERT INTO '.$request['model'],
					sqlInsertFields($request),
					sqlCondition($request)
				);
				break;

			case 'CREATE':
				$query = array(
					'CREATE TABLE `'.$request['model'].'`',
					sqlCreateFields($request),
					'ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
				);
				$response = 'created table';
				break;

			case 'DELETE':
				$query = array(
					'DELETE FROM `'.$request['model'].'`',
					sqlCondition($request)
				);
				$response = 'record(s) deleted';
				break;

			case 'DROP':
				$query = array(
					'DROP TABLE `'.$request['model'].'`'
				);
				$response = 'table removed';
				break;
		}

		$query = preg_replace('@'.chr(9).'@', '', implode(' ', $query));
		$mysqlresult = mysqlExecute($request, $mysql, $query, $start_time, $response);
//		print_r($mysqlresult);

		// Print query output to command line:
		if($GLOBALS['debug']) {
			echo	chr(10).
					'MySQL======================================================================================='.
					chr(10).$query.chr(10).chr(10);
			print_r($mysqlresult['result']);
			echo 	'============================================================================================'.
					chr(10);
		}

		return array('result' => $mysqlresult['result'], 'query' => $query);
	}

	// MySQL execute query procedure:
	function mysqlExecute($request, $mysql, $query, $start_time, $response) {
		$result = false;
//		echo(chr(10).$query.chr(10));
		if($mysqlresult = $mysql->query($query)) {
			$time = stopMicrotime($start_time);

			switch($request['mode']) {
				case 'SELECT':
				case 'SELECT COUNT':
					if($mysqlresult->num_rows == true) {
						while($row = $mysqlresult->fetch_array(MYSQLI_ASSOC)) {
							$result['records'][$row['id']] = $row;
						}
					}
					$response = $mysqlresult->num_rows.' record(s) retrieved';

					if($request['mode'] == 'SELECT COUNT') {
						$count_query = 'SELECT FOUND_ROWS() as record_count';
//						echo(chr(10).$count_query.chr(10));
						$countresult = $mysql->query($count_query);
						while($row = $countresult->fetch_array(MYSQLI_ASSOC)) {
							$result['record_count'] = $row['record_count'];
						}
					}
					$mysqlresult->close();
					break;

				case 'INSERT':
					$response = 'created record with id = '.$mysql->insert_id;
					break;
			}
			$result['response'] = $response;
			return array('result' => $result, 'execution_time' => $time);
		
		} else {
			echo(chr(10).'MySQL ERROR - '.$mysql->error.chr(10).chr(10));
//			die();
			return array('result' => false, 'execcution_time' => 0);
		}
	}

	// SQL query WHERE statement:
	function sqlCondition($request) {
		$sql_condition = '';
		if(isset($request['condition'])) {
			$sql_condition = 'WHERE '.implode(' AND ', $request['condition']);
		}
		return $sql_condition;
	}

	// SQL query TABLE and JOIN statement:
	function sqlRoute($request) {
		$sql_route = $request['model'];
		if(isset($request['route'])) {
			$join_statement = array(0 => $request['model']);
			foreach($request['route'] as $table => $rules) {
				$join[2] = $table;
				$join[3] = 'ON';
				if(isset($rules['to_get'])) {
					$join[4] = $request['model'].'.id = '.$rules['using'];
					$relationship = $rules['to_get'];
				} elseif(isset($rules['belongs_to'])) {
					$join[4] = $rules['using'].' = '.$table.'.id';
					$relationship = $rules['belongs_to'];
				}
				$join[1] = preg_replace(array('@many_records@', '@one_record@'), array('LEFT JOIN', 'JOIN'), $relationship);
				ksort($join);
				if(isset($rules['condition'])) {
					array_splice($join, 3, 0, '(');
					$join[] = 'AND '.$rules['condition'].' )';
				}
//				print_r($join);
				$join_statement[] = implode(' ', $join);
			}
			$sql_route = implode(' ', $join_statement);
		}
		return $sql_route;
	}

	// SQL query FIELDS statement:
	function sqlReturn($request) {
		$sql_return = $request['model'].'.*';
		if(isset($request['return'])) {
			$return = array($request['model'].'.id');
			foreach($request['return'] as $field => $new_field) {
				if(!empty($new_field)) {
					$field = $field.' AS '.$new_field;
				}
				$return[] = $field;
			}
			$sql_return = implode(', ', $return);
		}
		return $sql_return;
	}

	// SQL query LIMIT statement:
	function sqlLimit($request) {
		$sql_limit = '';
		if(isset($request['limit'])) {
			$sql_limit = 'LIMIT '.implode(',', $request['limit']);
		}
		return $sql_limit;
	}

	// SQL query ORDER BY statement:
	function sqlOrder($request) {
		if(!isset($request['order'])) {
			$request['order'] = array($request['model'].'.id');
		}
		$sql_order = 'ORDER BY '.implode(',', $request['order']);
		return $sql_order;
	}

	// SQL query CREATE FIELDS statement:
	function sqlCreateFields($request) {
		$sql_create_fields = array(
			'`id` int(11) unsigned NOT NULL AUTO_INCREMENT',
			'`created_at` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
			'`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
		);
		if(isset($request['fields'])) {
			$field_structure = array();
			foreach($request['fields'] as $field => $value) {
				$field_structure[$field] = '`'.$field.'` '.$value;
			}
			array_splice($sql_create_fields, 1, 0, implode(', ', $field_structure));
		}
		$sql_create_fields[] = 'PRIMARY KEY (`id`)';
		return '('.implode(', ',$sql_create_fields).')';
	}

	// SQL query INSERT FIELDS statement:
	function sqlInsertFields($request) {
		$insert_fields = array('id');
		$insert_data = array('null');
		if(isset($request['fields'])) {
			foreach($request['fields'] as $field => $data) {
				$insert_fields[] = $field;
				$insert_data[] = $data;
			}
		}
		$insert_fields[] = 'created_at';
		$insert_data[] = 'CURRENT_TIMESTAMP()';
		return '('.implode(', ',$insert_fields).') VALUES('.implode(', ', $insert_data).')';
	}

	// Query START execution time:
	function getMicrotime() {
		$microtime_start = null;
		return (microtime(true) - $microtime_start);
	}

	// Query GET execution time - ( NOTE: doesn't work for CREATE TABLE qeries):
	function stopMicrotime($start_time) {
		return number_format((getMicrotime() - $start_time), 6) * 1000;
	}

	// Function to update database content for html output in view:
	function charLookup($string, $markup_rules) {
		$find = array('@\x0a\x0a@', '@\x0a@', '@‘@', '@’@', '@-@', '@“@', '@”@', '@<dash>@');
		$replace = array('<p /><p class="body">', '<br />', '&#8216;', '&#8217;', '&#8211;', '&#8220;', '&#8221;', '-');
		if(!empty($markup_rules)) {
			foreach($markup_rules as $html_element => $css) {
				$find = array_merge($find, array('@<'.$html_element.'>@', '@</'.$html_element.'>@'));
				$replace = array_merge($replace, array('<span class="'.$css.'">', '</span>'));
			}
		}

//		print_r($find);
//		print_r($replace);
		$markup_string = preg_replace($find, $replace, $string);
//		echo($markup_string);
		return $markup_string;
	}

	// Function to generate App section MVC files:
	function createMVC($routes, $section, $mysql) {
		require('_system/mvc.php');
		foreach($routes as $route) {
			$path = array();
			if($route == 'views') {
				if (!mkdir('app/'.$route.'/'.$section, 0755, true)) {
					$report = false;
				}
				$path['view1'] = 'app/'.$route.'/'.$section.'/'.$section;
				$path['view2'] = 'app/'.$route.'/'.$section.'/item';
				$mvc_code['view1'] = $mvc['view1'];
				$mvc_code['view2'] = $mvc['view2'];
			} else {
				$path[$route] = 'app/'.$route.'/'.$section;
				$version = 1;
				if($mysql == true) {
					$version = 2;
				}
				$mvc_code[$route] = $mvc[substr($route, 0, -1).$version];
			}
			foreach($path as $file_name => $file_path) {
				$file = fopen($path[$file_name].'.php',"w");
				if(fwrite($file, $mvc_code[$file_name])) {
					$report[substr($route, 0, -1)] = 'generated';
				} else {
					$report = false;
				}
				fclose($file);
			}
 		} 
 		return array('result' => array('response' => $report));
	}

	// Function to remove App section MVC files:
	function removeMVC($routes, $section) {
		foreach($routes as $route) {
			if($route == 'views') {
				$action = unlink('app/'.$route.'/'.$section.'/'.$section.'.php');
				$action = unlink('app/'.$route.'/'.$section.'/item.php');
				$action = rmdir('app/'.$route.'/'.$section);
			} else {
				$action = unlink('app/'.$route.'/'.$section.'.php');
			}
			if($action) {
				$report[substr($route, 0, -1)] = 'removed';
			} else {
				$report = false;
			}
		}
		return array('result' => array('response' => $report));
	}

	// Function to generate section in App Routes: 
	function updateRoutes($config, $app_paths, $routes, $section) {
		$file = fopen($app_paths['routes'], "w");
		$routes[$section] = array(
			'request'	=>	$section,
			'referer'	=>	'',
			'navbar'	=>	array(
				'name'	=>	ucwords(preg_replace('@_@', ' ', $section)),
				'type'	=>	'link',
				'group'	=>	''
			)
		);
		if(fwrite($file,pretty_json(json_encode($routes)))) {
			$report = 'routes.json: New section included';
		} else {
			$report = false;
		}
		fclose($file);
		return array('result' => array('response' => $report));
	}

	// Function to remove section from App Routes: 
	function resetRoutes($app_paths, $routes, $section) {
		unset($routes[$section]);
		$file = fopen($app_paths['routes'], "w");
		if(fwrite($file,pretty_json(json_encode($routes)))) {
			$report = 'routes.json: Section removed';
		} else {
			$report = false;
		}
		fclose($file);
		return array('result' => array('response' => $report));
	}

	// Function to output readable JSON:
	function pretty_json($json) {
 
		$result      = '';
		$pos         = 0;
		$strLen      = strlen($json);
		$indentStr   = '  ';
		$newLine     = chr(10);
		$prevChar    = '';
		$outOfQuotes = true;

		for ($i=0; $i<=$strLen; $i++) {

			// Grab the next character in the string.
			$char = substr($json, $i, 1);

			// Are we inside a quoted string?
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;

			// If this character is the end of an element, 
			// output a new line and indent the next line.
			} else if(($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine.$newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++) {
					$result .= $indentStr;
				}
			}

			// Add the character to the result string.
			$result .= $char;

			// If the last character was the beginning of an element, 
			// output a new line and indent the next line.
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}

			$prevChar = $char;
		}

		return $result;
	}

	function updateHTaccessRules($app_paths, $section, $action) {
		$file = fopen($app_paths['htaccess_rules'], 'r');
		$htaccess_rules = fread($file, 8192);
		fclose($file);
//		echo($htaccess_rules);
		if($action == 'include') {
			$update_rules = preg_replace('@(home)@', '$1|'.strtolower($section), $htaccess_rules);
		} elseif($action == 'remove') {
			$update_rules = preg_replace('@\|'.$section.'@', '', $htaccess_rules);
		}
//		echo($update_rules);
		$file = fopen($app_paths['htaccess_rules'], 'w');
		if(fwrite($file, $update_rules)) {
			$report = '.htaccess rules: new section '.$action.'d';
		} else {
			$report = false;
		}
		fclose($file);
		return array('result' => array('response' => $report));
	}

?>