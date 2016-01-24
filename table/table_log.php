<?php

class table_log extends discuz_table {

	public function __construct() {
		$this->_table = 'nyasec_log';
		$this->_pk = 'id';
		parent::__construct();
	}

}