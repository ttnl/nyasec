<?php

require_once DISCUZ_ROOT.'./source/plugin/nyasec/common.inc.php';

define('REQUEST_INTERVAL', 3 * 60);
define('REQUEST_EXPIRE', 24 * 60 * 60);

function is_valid_phone_number($num) {
	// TODO
	return !!$num;
}

$uid = $_G['uid'];
if (!($uid > 0))
	exit_with('error', 'please login first');

$ac = $_GET['ac'];
if ($ac === 'request') {
	$phonenum = $_GET['phonenum'];
	if (!is_valid_phone_number($phonenum))
		exit_with('error', 'invalid phone number');

	$data = C::t(TB)->fetch_all($uid)[$uid];
	if (!$data) {
		$data = array('uid' => $uid, 'request_time' => 0);
		C::t(TB)->insert($data);
	}

	$last_request_time = $data['request_time'];
	if (time() - $last_request_time < REQUEST_INTERVAL)
		exit_with('error', 'please retry 3min later');

	$data['key'] = bin2hex(openssl_random_pseudo_bytes(256));
	$data['request_code'] = bin2hex(openssl_random_pseudo_bytes(32));
	$data['request_time'] = time();
	C::t(TB)->update($uid, $data);

	// TODO: send the code with phone
	exit_with('ok', $data['request_code']);
}

else if ($ac === 'download') {
	$data = C::t(TB)->fetch_all($uid)[$uid];
	if (!$data['request_code'] || $data['request_code'] !== $_GET['code'] ||
			time() - $data['request_time'] > REQUEST_EXPIRE)
		exit_with('error', 'invalid download link');

	$data['request_code'] = '';
	C::t(TB)->update($uid, $data);

	exit_with('ok', $data['key']);
}

else if ($ac === 'check') {
	$data = C::t(TB)->fetch_all($uid)[$uid];
	exit_with($data ? 'ok' : 'error');
}

else if ($ac === 'cancel') {
	C::t(TB)->delete($uid);
	exit_with('ok');
}

exit_with('error', 'invalid access');

