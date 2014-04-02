<?PHP

// Default code to generate model, view and controller:

$mvc = array(

// MODEL Code:
'model'		=>	'<?PHP 
	// '.strtoupper($section).' Model //

	// Set rules for command-line access:
	if(isset($argv[0])) {			
		require_once(\'app/config/global.php\'); 
		require_once(\'app/core/functions.php\');
		$show_model_output = true;
	}

	// Query MySQL database and return to controller as an array:
	$sql_filter = \'\';
	$mysql_return = mysqlQuery(
						$db_config,

						\'SELECT\', 
						
						\'*\',
						
						\''.$section.'\',

						\'\',

						\'\',

						\'\',
						
						\'id\',
						
						false,
						
						$show_model_output
					);
	$model = $mysql_return[\'response\'];
?>',

// VIEW Code:
'view'		=>	'<?PHP
	// '.strtoupper($section).' View //

	// Set display html for events controller:
	$body_content = \'
	<!--Main Content Section-->
	<section id="page-title" class="section">
		<div class="container">
			<div class="row">
				<div class="col-sm-12 text-center">
					<h2 class="black">'.ucwords(preg_replace('@_@', ' ', $section)).'</h2>
				</div>
			</div>
		</div>
	</section>

	<section id="content2" class="section">
	<div class="container">
			<div class="row">
				<div class="col-sm-12 text-center">

					<!-- Section content goes here -->
					<p>\'.$content[\''.$section.'\'].\'</p>

				</div>
			</div>
		</div>

	</section>
	\';

	// Set additional javascript for application controller:
	$js_content = \'\';
?>',

// CONTROLLER Code:
'controller' => '<?PHP
	// '.strtoupper($section).' Controller //

	// Set rules for command-line access:
	if(!empty($argv[1])) {
		require_once(\'app/config/global.php\');
		require_once(\'app/core/functions.php\');
		$request_parameters = declareRequestParameters(array($argv[0]), \'\', \'\', \'\', \'\', \'\', \'\', \'shared/_null\', \'\');
	}

	// Get data from the events model:
	require(\'app/models/'.$section.'.php\');
	
	// Set the display content for the view:
	$content = $model[1];
// 	print_r($content);

	// Send the content to the view:
	require(\'app/views/\'.$request_parameters[\'route_view\'].\'.php\');
?>',

);


?>