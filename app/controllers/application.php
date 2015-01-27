<?PHP

	// APPLICATION Controller //

	// Establish client device type:
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

	// Request Section controller:
	require(loadMVC('controller', $request_parameters['route_request']));

/*	
	// Client browser caching to do:
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

		echoContent($content);

		// Get navbar display:
		require(loadMVC('view', 'shared/navbar'));

		// ADD common views in app/views/shared:
			// Get page title:
			// require(loadMVC('view', 'shared/title'));

			// Get page footer:
			// require(loadMVC('view', 'shared/footer'));

		// If html is expected, send display content to application view:
		require(loadMVC('view', 'application'));

	} else {

		// Otherwies send to alternative output controller:
		require(loadMVC('controller', $request_parameters['output_format']));

	}

?>