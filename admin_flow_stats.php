<?php
/**
 * 综合流量统计
 * @author wutifang
 * 
*/
defined('IN_ECJIA') or exit('No permission resources.');

class admin_flow_stats extends ecjia_admin {
	private $db_stats;
	public function __construct() {
		parent::__construct();
		
        $this->db_stats = RC_Model::model('stats/stats_model');
        
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
        RC_Script::enqueue_script('general_stats', RC_App::apps_url('statics/js/general_stats.js', __FILE__));
        RC_Script::enqueue_script('area_stats', RC_App::apps_url('statics/js/area_stats.js', __FILE__));
        RC_Script::enqueue_script('from_stats', RC_App::apps_url('statics/js/from_stats.js', __FILE__));
        RC_Script::enqueue_script('flow_stats_chart', RC_App::apps_url('statics/js/flow_stats_chart.js', __FILE__));
        
        RC_Script::localize_script('general_stats', 'js_lang', RC_Lang::get('stats::flow_stats.js_lang'));
        RC_Script::localize_script('area_stats', 'js_lang', RC_Lang::get('stats::flow_stats.js_lang'));
        RC_Script::localize_script('from_stats', 'js_lang', RC_Lang::get('stats::flow_stats.js_lang'));
        RC_Script::localize_script('flow_stats_chart', 'js_lang', RC_Lang::get('stats::flow_stats.js_lang'));
        
       	ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(RC_Lang::get('stats::flow_stats.traffic_analysis'), RC_Uri::url('stats/admin_flow_stats/general_stats')));
	}
	
	/**
	 * 综合访问量
	 */
	public function general_stats() {
		$this->admin_priv('flow_stats', ecjia::MSGTYPE_JSON);
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(RC_Lang::get('stats::flow_stats.tab_general')));
		ecjia_screen::get_current_screen()->add_help_tab(array(
			'id'		=> 'overview',
			'title'		=> RC_Lang::get('stats::flow_stats.overview'),
			'content'	=> '<p>' . RC_Lang::get('stats::flow_stats.general_stats_help') . '</p>'
		));
		ecjia_screen::get_current_screen()->set_help_sidebar(
			'<p><strong>' . RC_Lang::get('stats::flow_stats.more_info') . '</strong></p>' .
			'<p>' . __('<a href="https://ecjia.com/wiki/帮助:ECJia智能后台:流量分析#.E7.BB.BC.E5.90.88.E8.AE.BF.E9.97.AE.E9.87.8F" target="_blank">'. RC_Lang::get('stats::flow_stats.about_general_stats') .'</a>') . '</p>'
		);
		
		$this->assign('ur_here', RC_Lang::get('stats::flow_stats.tab_general'));
        $this->assign('action_link', array('text' => RC_Lang::get('stats::flow_stats.down_general'), 'href' => RC_Uri::url('stats/admin_flow_stats/general_stats_download')));
        $this->assign('ajax_loader', RC_App::apps_url('statics/images/ajax_loader.gif', __FILE__));
        
        //按年查询
		$start_year = !empty($_GET['start_year']) 	? $_GET['start_year'] 	: RC_Time::local_date('Y');
		$end_year 	= !empty($_GET['end_year']) 	? $_GET['end_year'] 	: '';
		
		//按月查询
		$start_month = !empty($_GET['start_month'])	? $_GET['start_month'] 	: RC_Time::local_date('Y-m');
		$end_month   = !empty($_GET['end_month']) 	? $_GET['end_month'] 	: '';
		$type 		 = !empty($_GET['type']) 		? true 					: false;
		
		$this->assign('start_year', $start_year);
		$this->assign('end_year', $end_year);
		$this->assign('start_month', $start_month);
		$this->assign('end_month', $end_month);
		$this->assign('type', $type);
		
		$this->assign_lang();
		$this->display('general_stats.dwt');
	}
	
	/**
	 * 地区分布
	 */	
	public function area_stats() {
		$this->admin_priv('flow_stats', ecjia::MSGTYPE_JSON);
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
		
        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(RC_Lang::get('stats::flow_stats.tab_area')));
		ecjia_screen::get_current_screen()->add_help_tab(array(
			'id'		=> 'overview',
			'title'		=> RC_Lang::get('stats::flow_stats.overview'),
			'content'	=> '<p>' . RC_Lang::get('stats::flow_stats.area_stats_help') . '</p>'
		));
		
		ecjia_screen::get_current_screen()->set_help_sidebar(
			'<p><strong>' . RC_Lang::get('stats::flow_stats.more_info') . '</strong></p>' .
			'<p>' . __('<a href="https://ecjia.com/wiki/帮助:ECJia智能后台:流量分析#.E5.9C.B0.E5.8C.BA.E5.88.86.E5.B8.83" target="_blank">'. RC_Lang::get('stats::flow_stats.about_area_stats') .'</a>') . '</p>'
		);
		
        $this->assign('ur_here', RC_Lang::get('stats::flow_stats.tab_area'));
        $this->assign('action_link', array('text' => RC_Lang::get('stats::flow_stats.down_area'), 'href' => RC_Uri::url('stats/admin_flow_stats/area_stats_download')));
        $this->assign('ajax_loader', RC_App::apps_url('statics/images/ajax_loader.gif', __FILE__));
		
		if (empty($type)) {
			$today = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_strtotime('today'));
			$start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_strtotime('-3 days'));
			$end_date   = !empty($_GET['end_date']) ? $_GET['end_date'] : $today;
		} elseif ($type == 1) {
			$start_date = RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y')));
			$end_date 	= RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1);
		} elseif ($type == 2) {
			$start_date = RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-1, date('Y')));
			$end_date 	= RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'))-1);
		} elseif ($type == 3) {
			$start_date = RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-7, date('Y')));
			$end_date 	= RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1);
		} elseif ($type == 4) {
			$start_date = RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-30, date('Y')));
			$end_date 	= RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1);
		}
		$area_data = $this->get_area_data();
		$this->assign('area_data', $area_data);

		$this->assign('type', $type);
		$this->assign('start_date', $start_date);
		$this->assign('end_date', $end_date);
		
		$this->assign_lang();
		$this->display('area_stats.dwt');
	}
	
	/**
	 * 来源网站
	 */
	public function from_stats() {
		$this->admin_priv('flow_stats', ecjia::MSGTYPE_JSON);
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
		
        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(RC_Lang::get('stats::flow_stats.tab_from')));
		ecjia_screen::get_current_screen()->add_help_tab(array(
			'id'		=> 'overview',
			'title'		=> RC_Lang::get('stats::flow_stats.overview'),
			'content'	=> '<p>' . RC_Lang::get('stats::flow_stats.from_stats_help') . '</p>'
		));
		
		ecjia_screen::get_current_screen()->set_help_sidebar(
			'<p><strong>' . RC_Lang::get('stats::flow_stats.more_info') . '</strong></p>' .
			'<p>' . __('<a href="https://ecjia.com/wiki/帮助:ECJia智能后台:流量分析#.E6.9D.A5.E6.BA.90.E7.BD.91.E7.AB.99" target="_blank">'. RC_Lang::get('stats::flow_stats.about_from_stats') .'</a>') . '</p>'
		);
		
        $this->assign('ur_here', RC_Lang::get('stats::flow_stats.tab_from'));
        $this->assign('action_link', array('text' => RC_Lang::get('stats::flow_stats.down_from'), 'href' => RC_Uri::url('stats/admin_flow_stats/from_stats_download')));
        $this->assign('ajax_loader', RC_App::apps_url('statics/images/ajax_loader.gif', __FILE__));
        
		if (empty($type)) {
			$today = RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_strtotime('today'));
			$start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_strtotime('-3 days'));
			$end_date   = !empty($_GET['end_date']) ? $_GET['end_date'] : $today;
		} elseif ($type == 1) {
			$start_date = RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y')));
			$end_date 	= RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1);
		} elseif ($type == 2) {
			$start_date = RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-1, date('Y')));
			$end_date 	= RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'))-1);
		} elseif ($type == 3) {
			$start_date = RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-7, date('Y')));
			$end_date 	= RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1);
		} elseif ($type == 4) {
			$start_date = RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-30, date('Y')));
			$end_date 	= RC_Time::local_date('Y-m-d', RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1);
		}
		
		$from_data = $this->get_from_data();

		$from_type = !empty($_GET['from_type']) ? $_GET['from_type'] : '';
		$this->assign('from_data', $from_data);
		$this->assign('from_type', $from_type);
		$this->assign('type', $type);
		$this->assign('start_date', $start_date);
		$this->assign('end_date', $end_date);
		
		$this->assign_lang();
		$this->display('from_stats.dwt');
	}
	
	/**
	 * 按年份获取综合访问量图表数据
	 */
	public function get_general_chart_data() {
		$db_stats = RC_DB::table('stats');
		//按年查询
		$type 		= !empty($_GET['type']) 		? true 					: false;
		$start_year = !empty($_GET['start_year']) 	? $_GET['start_year'] 	: '';
		$end_year 	= !empty($_GET['end_year']) 	? $_GET['end_year'] 	: '';
		$arr 		= array($start_year, $end_year);

		$start_date_arr = $end_date_arr = $general_data = $arr1 = array();
		if (!$type) {
			for ($i = 0; $i < count($arr); $i++) {
				if (!empty($arr[$i])) {
					$start_year_arr[]	= RC_Time::local_mktime(0, 0, 0, 1, 1, $arr[$i]);
					$end_year_arr[]   	= RC_Time::local_mktime(23, 59, 59, 12, 31, $arr[$i]);
				}
			}
			foreach ($start_year_arr as $k => $v) {
// 				$where = "access_time >= '$start_year_arr[$k]' AND access_time <= '$end_year_arr[$k]+86400' ";
// 				$field = 'FLOOR((access_time - '.$start_year_arr[$k].') / (24 * 31 * 3600)) AS sn, access_time, COUNT(*) AS access_count';
// 				$general_data[] = $this->db_stats->stats_select($where, $field, 'sn');
				
				$db_stats->where('access_time', '>=', $start_year_arr[$k])->where('access_time', '<=', $end_year_arr[$k]+86400);
				$db_stats->select(RC_DB::raw('FLOOR((access_time - '.$start_year_arr[$k].') / (24 * 31 * 3600)) AS sn, access_time'), 
						RC_DB::raw('COUNT(*) AS access_count'))->groupby('sn')->get();
			}
			
			if (!empty($general_data)) {
				foreach ($general_data as $key => $val) {
					if (empty($val)){
						unset($general_data[$key]);
					}
					if (!empty($val)) {
						foreach ($val as $k => $v) {
							$year = RC_Time::local_date('Y', $v['access_time']);
							$month = intval(RC_Time::local_date('m', $v['access_time']));
							$arr1[$year][$month] = $v;
							unset($arr1[$year][$month]['sn']);
						}
					}
				}
			}
				
			for ($i = 1; $i <= 12; $i++) {
				$arr2[] = $i;
			}
			foreach ($arr1 as $k => $v) {
				foreach (array_unique($arr2) as $v1) {
					if (!array_key_exists($v1, $v)) {
						foreach ($v as $k1 => $v2) {
							$arr1[$k][$v1] = array('access_time' => 0, 'access_count' => 0);
						}
					}
				}
			}
				
			$chart_datas = json_encode($arr1);
			echo $chart_datas;
		}
	}
	
	public function get_general_chart_datas() {
		$db_stats = RC_DB::table('stats');
		//按月查询
		$type 		= !empty($_GET['type']) 		? true 					: false;
		$start_month= !empty($_GET['start_month'])	? $_GET['start_month'] 	: '';
		$end_month  = !empty($_GET['end_month']) 	? $_GET['end_month'] 	: '';
		$arr		= array($start_month, $end_month);
			
		$start_date_arr = $end_date_arr = $data = $arr1 = array();
		if ($type) {
			for ($i = 0; $i < count($arr); $i++) {
				if (!empty($arr[$i])) {
					$start_date_arr[]	= RC_Time::local_strtotime($arr[$i] . '-1');
					$day 				= date('t', strtotime($arr[$i]));
					$end_date_arr[]   	= RC_Time::local_strtotime($arr[$i] .'-'.$day)+28800;
				}
			}
			foreach ($start_date_arr as $k => $val) {
// 				$where = "access_time>= '$start_date_arr[$k]' AND access_time <= '$end_date_arr[$k]+86400'";
// 				$field = 'FLOOR((access_time - '.$start_date_arr[$k].') / (24 * 3600)) AS sn, access_time, COUNT(*) AS access_count';
// 				$data[] = $this->db_stats->stats_select($where, $field, 'sn');
				
				$db_stats->where('access_time', '>=', $start_date_arr[$k])->where('access_time', '<=', $end_date_arr[$k]+86400);
				$db_stats->select(RC_DB::raw('FLOOR((access_time - '.$start_date_arr[$k].') / (24 * 3600)) AS sn, access_time'),
						RC_DB::raw('COUNT(*) AS access_count'))->groupby('sn')->get();
			}
				
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					if (!$val) {
						unset($data[$key]);
					}
					if (!empty($val)) {
						foreach ($val as $k => $v) {
							$month = RC_Time::local_date('Y-m', $v['access_time']);
							$day = intval(RC_Time::local_date('d', $v['access_time']));
							$arr1[$month][$day] = $v;
							unset($arr1[$month][$day]['sn']);
						}
					}
				}
			}
				
			for ($i=1; $i<=31; $i++) {
				$arr2[] = $i;
			}
			foreach ($arr1 as $k => $v) {
				foreach (array_unique($arr2) as $v1) {
					if (!array_key_exists($v1, $v)) {
						foreach ($v as $k1 => $v2) {
							$arr1[$k][$v1] = array('access_time' => 0, 'access_count' => 0);
						}
					}
				}
			}
				
			$chart_datas = json_encode($arr1);
			echo $chart_datas;
		}
	}
	/**
	 * 获取地区分布图表数据
	 */
	public function get_area_chart_data() {
		$db_stats = RC_DB::table('stats');
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
		
		if ($type == 1) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 2) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'))-1;
		} elseif ($type == 3) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-7, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 4) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-30, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} else {
			$start_date = !empty($_GET['start_date']) ? RC_Time::local_strtotime($_GET['start_date']) : RC_Time::local_strtotime('-3 days');
			$end_date 	= !empty($_GET['end_date'])   ? RC_Time::local_strtotime($_GET['end_date'])   : RC_Time::local_strtotime('today');
		}
