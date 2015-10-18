<?php

require_once DISCUZ_ROOT.'./source/plugin/nyasec/common.inc.php';

$table = DB::table(C::t(TB)->getTable());

$sql = <<<EOF

CREATE TABLE IF NOT EXISTS $table (
  `uid` mediumint(8) unsigned NOT NULL,
  `key` varchar(512) NOT NULL,
  `request_time` int(64),
  `request_code` varchar(256),
  PRIMARY KEY (`uid`)
) ENGINE=MYISAM;

EOF;

runquery($sql);

$finish = TRUE;

?>