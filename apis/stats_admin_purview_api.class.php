<?php
defined('IN_ECJIA') or exit('No permission resources.');
/**
 * 后台权限API
 * @author wutifang
 *
 */
class stats_admin_purview_api extends Component_Event_Api {
    
    public function call(&$options) {
        $purviews = array(
            array('action_name' => __('流量分析'),'action_code' => 'flow_stats','relevance' => ''),
        	array('action_name' => __('搜索引擎'),'action_code' => 'searchengine_stats','relevance' => ''),
        	array('action_name' => __('搜索关键字'),'action_code' => 'keywords_stats','relevance' => '')
        );
        
        return $purviews;
    }
}

// end