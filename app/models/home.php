<?PHP

	/*	**** UN-COMMENT for MySQL data source function ****
		// Query MySQL database and return to controller as an array:

		// Used if $filter array is set in the controller
		$sql_filter = '';
		if(isset($filter)) {
			$sql_filter = 'WHERE '.implode(' AND ', $filter);
		}
		$mysql_return = mysqlQuery(
							$db_config,
							
							'SELECT', 
							
							'*',				// FIELDS

							'blog19',	// TABLE

							$sql_filter,		// WHERE

							'',				// ORDER

							'',				// LIMIT
							
							'id',				// Primary record ID field for keys in returned data array
							
							false,				// Set to TRUE if requiring query execution time 
							
							$echo_output
						);

		// Set $model to pass returned data back to the controller:				
		$model = $mysql_return['response'];
	*/

	$model = array(
		'content' => '<h2>IT WORKS !</h2><br /><br /><h3>HOME page content</h3>'
	);

?>