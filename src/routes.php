<?php

use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/lti_validate.php';

// Routes

$app->get('/lti', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Requested '/lti' route");

    // Render index view
    return $this->renderer->render($response, 'meiro.html', $args);
})->add($lti_db_open);

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Requested '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
