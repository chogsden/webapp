<?PHP

	// JSON Controller:

	// Convert Content to json string:
	$json_output = json_encode(array_merge(array('uri' => $request_parameters['this_url']), $content));

	// Send the content to the view:
	require(loadMVC('view', 'shared/json'));

?>