<?php

use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/lti_validate.php';

// Routes

$app->post('/lti', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Requested '/lti' route");

    $tool = $request->getAttribute('tool_provider');
	error_log('/lti handler: '.json_encode($tool));
	return $tool->handleRequest();
})->add($lti_store_consumer)->add($lti_db_open);

$app->get('/ping', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Requested '/ping' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/meiro', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Requested '/meiro' route");

    // Render index view
    return $this->renderer->render($response, 'meiro.html', $args);
});
