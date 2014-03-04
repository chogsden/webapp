<?PHP

	// Set rules for command-line access:
	if(isset($argv[0])) {
		require_once('app/config/global.php');
		require_once('app/core/functions.php');
		$request_parameters = declareRequestParameters(array($argv[0]), '', '', '', '', '', '', '_null', '');
	}

	// Get data from the home model:
	require('app/models/home.php');

	// Set the display content for the view:
	$content = $model;

//	print_r($content);

	// Send the content to the view:
	require('app/views/'.$request_parameters['route_view'].'.php');

?>