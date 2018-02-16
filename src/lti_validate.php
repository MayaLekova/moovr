<?php

use IMSGlobal\LTI\ToolProvider\DataConnector;
use IMSGlobal\LTI\ToolProvider;

require __DIR__ . '/config.php';
require __DIR__ . '/db_connector.php';
require __DIR__ . '/meiro_provider.php';

$lti_db_open = function ($request, $response, $next) {
	$db = open_db();
	if (!$db['result']) {
		$response->getBody()->write('Error while opening DB: '.json_encode($db['message']));
		return $response->withStatus(500);
	}

	$result = init_db($db['db']);
	if(!$result['ok']) {
		$response->getBody()->write('Error while initializing DB: '.json_encode($result['error']));
		return $response->withStatus(500);
	}
	// TODO: attach to request, session or something
	$data_connector = DataConnector\DataConnector::getDataConnector(DB_TABLENAME_PREFIX, $db['db']);
	$request = $request->withAttribute('db_connector', $data_connector);

	$response = $next($request, $response);

    return $response;
};

$lti_store_consumer = function($request, $response, $next) {
	$db_connector = $request->getAttribute('db_connector');

	$consumer = new ToolProvider\ToolConsumer('moodle_home', $db_connector);
	$consumer->name = 'MoodleHome';
	$consumer->secret = 'ParolkaMarolka';
	$consumer->enabled = TRUE;
	$consumer->save();

	$tool = new MeiroToolProvider($db_connector);
	$request = $request->withAttribute('tool_provider', $tool);

	$response = $next($request, $response);
	return $response;
}

?>
