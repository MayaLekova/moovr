<?php

use IMSGlobal\LTI\ToolProvider;

require __DIR__ . '/util.php';

class MeiroToolProvider extends ToolProvider\ToolProvider {
    function __construct($data_connector) {
		parent::__construct($data_connector);

		$this->debugMode = TRUE;
	}


	function onLaunch() {
		$_SESSION['launchSettings'] = $this->resourceLink->getSettings();
		$_SESSION['returnUrl'] = $this->returnUrl;
		/*
		Settings:{"lis_result_sourcedid":"{\"data\":{\"instanceid\":\"5\",\"userid\":\"462\",\"launchid\":1510000295},\"hash\":\"25a6b3df5de776090edb027fbae0b91938802927de177de4a2301ef451538eb4\"}","lis_outcome_service_url":"http:\/\/moodle.mnknowledge.com\/mod\/lti\/service.php"}
		User:{"firstname":"\u041c\u0430\u044f","lastname":"\u041b\u0435\u043a\u043e\u0432\u0430","fullname":"\u041c\u0430\u044f \u041b\u0435\u043a\u043e\u0432\u0430","email":"apokalyptra@gmail.com","image":"","roles":["urn:lti:role:ims\/lis\/Instructor"],"groups":[],"ltiResultSourcedId":"{\"data\":{\"instanceid\":\"5\",\"userid\":\"462\",\"launchid\":1510000295},\"hash\":\"25a6b3df5de776090edb027fbae0b91938802927de177de4a2301ef451538eb4\"}","created":1518774351,"updated":1518774571,"ltiUserId":"462"}
		Context:{"ltiContextId":"29","title":"MM\/C-Training","settings":[],"created":1518774288,"updated":1518774571}
		*/

		$this->redirectUrl = getHost().'/meiro';
	}

	function onError() {
		$errorOutput = 'Error from MeiroProvider: '.$this->reason.'; Details: '.json_encode($this->details);
		var_dump($errorOutput);
		error_log($errorOutput);

	}
 
}

?>
