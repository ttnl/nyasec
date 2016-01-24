<?php

require_once DISCUZ_ROOT.'./source/plugin/nyasec/common.inc.php';

// don't use DB:table()
// discuz will replace 'pre_' in runquery()
$table = 'pre_'.C::t(TB)->getTable();
$log = 'pre_'.C::t(LOG)->getTable();

$sql = <<<EOF

CREATE TABLE IF NOT EXISTS $table (
  `uid` mediumint unsigned NOT NULL,
  `key` varchar(512) NOT NULL,
  `fail_count` mediumint DEFAULT 0,
  `fail_ban_until` bigint DEFAULT 0,
  `request_time` bigint,
  `request_code` varchar(256),
  PRIMARY KEY (`uid`)
) ENGINE=MYISAM;

CREATE TABLE IF NOT EXISTS $log (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint unsigned NOT NULL,
  `action` varchar(512) NOT NULL,
  `result` varchar(512) NOT NULL,
  `time` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM;

EOF;

runquery($sql);

$finish = TRUE;

?>