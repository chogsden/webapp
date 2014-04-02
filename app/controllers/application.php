<?PHP

	// Set rules for command-line access:
	if(isset($argv[0])) {
		require_once('app/config/global.php');
		require_once('app/core/functions.php');
		$_SERVER["HTTP_USER_AGENT"] = '';
		$request_parameters = declareRequestParameters(array('', $argv[1], $argv[2]), '_null', '', '', '', $argv[1], '', '', $argv[3]);
	}

	//Establish client device type:
	if(!empty($config['mobile_agents'])) {
		$client_device = clientDevice($config['mobile_agents'], $_SERVER["HTTP_USER_AGENT"]);
	}
//		echo($client_device);

	$show_model_output = false;

	// Request section controller:
	require('app/controllers/'.$request_parameters['route_request'].'.php');

/*	Client browser caching to do:
	$mod_time = filemtime($image_path.'tiny/'.$filename);
	$expires = 604800;

	header("Content-type: image/jpeg");
	header("Cache-Control: private, max-age=".$expires.", pre-check=".$expires."");
	header("Expires: " . gmdate('D, d M Y H:i:s', strtotime( '+'.$expires.' seconds')) . " GMT");

	if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
		// if the browser has a cached version of this image, send 304
		header("Last-Modified: " . gmdate('D, d M Y H:i:s', $mod_time).' GMT');
		header("HTTP/1.1 304 Not Modified");
		die;
	} elseif(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $mod_time)) {
		// option 2, if you have a file to base your mod date off:
		// send the last mod time of the file back
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', $mod_time).' GMT',true, 304);
		header("HTTP/1.1 304 Not Modified");
		die;
	} else {
		header("Last-Modified: " . gmdate('D, d M Y H:i:s', $mod_time).' GMT');
	}
*/

	// Output route:
	if($request_parameters['output_format'] == 'html') {

		// Set up navbar:
		require('app/helpers/navbar.php');

		// Get navbar display:
		require('app/views/shared/navbar.php');

		// If html is expected, send display content to application view:
		require('app/views/'.$request_parameters['app_view'].'.php');

	} else {

		// Otherwies send to alternative output controller:
		require('app/controllers/'.$request_parameters['output_format'].'.php');

	}

?>