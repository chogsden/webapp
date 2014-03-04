<?PHP 
	require('app/config/global.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<title>The page you were looking for doesn't exist (404)</title>
		<?PHP echo('
		<link rel="stylesheet" href="'.$config['domain'].$config['root_dir'].'/css/bootstrap.min.css">
		<link rel="stylesheet" href="'.$config['domain'].$config['root_dir'].'/css/main.css">
		'); ?>
	</head>

	<body>
		<section id="page-title" class="section">
			<div class="container">
				<div class="col-sm-1"></div>
				<div class="row margin-20">
					<div class="col-sm-10 text-center">
						<h2>The page you were looking for doesn't exist.</h2>
						<h3>You may have mistyped the address.</h3>
					</div>
				</div>
				<div class="col-sm-1"></div>
			</div>
		</section>
	</body>
</html>