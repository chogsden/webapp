<?PHP

	$echo_output = false;

	// Set rules for command-line access:
	if(isset($argv[0])) {
		require_once('app/config/global.php');
		require_once('app/core/functions.php');
		$_SERVER["HTTP_USER_AGENT"] = '';
		if(empty($argv[2])) {
			$argv[2] = 'html';
		}
		$request_parameters = declareRequestParameters(explode('/', $argv[1]), 'application', '', '', '', $argv[1], '', '', '', $argv[2]);
		$request_parameters['route_request'] = $request_parameters['app_request'][0];
		$request_parameters['route_view'] = $request_parameters['app_request'][0];
		$routes = json_decode(file_get_contents('app/config/routes.json'), true);
		unset($argv[0]);
		$echo_output = true;
	}

	//Establish client device type:
	if(!empty($config['mobile_agents'])) {
		$client_device = clientDevice($config['mobile_agents'], $_SERVER["HTTP_USER_AGENT"]);
	}
//	echo($client_device);

	// Set browser display output:
	$google_analytics_content = '';
	$navbar_content = '';
	$body_content = '';
	$footer_content = '';
	$js_content = '';

	// Load any common rules:
	require(loadMVC('helper', 'application'));

	// Request section controller:
	require(loadMVC('controller', $request_parameters['route_request']));

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

		$app_title = $request_parameters['route_view'];

		// Set up navbar:
		require(loadMVC('helper', 'navbar'));

		// Get navbar display:
		require(loadMVC('view', 'shared/navbar'));

		// ADD common views in app/views/shared:
			// Get page title:
			// require(loadMVC('view', 'shared/title'));

			// Get page footer:
			// require(loadMVC('view', 'shared/footer'));

		// If html is expected, send display content to application view:
		require(loadMVC('view', $request_parameters['app_view']));

	} else {

		// Otherwies send to alternative output controller:
		require(loadMVC('controller', $request_parameters['output_format']));

	}

?>