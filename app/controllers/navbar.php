<?PHP

	// Set rules for command-line access:
	if(isset($argv[0])) {
		require_once('app/config/global.php');
		require_once('app/core/functions.php');
//		$request_parameters['route_request'] = '_null';
	}

	// Get data from the navbar model:
	require('app/models/navbar.php');

	// Set the display content for the view: 

	// Include home image/logo in navbar:
	$content['nav_bar_logo']['image'] = '';
	$content['nav_bar_logo']['url_link'] = $config['domain'].$config['root_dir'];
	$content['nav_bar_logo']['link_target'] = 'self';

	// Set navbar menu items:
	$content['navbar']['home'] = '';
	foreach($model as $id => $data) {

		// Set view navbar home link:
		if($data['type'] == 'page') {
			$content['navbar'][$id] = '
			<li><a href="'.$config['domain'].$config['root_dir'].$data['url'].'">'.$data['name'].'</a></li>
			';

		// Set view navbar for a menu of links:
		} elseif($data['type'] == 'list') {
			$list_html = array();

			// Where the menu items are listed in app/core/routes.json:
			if($data['source']['type'] == 'list') {
				foreach($data['source']['items'] as $item) {
					$list_html[] = '<li><a href="'.$config['domain'].$config['root_dir'].$data['url'].$item['id'].'/">'.$item['name'].'</a></li>';
				}

			// Where the menu items are in a database table and accessd via a model:	
			} elseif($data['source']['type'] == 'model') {		
				$data_filter = 0;
				require('app/models/'.$data['source']['model']['name'].'.php');
				foreach($model as $item_id => $item) {
					$list_html[] = '<li><a href="'.$config['domain'].$config['root_dir'].$data['url'].$item_id.'/">'.$item[$data['source']['model']['field']].'</a></li>';
				}
			}
			
			$content['navbar'][$id] = '
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$data['name'].' <b class="fa fa-caret-down fa-1x black"></b></a>
				<ul class="dropdown-menu">
				    '.implode('', $list_html).'
				</ul>
			</li>
			';

		}
		if($id != 'home' AND $request_parameters['route_request'] == $id) {
			$content['navbar'][$id] = preg_replace('@<a href@', '<a style="color: #FFF;" href', $content['navbar'][$id]);
		}
		
	}

//	print_r($content['navbar']);

	// Send the content to the view:
	require('app/views/shared/navbar.php');

?>