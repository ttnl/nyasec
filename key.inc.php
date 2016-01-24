<?php

require_once DISCUZ_ROOT.'./source/plugin/nyasec/common.inc.php';
require_once DISCUZ_ROOT.'./source/plugin/nyasec/lib/Ucpaas.class.php';

define('REQUEST_INTERVAL', 3 * 60);
define('REQUEST_EXPIRE', 10 * 60);

function check_phone_number($num) {
	if ($num)
		$num = trim($num);
	if (preg_match("/^[0-9]{11}$/", $num))
		return $num;
}

function send_sms($num, $code) {
	global $_G;
	$config = $_G['cache']['plugin']['nyasec'];
	if (!$config['accountsid'] ||
		!$config['token'] ||
		!$config['appid'] ||
		!$config['tmplid'])
		return -1;

	$ucpass = new Ucpaas(array(
		'accountsid' => $config['accountsid'],
		'token' => $config['token'],
	));

	$params = " $code,10 ";
	$sms = $ucpass->templateSMS($config['appid'], $num, $config['tmplid'], $params);
	$result = json_decode($sms, true);
	return $result['resp']['respCode'];
}

$uid = $_G['uid'];
if (!($uid > 0))
	exit_with('error', 'please login first');

$ac = $_GET['ac'];
if ($ac === 'request') {
	$phonenum = check_phone_number($_GET['phonenum']);
	if (!$phonenum)
		exit_with('error', 'invalid phone number');

	$data = C::t(TB)->fetch_all($uid)[$uid];
	if (!$data) {
		$data = array('uid' => $uid, 'request_time' => 0);
		C::t(TB)->insert($data);
	}

	$last_request_time = $data['request_time'];
	if (time() - $last_request_time < REQUEST_INTERVAL)
		exit_with('error', 'retry later');

	$request_code = substr(rand(0, 1e6) + 1e6, -6);
	$status = send_sms($phonenum, $request_code);
	if ($status === -1)
		exit_with('error', 'server setup error');

	C::t(LOG)->insert(array('uid' => $uid,
		'action' => 'send sms', 'result' => 'status: '.$status));
	if ($status !== '000000')
		exit_with('error', 'send sms failed');

	$data['key'] = bin2hex(openssl_random_pseudo_bytes(256));
	$data['request_code'] = $request_code;
	$data['request_time'] = time();
	C::t(TB)->update($uid, $data);
	exit_with('ok', $request_code);
}

else if ($ac === 'download') {
	$data = C::t(TB)->fetch_all($uid)[$uid];
	if (!$data['request_code'] || $data['request_code'] !== $_GET['code'] ||
			time() - $data['request_time'] > REQUEST_EXPIRE)
		exit_with('error', 'invalid download link');

	$data['request_code'] = '';
	C::t(TB)->update($uid, $data);

	C::t(LOG)->insert(array('uid' => $uid,
		'action' => 'download key', 'result' => 'ok'));
	exit_with('ok', $data['key']);
}

else if ($ac === 'check') {
	$data = C::t(TB)->fetch_all($uid)[$uid];
	exit_with($data ? 'ok' : 'error');
}

else if ($ac === 'cancel') {
	C::t(TB)->delete($uid);

	C::t(LOG)->insert(array('uid' => $uid,
		'action' => 'remove key', 'result' => 'ok'));
	exit_with('ok');
}

exit_with('error', 'invalid access');

