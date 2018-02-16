<?php

// Inspired by https://github.com/IMSGlobal/LTI-Sample-Tool-Provider-PHP/blob/master/src/db.php

use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\DataConnector;

require __DIR__ . '/config.php';
require __DIR__ . '/../vendor/autoload.php';

function open_db() {
	try {
		$db = new PDO(DB_NAME);//TODO: , DB_USERNAME, DB_PASSWORD);
		return array(
			'db' => $db,
			'result' => TRUE
		);
	} catch(PDOException $e) {
		return array(
			'code' => $e->getCode(),
			'message' => $e->getMessage(),
			'result' => FALSE
		);
	}
}

function table_exists($db, $name) {
	return FALSE;
	$sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='{$name}';";
	// $sql = "select 1 from {$name};";
	$query = $db->prepare($sql);
	if(!$query) {
		var_dump('Error: '.$db->errorInfo());
		return FALSE;
	}
	$res = $query->execute();
	var_dump('Res: '.$res);
	return $res !== FALSE;
}

function init_db($db) {
	$db_type = '';
	$pos = strpos(DB_NAME, ':');
	if ($pos !== FALSE) {
		$db_type = strtolower(substr(DB_NAME, 0,$pos));
	}
	$ok = TRUE;
	$prefix = DB_TABLENAME_PREFIX;
	if (!table_exists($db, $prefix . DataConnector\DataConnector::CONSUMER_TABLE_NAME)) {
		$sql = "CREATE TABLE IF NOT EXISTS {$prefix}" . DataConnector\DataConnector::CONSUMER_TABLE_NAME . ' (' .
					 'consumer_pk INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL , ' .
					 'name varchar(50) NOT NULL, ' .
					 'consumer_key256 varchar(256) NOT NULL, ' .
					 'consumer_key text DEFAULT NULL, ' .
					 'secret varchar(1024) NOT NULL, ' .
					 'lti_version varchar(10) DEFAULT NULL, ' .
					 'consumer_name varchar(255) DEFAULT NULL, ' .
					 'consumer_version varchar(255) DEFAULT NULL, ' .
					 'consumer_guid varchar(1024) DEFAULT NULL, ' .
					 'profile text DEFAULT NULL, ' .
					 'tool_proxy text DEFAULT NULL, ' .
					 'settings text DEFAULT NULL, ' .
					 'protected tinyint(1) NOT NULL, ' .
					 'enabled tinyint(1) NOT NULL, ' .
					 'enable_from datetime DEFAULT NULL, ' .
					 'enable_until datetime DEFAULT NULL, ' .
					 'last_access date DEFAULT NULL, ' .
					 'created datetime NOT NULL, ' .
					 'updated datetime NOT NULL);';
		$ok = $db->exec($sql) !== FALSE;
		if ($ok) {
			$tb_name = $prefix.DataConnector\DataConnector::CONSUMER_TABLE_NAME;
			$sql = "CREATE UNIQUE INDEX IF NOT EXISTS {$tb_name}_consumer_key_UNIQUE ON {$tb_name} (consumer_key256 ASC);";
			$ok = $db->exec($sql) !== FALSE;
		}
	}
	if (!table_exists($db, $prefix . DataConnector\DataConnector::TOOL_PROXY_TABLE_NAME)) {
		$sql = "CREATE TABLE IF NOT EXISTS {$prefix}" . DataConnector\DataConnector::TOOL_PROXY_TABLE_NAME . ' (' .
					 'tool_proxy_pk INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL , ' .
					 'tool_proxy_id varchar(32) NOT NULL, ' .
					 'consumer_pk INTEGER NOT NULL, ' .
					 'tool_proxy text NOT NULL, ' .
					 'created datetime NOT NULL, ' .
					 'updated datetime NOT NULL, FOREIGN KEY(consumer_pk) '.
					 "REFERENCES {$prefix}" . DataConnector\DataConnector::CONSUMER_TABLE_NAME . " (consumer_pk));";
		$ok = $db->exec($sql) !== FALSE;
		if ($ok) {
			$tb_name = $prefix . DataConnector\DataConnector::TOOL_PROXY_TABLE_NAME;
			$sql = "CREATE INDEX {$tb_name}_consumer_id_IDX ON {$tb_name} (consumer_pk ASC);";
			$ok = $db->exec($sql) !== FALSE;
		}
		if ($ok) {
			$tb_name = $prefix . DataConnector\DataConnector::TOOL_PROXY_TABLE_NAME;
			$sql = "CREATE UNIQUE INDEX IF NOT EXISTS {$tb_name}_tool_proxy_id_UNIQUE ON {$tb_name} (tool_proxy_id ASC);";
			$ok = $db->exec($sql) !== FALSE;
		}
	}
	if ($ok && !table_exists($db, $prefix . DataConnector\DataConnector::NONCE_TABLE_NAME)) {
		$sql = "CREATE TABLE IF NOT EXISTS {$prefix}" . DataConnector\DataConnector::NONCE_TABLE_NAME . ' (' .
					 'consumer_pk int(11) NOT NULL, ' .
					 'value varchar(32) NOT NULL, ' .
					 'expires datetime NOT NULL, ' .
					 'PRIMARY KEY (consumer_pk, value),'.
					 "FOREIGN KEY (consumer_pk) REFERENCES {$prefix}" . DataConnector\DataConnector::CONSUMER_TABLE_NAME . " (consumer_pk));";
		$ok = $db->exec($sql) !== FALSE;
	}
	if (!table_exists($db, $prefix . DataConnector\DataConnector::CONTEXT_TABLE_NAME)) {
		$sql = "CREATE TABLE IF NOT EXISTS {$prefix}" . DataConnector\DataConnector::CONTEXT_TABLE_NAME . ' (' .
					 'context_pk INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL , ' .
					 'consumer_pk int(11) NOT NULL, ' .
					 'lti_context_id varchar(255) NOT NULL, ' .
					 'settings text DEFAULT NULL, ' .
					 'created datetime NOT NULL, ' .
					 'updated datetime NOT NULL, ' .
					 "FOREIGN KEY (consumer_pk) REFERENCES {$prefix}" . DataConnector\DataConnector::CONSUMER_TABLE_NAME . " (consumer_pk));";
		$ok = $db->exec($sql) !== FALSE;
		if ($ok) {
			$tb_name = $prefix . DataConnector\DataConnector::CONTEXT_TABLE_NAME;
			$sql = "CREATE INDEX IF NOT EXISTS {$tb_name}_consumer_id_IDX ON {$tb_name} (consumer_pk ASC);";
			$ok = $db->exec($sql) !== FALSE;
		}
	}
	if ($ok && !table_exists($db, $prefix . DataConnector\DataConnector::RESOURCE_LINK_TABLE_NAME)) {
		$sql = "CREATE TABLE IF NOT EXISTS {$prefix}" . DataConnector\DataConnector::RESOURCE_LINK_TABLE_NAME . ' (' .
					 'resource_link_pk INTEGER PRIMARY KEY AUTOINCREMENT, ' .
					 'context_pk int(11) DEFAULT NULL, ' .
					 'consumer_pk int(11) DEFAULT NULL, ' .
					 'lti_resource_link_id varchar(255) NOT NULL, ' .
					 'settings text, ' .
					 'primary_resource_link_pk int(11) DEFAULT NULL, ' .
					 'share_approved tinyint(1) DEFAULT NULL, ' .
					 'created datetime NOT NULL, ' .
					 'updated datetime NOT NULL, ' .
					 "FOREIGN KEY (context_pk) REFERENCES {$prefix}" . DataConnector\DataConnector::CONTEXT_TABLE_NAME . " (context_pk));";
		$ok = $db->exec($sql) !== FALSE;
		if ($ok) {
			$tb_name = $prefix . DataConnector\DataConnector::RESOURCE_LINK_TABLE_NAME;
			$sql = "CREATE INDEX IF NOT EXISTS {$tb_name}_consumer_pk_IDX ON {$tb_name} (consumer_pk ASC);";
			$ok = $db->exec($sql) !== FALSE;
		}
		if ($ok) {
			$tb_name = $prefix . DataConnector\DataConnector::RESOURCE_LINK_TABLE_NAME;
			$sql = "CREATE INDEX IF NOT EXISTS {$tb_name}_context_pk_IDX ON {$tb_name} (context_pk ASC);";
			$ok = $db->exec($sql) !== FALSE;
		}
	}
	if ($ok && !table_exists($db, $prefix . DataConnector\DataConnector::USER_RESULT_TABLE_NAME)) {
		$sql = "CREATE TABLE IF NOT EXISTS {$prefix}" . DataConnector\DataConnector::USER_RESULT_TABLE_NAME . ' (' .
					 'user_pk INTEGER PRIMARY KEY AUTOINCREMENT, ' .
					 'resource_link_pk int(11) NOT NULL, ' .
					 'lti_user_id varchar(255) NOT NULL, ' .
					 'lti_result_sourcedid varchar(1024) NOT NULL, ' .
					 'created datetime NOT NULL, ' .
					 'updated datetime NOT NULL, ' .
					 "FOREIGN KEY (resource_link_pk) REFERENCES {$prefix}" . DataConnector\DataConnector::RESOURCE_LINK_TABLE_NAME . " (resource_link_pk));";
		$ok = $db->exec($sql) !== FALSE;
		if ($ok) {
			$tb_name = $prefix . DataConnector\DataConnector::USER_RESULT_TABLE_NAME;
			$sql = "CREATE INDEX IF NOT EXISTS {$tb_name}_resource_link_pk_IDX ON {$tb_name} (resource_link_pk ASC);";
			$ok = $db->exec($sql) !== FALSE;
		}
	}
	if ($ok && !table_exists($db, $prefix . DataConnector\DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME)) {
		$sql = "CREATE TABLE IF NOT EXISTS {$prefix}" . DataConnector\DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME . ' (' .
					 'share_key_id varchar(32) NOT NULL, ' .
					 'resource_link_pk int(11) NOT NULL, ' .
					 'auto_approve tinyint(1) NOT NULL, ' .
					 'expires datetime NOT NULL, ' .
					 'PRIMARY KEY (share_key_id),' .
					 "FOREIGN KEY (resource_link_pk) REFERENCES {$prefix}" . DataConnector\DataConnector::RESOURCE_LINK_TABLE_NAME . " (resource_link_pk));";
		$ok = $db->exec($sql) !== FALSE;
		if ($ok) {
			$tb_name = $prefix . DataConnector\DataConnector::RESOURCE_LINK_SHARE_KEY_TABLE_NAME;
			$sql = "CREATE INDEX IF NOT EXISTS {$tb_name}_resource_link_pk_IDX ON {$tb_name} (resource_link_pk ASC);";
			$ok = $db->exec($sql) !== FALSE;
		}
	}
	return array(
		'ok' => $ok,
		'error' => $db->errorInfo()
	);
}

?>