// 		$where = "access_time >= '$start_date' AND access_time <= '$end_date' AND area != '' ";
// 		$field = 'area, COUNT(*) AS access_count';
// 		$area_data = $this->db_stats->stats_select($where, $field, 'area', array('access_count' => 'DESC'), 15);
		
		$area_data = $db_stats->where('access_time', '>=', $start_date)->where('access_time', '<=', $end_date)->where('area', '!=', '')
		->select(RC_DB::raw('area, count(*) as access_count'))->groupby('area')->orderby('access_count', 'desc')->take(15)->get();
		
		$arr1 = array();
		if (!empty($area_data)) {
			foreach ($area_data as $key => $val) {
				if (isset($val['area'])) {
					$arr1[$val['area']] = '';
					$arr1[$val['area']] += $val['access_count'];
				}
			}
		}
		$area_datas = json_encode($arr1);
		echo $area_datas;
	}
	
	/**
	 * 获取来源网站图表数据
	 */
	public function get_from_chart_data() {
		$db_stats = RC_DB::table('stats');
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
		$from_type = !empty($_GET['from_type']) ? $_GET['from_type'] : 1;
		
		if ($type == 1) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 2) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'))-1;
		} elseif ($type == 3) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-7, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 4) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-30, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif (empty($type)) {
			$start_date = !empty($_GET['start_date']) ? RC_Time::local_strtotime($_GET['start_date']) : RC_Time::local_strtotime('-3 days');
			$end_date 	= !empty($_GET['end_date'])   ? RC_Time::local_strtotime($_GET['end_date'])   : RC_Time::local_strtotime('today');
		}
		
