<?php

use IMSGlobal\LTI\ToolProvider\DataConnector;

require __DIR__ . '/config.php';
require __DIR__ . '/db_connector.php';

$lti_db_open = function ($request, $response, $next) {
	$db = open_db();
	if (!$db['result']) {
		$response->getBody()->write('Error while opening DB: '.$db['message']);
		return $response->withStatus(500);
	}

	$result = init_db($db['db']);
	if(!$result['ok']) {
		$response->getBody()->write('Error while initializing DB: '.$result['error']);
		return $response->withStatus(500);
	}
	// TODO: attach to request, session or something
	$data_connector = DataConnector\DataConnector::getDataConnector(DB_TABLENAME_PREFIX, $db['db']);

	$response = $next($request, $response);

    return $response;
};

?>
