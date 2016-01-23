<?php

require_once DISCUZ_ROOT.'./source/plugin/nyasec/common.inc.php';

$table = 'pre_'.C::t(TB)->getTable();

$sql = <<<EOF
DROP TABLE $table;
EOF;

runquery($sql);

$finish = TRUE;

?>