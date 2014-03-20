<!DOCTYPE html>
<html>
	<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>WebApp - Framework</title>
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="viewport" content="width=device-width">

		<!-- Styles -->
		<?PHP echo('
		<link rel="stylesheet" href="'.$config['domain'].$config['root_dir'].'app/assets/css/font-awesome.min.css">
		<link rel="stylesheet" href="'.$config['domain'].$config['root_dir'].'app/assets/css/animate.css">
		<link rel="stylesheet" href="'.$config['domain'].$config['root_dir'].'app/assets/css/bootstrap.min.css">
		<link rel="stylesheet" href="'.$config['domain'].$config['root_dir'].'app/assets/css/main.css">
		<link rel="stylesheet" href="'.$config['domain'].$config['root_dir'].'app/assets/css/custom-styles.css">
		'); ?>
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic" type="text/css">

	</head>
	<body>

		<?PHP
		// If config [google_analytics] set: 
		if($config['google_analytics'][0] == true AND isset($config['google_analytics'][1])) {
		echo'
		<!--Google Analytics-->
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push([\'_setAccount\', \''.$config['google_analytics'][1].'\']);
			_gaq.push([\'_trackPageview\']);

			(function() {
				var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
				ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
				var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
		';}
		?>

		<?PHP 
		// <!--Header-->
		echo($navbar_content);

		// <!--Content-->
		echo($body_content);
		?>
    
		<section id="footer">
			<div class="container">
				<div class="row">
					<div class="col-sm-12 text-center">
						<p>footer</p>
					</div>
				</div>
			</div>
		</section>

		<!-- Javascript -->
		<?PHP echo('
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<script src="'.$config['domain'].$config['root_dir'].'jscript/bootstrap.min.js"></script>
		
		<!-- Load content specific js -->

		'.$js_content.'

		'); ?>

	</body>
</html>

