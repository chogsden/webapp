<?PHP

	// Load application core config settings and shared scripts:
	require_once('app/config/global.php');
	require_once('app/core/functions.php');

	global $argv;
	$request_parameters = clientRequest($config);

	// Load Application helper:
	require(loadMVC('helper', 'application'));

	// Load Application helper:
	require_once(loadMVC('controller', $GLOBALS['controller']));

?>