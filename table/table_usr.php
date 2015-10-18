<?php

class table_usr extends discuz_table {

	public function __construct() {
		$this->_table = 'nyasec_usr';
		$this->_pk = 'uid';
		parent::__construct();
	}

}