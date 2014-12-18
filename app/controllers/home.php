<?PHP

	// Set rules for command-line access:
	if(isset($argv[0])) {
		require_once('app/config/global.php');
		require_once('app/core/functions.php');
		$request_parameters = declareRequestParameters(explode('/', $argv[1]), '', '', '', '', '', '', 'shared/_null', '', '');
		$echo_output = true;
	}

	// Get data from the home model:

		/* 
		If using MySQL model set filter here - e.g...:
		$filter = array(
			'id = some_value'
		);
		*/

	require(loadMVC('model', 'home'));

	// Set the display content for the view:

		/*
		If returning data from MySQL model, e.g...:
		$content['data'] = $model['records'];
		*/

	$content['data'] = $model['content'];

	echoContent($echo_output, $content);

	// Send the content to the view:
	require(loadMVC('view', 'home'));

?>