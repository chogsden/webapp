<?PHP

	// HOME Model //

	/*	**** UN-COMMENT for MySQL data source function ****

		$search = setModelParameters($search);
		
		// Set Config for data query parameters:
		$search_list = array(

			// Model request for Media Items:
			'all_items' => array(

				'mode'		=> 'SELECT',	// default = SELECT; options = SELECT COUNT, INSERT, DELETE, UPDATE
				'route'		=> array('table_name' => array(	'using' => '',		// related table
															'belongs_to' => '',	// relationship; options BELONGS_TO, MANY_RECORDS
															'condition' => ''	// optonal (remove if not needed); relationship clause
									)),
				'condition'	=> $search['condition'],	// Query WHERE clause; enter as an array list where each condition will filter according to boolean AND
				'limit'		=> array('start_record', 'amount_of_records'),	// Control which records of a larger set to return
				'return'	=> array('field_name' => 'optional new name'),	// fields to return; enter as an array list - field as key (value is optional)
				'order'		=> array('field_name', 'field_name'))	// Order records by specific fields

			//	All parameters are optional, only need to be declared if deviating from:
			//	SELECT table.* FROM table
				
		);

		$timestamp = false;	// Set to true to return model process time

		// ------------------------------------------------------
		
		// Select query:
		switch($GLOBALS['controller']) {
			default: $request = $search_list['all_items']; break;
		}

		// Query MySQL database and return to controller as an array:

		$mysql_return = mysqlQuery(
							$db_config,
							$request,
							'id',
							$timestamp
						);

		$model = $mysql_return['response'];

	*/

	$model = array(
		'content' => '<h2>IT WORKS !</h2><br /><br /><h3>HOME page content</h3>'
	);

?>