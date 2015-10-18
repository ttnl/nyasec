<?php

require_once DISCUZ_ROOT.'./source/plugin/nyasec/common.inc.php';

// the code changes every 60s and is valid in 5min
define('CODE_INTERVAL', 60);
define('CODE_LIFE', 5);

function get_uid($username) {
	loaducenter();
	list($uid) = uc_get_user($username);
	return $uid;
}

function user_login($uid) {
	$member = getuserbyuid($uid);
	$cookietime = 1296000;
	require_once libfile('function/member');
	setloginstatus($member, $cookietime);
}

function make_code($key, $tick) {
	$hash = md5($key.$tick);
	$hex = substr($hash, -8);
	$num = hexdec($hex);
	return substr($num % 1000000 + 1000000, -6);
}

$uid = isset($_GET['uid']) ? intval($_GET['uid']) :
	(isset($_GET['username']) ? get_uid($_GET['username']) : 0);
$code = isset($_GET['code']) ? $_GET['code'] : '';
if (!($uid > 0) || !$code)
	exit_with('error', 'invalid access');

$data = C::t(TB)->fetch_all($uid)[$uid];
if (!$data || !$data['key'])
	exit_with('error', 'undefined key');

$key = $data['key'];
$tick = floor(time() / CODE_INTERVAL);
for ($i = 0; $i < CODE_LIFE; $i ++)
	if (make_code($key, $tick - $i) === $code)
		exit_with('ok', user_login($uid));

exit_with('error', 'login failed');
