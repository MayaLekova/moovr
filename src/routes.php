<?php

use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/lti_validate.php';
require __DIR__ . '/grader.php';

// Routes

$app->post('/lti', function (Request $request, Response $response, array $args) {
    $this->logger->info("Requested '/lti' route");

    $tool = $request->getAttribute('tool_provider');
	error_log('/lti handler: '.json_encode($tool));
	return $tool->handleRequest();
})->add($lti_store_consumer)->add($lti_db_open);

$app->get('/ping', function (Request $request, Response $response, array $args) {
    $this->logger->info("Requested '/ping' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/meiro', function (Request $request, Response $response, array $args) {
    $this->logger->info("Requested '/meiro' route");

    // Render Meiro index view
    return $this->renderer->render($response, 'meiro.html', $args);
});

$app->post('/submit_answer', function(Request $request, Response $response, array $args) {
	global $grade;

    $this->logger->info("Requested '/submit_answer' route");
    $params = $request->getQueryParams();
    $parsedBody = $request->getParsedBody();

    // TODO: obtain student from session
    $student = null;

    $db_connector = $request->getAttribute('db_connector');
    $grade->call($this, $params['model'], $parsedBody['answer1'], $student, $db_connector);
})->add($lti_db_open);
