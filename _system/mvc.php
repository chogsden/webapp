<?PHP

// Default code to generate model, view and controller:

$mvc = array(

// MODEL Code:
'model1'	=>	'
<?PHP 
	// '.strtoupper($section).' Model //

	$model = array(
		\'records\' => array(
			1 => array(
				\'title\' => \''.ucfirst(preg_replace('@_@', ' ', $section)).'\',
				\'content\' => \''.preg_replace('@_@', ' ', $section).' page content\',)));

?>',

'model2'	=>	'<?PHP 
	// '.strtoupper($section).' Model //

	// Query MySQL database and return to controller as an array:

		$search = setModelParameters($search);
		
		// Set Config for data query parameters:
		$search_list = array(

			// Model request for Media Items:
			\'item\' => array(

				\'condition\'	=> array(\'id = \'.$search[\'item_id\']),
				\'return\'	=> array(\'content\' => \'\')),

			\'all_items\' => array(

				\'mode\'		=> \'SELECT COUNT\')
				
		);

		$timestamp = false;	// Set to true to return model process time

		// ------------------------------------------------------
		
		// Select query:
		switch($GLOBALS[\'controller\']) {
			case \''.$section.'\': if($search[\'item_id\'] == true) { $request = $search_list[\'item\']; break; }
			default: $request = $search_list[\'all_items\']; break;
		}

		// Query MySQL database and return to controller as an array:

		$mysql_return = mysqlQuery(
							$db_config,
							$request
						);

		$model = $mysql_return[\'result\'];

	/*

		// Default content return to contrller:
		$model = array(
			\'records\' => array(1 => array(\''.$section.'\' => \''.$section.' page content\'))
		);

	*/

?>',

// VIEW Code:
'view1'		=>	'
<?PHP
	// '.strtoupper($section).' View //

	foreach($content[\'data\'][\'records\'] as $id => $record) {
		$items[$id] = \'
		<a href="\'.$config[\'domain\'].$config[\'root_dir\'].\''.$section.'/item/\'.$id.\'">
			<h3>\'.$record[\'title\'].\'</h3>
		</a>
		\';
	}

	// Set display html for controller:
	$body_content = \'
	<!--Main Content Section-->
	<section id="content1" class="section">
		<div class="container">
			<div class="row">
				<h3>\'.implode(\'<br /><br />\', $items).\'</h3>
			</div>
		</div>
	</section>
	\';

	// Set additional javascript:
	$js_content = \'\';
?>',

'view2'		=>	'
<?PHP
	// '.strtoupper($section).' ITEM View //

	// Set display html for controller:
	$body_content = \'
	<!--Main Content Section-->
	<section id="content1" class="section">
		<div class="container">
			<div class="row">
				<h3>\'.$content[\'data\'][\'records\'][$search[\'item_id\']][\'content\'].\'</h3>
			</div>
		</div>
	</section>
	\';

	// Set additional javascript:
	$js_content = \'\';
?>',

// CONTROLLER Code:
'controller1' => '
<?PHP
	// '.strtoupper($section).' Controller //

	// Get data from the SECTION model:
	require(loadMVC(\'model\', \''.$section.'\'));
	
	// Set the display content for the view:
	$content[\'data\'] = $model;
	
	echoContent($content);

	// Send the content to the view:
	require(loadMVC(\'view\', \''.$section.'\'));

?>',

'controller2' => '
<?PHP
	// '.strtoupper($section).' Controller //

	$search = array();
	$view_path = \''.$section.'\';
	if(isset($request_parameters[\'app_request\'][2])) {
		$search[\'item_id\'] = $request_parameters[\'app_request\'][2];
		$search[\'condition\'] = array(
			\'id = \'.$search[\'item_id\']
		);
		$view_path .= \'/item\';
	}

	// Get data from the SECTION model:
	require(loadMVC(\'model\', \''.$section.'\'));
	
	// Set the display content for the view:
	$content[\'data\'] = $model;
	
	echoContent($content);

	// Send the content to the view:
	require(loadMVC(\'view\', $view_path));

?>',

);


?>