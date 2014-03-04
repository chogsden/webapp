<?PHP

	// Set rules for command-line access:
	if(isset($argv[0])) {			
		require_once('app/config/global.php'); 
		require_once('app/core/functions.php');
		$show_model_output = true;
	}

	// Set data to return to controller as an array:
	foreach($routes as $key => $data) {
		if(isset($data['navbar'])) {
			$model[$key] = $data['navbar'];
		}
	}
	print_r($model);

	// Print to screen for command-line acess:
	if(isset($argv[0])) {
		print_r($model);
	}

?>