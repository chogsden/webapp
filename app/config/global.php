<?PHP

	// Set server domain name:
	if(!isset($_SERVER['SERVER_NAME'])) {
		$_SERVER['SERVER_NAME'] = '';
	}
	
	// Application configuration:
	$config = array(

		// Server domain name:
		'domain'			=>	'http://'.$_SERVER['SERVER_NAME'].'/',
		
		// Application root directory:
		'root_dir'			=>	'webapp/',

		// Paths to image directories:
		'image_path'		=>	'image/',
		'image_path_small'	=>	'image/small/',
		'image_path_medium'	=>	'image/medium/',
		'image_path_large'	=>	'image/large/',

		// Client mobile devices to identify:
		'mobile_agents' 	=> array(
								'iPad', 'iPhone', 'Android', 'webOS', 'BlackBerry', 'Windows Phone', 'Nokia'
		),

		// Google analytics settings:
		'google_analytics'	=>	array(
		
			// Include Google Analytics:
			false, 
		
			//Google Analytics account:
			''
		
		),

		// CSS class names according to text markup type for customised text display:
		'text_markup'		=>	array(

			// Type:		// Class name:
			''			=>	''

		),

		// Enter additional lookup rules to validate against URL request.
		'url_validation_rules' => array(

			'^(html|json|xml)',

			// This is additional to http://host/root_dir/route_request/ element1/element2/element3 etc...
			// e.g. to only parse numbers in addition to the default allowance for anything appearing after http://host/root_dir/route_request/:
			// (^[1-9][0-9]*$)

			'',

		),

		// List of accepted output formats for applcation content:
		'allowed_output_formats' => array(

			// view path:		// format:
			'application'	=>	'html',		// Default display format
											// Other display/output formats for content:
			'shared/json'	=>	'json',		//		JSON
			'shared/xml'	=>	'xml'		//		XML

		)

	);

	// MySQL database configuration:
	$db_config = array(
		'db_server'			=>	'localhost',	// MySQL server
		'db_user'			=>	'chogsden',		// MySQL user name
		'db_pass'			=>	'',				// Mysql user password
		'db'				=>	'',		// WebApp MySQL database


//	error_reporting(0);		// Set to true (1) in production mode to turn off PHP error reporting


?>