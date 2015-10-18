<?php

if (!defined('IN_DISCUZ'))
	exit('Access Denied');

define('TB', '#nyasec#usr');

function exit_with($result, $message) {
	echo json_encode(array('result' => $result, 'message' => $message));
	exit;
}

?>