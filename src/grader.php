<?php

use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\ResourceLink;

require __DIR__ . '/config.php';

$grade = function ($model, $answer, $student, $db_connector) {
	include __DIR__ . '/questions/' . $model . '.php';
	
	$result = $processAnswer->call($this, $answer);
	error_log('Grade: '.$result['grade']);
	error_log('Outcome: '.$result['outcome']);

	$launchData = json_decode($_SESSION['launchSettings']['lis_result_sourcedid']);
	$resource_link_id = $launchData->data->instanceid;
	$user_id = $launchData->data->userid;

	$consumer = new ToolProvider\ToolConsumer(CONSUMER_KEY, $db_connector);
	$resource_link = ToolProvider\ResourceLink::fromConsumer($consumer, $resource_link_id);	
	$outcome = new ToolProvider\Outcome($result['grade']);
	$user = ToolProvider\User::fromResourceLink($resource_link, $user_id);
	$ok = $resource_link->doOutcomesService(ResourceLink::EXT_WRITE, $outcome, $user);
	if(!$ok) {
		error_log('Warning: possible grading error for user '.$user_id);
	}

	// TODO: obtain from tool_provider from /lti
	$returnUrl = 'http://moodle.mnknowledge.com/mod/lti/return.php?course=29&launch_container=4&instanceid=5';
	// TODO: send outcome to client
}

?>
