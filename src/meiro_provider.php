<?php

use IMSGlobal\LTI\ToolProvider;

class MeiroToolProvider extends ToolProvider\ToolProvider {
 
	function onLaunch() {
		var_dump('onLaunch');

	// Insert code here to handle incoming connections - use the user,
	// context and resourceLink properties of the class instance
	// to access the current user, context and resource link.

	}

	function onError() {
		var_dump('onError');

	}
 
}

?>
