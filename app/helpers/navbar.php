<?PHP

	// Set rules for command-line access:
	if(isset($argv[0])) {
		require_once('app/config/global.php');
		require_once('app/core/functions.php');
		$request_parameters['route_request'] = '_null';
	}

	// Set the display content for the view: 

	// Include home image/logo in navbar:
	$content['nav_bar_logo']['image'] = '';
	$content['nav_bar_logo']['url_link'] = $config['domain'].$config['root_dir'];
	$content['nav_bar_logo']['link_target'] = 'self';

	// Set navbar menu items:

	$content['navbar']['home'] = '';

	// Get data from routes:
	foreach($routes as $id => $route) {
		if(isset($route['navbar'])) {
			$data = $route['navbar'];
			
			// Set view navbar with a sngle page link:
			if($data['type'] == 'link') {
				
				// To set single link within a group of navbar links:
				if(!empty($data['group'])) {
					$content['navbar'][$data['group']]['type'] = 'list';
					$content['navbar'][$data['group']]['name'] = $data['group'];
					$content['navbar'][$data['group']]['items'][$id] = array('url' => $data['url'], 'name' => $data['name']);
				} else {
					$content['navbar'][$id] = array('type' => 'link', 'url' => $data['url'], 'name' => $data['name']);
				}

			// Set view navbar with a menu of sub-section links:
			} elseif($data['type'] == 'list') {
				$list_html = array();
				$content['navbar'][$id] = array('type' => 'list', 'name' => $id, 'items' => array());

				// Where the menu items are listed in app/core/routes.json:
				if($data['source']['type'] == 'list') {
					foreach($data['source']['items'] as $item_id => $item) {
						$content['navbar'][$id]['items'][$item_id] = array('url' => $data['url'].$item_id, 'name' => $item['name']);
					}

				// Where the menu items are in a database table and accessd via a model:	
				} elseif($data['source']['type'] == 'model') {		
					$data_filter = 0;
					require('app/models/'.$data['source']['model']['name'].'.php');
					foreach($model as $item_id => $item) {
						$content['navbar'][$id]['items'][] = array('url' => $data['url'].$item_id, 'name' => $item[$data['source']['model']['field']]);
					}
				}
			}
		}
	}
//	print_r($content['navbar']);

?>