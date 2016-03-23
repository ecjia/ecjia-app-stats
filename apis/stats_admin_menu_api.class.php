<?php
defined('IN_ECJIA') or exit('No permission resources.');
/**
 * 后台菜单API
 * @author wutifang
 *
 */
class stats_admin_menu_api extends Component_Event_Api {
	
	public function call(&$options) {

		$menus = ecjia_admin::make_admin_menu('13_stats',__('报表统计'), '', 13);
		
		$submenus = array(
			ecjia_admin::make_admin_menu('01_flow_stats',__('流量分析'),RC_Uri::url('stats/admin_flow_stats/general_stats'), 1)->add_purview('flow_stats'),
			ecjia_admin::make_admin_menu('02_searchengine_stats',__('搜索引擎'),RC_Uri::url('stats/admin_searchengine_stats/init'), 2)->add_purview('searchengine_stats'),
			ecjia_admin::make_admin_menu('03_keywords_stats',__('搜索关键字'),RC_Uri::url('stats/admin_keywords_stats/init'), 3)->add_purview('keywords_stats'),
		);
        $menus->add_submenu($submenus);
		
        $menus = RC_Hook::apply_filters('stats_admin_menu_api', $menus);
		
        if ($menus->has_submenus()) {
            return $menus;
        }
        return false;
	}
}

// end