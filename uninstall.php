<?php

require_once DISCUZ_ROOT.'./source/plugin/nyasec/common.inc.php';

$table = DB::table(C::t(TB)->getTable());

$sql = <<<EOF
DROP TABLE $table;
EOF;

runquery($sql);

$finish = TRUE;

?>