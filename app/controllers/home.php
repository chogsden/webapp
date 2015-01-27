<?PHP

	// HOME Controller //

	// LOAD CONTENT 

		// Get data from the home model:

		/* 
		If using MySQL model set filter here to return one record according to URL request:
			http://dmain_name/root_dir/section_name/item/id...
			
			$search['item_id'] = $request_parameters['app_request'][2];
			$search['condition'] = array(
				'id = '.$search['item']
			);
		*/

	require(loadMVC('model', 'home'));

	// Set the display content for the view:

		/*
		If returning data from MySQL model, e.g...:
		$content['data'] = $model['records'];
		*/

	$content['data'] = $model['content'];

	echoContent($content);

	// Send the content to the view:
	require(loadMVC('view', 'home'));

?>