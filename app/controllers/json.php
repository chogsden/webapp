<?PHP

	// Set rules for command-line access:
	if(!empty($argv[1])) {
		require_once('app/config/global.php');
	}

	unset($content['navbar']);
	unset($content['nav_bar_logo']);


	// JSON structure routines according to content:
 
	// For Object Collections:
	if($request_parameters['route_request'] == 'object_collections' |
	   $request_parameters['route_request'] == 'object_themes') {
		$objects = $content['objects'];
		unset($content['objects']);
		$i = 1;
		foreach($objects as $id => $array) {
			$object_list[$i] = array(
				'id'				=>	$id,
				'uri' 				=>	$config['domain'].$config['root_dir'].'objects/'.$id.'/',
				'title'				=>	$array['title'],
				'image_uri_medium'	=>	$config['domain'].$config['root_dir'].$config['image_path_medium'].$array['image'],
				'image_uri_large'	=>	$config['domain'].$config['root_dir'].$config['image_path_large'].$array['image']
			);
			$i++;
		}
		$content['objects'] = array_merge(array('count' => count($object_list)), $object_list);
	}

	// For Objects:
	if($request_parameters['route_request'] == 'objects') {
		unset($content['navigation']);
		$images = $content['images'];
		unset($content['images']);
		$i = 1;
		foreach($images as $id) {
			$image_list['image-'.$i] = array(
				'image_uri_medium'	=>	$config['domain'].$config['root_dir'].$config['image_path_medium'].$id.'.jpg',
				'image_uri_large'	=>	$config['domain'].$config['root_dir'].$config['image_path_large'].$id.'.jpg'
			);
			$i++;
		}
		$content['images'] = array_merge(array('count' => count($image_list)), $image_list);
	}

	// For Objects:
	if($request_parameters['route_request'] == 'home') {
	}



	print_r($content);
	$output = json_encode(array_merge(array('uri' => $request_parameters['this_url']), $content));

	// --- *
	
	// Send the content to the view:
	require('app/views/shared/json.php');

?>