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
		$route_name = '';
		$app_request = array();
		$output_format = $config['allowed_output_formats']['application'];

//		print_r($_SERVER);
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
			if(!empty($routes['navbar'])) {
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
		return declareRequestParameters($app_request, $app_view, $config['domain'].$config['root_dir'].implode('/', $app_request).'/', $_SERVER["REQUEST_URI"], $client_request, $route_request, $routes[$client_request]['referer'], $route_view, $route_name, $output_format);

	}

	// Function to set core App declarations:
	function declareRequestParameters($app_request, $app_view, $this_url, $request_uri, $client_request, $route_request, $route_referer, $route_view, $route_name, $output_format) {

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
		$view = $module;
		// Prepend $module with view folder:
		if($type == 'view') {
			$view = $module.'/'.$module;
			// Ignore the following view paths: encounters/x.php, x/item.php or shared/x.php:
			preg_replace_callback(
				'@(item|shared|template[0-9])@',
				function($matches) use (&$view, &$module) {
//					print_r($matches);
					$view = $module;
				},
				$module
			);
		}
		return 'app/'.$type.'s/'.$view.'.php';
	}

	function echoContent($echo_state, $content) {
		if($echo_state == true) {
			echo(chr(10));
			print_r($content);
			echo(chr(10));
		}
	}

	// Function to access MySQL database:
	function mysqlAccess($db_config) {
		mysql_connect($db_config['db_server'],$db_config['db_user'],$db_config['db_pass']) or exit();
		mysql_query('SET NAMES utf8');
		mysql_select_db($db_config['db']) or die('Could not select database');
	}

	function mysqlExecute($qry, $show_time, $start_time) {
//		echo(chr(10).$qry.chr(10));
		$res = mysql_query($qry);
		if($show_time == true) {
			echo(stopMicrotime($start_time));
		}
		if(!$res) {
//			echo($qry);
			echo(chr(10).'MySQL ERROR - '.mysql_error().chr(10).chr(10));
//			die();
			$res = false;
		}
		return $res;
	}

	// Function to query MySQL database and return result:
	function mysqlQuery($db_config, $type, $fields, $tables, $filter, $order, $limit, $id_field, $show_time, $echo_output) {
		mysqlAccess($db_config);
		$start_time = getMicrotime();
		$result = false;
		$echo_query = false;
//		$show_time = true;
		$count = '';
		$check_for_count = preg_replace_callback(
			'@(SELECT) (COUNT)@',
			function($matches) use (&$type, &$count) {
//				print_r($matches);
				$type = $matches[1];
				$count = $matches[2];
			},
			$type
		);
		// SELECT query:
		if(strstr('SELECT', $type)) {
			$echo_query = true;
			$qry = $type.' '.$fields.' FROM '.$tables.' '.$filter.' '.$order.' '.$limit;
			$res = mysqlExecute($qry, $show_time, $start_time);
			$result = array();
			if($res) {
				if(mysql_num_rows($res) == true) {
					while($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
						$result['records'][$row[$id_field]] = $row;
					}
				}
				if($count == true) {
					$count_res = mysql_query('SELECT COUNT(*) as rec_count FROM '.$tables.' '.$filter);
					$result['record_count'] = mysql_result($count_res,0, 'rec_count');
				}
			}

		// SHOW tables:	
		} elseif($type == 'SHOW') {
			$qry = $type.' TABLES FROM '.$db_config['db'];
			$res = mysqlExecute($qry, $show_time, $start_time);
			$result = array();
			while($row = mysql_fetch_array($res, MYSQL_NUM)) {
				$result[] = $row[0];
			}

		// INSERT new record:
		} elseif($type == 'INSERT') {
			$qry = $type.' INTO '.$tables.''.$fields;
			$res = mysqlExecute($qry, $show_time, $start_time);
			if($res) {
				$result = mysql_insert_id();
			}
		
		// UPDATE record:
		} elseif($type == 'UPDATE') {
			$qry = $type.' '.$tables.' '.$fields.' '.$filter;
			$res = mysqlExecute($qry, $show_time, $start_time);
			if($res) {
				$result = 'record '.$type.'D';
			}

		// DELETE record:
		} elseif($type == 'DELETE') {
			$qry = $type.' FROM '.$tables;
			$res = mysqlExecute($qry, $show_time, $start_time);
			if($res) {
				$result = 'record '.$type.'D';
			}

		// CREATE table:
		} elseif($type == 'CREATE') {
			$qry = $type.' TABLE '.$fields;
			$res = mysqlExecute($qry, $show_time, $start_time);
			if($res) {
				$result = 'created table';
			}

		// DROP table:
		} elseif($type == 'DROP') {
			$qry = $type.' TABLE '.$tables;
			$res = mysqlExecute($qry, $show_time, $start_time);
			if($res) {
				$result = 'deleted table';
			}
		}

		// Set screen print output for SELECT query:
		if($echo_output AND $echo_query == true) {
			echo	chr(10).
					'----------------------------------------------------------------------------------------------------------'.
					chr(10).preg_replace('@'.chr(9).'@', '', $qry).chr(10).chr(10);
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
			$path = 'app/'.$route.'/'.$section;
			if($route == 'views') {
				if (!mkdir('app/'.$route.'/'.$section, 0755, true)) {
					$report = false;
				}
				$path = 'app/'.$route.'/'.$section.'/'.$section;
			}
			if($route == 'models') {
				if($mysql == true) {
					$mvc_code = $mvc['model2'];
				} else {
					$mvc_code = $mvc['model1'];
				}
			} else {
				$mvc_code = $mvc[substr($route, 0, -1)];
			}
			$file = fopen($path.'.php',"w");
			if(fwrite($file, $mvc_code)) {
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
			if($route == 'views') {
				$action = unlink('app/'.$route.'/'.$section.'/'.$section.'.php');
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