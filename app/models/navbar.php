<?PHP

	// Set rules for command-line access:
	if(isset($argv[0])) {			
		require_once('app/config/global.php'); 
		require_once('app/core/functions.php');
		$routes = json_decode(file_get_contents('app/config/routes.json'), true);
//		print_r($routes);
		$show_model_output = true;
	}

	// Set data to return to controller as an array:
	foreach($routes as $key => $data) {
		if(isset($data['navbar'])) {
			$model[$key] = $data['navbar'];
		}
	}

	// Print to screen for command-line acess:
	if(isset($argv[0])) {
		print_r($model);
	}

?>