// 		$where = "access_time >= '$start_date' AND access_time <= '$end_date'";
		$db_stats->where('access_time', '>=', $start_date)->where('access_time', '<=', $end_date);
		//全部来源
		if ($from_type == 1) {
// 			$field = 'referer_domain, COUNT(*) AS access_count';
// 			$data = $this->db_stats->stats_select($where, $field, 'referer_domain', array('access_count' => 'DESC'));
			
			$data = $db_stats->select(RC_DB::raw('referer_domain, COUNT(*) AS access_count'))->groupby('referer_domain')->orderby('access_count', 'desc')->get();

			$arr1 = array();
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					if (empty($val['referer_domain']) || strpos($val['referer_domain'], 'localhost') || strpos($val['referer_domain'], '127.0.0.1')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.direct_access');
					} elseif (strpos($val['referer_domain'], 'baidu') || strpos($val['referer_domain'], 'google') || strpos($val['referer_domain'], 'haosou') || strpos($val['referer_domain'], 'sogou') || strpos($val['referer_domain'], RC_Lang::get('stats::flow_stats.bing'))) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.search_engine');
					} else {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.external_link');
					}
				}
				foreach ($data as $key => $val) {
					if (isset($data[$key]['referer_domain'])) {
						$arr1[$val['referer_domain']] = '';
						$arr1[$val['referer_domain']] += $val['access_count'];
					}
				}
			}
			
			if (!empty($arr1)) {
				$arr = $this->array_sort($arr1, 'access_count', SORT_DESC);
			} else {
				$arr = array();
			}
			$from_datas = json_encode($arr);
			echo $from_datas;
		//外部链接
		} elseif ($from_type == 2) {
// 			$where .= " AND referer_domain != '' AND referer_domain not like '%www.baidu.com%' AND referer_domain not like '%www.google.com%' AND referer_domain not like '%www.haosou.com%' AND referer_domain not like '%www.sogou.com%' AND referer_domain not like '%www.bing.com%' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%'";
// 			$field = 'referer_domain, COUNT(*) AS access_count';
// 			$data = $this->db_stats->stats_select($where, $field, 'referer_domain', array('access_count' => 'DESC'), 15);
			
			$data = $db_stats->where('referer_domain', '!=', '')
				->where('referer_domain', 'not like', '%www.baidu.com%')
				->where('referer_domain', 'not like', '%www.google.com%')
				->where('referer_domain', 'not like', '%www.haosou.com%')
				->where('referer_domain', 'not like', '%www.sogou.com%')
				->where('referer_domain', 'not like', '%www.bing.com%')
				->where('referer_domain', 'not like', '%localhost%')
				->where('referer_domain', 'not like', '%127.0.0.1%')
				->select(RC_DB::raw('referer_domain, COUNT(*) AS access_count'))
				->groupby('referer_domain')->orderby('access_count', 'desc')->take(15)->get();
			
			$arr1 = array();
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					if (isset($data[$key]['referer_domain'])) {
						$arr1[$val['referer_domain']] = '';
						$arr1[$val['referer_domain']] += $val['access_count'];
					}
				}
				arsort($arr1);
			}
			$from_datas = json_encode($arr1);
			echo $from_datas;
		//搜索引擎
		} elseif ($from_type == 3) {
// 			$where .= " AND referer_domain != '' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%' AND (referer_domain like '%www.baidu.com%' or referer_domain like '%www.google.com%' or referer_domain like '%www.haosou.com%' or referer_domain like '%www.sogou.com%' or referer_domain like '%www.bing.com%') ";
// 			$field = 'referer_domain, COUNT(*) AS access_count';
// 			$data = $this->db_stats->stats_select($where, $field, 'referer_domain', array('access_count' => 'DESC'), 15);
			
			$data = $db_stats->where('referer_domain', '!=', '')
				->where('referer_domain', 'not like', '%localhost%')
				->where('referer_domain', 'not like', '%127.0.0.1%')
				->where(function($query) {
					$query->where('referer_domain', 'like', '%www.baidu.com%')
					->where('referer_domain', 'like', '%www.google.com%')
					->where('referer_domain', 'like', '%www.haosou.com%')
					->where('referer_domain', 'like', '%www.sogou.com%')
					->where('referer_domain', 'like', '%www.bing.com%');
				})
				->select(RC_DB::raw('referer_domain, COUNT(*) AS access_count'))
				->groupby('referer_domain')->orderby('access_count', 'desc')->take(15)->get();
			
			$arr1 = array();
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					if (strpos($val['referer_domain'], 'baidu')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.baidu');
					} elseif (strpos($val['referer_domain'], 'google')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.google');
					} elseif (strpos($val['referer_domain'], 'haosou')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.haosou');
					} elseif (strpos($val['referer_domain'], 'sogou')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.sogou');
					} elseif (strpos($val['referer_domain'], RC_Lang::get('stats::flow_stats.bing')) || strpos($val['referer_domain'], RC_Lang::get('stats::flow_stats.youdao'))) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.other');
					}
				}
				foreach ($data as $key => $val) {
					$arr1[$val['referer_domain']] = '';
					if (isset($data[$key]['referer_domain'])) {
						$arr1[$val['referer_domain']] += $val['access_count'];
					} else {
						$arr1[$val['referer_domain']] = $val['access_count'];
					}
				}
			}
			
			$from_datas = json_encode($arr1);
			echo $from_datas;
		}
	}
	
	/**
	 * 综合访问量统计报表下载
	 */
	public function general_stats_download() {
		$this->admin_priv('flow_stats', ecjia::MSGTYPE_JSON);
		
		/*文件名*/
		$filename = mb_convert_encoding(RC_Lang::get('stats::flow_stats.general_stats'), "GBK", "UTF-8");
		
		header("Content-type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		
		//按年查询
		$start_year = !empty($_GET['start_year']) 	? $_GET['start_year'] 	: '';
		$end_year 	= !empty($_GET['end_year']) 	? $_GET['end_year'] 	: '';
		$arr 		= array($start_year, $end_year);
		$db_stats	= RC_DB::table('stats');
		
		$start_date_arr = $end_date_arr = $general_data = $arr1 = array();
		for ($i = 0; $i < count($arr); $i++) {
			if (!empty($arr[$i])) {
				$start_year_arr[]	= RC_Time::local_mktime(0, 0, 0, 1, 1, $arr[$i]);
				$end_year_arr[]   	= RC_Time::local_mktime(23, 59, 59, 12, 31, $arr[$i]);
			}
		}
		foreach ($start_year_arr as $k => $v) {
// 			$where = "access_time >= '$start_year_arr[$k]' AND access_time <= '$end_year_arr[$k]+86400' ";
// 			$field = 'FLOOR((access_time - '.$start_year_arr[$k].') / (24 * 31 * 3600)) AS sn, access_time, COUNT(*) AS access_count';
// 			$general_data[] = $this->db_stats->stats_select($where, $field, 'sn');
			
			$db_stats->where('access_time', '>=', $start_year_arr[$k])->where('access_time', '<=', $end_year_arr[$k]+86400);
			$db_stats->select(RC_DB::raw('FLOOR((access_time - '.$start_year_arr[$k].') / (24 * 31 * 3600)) AS sn, access_time'),
					RC_DB::raw('COUNT(*) AS access_count'))->groupby('sn')->get();
		}
		
		$data  = RC_Lang::get('stats::flow_stats.tab_general') . "\t\n";
		$data .= RC_Lang::get('stats::flow_stats.date') . "\t";
		$data .= RC_Lang::get('stats::flow_stats.access_count') . "\t\n";
		
		if (!empty($general_data)) {
			foreach ($general_data as $key => $val) {
				foreach ($val as $k => $v) {
					$data .= RC_Time::local_date('Y-m-d', $v['access_time']) . "\t";
					$data .= $v['access_count'] . "\t\n";
				}
			}
		}
		echo mb_convert_encoding($data."\t", "GBK", "UTF-8");
		exit;
	}
	
	/**
	 * 地区分布统计报表下载
	 */
	public function area_stats_download() {
		$this->admin_priv('flow_stats', ecjia::MSGTYPE_JSON);
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
		$db_stats = RC_DB::table('stats');
		
		$filename = mb_convert_encoding(RC_Lang::get('stats::flow_stats.area_stats'), "GBK", "UTF-8");
		header("Content-type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		
		/* 时间参数 */
		if ($type == 1) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 2) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'))-1;
		} elseif ($type == 3) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-7, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 4) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-30, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} else {
			$start_date = RC_Time::local_strtotime($_GET['start_date']);
			$end_date 	= RC_Time::local_strtotime($_GET['end_date']);
		}
		
