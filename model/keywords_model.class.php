<?php
/**
 * 搜索引擎模型
 */
defined('IN_ECJIA') or exit('No permission resources.');
class keywords_model extends Component_Model_Model {
	public $table_name = '';
	public $view = array();
	public function __construct() {
		$this->db_config = RC_Config::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'keywords';
		parent::__construct();
	}
}

// end