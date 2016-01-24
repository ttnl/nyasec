<?php

require_once DISCUZ_ROOT.'./source/plugin/nyasec/common.inc.php';

$table = 'pre_'.C::t(TB)->getTable();
$log = 'pre_'.C::t(LOG)->getTable();

$sql = <<<EOF
DROP TABLE $table;
DROP TABLE $log;
EOF;

runquery($sql);

$finish = TRUE;

?>