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
		<link rel="stylesheet" href="'.$config['domain'].$config['root_dir'].'app/assets/css/bootstrap.css">
		<link rel="stylesheet" href="'.$config['domain'].$config['root_dir'].'app/assets/css/blueimp-gallery.css">
		<link rel="stylesheet" href="'.$config['domain'].$config['root_dir'].'app/assets/css/custom-styles.css">
		'); ?>
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic" type="text/css">

	</head>
	<body>

		<?PHP 
		// <!--Google API-->
		echo($google_analytics_content);

		// <!--Header Content-->
		echo($navbar_content);

		// <!--Main Content-->
		echo($body_content);

		// <!--Footer Content-->
		echo($footer_content);
		?>
    
		<!-- Javascript -->
		<?PHP echo('
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<script src="'.$config['domain'].$config['root_dir'].'jscript/bootstrap.min.js"></script>
		
		<!-- Load content specific js -->

		'.$js_content.'

		'); ?>

	</body>
</html>