// 		$where = "access_time >= '$start_date' AND access_time <= '$end_date' AND area != '' ";
// 		$field = 'area, COUNT(*) AS access_count';
// 		$area_data = $this->db_stats->stats_select($where, $field, 'area', array('access_count' => 'DESC'), 15);
		
		$area_data = $db_stats->where('access_time', '>=', $start_date)->where('access_time', '<=', $end_date)->where('area', '!=', '')
		->select(RC_DB::raw('area, count(*) as access_count'))->groupby('area')->orderby('access_count', 'desc')->take(15)->get();
		
		$arr1 = array();
		if (!empty($area_data)) {
			foreach ($area_data as $key => $val) {
				if (isset($val['area'])) {
					$arr1[$val['area']] = '';
					$arr1[$val['area']] += $val['access_count'];
				}
			}
		}
		
		$data = RC_Lang::get('stats::flow_stats.tab_area') . "\t\n";
		$data .= RC_Lang::get('stats::flow_stats.area') . "\t";
		$data .= RC_Lang::get('stats::flow_stats.access_count') . "\t\n";
		
		if (!empty($arr1)) {
			foreach ($arr1 as $key => $val) {
				$data .= $key . "\t";
				$data .= $val . "\t\n";
			}
		}
		echo mb_convert_encoding($data."\t", "GBK", "UTF-8");
		exit;
	}
	
	/**
	 * 来源网站统计报表下载
	 */
	public function from_stats_download () {
		$this->admin_priv('flow_stats', ecjia::MSGTYPE_JSON);
		
		/*文件名*/
		$filename = mb_convert_encoding(RC_Lang::get('stats::flow_stats.from_stats'), "GBK", "UTF-8");
		header("Content-type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
		$from_type = !empty($_GET['from_type']) ? $_GET['from_type'] : 1;
		$db_stats = RC_DB::table('stats');
		
		/*时间参数 */
		if ($type == 1) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 2) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'))-1;
		} elseif ($type == 3) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-7, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 4) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-30, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif (empty($type)) {
			$start_date = !empty($_GET['start_date']) ? RC_Time::local_strtotime($_GET['start_date']) : RC_Time::local_strtotime('-3 days');
			$end_date 	= !empty($_GET['end_date'])   ? RC_Time::local_strtotime($_GET['end_date'])   : RC_Time::local_strtotime('today');
		}
		
