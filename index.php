<?PHP

	//	Load application core config settings and shared scripts:
	require_once('app/config/global.php');
	require_once('app/core/functions.php');

	// Set default PHP Server request property if empty:
	if(!isset($_SERVER["REQUEST_URI"])) {
		$_SERVER["REQUEST_URI"] = '/'.$config['root_dir'].'home';
	}

	$routes = json_decode(file_get_contents('app/config/routes.json'), true);
//	print_r($routes);

	// Set global application uri request parameters:
	$request_parameters = clientRequestValidation($config, $routes, true);

	// Load application:
	require_once('app/controllers/application.php');

?>