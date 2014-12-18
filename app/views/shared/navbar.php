<?PHP

	function navbarHTML($config, $request, $id, $url, $name) {

		$navbar_item = '
		<li><a href="'.$config['domain'].$config['root_dir'].$url.'">'.$name.'</a></li>
		';
		if($id == $request) {
			$navbar_item = preg_replace('@<a href@', '<a style="color: #fff;" href', $navbar_item);
		}
		return $navbar_item;

	}

	$navbar_items = array();
	foreach($content['navbar'] as $id => $item) {

		if($item['type'] == 'link') {
			$navbar_items[$id] = navbarHTML($config, $request_parameters['route_request'], $id, $item['url'], $item['name']);

		} elseif($item['type'] == 'list') {
			$list_items = array();
			foreach($item['items'] as $list_id => $list_item) {
				$list_items[] = navbarHTML($config, $request_parameters['route_request'], $list_id, $list_item['url'], $list_item['name']);
			}
			$navbar_items[$id.'-list'] = '
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$item['name'].' <b class="fa fa-caret-down fa-1x"></b></a>
				<ul class="dropdown-menu">
					'.implode('', $list_items).'
				</ul>
			</li>
			';
		}
	}

	// Set display html for controller:
	$navbar_content = '
	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<!--
				<a class="navbar-brand img-responsive" href="'.$content['nav_bar_logo']['url_link'].'" target="_'.$content['nav_bar_logo']['link_target'].'">
					<img src="'.$content['nav_bar_logo']['image'].'" alt="">
				</a>
				-->
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-left">
					'.implode(chr(10), $navbar_items).'
				</ul>
			</div><!-- /.navbar-collapse -->
		</div>
	</div>
	';

?>