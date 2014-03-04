<?PHP

	// Set display html for controller:
	$navbar_content = '
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand img-responsive" href="'.$content['nav_bar_logo']['url_link'].'" target="_'.$content['nav_bar_logo']['link_target'].'">
					<img src="'.$content['nav_bar_logo']['image'].'" alt="">
				</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-left">
					'.implode('', $content['navbar']).'
				</ul>
			</div><!-- /.navbar-collapse -->
		</div>
	</div>
	';

?>