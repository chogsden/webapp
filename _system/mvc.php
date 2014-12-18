<?PHP

// Default code to generate model, view and controller:

$mvc = array(

// MODEL Code:
'model1'	=>	'<?PHP 
	// '.strtoupper($section).' Model //

	/*	**** UN-COMMENT for MySQL data source function ****
		// Query MySQL database and return to controller as an array:

		// Used if $filter array is set in the controller
		$sql_filter = \'\';
		if(isset($filter)) {
			$sql_filter = \'WHERE \'.implode(\' AND \', $filter);
		}
		$mysql_return = mysqlQuery(
							$db_config,
							
							\'SELECT\', 
							
							\'id, '.$section.'\',	// FIELDS

							\''.$section.'\',		// TABLE

							$sql_filter,			// WHERE

							\'\',					// ORDER

							\'\',					// LIMIT
							
							\'id\',					// Primary record ID field for keys in returned data array
							
							false,					// Set to TRUE if requiring query execution time 
							
							$echo_output
						);

		// Set $model to pass returned data back to the controller:				
		$model = $mysql_return[\'response\'];
	*/

	$model = array(
		\'records\' => array(1 => array(\''.$section.'\' => \''.$section.' page content\'))
	);

?>',

'model2'	=>	'<?PHP 
	// '.strtoupper($section).' Model //

	// Query MySQL database and return to controller as an array:

	// Used if $filter array is set in the controller
	$sql_filter = \'\';
	if(isset($filter)) {
		$sql_filter = \'WHERE \'.implode(\' AND \', $filter);
	}
	$mysql_return = mysqlQuery(
						$db_config,
						
						\'SELECT\', 
						
						\'id, '.$section.'\',	// FIELDS

						\''.$section.'\',		// TABLE

						$sql_filter,			// WHERE

						\'\',					// ORDER

						\'\',					// LIMIT
						
						\'id\',					// Primary record ID field for keys in returned data array
						
						false,					// Set to TRUE if requiring query execution time 
						
						$echo_output
					);

	// Set $model to pass returned data back to the controller:				
	$model = $mysql_return[\'response\'];

	/*
	$model = array(
		\'records\' => array(1 => array(\''.$section.'\' => \''.$section.' page content\'))
	);
	*/

?>',

// VIEW Code:
'view'		=>	'<?PHP
	// '.strtoupper($section).' View //

	// Set display html for controller:
	$body_content = \'
	<!--Main Content Section-->
	<section id="content1" class="section">
		<div class="container">
			<div class="row">
				<h3>\'.$content[\'data\'].\'</h3>
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
	if(isset($argv[0])) {
		require_once(\'app/config/global.php\');
		require_once(\'app/core/functions.php\');
		$request_parameters = declareRequestParameters(explode(\'/\', $argv[1]), \'\', \'\', \'\', \'\', \'\', \'\', \'shared/_null\', \'\', \'\');
		$echo_output = true;
	}

	// Get data from the home model:

		/* 
		If using MySQL model set filter here - e.g...:
		$filter = array(
			\'id = some_value\'
		);
		*/

	require(loadMVC(\'model\', \''.$section.'\'));

	// Set the display content for the view:
	$content[\'data\'] = $model[\'records\'][1][\''.$section.'\'];
	echoContent($echo_output, $content);

	// Send the content to the view:
	require(loadMVC(\'view\', \''.$section.'\'));
?>',

);


?>