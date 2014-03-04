<?PHP

	// Set rules for command-line access:
	if(!empty($argv[1])) {
		require_once('app/config/global.php');
	}


	// XML structure routines according to content:


	// --- *

	$output = '';
	
	// Send the content to the view:
	require('app/views/shared/xml.php');

?>