// 		$where = "access_time >= '$start_date' AND access_time <= '$end_date'";
		$db_stats->where('access_time', '>=', $start_date)->where('access_time', '<=', $end_date);
		
		$data = '';
		//全部来源
		if ($from_type == 1) {
// 			$field = 'referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count';
// 			$arr = $this->db_stats->stats_select($where, $field, 'referer_domain', array('access_count' => 'DESC'));
			
			$arr = $db_stats->select(RC_DB::raw('referer_domain, COUNT(*) AS access_count'), RC_DB::raw('SUM(visit_times) AS visit_count'))->groupby('referer_domain')->orderby('access_count', 'desc')->get();
			
			$count = '';
			if (!empty($arr)) {
				foreach ($arr as $key => $val) {
					if (empty($val['referer_domain']) || strpos($val['referer_domain'], 'localhost') || strpos($val['referer_domain'], '127.0.0.1')) {
						$arr[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.direct_access');
					} elseif (strpos($val['referer_domain'], 'baidu') || strpos($val['referer_domain'], 'google') || strpos($val['referer_domain'], 'haosou') || strpos($val['referer_domain'], 'sogou') || strpos($val['referer_domain'], RC_Lang::get('stats::flow_stats.bing'))) {
						$arr[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.search_engine');
					} else {
						$arr[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.external_link');
					}
					$count += $val['access_count'];
				}
				
				foreach ($arr as $key => $val) {
					if (!isset($arr1[$val['referer_domain']])) {
						$arr1[$val['referer_domain']] = '';
						$arr1[$val['referer_domain']] = $val;
					} else {
						$arr1[$val['referer_domain']]['access_count'] = '';
						$arr1[$val['referer_domain']]['visit_count'] = '';
						
						$arr1[$val['referer_domain']]['access_count'] 	+= $val['access_count'];
						$arr1[$val['referer_domain']]['visit_count']  	+= $val['visit_count'];
					}
					$arr1[$val['referer_domain']]['percent'] = (round($arr1[$val['referer_domain']]['access_count'] / $count,4))*100 .'%';
				}
			}
			
			$data .= RC_Lang::get('stats::flow_stats.tab_from') . "\t\n";
			$data .= RC_Lang::get('stats::flow_stats.from_type') . "\t";
			$data .= RC_Lang::get('stats::flow_stats.access_count') . "\t";
			$data .= RC_Lang::get('stats::flow_stats.pageviews_account') . "\t";
			$data .= RC_Lang::get('stats::flow_stats.visits') . "\t\n";

		//外部链接
		} elseif ($from_type == 2) {
// 			$where .= " AND referer_domain != '' AND referer_domain not like '%baidu%' AND referer_domain not like '%google%' AND referer_domain not like '%haosou%' AND referer_domain not like '%sogou%' AND referer_domain not like '%bing%' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%'";
// 			$field = 'referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count';
// 			$arr1 = $this->db_stats->stats_select($where, $field, 'referer_domain', array('access_count' => 'DESC'));
			
			$arr1 = $db_stats->where('referer_domain', '!=', '')
				->where('referer_domain', 'not like', '%www.baidu.com%')
				->where('referer_domain', 'not like', '%www.google.com%')
				->where('referer_domain', 'not like', '%www.haosou.com%')
				->where('referer_domain', 'not like', '%www.sogou.com%')
				->where('referer_domain', 'not like', '%www.bing.com%')
				->where('referer_domain', 'not like', '%localhost%')
				->where('referer_domain', 'not like', '%127.0.0.1%')
				->select(RC_DB::raw('referer_domain, COUNT(*) AS access_count'), RC_DB::raw('SUM(visit_times) AS visit_count'))
				->groupby('referer_domain')->orderby('access_count', 'desc')->get();
			
			$counts = '';
			if (!empty($arr1)) {
				foreach ($arr1 as $k => $v) {
					$counts += $v['access_count'];
				}
				foreach ($arr1 as $k => $v) {
					$arr1[$k]['percent'] = (round($v['access_count'] / $counts,4))*100 .'%';
				}
			}
			
			$data .= "\n" . RC_Lang::get('stats::flow_stats.tab_from') . "\t\n";
			$data .= RC_Lang::get('stats::flow_stats.tab_from') . "\t";
			$data .= RC_Lang::get('stats::flow_stats.access_count') . "\t";
			$data .= RC_Lang::get('stats::flow_stats.pageviews_account') . "\t";
			$data .= RC_Lang::get('stats::flow_stats.visits') . "\t\n";

		//搜索引擎
		} elseif ($from_type == 3) {
// 			$where .= " AND referer_domain != '' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%' AND (referer_domain like '%www.baidu.com%' or referer_domain like '%www.google.com%' or referer_domain like '%www.haosou.com%' or referer_domain like '%www.sogou.com%' or referer_domain like '%www.bing%') ";
// 			$field = 'referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count';
// 			$data = $this->db_stats->stats_select($where, $field, 'referer_domain', array('access_count' => 'DESC'));
			
			$data = $db_stats->where('referer_domain', '!=', '')
				->where('referer_domain', 'not like', '%localhost%')
				->where('referer_domain', 'not like', '%127.0.0.1%')
				->where(function($query) {
					$query->where('referer_domain', 'like', '%www.baidu.com%')
					->where('referer_domain', 'like', '%www.google.com%')
					->where('referer_domain', 'like', '%www.haosou.com%')
					->where('referer_domain', 'like', '%www.sogou.com%')
					->where('referer_domain', 'like', '%www.bing.com%');
				})
				->select(RC_DB::raw('referer_domain, COUNT(*) AS access_count'), RC_DB::table('SUM(visit_times) AS visit_count'))
				->groupby('referer_domain')->orderby('access_count', 'desc')->get();
				
			$arr1 = array();
			$count = '';
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					if (strpos($val['referer_domain'], 'baidu')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.baidu');
					} elseif (strpos($val['referer_domain'], 'google')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.google');
					} elseif (strpos($val['referer_domain'], 'haosou')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.haosou');
					} elseif (strpos($val['referer_domain'], 'sogou')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.sogou');
					} elseif (strpos($val['referer_domain'], RC_Lang::get('stats::flow_stats.bing')) || strpos($val['referer_domain'], RC_Lang::get('stats::flow_stats.youdao'))) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.other');
					}
					$count += $val['access_count'];
				}
					
				foreach ($data as $key => $val) {
					if (!isset($arr1[$val['referer_domain']])) {
						$arr1[$val['referer_domain']] = '';
						$arr1[$val['referer_domain']] = $val;
					} else {
						$arr1[$val['referer_domain']]['access_count'] = '';
						$arr1[$val['referer_domain']]['access_count'] += $val['access_count'];
					}
				}
			}
			
			foreach ($arr1 as $k => $v) {
				$arr1[$k]['percent'] = (round($v['access_count'] / $count,4))*100 .'%';
			}
			
			$data .= "\n" . RC_Lang::get('stats::flow_stats.tab_from') . "\t\n";
			$data .= RC_Lang::get('stats::flow_stats.search_engine') . "\t";
			$data .= RC_Lang::get('stats::flow_stats.access_count') . "\t";
			$data .= RC_Lang::get('stats::flow_stats.pageviews_account') . "\t";
			$data .= RC_Lang::get('stats::flow_stats.visits') . "\t\n";
		}
		
		if (!empty($arr1)) {
			foreach ($arr1 as $key => $val) {
				$data .= $val['referer_domain'] . "\t";
				$data .= $val['access_count'] . "\t";
				$data .= $val['percent'] . "\t";
				$data .= $val['visit_count'] . "\t\n";
			}
		};
		echo mb_convert_encoding($data."\t", "GBK", "UTF-8");
		exit;
	}
	
	/**
	 * 获取地区分布数据
	 * */
	private function get_area_data() {
		$db_stats = RC_DB::table('stats');
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
	
		if ($type == 1) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 2) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'))-1;
		} elseif ($type == 3) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-7, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 4) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-30, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} else {
			$start_date = !empty($_GET['start_date']) ? RC_Time::local_strtotime($_GET['start_date']) : RC_Time::local_strtotime('-3 days');
			$end_date 	= !empty($_GET['end_date'])   ? RC_Time::local_strtotime($_GET['end_date'])   : RC_Time::local_strtotime('today');
		}
