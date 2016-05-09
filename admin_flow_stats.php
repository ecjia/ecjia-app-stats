<?php
/**
 * 综合流量统计
*/
defined('IN_ECJIA') or exit('No permission resources.');

class admin_flow_stats extends ecjia_admin {
	private $db_stats;
	public function __construct() {
		parent::__construct();
		
        $this->db_stats = RC_Loader::load_app_model('stats_model', 'stats');
        RC_Loader::load_app_func('global', 'stats');
        RC_Lang::load('statistic');
        RC_Lang::load('flow_stats');
        
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
        
        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('流量分析'), RC_Uri::url('stats/admin_flow_stats/general_stats')));
	}
	
	/**
	 * 综合访问量
	 */
	public function general_stats() {
		$this->admin_priv('flow_stats', ecjia::MSGTYPE_JSON);
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('综合访问量')));
		ecjia_screen::get_current_screen()->add_help_tab(array(
			'id'		=> 'overview',
			'title'		=> __('概述'),
			'content'	=>
			'<p>' . __('欢迎访问ECJia智能后台综合访问量页面，系统上所有的综合访问量信息都会显示在此页面上。') . '</p>'
		));
		
		ecjia_screen::get_current_screen()->set_help_sidebar(
			'<p><strong>' . __('更多信息:') . '</strong></p>' .
			'<p>' . __('<a href="https://ecjia.com/wiki/帮助:ECJia智能后台:流量分析#.E7.BB.BC.E5.90.88.E8.AE.BF.E9.97.AE.E9.87.8F" target="_blank">关于综合访问量帮助文档</a>') . '</p>'
		);
		
		$this->assign('ur_here', __('综合访问量'));
		$this->assign('action_link', array('text' => '综合访问量报表下载', 'href' => RC_Uri::url('stats/admin_flow_stats/general_stats_download')));
		
		$is_multi = empty($_GET['is_multi']) ? false : true;
		$start_year = !empty($_GET['start_year']) ? $_GET['start_year'] : '';
		
		/*按年查询时间参数*/
		if (!empty($start_year)) {
			$filter	= explode('.', $start_year);
			$arr 	= array_filter($filter);
			for ($i = 0; $i < count($arr); $i++) {
				if (!empty($arr[$i])) {
					$start_year_arr[] = RC_Time::local_strtotime($arr[$i] . '-1');
				}
			}
		} else {
			$start_year_arr[] = RC_Time::local_strtotime(RC_Time::local_date('Y').'-1-1');
			$start_year = RC_Time::local_date('Y');
		}
		for ($i = 0; $i < 2; $i++) {
			if (isset($start_year_arr[$i])) {
				$start_year_arr[$i] = RC_Time::local_date('Y', $start_year_arr[$i]);
			} else {
				$start_year_arr[$i] = '';
			}
		}
		$this->assign('start_year_arr', $start_year_arr);
		
		$year_month = !empty($_GET['year_month']) ? $_GET['year_month'] : '';
		/*按月查询时间参数*/
		if (!empty($year_month)) {
			$filter	= explode('.', $year_month);
			$arr 	= array_filter($filter);
			for ($i = 0; $i < count($arr); $i++) {
				if (!empty($arr[$i])) {
					$start_date_arr[] = RC_Time::local_strtotime($arr[$i] . '-1');
				}
			}
		} else {
			$start_date_arr[] = RC_Time::local_strtotime(RC_Time::local_date('Y-m') . '-1');
		}
		
		for ($i = 0; $i < 2; $i++) {
			if (isset($start_date_arr[$i])) {
				$start_date_arr[$i] = RC_Time::local_date('Y-m', $start_date_arr[$i]);
			} else {
				$start_date_arr[$i] = '';
			}
		}
		
		$this->assign('start_date_arr', $start_date_arr);
		$this->assign('is_multi', $is_multi);
		$this->assign('start_year', $start_year);
		$this->assign('year_month', $year_month);
		
		$this->assign_lang();
		$this->display('general_stats.dwt');
	}
	
	/**
	 * 地区分布
	 */	
	public function area_stats() {
		$this->admin_priv('flow_stats', ecjia::MSGTYPE_JSON);
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('地区分布')));
		ecjia_screen::get_current_screen()->add_help_tab(array(
			'id'		=> 'overview',
			'title'		=> __('概述'),
			'content'	=>
			'<p>' . __('欢迎访问ECJia智能后台地区分布页面，系统上所有的地区分布信息都会显示在此页面上。') . '</p>'
		));
		ecjia_screen::get_current_screen()->set_help_sidebar(
			'<p><strong>' . __('更多信息:') . '</strong></p>' .
			'<p>' . __('<a href="https://ecjia.com/wiki/帮助:ECJia智能后台:流量分析#.E5.9C.B0.E5.8C.BA.E5.88.86.E5.B8.83" target="_blank">关于地区分布帮助文档</a>') . '</p>'
		);
		
		$this->assign('ur_here', __('地区分布'));
		$this->assign('action_link', array('text' => '地区分布报表下载', 'href' => RC_Uri::url('stats/admin_flow_stats/area_stats_download')));
		
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
		
		ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('来源网站')));
		ecjia_screen::get_current_screen()->add_help_tab(array(
			'id'		=> 'overview',
			'title'		=> __('概述'),
			'content'	=>
			'<p>' . __('欢迎访问ECJia智能后台来源网站页面，系统上所有的来源网站信息都会显示在此页面上。') . '</p>'
		));
		
		ecjia_screen::get_current_screen()->set_help_sidebar(
			'<p><strong>' . __('更多信息:') . '</strong></p>' .
			'<p>' . __('<a href="https://ecjia.com/wiki/帮助:ECJia智能后台:流量分析#.E6.9D.A5.E6.BA.90.E7.BD.91.E7.AB.99" target="_blank">关于来源网站帮助文档</a>') . '</p>'
		);
		
		$this->assign('ur_here', __('来源网站'));
		$this->assign('action_link', array('text' => '来源网站报表下载', 'href' => RC_Uri::url('stats/admin_flow_stats/from_stats_download')));
	
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
		$is_multi = empty($_GET['is_multi']) ? false : true;
		$start_year = !empty($_GET['start_year']) ? $_GET['start_year'] : '';
		
		if (!$is_multi) {
			/*时间参数*/
			if (!empty($start_year)) {
				$filter	= explode('.', $start_year);
				$arr 	= array_filter($filter);
				
				for ($i = 0; $i < count($arr); $i++) {
					if (!empty($arr[$i])) {
						$start_year_arr[]	= RC_Time::local_mktime(0, 0, 0, 1, 1, $arr[$i]);
						$end_year_arr[]   	= RC_Time::local_mktime(23, 59, 59, 12, 31, $arr[$i]);
					}
				}
				
				if (!empty($start_year_arr)) {
					foreach ($start_year_arr as $k => $v) {
						$where = "access_time >= '$start_year_arr[$k]' AND access_time <= '$end_year_arr[$k]+86400' ";
						$general_data[] = $this->db_stats->field('FLOOR((access_time - '.$start_year_arr[$k].') / (24 * 31 * 3600)) AS sn, access_time, COUNT(*) AS access_count')->where($where)->group('sn')->select();
					}
				}
				
				if (!empty($general_data)) {
					foreach ($general_data as $key=>$val) {
						if (empty($val)){
							unset($general_data[$key]);
						}
						if (!empty($val)) {
							foreach ($val as $k=> $v) {
								unset($general_data[$key][$k]['sn']);
								$general_data[$key][$k]['access_time'] = RC_Time::local_date('Y-m', $v['access_time']);
							}
						}
					}
				}
				
				$arr1 = array();
				if (!empty($general_data)) {
					foreach ($general_data as $k=>$v) {
						foreach ($v as $k1=>$v1) {
							$year = RC_Time::local_date('Y', RC_Time::local_strtotime($v1['access_time']));
							$month = intval(RC_Time::local_date('m', RC_Time::local_strtotime($v1['access_time'])));
							$arr1[$year][$month] = $v1;
						}
					}
				}
				
				for ($i = 1; $i <= 12; $i++) {
					$arr2[] = $i;
				}
				
				foreach ($arr1 as $k=>$v) {
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
	}
	
	/**
	 * 按年月获取综合访问量图表数据
	 */
	public function get_general_chart_datas () {
		$year_month = !empty($_GET['year_month']) ? $_GET['year_month'] : '';
		
		if (!empty($year_month)) {
			$filter	= explode('.', $year_month);
			$arr 	= array_filter($filter);
			
			for ($i = 0; $i < count($arr); $i++) {
				if (!empty($arr[$i])) {
					$start_date_arr[]	= RC_Time::local_strtotime($arr[$i] . '-1');
					$day 				= date('t', strtotime($arr[$i]));
					$end_date_arr[]   	= RC_Time::local_strtotime($arr[$i] .'-'.$day)+28800;
				}
			}
			
			foreach ($start_date_arr as $k=>$val) {
				$where = "access_time>= '$start_date_arr[$k]' AND access_time <= '$end_date_arr[$k]+86400'";
				$data[] = $this->db_stats->field('FLOOR((access_time - '.$start_date_arr[$k].') / (24 * 3600)) AS sn, access_time, COUNT(*) AS access_count')->where($where)->group('sn')->select();
			}
			
			$arr1 = array();
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					if (!$val) {
						unset($data[$key]);
					}
					if (!empty($val)) {
						foreach ($val as $k=> $v) {
							unset($data[$key][$k]['sn']);
							$data[$key][$k]['access_time'] = RC_Time::local_date(ecjia::config('date_format'), $v['access_time']);
						}
					}
				}
				foreach ($data as $k=>$v) {
					foreach ($v as $k1=>$v1) {
						$month = RC_Time::local_date('Y-m', RC_Time::local_strtotime($v1['access_time']));
						$day = intval(RC_Time::local_date('d', RC_Time::local_strtotime($v1['access_time'])));
						$arr1[$month][$day] = $v1;
					}
				}
			}
			
			for ($i=1; $i<=31; $i++) {
				$arr2[] = $i;
			}
			
			foreach ($arr1 as $k=>$v) {
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
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
		
		if ($type == 1) {
			$start_date = RC_Time::local_mktime(0, 0, 0,date('m'), date('d'), date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0,date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 2) {
			$start_date = RC_Time::local_mktime(0, 0, 0,date('m'), date('d')-1, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0,date('m'), date('d'), date('Y'))-1;
		} elseif ($type == 3) {
			$start_date = RC_Time::local_mktime(0, 0, 0,date('m'), date('d')-7, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0,date('m'), date('d')+1, date('Y'))-1;
		} elseif ($type == 4) {
			$start_date = RC_Time::local_mktime(0, 0, 0,date('m'), date('d')-30, date('Y'));
			$end_date 	= RC_Time::local_mktime(0, 0, 0,date('m'), date('d')+1, date('Y'))-1;
		} else {
			$start_date = !empty($_GET['start_date']) ? RC_Time::local_strtotime($_GET['start_date']) : RC_Time::local_strtotime('-3 days');
			$end_date 	= !empty($_GET['end_date'])   ? RC_Time::local_strtotime($_GET['end_date'])   : RC_Time::local_strtotime('today');
		}
		$where = "access_time >= '$start_date' AND access_time <= '$end_date' AND area != '' ";
		$area_data = $this->db_stats->field('area,COUNT(*) AS access_count')->where($where)->group('area')->order(array('access_count' => 'DESC'))->limit(15)->select();
		
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
		
		$where = "access_time >= '$start_date' AND access_time <= '$end_date'";
		//全部来源
		if ($from_type == 1) {
			$data = $this->db_stats->field('referer_domain, COUNT(*) AS access_count')->where($where)->group('referer_domain')->order(array('access_count' => 'DESC'))->select();
			
			$arr1 = array();
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					if (empty($val['referer_domain']) || strpos($val['referer_domain'], 'localhost') || strpos($val['referer_domain'], '127.0.0.1')) {
						$data[$key]['referer_domain'] = '直接访问';
					} elseif (strpos($val['referer_domain'], 'baidu') || strpos($val['referer_domain'], 'google') || strpos($val['referer_domain'], 'haosou') || strpos($val['referer_domain'], 'sogou') || strpos($val['referer_domain'], 'bing')) {
						$data[$key]['referer_domain'] = '搜索引擎';
					} else {
						$data[$key]['referer_domain'] = '外部链接';
					}
				}
				foreach ($data as $key =>$val) {
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
			$where .= " AND referer_domain != '' AND referer_domain not like '%www.baidu.com%' AND referer_domain not like '%www.google.com%' AND referer_domain not like '%www.haosou.com%' AND referer_domain not like '%www.sogou.com%' AND referer_domain not like '%www.bing%' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%'";
			$data = $this->db_stats->field('referer_domain, COUNT(*) AS access_count')->where($where)->group('referer_domain')->order(array('access_count' => 'DESC'))->limit(15)->select();
			
			$arr1 = array();
			if (!empty($data)) {
				foreach ($data as $key =>$val) {
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
			$where .= " AND referer_domain != '' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%' AND (referer_domain like '%www.baidu.com%' or referer_domain like '%www.google.com%' or referer_domain like '%www.haosou.com%' or referer_domain like '%www.sogou.com%' or referer_domain like '%www.bing%') ";
			$data = $this->db_stats->field('referer_domain, COUNT(*) AS access_count')->where($where)->group('referer_domain')->order(array('access_count' => 'DESC'))->limit(15)->select();
			
			$arr1 = array();
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					if (strpos($val['referer_domain'], 'baidu')) {
						$data[$key]['referer_domain'] = '百度';
					} elseif (strpos($val['referer_domain'], 'google')) {
						$data[$key]['referer_domain'] = '谷歌';
					} elseif (strpos($val['referer_domain'], 'haosou')) {
						$data[$key]['referer_domain'] = '好搜';
					} elseif (strpos($val['referer_domain'], 'sogou')) {
						$data[$key]['referer_domain'] = '搜狗';
					} elseif (strpos($val['referer_domain'], 'bing') || strpos($val['referer_domain'], '有道')) {
						$data[$key]['referer_domain'] = '其他';
					}
				}
				foreach ($data as $key =>$val) {
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
		$filename = mb_convert_encoding(RC_Lang::lang('general_statement'), "GBK", "UTF-8");
		
		header("Content-type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		
		/* 时间参数 */
		if (!empty($_GET['start_year'])) {
			$filter	= explode('.', $_GET['start_year']);
			$arr 	= array_filter($filter);
			
			for ($i = 0; $i < count($arr); $i++) {
				if (!empty($arr[$i])) {
					$start_year_arr[]	= RC_Time::local_mktime(0, 0, 0, 1, 1, $arr[$i]);
					$end_year_arr[]   	= RC_Time::local_mktime(23, 59, 59, 12, 31, $arr[$i]);
				}
			}
			
			foreach ($start_year_arr as $k => $v) {
				$where = "access_time >= '$start_year_arr[$k]' AND access_time <= '$end_year_arr[$k]' ";
				$general_datas[] = $this->db_stats->field('FLOOR((access_time - '.$start_year_arr[$k].') / (24 * 31 * 3600)) AS sn, access_time, COUNT(*) AS access_count')->where($where)->group('sn')->select();
			}
			
			$arr1 = array();
			if (!empty($general_datas)) {
				foreach ($general_datas as $key=>$val) {
					if (empty($val)) {
						unset($general_datas[$key]);
					}
					foreach ($val as $k=> $v) {
						unset($general_datas[$key][$k]['sn']);
						$general_datas[$key][$k]['access_time'] = RC_Time::local_date('Y-m', $v['access_time']);
					}
				}
					
				foreach ($general_datas as $k => $v) {
					foreach ($v as $k1 => $v1) {
						$arr1[RC_Time::local_date('Y', RC_Time::local_strtotime($v1['access_time']))][intval(RC_Time::local_date('m', RC_Time::local_strtotime($v1['access_time'])))] = $v1;
					}
				}
			}
		}
		
		$data  = RC_Lang::lang('general_stats') . "\t\n";
		$data .= RC_Lang::lang('date') . "\t";
		$data .= "\t";
		$data .= RC_Lang::lang('access_count') . "\t\n";
		
		if (!empty($arr1)) {
			foreach ($arr1 as $key=>$val) {
				foreach ($val as $k=>$v) {
					$data .= $key . "\t";
					$data .= $v['access_time'] . "\t";
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
		
		$filename = mb_convert_encoding(RC_Lang::lang('area_statement'), "GBK", "UTF-8");
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
			$start_date = !empty($_GET['start_date']) ? RC_Time::local_strtotime($_GET['start_date']) : '';
			$end_date 	= !empty($_GET['end_date']) ? RC_Time::local_strtotime($_GET['end_date']) : '';
		}
		
		$where = "access_time >= '$start_date' AND access_time <= '$end_date' AND area != '' ";
		$area_data = $this->db_stats->field('area,COUNT(*) AS access_count')->where($where)->group('area')->order(array('access_count' => 'DESC'))->limit(15)->select();
		
		$arr1 = array();
		if (!empty($area_data)) {
			foreach ($area_data as $key=>$val) {
				if (isset($val['area'])) {
					$arr1[$val['area']] = '';
					$arr1[$val['area']] += $val['access_count'];
				}
			}
		}
		
		$data .= RC_Lang::lang('area_stats') . "\t\n";
		$data .= RC_Lang::lang('area') . "\t";
		$data .= RC_Lang::lang('access_count') . "\t\n";

		if (!empty($arr1)) {
			foreach ($arr1 as $key=>$val) {
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
		$filename = mb_convert_encoding(RC_Lang::lang('from_statement'), "GBK", "UTF-8");
		header("Content-type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		
		$type = !empty($_GET['type']) ? $_GET['type'] : '';
		$from_type = !empty($_GET['from_type']) ? $_GET['from_type'] : 1;
		
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
		
		$where = "access_time >= '$start_date' AND access_time <= '$end_date'";
		$data = '';
		//全部来源
		if ($from_type == 1) {
			$arr = $this->db_stats->field('referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count')->where($where)->group('referer_domain')->order(array('access_count' => 'DESC'))->select();
			
			$count = '';
			if (!empty($arr)) {
				foreach ($arr as $key => $val) {
					if (empty($val['referer_domain']) || strpos($val['referer_domain'], 'localhost') || strpos($val['referer_domain'], '127.0.0.1')) {
						$arr[$key]['referer_domain'] = '直接访问';
					} elseif (strpos($val['referer_domain'], 'baidu') || strpos($val['referer_domain'], 'google') || strpos($val['referer_domain'], 'haosou') || strpos($val['referer_domain'], 'sogou') || strpos($val['referer_domain'], 'bing')) {
						$arr[$key]['referer_domain'] = '搜索引擎';
					} else {
						$arr[$key]['referer_domain'] = '外部链接';
					}
					$count += $val['access_count'];
				}
				
				foreach ($arr as $key =>$val) {
					if (!isset($arr1[$val['referer_domain']])) {
						$arr1[$val['referer_domain']] = '';
						$arr1[$val['referer_domain']] = $val;
					} else {
						$arr1[$val['referer_domain']]['access_count'] = '';
						$arr1[$val['referer_domain']]['visit_count'] = '';
						
						$arr1[$val['referer_domain']]['access_count'] += $val['access_count'];
						$arr1[$val['referer_domain']]['visit_count'] += $val['visit_count'];
					}
					$arr1[$val['referer_domain']]['percent'] = (round($arr1[$val['referer_domain']]['access_count'] / $count, 4))*100 .'%';
				}
			}
			
			$data .= RC_Lang::lang('from_stats') . "\t\n";
			$data .= '来源类型' . "\t";
			$data .= RC_Lang::lang('access_count') . "\t";
			$data .= '浏览量占比' . "\t";
			$data .= '访问次数' . "\t\n";

		//外部链接
		} elseif ($from_type == 2) {
			$where .= " AND referer_domain != '' AND referer_domain not like '%baidu%' AND referer_domain not like '%google%' AND referer_domain not like '%haosou%' AND referer_domain not like '%sogou%' AND referer_domain not like '%bing%' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%'";
			$arr1 = $this->db_stats->field('referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count')->where($where)->group('referer_domain')->order(array('access_count' => 'DESC'))->select();
			
			$counts = '';
			if (!empty($arr1)) {
				foreach ($arr1 as $k => $v) {
					$counts += $v['access_count'];
				}
				foreach ($arr1 as $k => $v) {
					$arr1[$k]['percent'] = (round($v['access_count'] / $counts, 4))*100 .'%';
				}
			}
			
			$data .= "\n" . RC_Lang::lang('from_stats') . "\t\n";
			$data .= '来源网站' . "\t";
			$data .= RC_Lang::lang('access_count') . "\t";
			$data .= '浏览量占比' . "\t";
			$data .= '访问次数' . "\t\n";

		//搜索引擎
		} elseif ($from_type == 3) {
			$where .= " AND referer_domain != '' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%' AND (referer_domain like '%www.baidu.com%' or referer_domain like '%www.google.com%' or referer_domain like '%www.haosou.com%' or referer_domain like '%www.sogou.com%' or referer_domain like '%www.bing%') ";
			$data = $this->db_stats->field('referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count')->where($where)->group('referer_domain')->order(array('access_count' => 'DESC'))->select();
				
			$arr1 = array();
			$count = '';
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					if (strpos($val['referer_domain'], 'baidu')) {
						$data[$key]['referer_domain'] = '百度';
					} elseif (strpos($val['referer_domain'], 'google')) {
						$data[$key]['referer_domain'] = '谷歌';
					} elseif (strpos($val['referer_domain'], 'haosou')) {
						$data[$key]['referer_domain'] = '好搜';
					} elseif (strpos($val['referer_domain'], 'sogou')) {
						$data[$key]['referer_domain'] = '搜狗';
					} elseif (strpos($val['referer_domain'], 'bing') || strpos($val['referer_domain'], '有道')) {
						$data[$key]['referer_domain'] = '其他';
					}
					$count += $val['access_count'];
				}
					
				foreach ($data as $key =>$val) {
					if (!isset($arr1[$val['referer_domain']])) {
						$arr1[$val['referer_domain']] = '';
						$arr1[$val['referer_domain']] = $val;
					} else {
						$arr1[$val['referer_domain']]['access_count'] = '';
						$arr1[$val['referer_domain']]['access_count'] += $val['access_count'];
					}
				}
			}
			
			foreach ($arr1 as $k=>$v) {
				$arr1[$k]['percent'] = (round($v['access_count'] / $count, 4))*100 .'%';
			}
			
			$data .= "\n" . RC_Lang::lang('from_stats') . "\t\n";
			$data .= '搜索引擎' . "\t";
			$data .= RC_Lang::lang('access_count') . "\t";
			$data .= '浏览量占比' . "\t";
			$data .= '访问次数' . "\t\n";
		}
		
		if (!empty($arr1)) {
			foreach ($arr1 as $key=>$val) {
				$data .= $val['referer_domain'] . "\t";
				$data .= $val['access_count'] . "\t";
				$data .= $val['percent'] . "\t";
				$data .= $val['visit_count'] . "\t\n";
			}
		};
		echo mb_convert_encoding($data."\t", "GBK", "UTF-8");
		exit;
	}
	
	/*数组排序*/
	public function array_sort($array, $on, $order=SORT_ASC) {
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
	
	/**
	 * 获取地区分布数据
	 * */
	private function get_area_data() {
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
		$where = "access_time >= '$start_date' AND access_time <= '$end_date'  AND area != '' ";
	
		$area_data = $this->db_stats->field('area,COUNT(*) AS access_count')->where($where)->group('area')->order(array('access_count' => 'DESC'))->select();
		$count = count($area_data);
		$page = new ecjia_page($count, 20, 5);
	
		$area_datas = $this->db_stats->field('area, COUNT(*) AS access_count')->where($where)->group('area')->order(array('access_count' => 'DESC'))->limit($page->limit())->select();
	
		$row = array('item' => $area_datas, 'page' => $page->show(5), 'desc' => $page->page_desc(),'current_page' => $page->current_page);
		$options = array();
		return $row;
	}
	
	/**
	 * 按年份获取来源网站数据
	 */
	private function get_from_data() {
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
	
		$where = "access_time >= '$start_date' AND access_time <= '$end_date'";
		//全部来源
		if ($from_type == 1) {
			$from_datas = $this->db_stats->field('referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count')->where($where)->group('referer_domain')->order(array('access_count' => 'DESC'))->select();
				
			$count = '';
			if (!empty($from_datas)) {
				foreach ($from_datas as $key => $val) {
					if (empty($val['referer_domain']) || strpos($val['referer_domain'], 'localhost') || strpos($val['referer_domain'], '127.0.0.1')) {
						$from_datas[$key]['referer_domain'] = '直接访问';
					} elseif (strpos($val['referer_domain'], 'baidu') || strpos($val['referer_domain'], 'google') || strpos($val['referer_domain'], 'haosou') || strpos($val['referer_domain'], 'sogou') || strpos($val['referer_domain'], 'bing')) {
						$from_datas[$key]['referer_domain'] = '搜索引擎';
					} else {
						$from_datas[$key]['referer_domain'] = '外部链接';
					}
					$count += $val['access_count'];
				}	
			}
				
			$arr = array();
			$arr1 = array();
			if (!empty($from_datas)) {
				foreach ($from_datas as $key =>$val) {
					if (!isset($arr1[$val['referer_domain']])) {
						$arr1[$val['referer_domain']] = $val;
					} else {
						$arr1[$val['referer_domain']]['access_count'] = '';
						$arr1[$val['referer_domain']]['visit_count'] = '';
						$arr1[$val['referer_domain']]['access_count'] += $val['access_count'];
						$arr1[$val['referer_domain']]['visit_count'] += $val['visit_count'];
					}
					$arr1[$val['referer_domain']]['percent'] = (round($arr1[$val['referer_domain']]['access_count'] / $count, 4))*100 .'%';
				}
				$arr = $this->array_sort($arr1, 'access_count', SORT_DESC);
			}
			return array('item' => $arr);
		} elseif ($from_type == 2) {
			$where .= " AND referer_domain != '' AND referer_domain not like '%baidu%' AND referer_domain not like '%google%' AND referer_domain not like '%haosou%' AND referer_domain not like '%sogou%' AND referer_domain not like '%bing%' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%'";
			$from_datas = $this->db_stats->field('referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count')->where($where)->group('referer_domain')->order(array('access_count' => 'DESC'))->select();
				
			$count = '';
			if (!empty($from_datas)) {
				foreach ($from_datas as $k => $v) {
					$counts += $v['access_count'];
				}
				$count = count($from_datas);
			}
			
			$page = new ecjia_page($count, 20, 5);
			$arr1 = $this->db_stats->field('referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count')->where($where)->group('referer_domain')->order(array('access_count' => 'DESC'))->limit($page->limit())->select();
				
			if (!empty($arr1)) {
				foreach ($arr1 as $k => $v) {
					$arr1[$k]['percent'] = (round($v['access_count'] / $counts, 4))*100 .'%';
				}
			}
			
			return array('item' => $arr1, 'page' => $page->show(5), 'desc' => $page->page_desc(), 'current_page' => $page->current_page);
				
		} elseif ($from_type == 3) {
			$where .= " AND referer_domain != '' AND referer_domain not like '%localhost%' AND referer_domain not like '%127.0.0.1%' AND (referer_domain like '%www.baidu.com%' or referer_domain like '%www.google.com%' or referer_domain like '%www.haosou.com%' or referer_domain like '%www.sogou.com%' or referer_domain like '%www.bing%') ";
			$data = $this->db_stats->field('referer_domain, COUNT(*) AS access_count, SUM(visit_times) AS visit_count')->where($where)->group('referer_domain')->order(array('access_count' => 'DESC'))->limit(15)->select();
			
			$arr1 = array();
			if (!empty($data)) {
				foreach ($data as $key => $val) {
					if (strpos($val['referer_domain'], 'baidu')) {
						$data[$key]['referer_domain'] = '百度';
					} elseif (strpos($val['referer_domain'], 'google')) {
						$data[$key]['referer_domain'] = '谷歌';
					} elseif (strpos($val['referer_domain'], 'haosou')) {
						$data[$key]['referer_domain'] = '好搜';
					} elseif (strpos($val['referer_domain'], 'sogou')) {
						$data[$key]['referer_domain'] = '搜狗';
					} elseif (strpos($val['referer_domain'], 'bing') || strpos($val['referer_domain'], '有道')) {
						$data[$key]['referer_domain'] = '其他';
					}
					$count += $val['access_count'];
				}
					
				foreach ($data as $key =>$val) {
					if (!isset($arr1[$val['referer_domain']])) {
						$arr1[$val['referer_domain']] = '';
						$arr1[$val['referer_domain']] = $val;
					} else {
						$arr1[$val['referer_domain']]['access_count'] = '';
						$arr1[$val['referer_domain']]['access_count'] += $val['access_count'];
					}
				}
			}
			
			foreach ($arr1 as $k=>$v) {
				$arr1[$k]['percent'] = (round($v['access_count'] / $count, 4))*100 .'%';
			}
			return array('item' => $arr1);
		}
	}
}

// end