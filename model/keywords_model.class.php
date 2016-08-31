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
	
	public function keywords_select($where, $field, $order=null, $limit=null) {
		return $this->where($where)->field($field)->order($order)->limit($limit)->select();
	}
	
	public function keywords_count($where) {
		return $this->where($where)->count();
	}
}

// end