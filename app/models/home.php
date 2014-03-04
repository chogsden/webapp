<?PHP

	// Set rules for command-line access:
	if(isset($argv[0])) {			
		require_once('app/config/global.php'); 
		require_once('app/core/functions.php');
		$show_model_output = true;
	}
	
	$model = array(
		'content'	=>	'It Works!'
	);

?>