// 		$where = "access_time >= '$start_date' AND access_time <= '$end_date'  AND area != '' ";
// 		$area_data = $db_stats->stats_select($where, 'area, COUNT(*) AS access_count', 'area', array('access_count' => 'DESC'));
		
		$db_stats->where('access_time', '>=', $start_date)->where('access_time', '<=', $end_date)->where('area', '!=', '');
		$area_data = $db_stats->select(RC_DB::raw('area, COUNT(*) AS access_count'))->groupby('area')->orderby('access_count', 'desc')->get();
		
		$count = count($area_data);
		$page = new ecjia_page($count, 20, 5);
	
// 		$area_datas = $db_stats->stats_select($where, 'area, COUNT(*) AS access_count', 'area', array('access_count' => 'DESC'), $page->limit());
		$area_datas = $db_stats->select(RC_DB::raw('area, COUNT(*) AS access_count'))->groupby('area')->orderby('access_count', 'desc')->take(20)->skip($page->start_id-1)->get();
		
		return array('item' => $area_datas, 'page' => $page->show(5), 'desc' => $page->page_desc(), 'current_page' => $page->current_page);
	}
	
	/**
	 * 按年份获取来源网站数据
	 */
	private function get_from_data() {
		$db_stats = RC_DB::table('stats');
		
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
		$from_type = !empty($_GET['from_type']) ? $_GET['from_type'] : 1;
	
		if ($type == 1) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 2) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d'), date('Y'))-1;
		} elseif ($type == 3) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-7, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 4) {
			$start_date = RC_Time::local_mktime(0, 0, 0, date('m'), date('d')-30, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0, date('m'), date('d')+1, date('Y'))-1;
		} elseif (empty($type)) {
			$start_date = !empty($_GET['start_date']) ? RC_Time::local_strtotime($_GET['start_date']) : RC_Time::local_strtotime('-3 days');
			$end_date 	= !empty($_GET['end_date'])   ? RC_Time::local_strtotime($_GET['end_date'])   : RC_Time::local_strtotime('today');
		}
	
