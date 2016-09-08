<?php
/**
 * 搜索引擎
 * @author wutifang
 * 
*/
defined('IN_ECJIA') or exit('No permission resources.');

class admin_searchengine_stats extends ecjia_admin {
	private $db_searchengine;
	public function __construct() {
		parent::__construct();
        $this->db_searchengine = RC_Model::model('stats/searchengine_model');
        
        RC_Loader::load_app_func('global', 'stats');
        
        /* 加载所有全局 js/css */
        RC_Script::enqueue_script('bootstrap-placeholder');
        RC_Script::enqueue_script('jquery-validate');
        RC_Script::enqueue_script('jquery-form');
        RC_Script::enqueue_script('smoke');
        RC_Script::enqueue_script('jquery-chosen');
        RC_Style::enqueue_style('chosen');
        RC_Script::enqueue_script('jquery-uniform');
        RC_Style::enqueue_style('uniform-aristo');
        RC_Script::enqueue_script('bootstrap-editable-script', RC_Uri::admin_url('statics/lib/x-editable/bootstrap-editable/js/bootstrap-editable.min.js'));
        RC_Style::enqueue_style('bootstrap-editable-css', RC_Uri::admin_url('statics/lib/x-editable/bootstrap-editable/css/bootstrap-editable.css'));

        //时间控件
        RC_Style::enqueue_style('datepicker', RC_Uri::admin_url('statics/lib/datepicker/datepicker.css'));
        RC_Script::enqueue_script('bootstrap-datepicker', RC_Uri::admin_url('statics/lib/datepicker/bootstrap-datepicker.min.js'));
        /*加载图表js*/
        RC_Script::enqueue_script('acharts-min', RC_App::apps_url('statics/js/acharts-min.js', __FILE__));
        
        /*加载自定义js*/
        RC_Style::enqueue_style('stats-css', RC_App::apps_url('statics/css/stats.css', __FILE__));
        RC_Script::enqueue_script('searchengine_stats', RC_App::apps_url('statics/js/searchengine_stats.js', __FILE__));
        RC_Script::localize_script('searchengine_stats', 'js_lang', RC_Lang::get('stats::statistic.js_lang'));
        
        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(RC_Lang::get('stats::statistic.searchengine')));
	}
	
	/**
	 * 搜索引擎
	 */
	public function init() {
		$this->admin_priv('searchengine_stats', ecjia::MSGTYPE_JSON);
		
		$this->assign('ur_here', RC_Lang::get('stats::statistic.searchengine'));
		$this->assign('action_link', array('text' => RC_Lang::get('stats::statistic.down_searchengine'), 'href' => RC_Uri::url('stats/admin_searchengine_stats/download')));
		$this->assign('form_action', RC_Uri::url('stats/admin_searchengine_stats/init'));
		$this->assign('ajax_loader', RC_App::apps_url('statics/images/ajax_loader.gif', __FILE__));
		
		$type = !empty($_GET['type']) ? intval($_GET['type']) : 2; 	//1昨天 2今天 3本周 4本月
		$month = !empty($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
		$month_list = RC_Lang::get('stats::statistic.month_list');
		
		$this->assign('search_action', RC_Uri::url('stats/admin_searchengine_stats/init', array('type' => $type)));
		$this->assign('month_list', $month_list);
		
		$this->assign('month', $month);
		$this->assign('type', $type);
		
		$this->display('searchengine_stats.dwt');
	}
	
	/**
	 * 获取图表数据
	 */
	public function get_chart_data() {
		$this->admin_priv('searchengine_stats', ecjia::MSGTYPE_JSON);
		
		$type = !empty($_GET['type']) ? intval($_GET['type']) : 2;	
		
		if ($type == 1) {
			$format = '%Y-%m-%d';
			$start_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-2, date('Y')));
			$end_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y')));
			
			$where = "date > '$start_date' AND date < '$end_date' ";
			$field = "DATE_FORMAT((date), '". $format ."') AS time, searchengine, count";
			$count = $this->db_searchengine->searchengine_select($where, $field, 'date asc');
			
			$counts = array();
			if (!empty($count)) {
				foreach ($count as $k => $v) {
					foreach ($v as $key => $val) {
						if ($key == 'searchengine') {
							$counts[$v['searchengine']] = $v['count'];
						}
					}
				}
			}
			$counts = json_encode($counts);
			echo $counts;
			
		} elseif ($type == 2) {
			$format = '%Y-%m-%d';
			$start_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0,0,0,date('m'),date('d')-1,date('Y')));
			$end_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0,0,0,date('m'),date('d')+1,date('Y')));
			
			$where = "date > '$start_date' AND date < '$end_date' ";
			$field = "DATE_FORMAT((date), '". $format ."') AS time, searchengine, count";
			$count = $this->db_searchengine->searchengine_select($where, $field, 'date asc');
			
			$counts = array();
			if (!empty($count)) {
				foreach ($count as $k => $v) {
					foreach ($v as $key => $val) {
						if ($key == 'searchengine') {
							$counts[$v['searchengine']] = $v['count'];
						}
					}
				}
			}
			
			$counts = json_encode($counts);
			echo $counts;
			
		} elseif ($type == 3) {
			$format = '%Y-%m-%d';
			$start_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-date('w')+1, date('Y')));
			$end_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(23, 59, 59, date('m'), date('d')-date('w')+7, date('Y')));
			
			$where = "date >= '$start_date' AND date <= '$end_date' ";
			$field = "DATE_FORMAT((date), '". $format ."') AS time, searchengine, count";
			$count = $this->db_searchengine->searchengine_select($where, $field, 'date asc');
			
			$arr = array();
			$arr1 = array();
			if (!empty($count)) {
				foreach ($count as $k => $v) {
					$arr[$v['searchengine']][] = array($v['time'] => $v['count']);
				}
					
				foreach ($arr as $k => $v) {
					foreach ($v as $key => $value) {
						foreach ($value as $v1 => $v2) {
							$day = date("w", RC_Time::local_strtotime($v1));
							$arr1[$k][$day+1] = '';
							$arr1[$k][$day+1] += $v2;
						}
					}
				}
					
				for ($i=1; $i<=7; $i++) {
					$arr2[] = $i;
				}
					
				foreach ($arr1 as $k => $v) {
					foreach ($arr2 as $v1) {
						if (!array_key_exists($v1, $v)) {
							foreach ($v as $k1 => $v2) {
								$arr1[$k][$v1] = 0;
							}
						}
					}
				}
			}

			$counts = json_encode($arr1);
			echo $counts;
			
		} elseif ($type == 4) {
			$month = !empty($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
			$format = '%Y-%m-%d';
			$start_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date($month), 1, date('Y')));
			$end_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date($month+1), 1, date('Y'))-86400);
			
			$where = "date >= '$start_date' AND date <= '$end_date' ";
			$field = "DATE_FORMAT((date), '". $format ."') AS time, searchengine, count";
			$count = $this->db_searchengine->searchengine_select($where, $field, 'date asc');
			
			$arr = array();
			$arr1 = array();
			if (!empty($count)) {
				foreach ($count as $k => $v) {
					$arr[$v['searchengine']][] = array($v['time'] => $v['count']);
				}
					
				foreach ($arr as $k => $v) {
					foreach ($v as $key => $value) {
						foreach ($value as $v1 => $v2) {
							$day = intval(RC_Time::local_date('d', RC_Time::local_strtotime($v1)));
							$arr1[$k][$day] = '';
							$arr1[$k][$day] += $v2;
						}
					}
				}
					
				for ($i=1; $i<=31; $i++) {
					$arr2[] = $i;
				}
					
				foreach ($arr1 as $k => $v) {
					foreach ($arr2 as $v1) {
						if (!array_key_exists($v1, $v)) {
							foreach ($v as $k1 => $v2) {
								$arr1[$k][$v1] = 0;
							}
						}
					}
				}
			}
			
			$counts = json_encode($arr1);
			echo $counts;
		}
	}
	
	/**
	 * 搜索引擎统计报表下载
	 */
	public function download() {
		$this->admin_priv('searchengine_stats', ecjia::MSGTYPE_JSON);
		
		$filename = RC_Lang::get('stats::statistic.search_engine_report');
		header("Content-type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		
		$type = !empty($_GET['type']) ? intval($_GET['type']) : '2';
		$month = !empty($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
		
		if ($type == 1) {
			$start_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-2, date('Y')));
			$end_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y')));
				
			$where = "date > '$start_date' AND date < '$end_date' ";
		} elseif ($type == 2) {
			$start_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-1, date('Y')));
			$end_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y')));
				
			$where = "date > '$start_date' AND date < '$end_date' ";
		} elseif ($type == 3) {
			$format = '%Y-%m-%d';
			$start_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-date('w')+1, date('Y')));
			$end_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(23, 59, 59, date('m'), date('d')-date('w')+7, date('Y')));
			
			$where = "date >= '$start_date' AND date <= '$end_date' ";
			$field = "DATE_FORMAT((date), '". $format ."') AS time, searchengine, count";
			$count = $this->db_searchengine->searchengine_select($where, $field, 'date asc');
			
			$data = '';
			$arr = array();
			$arr1 = array();
			if (!empty($count)) {
				foreach ($count as $k => $v) {
					$arr[$v['searchengine']][] = array($v['time'] => $v['count']);
				}
					
				foreach ($arr as $k => $v) {
					foreach ($v as $key => $value) {
						foreach ($value as $v1 => $v2) {
							$day = date("w", RC_Time::local_strtotime($v1));
							$arr1[$k][$day] = '';
							$arr1[$k][$day] += $v2;
						}
					}
				}
				
				$basical = RC_Lang::get('stats::statistic.day_list');
				
				foreach ($arr1 as $k => $v) {
					$data .= $k . "\t\n";
					foreach ($v as $key => $val) {
						$data .= $basical[$key] . "\t";
						$data .= $val . RC_Lang::get('stats::statistic.times') . "\t\n";
					}
					$data .= "\n";
				}
			}
			echo mb_convert_encoding($data."\t","GBK","UTF-8");
			exit;
		} elseif ($type == 4) {
			$month = !empty($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
			$format = '%Y-%m-%d';
			$start_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date($month), 1, date('Y')));
			$end_date = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_mktime(0, 0, 0, date($month+1), 1, date('Y'))-86400);
			
			$where = "date >= '$start_date' AND date <= '$end_date' ";
			$field = "DATE_FORMAT((date), '". $format ."') AS time, searchengine, count";
			$count = $this->db_searchengine->searchengine_select($where, $field, 'date asc');
			
			$data = '';
			$arr = $arr1 = array();
			if (!empty($count)) {
				foreach ($count as $k => $v) {
					$arr[$v['searchengine']][] = array($v['time'] => $v['count']);
				}
			}
			if (!empty($arr)) {
				foreach ($arr as $k => $v) {
					foreach ($v as $key => $value) {
						foreach ($value as $v1 => $v2) {
							$day = intval(RC_Time::local_date('d', RC_Time::local_strtotime($v1)));
							$arr1[$k][$day] = '';
							$arr1[$k][$day] += $v2;
						}
					}
				}
			}
			
			if (!empty($arr1)) {
				foreach ($arr1 as $k => $v) {
					$data .= $k . "\t\n";
					foreach ($v as $key => $val) {
						$data .= $month.'-'.$key. "\t";
						$data .= $val.RC_Lang::get('stats::statistic.times') . "\t\n";
					}
					$data .= "\n";
				}
			}
			echo mb_convert_encoding($data."\t", "GBK", "UTF-8");
			exit;
		} 
		if ($type == 1 || $type == 2) {
			$list = $this->db_searchengine->searchengine_select($where, 'date, searchengine, count', 'date asc');
			
			$data =	RC_Lang::get('stats::statistic.date')."\t".RC_Lang::get('stats::statistic.searchengine')."\t".RC_Lang::get('stats::statistic.hits')."\t\n";
			if (!empty($list)) {
				foreach ($list as $v) {
					$data .= $v['date'] . "\t";
					$data .= $v['searchengine'] . "\t";
					$data .= $v['count'].RC_Lang::get('stats::statistic.times') . "\t\n";
				}
			}
			echo mb_convert_encoding($data."\t", "GBK", "UTF-8");
			exit;
		}
	}
}

// end