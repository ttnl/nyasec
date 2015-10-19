<?php

require_once DISCUZ_ROOT.'./source/plugin/nyasec/common.inc.php';

// the code changes every 60s and is valid in 5min
define('CODE_INTERVAL', 60);
define('CODE_LIFE', 5);

// if failed straight 5 times, lock the account for 20min
define('MAX_FAIL_TIMES', 5);
define('FAIL_BAN_TIME', 20 * 60);

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

$uid = isset($_POST['uid']) ? intval($_POST['uid']) :
	(isset($_POST['username']) ? get_uid($_POST['username']) : 0);
$code = isset($_POST['code']) ? $_POST['code'] : '';
if (!($uid > 0) || !$code)
	exit_with('error', 'invalid access');

$data = C::t(TB)->fetch_all($uid)[$uid];
if (!$data || !$data['key'])
	exit_with('error', 'undefined key');

$fail_count = $data['fail_count'];
$ban_until = $data['fail_ban_until'];
if (time() < $ban_until)
	exit_with('error', 'failed too many times');

$key = $data['key'];
$tick = floor(time() / CODE_INTERVAL);
for ($i = 0; $i < CODE_LIFE; $i ++)
	if (make_code($key, $tick - $i + 1) === $code) {
		if ($fail_count > 0)
			C::t(TB)->update($uid, array('fail_count' => 0));
		exit_with('ok', user_login($uid));
	}

if (++ $fail_count > MAX_FAIL_TIMES) {
	$ban_until = time() + FAIL_BAN_TIME;
	$fail_count = 0;
}
C::t(TB)->update($uid, array('fail_count' => $fail_count, 'fail_ban_until' => $ban_until));
exit_with('error', 'login failed');