// 		$where = "access_time >= '$start_date' AND access_time <= '$end_date'";
		$db_stats->where('access_time', '>=', $start_date)->where('access_time', '<=', $end_date);
		//全部来源
		if ($from_type == 1) {
// 			$from_datas = $db_stats->stats_select($where, 'referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count', 'referer_domain', array('access_count' => 'DESC'));
			$from_datas = $db_stats->select(RC_DB::raw('referer_domain, COUNT(*) AS access_count'), RC_DB::raw('SUM(visit_times) AS visit_count'))->groupby('referer_domain')->orderby('access_count', 'desc')->get();
			
			$count = '';
			if (!empty($from_datas)) {
				foreach ($from_datas as $key => $val) {
					if (empty($val['referer_domain']) || strpos($val['referer_domain'], 'localhost') || strpos($val['referer_domain'], '127.0.0.1')) {
						$from_datas[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.direct_access');
					} elseif (strpos($val['referer_domain'], 'baidu') || strpos($val['referer_domain'], 'google') || strpos($val['referer_domain'], 'haosou') || strpos($val['referer_domain'], 'sogou') || strpos($val['referer_domain'], RC_Lang::get('stats::flow_stats.bing'))) {
						$from_datas[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.search_engine');
					} else {
						$from_datas[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.external_link');
					}
					$count += $val['access_count'];
				}
			}
	
			$arr = array();
			$arr1 = array();
			if (!empty($from_datas)) {
				foreach ($from_datas as $key => $val) {
					if (!isset($arr1[$val['referer_domain']])) {
						$arr1[$val['referer_domain']] = $val;
					} else {
						$arr1[$val['referer_domain']]['access_count'] = '';
						$arr1[$val['referer_domain']]['visit_count'] = '';
						$arr1[$val['referer_domain']]['access_count'] 	+= $val['access_count'];
						$arr1[$val['referer_domain']]['visit_count']  	+= $val['visit_count'];
					}
					$arr1[$val['referer_domain']]['percent'] = (round($arr1[$val['referer_domain']]['access_count'] / $count,4))*100 .'%';
				}
				$arr = $this->array_sort($arr1, 'access_count', SORT_DESC);
			}
			return array('item' => $arr);
		} elseif ($from_type == 2) {
// 			$where .= " AND referer_domain != '' AND referer_domain not like '%baidu%' AND referer_domain not like '%google%' AND referer_domain not like '%haosou%' AND referer_domain not like '%sogou%' AND referer_domain not like '%bing%' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%'";
// 			$from_datas = $db_stats->stats_select($where, 'referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count', 'referer_domain', array('access_count' => 'DESC'));
			
			$from_datas = $db_stats->where('referer_domain', '!=', '')
				->where('referer_domain', 'not like', '%www.baidu.com%')
				->where('referer_domain', 'not like', '%www.google.com%')
				->where('referer_domain', 'not like', '%www.haosou.com%')
				->where('referer_domain', 'not like', '%www.sogou.com%')
				->where('referer_domain', 'not like', '%www.bing.com%')
				->where('referer_domain', 'not like', '%localhost%')
				->where('referer_domain', 'not like', '%127.0.0.1%')
				->select(RC_DB::raw('referer_domain, COUNT(*) AS access_count'), RC_DB::raw('SUM(visit_times) AS visit_count'))
				->groupby('referer_domain')->orderby('access_count', 'desc')->get();
			
			
			$count = '';
			if (!empty($from_datas)) {
				$counts = 0;
				foreach ($from_datas as $k => $v) {
					$counts += $v['access_count'];
				}
				$count = count($from_datas);
			}
	
			$page = new ecjia_page($count, 20, 5);
// 			$arr1 = $db_stats->stats_select($where, 'referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count', 'referer_domain', array('access_count' => 'DESC'), $page->limit());
			$arr1 = $db_stats->select(RC_DB::raw('referer_domain, COUNT(*) AS access_count'), RC_DB::raw('SUM(visit_times) AS visit_count'))->groupby('referer_domain')->orderby('access_count', 'desc')->take(20)->skip($page->start_id-1)->get();
			
			if (!empty($arr1)) {
				foreach ($arr1 as $k => $v) {
					$arr1[$k]['percent'] = (round($v['access_count'] / $counts,4))*100 .'%';
				}
			}
	
			return array('item' => $arr1, 'page' => $page->show(5), 'desc' => $page->page_desc(), 'current_page' => $page->current_page);
	
		} elseif ($from_type == 3) {
// 			$where .= " AND referer_domain != '' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%' AND (referer_domain like '%www.baidu.com%' or referer_domain like '%www.google.com%' or referer_domain like '%www.haosou.com%' or referer_domain like '%www.sogou.com%' or referer_domain like '%www.bing%') ";
// 			$data = $db_stats->stats_select($where, 'referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count', 'referer_domain', array('access_count' => 'DESC'), 15);
			
			$data = $db_stats->where('referer_domain', '!=', '')
				->where('referer_domain', 'not like', '%localhost%')
				->where('referer_domain', 'not like', '%127.0.0.1%')
				->where(function($query) {
					$query->where('referer_domain', 'like', '%www.baidu.com%')
					->where('referer_domain', 'like', '%www.google.com%')
					->where('referer_domain', 'like', '%www.haosou.com%')
					->where('referer_domain', 'like', '%www.sogou.com%')
					->where('referer_domain', 'like', '%www.bing.com%');
				})
				->select(RC_DB::raw('referer_domain, COUNT(*) AS access_count'), RC_DB::raw('SUM(visit_times) AS visit_count'))
				->groupby('referer_domain')->orderby('access_count', 'desc')->take(15)->get();
			
			$arr1 = array();
			if (!empty($data)) {
				$count = 0;
				foreach ($data as $key => $val) {
					if (strpos($val['referer_domain'], 'baidu')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.baidu');
					} elseif (strpos($val['referer_domain'], 'google')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.google');
					} elseif (strpos($val['referer_domain'], 'haosou')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.haosou');
					} elseif (strpos($val['referer_domain'], 'sogou')) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.sogou');
					} elseif (strpos($val['referer_domain'], RC_Lang::get('stats::flow_stats.bing')) || strpos($val['referer_domain'], RC_Lang::get('stats::flow_stats.youdao'))) {
						$data[$key]['referer_domain'] = RC_Lang::get('stats::flow_stats.other');
					}
					$count += $val['access_count'];
				}
	
				foreach ($data as $key => $val) {
					if (!isset($arr1[$val['referer_domain']])) {
						$arr1[$val['referer_domain']] = '';
						$arr1[$val['referer_domain']] = $val;
					} else {
						$arr1[$val['referer_domain']]['access_count'] = '';
						$arr1[$val['referer_domain']]['access_count'] += $val['access_count'];
					}
				}
			}
	
			foreach ($arr1 as $k => $v) {
				$arr1[$k]['percent'] = (round($v['access_count'] / $count,4))*100 .'%';
			}
			return array('item' => $arr1);
		}
	}
	
	/*数组排序*/
	private function array_sort($array, $on, $order=SORT_ASC) {
		$new_array = array();
		$sortable_array = array();
	
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
	
			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}
	
			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}
		return $new_array;
	}
}

// end