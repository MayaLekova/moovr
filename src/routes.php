<?php

use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/lti_validate.php';

// Routes

$app->get('/lti', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Requested '/lti' route");

    $tool = $request->getAttribute('tool_provider');
	$tool->handleRequest();
    // Render index view
    return $this->renderer->render($response, 'meiro.html', $args);
})->add($lti_store_consumer)->add($lti_db_open);

$app->get('/ping', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Requested '/ping' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
