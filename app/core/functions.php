<?PHP 

	// Function to return client device type:
	function clientDevice($mobile_agents, $user_agent) {
		$client_device = 'desktop';
		if(preg_match('/'.implode('|', $mobile_agents).'/', $user_agent)) {
			$client_device = 'mobile';
		}
		return $client_device;
	}

	// Function to validate client URL request parameters - used for controlling access and link navigation throughout the app:
	function clientRequestValidation($config, $routes) {
		
		$app_view = 'shared/_404';
		$route_request = '_null';
		$route_view = 'shared/_null';
		$app_request = array();
		$output_format = $config['allowed_output_formats']['application'];

		$uri = explode('/', trim($_SERVER["REQUEST_URI"], '/'));
		array_shift($uri);
//		print_r($uri);
		if(empty($uri)) {
			$client_request = '/';
		} else {
			$client_request = array_shift($uri);
		}
		if(array_key_exists($client_request, $routes)) {
			$route_request = $routes[$client_request]['request'];
			$app_request = array($route_request);
			$app_view = 'application';
			$route_view = $route_request;
//			print_r($uri);
			for($i=0; $i<count($uri); $i++) {
				if(	!empty($uri[$i]) AND
					isset($config['url_validation_rules']) AND
					preg_match('@'.implode('|', $config['url_validation_rules']).'@', $uri[$i]) == true) {
					if(in_array($uri[$i], $config['allowed_output_formats'])) {
						$app_view = array_search($uri[$i], $config['allowed_output_formats']);
						$route_view = 'shared/_null';
						$output_format = $uri[$i];
					} else {
						$app_request[] = $uri[$i];
					}
				} else {
					$app_view = 'shared/_404';
					$route_request = '_null';
					$route_view = 'shared/_null';
					break;
				}
			}
//			print_r($app_request);
		} else {
			$client_request = '_null';
		}
		return declareRequestParameters($app_request, $app_view, $config['domain'].$config['root_dir'].implode('/', $app_request).'/', $_SERVER["REQUEST_URI"], $client_request, $route_request, $routes[$client_request]['referer'], $route_view, $output_format);

	}

	// Function to set core App declarations:
	function declareRequestParameters($app_request, $app_view, $this_url, $request_uri, $client_request, $route_request, $route_referer, $route_view, $output_format) {

		$request_parameters = array(	
			// Request properties to application:
			'app_request'	=>	$app_request,

			// Application view:
			'app_view'		=>	$app_view,

			// Client URI request elements:
			'this_url'		=>	$this_url,

			// Server request:
			'request_uri'	=>	$request_uri,

			// Client route request:
			'client_request'=>	$client_request,

			// Section route requested:
			'route_request'	=>	$route_request,

			// Section route back:
			'route_referer' =>	$route_referer,

			// Section route view
			'route_view'	=>	$route_view,

			// Format of application ouput:
			'output_format'	=>	$output_format,
		);
//		print_r($request_parameters);
		return $request_parameters;

	}

	// Function to access MySQL database:
	function mysqlAccess($db_config) {
		mysql_connect($db_config['db_server'],$db_config['db_user'],$db_config['db_pass']) or exit();
		mysql_query('SET NAMES utf8');
		mysql_select_db($db_config['db']) or die('Could not select database');
	}

	// Function to query MySQL database and return result:
	function mysqlQuery($db_config, $type, $qry, $id_field, $show_time, $show_model_output) {
		mysqlAccess($db_config);
//		echo($qry.chr(10));
		$start_time = getMicrotime();
		$result = false;
		$echo_query = false;
//		$show_time = true;
		$res = mysql_query($type.' '.$qry);
		if($show_time == true) {
			echo(stopMicrotime($start_time));
		}
		if(!$res) {
//			echo($type.' '.$qry);
			echo(chr(10).'MySQL ERROR - '.mysql_error().chr(10).chr(10));
//			die();
		
		} else {

			// SELECT query:
			if($type == 'SELECT') {
				$echo_query = true;
				if(mysql_num_rows($res) == true) {
					while($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
						$result[$row[$id_field]] = $row;
					}
				} else {
					$result = array();
				}
				if(strstr($qry, 'SQL_CALC_FOUND_ROWS') == true) {
					$foundrows = mysql_query("SELECT FOUND_ROWS()");
					$result['total_records'] = mysql_result($foundrows,0);
				}

			// SHOW query:	
			} elseif($type == 'SHOW') {
				$result = array();
				while($row = mysql_fetch_array($res, MYSQL_NUM)) {
					$result[] = $row[0];
				}

			// INSERT new record:
			} elseif($type == 'INSERT') {
				$result = mysql_insert_id();
			
			// UPDATE or DELETE record:
			} elseif($type == 'UPDATE' OR $type == 'DELETE') {
				$result = 'record '.$type.'D';

			// CREATE table:
			} elseif($type == 'CREATE') {
				$result = 'created table';

			// DROP table:
			} elseif($type == 'DROP') {
				$result = 'deleted table';
			
			}
		}

		// Set screen print output for SELECT query:
		if($show_model_output AND $echo_query == true) {
			echo	chr(10).
					'----------------------------------------------------------------------------------------------------------'.
					chr(10).preg_replace('@'.chr(9).'@', '', $type.' '.$qry).chr(10).chr(10);
			print_r($result);
			echo	'----------------------------------------------------------------------------------------------------------'.
					chr(10);
		}

		// Return MySQL query result and query:
		return array('response' => $result, 'query' => preg_replace('@[\s]+@', ' ', $type.' '.$qry));
		mysql_close();
	}

	function getMicrotime() {
		$microtime_start = null;
		return (microtime(true) - $microtime_start);
	}

	function stopMicrotime($start_time) {
		return chr(10). (number_format((getMicrotime() - $start_time), 6) * 1000). chr(10);
	}

	// Function to update database content for html output in view:
	function charReturn($string, $lookup) {
		$find = array('@'.chr(10).'@');
		$replace = array('<br />');
		if(!empty($lookup)) {
			foreach($lookup as $field => $markup) {
				$find = array_merge($find, $markup[0]);
				$replace = array_merge($replace, $markup[1]);
			}
		}
//		echo($string.chr(10));
//		print_r($find);
//		print_r($replace);
		return preg_replace($find, $replace, $string);
	}

	// Function to generate App section MVC files:
	function createMVC($routes, $section) {
		require('_system/mvc.php');
		foreach($routes as $route) {
			$file = fopen('app/'.$route.'/'.$section.'.php',"w");
			if(fwrite($file, $mvc[substr($route, 0, -1)])) {
				$report[substr($route, 0, -1)] = 'generated';
			} else {
				$report = false;
			}
			fclose($file);
 		} 
 		return array('response' => $report);
	}

	// Function to remove App section MVC files:
	function removeMVC($routes, $section) {
		foreach($routes as $route) {
			if(unlink('app/'.$route.'/'.$section.'.php')) {
				$report[substr($route, 0, -1)] = 'removed';
			} else {
				$report = false;
			}
		}
		return array('response' => $report);
	}

	// Function to generate section in App Routes: 
	function updateRoutes($config, $app_paths, $routes, $section) {
		$file = fopen($app_paths['routes'], "w");
		$routes[$section] = array(
			'request'	=>	$section,
			'referer'	=>	'',
			'navbar'	=>	array(
				'name'	=>	ucwords(preg_replace('@_@', ' ', $section)),
				'url'	=>	strtolower($section).'/',
				'type'	=>	'page',
			)
		);
		if(fwrite($file,pretty_json(json_encode($routes)))) {
			$report = 'routes.json: New section included';
		} else {
			$report = false;
		}
		fclose($file);
		return array('response' => $report);
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
		return array('response' => $report);
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
		return array('response' => $report);
	}

?>