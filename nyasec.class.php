<?php

class plugin_nyasec {

}


class plugin_nyasec_member extends plugin_nyasec {

	function logging_input_output() {
		return file_get_contents(DISCUZ_ROOT.'./source/plugin/nyasec/template/input.htm');
	}

}

?>