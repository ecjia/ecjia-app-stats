<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->
<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.searchengine.init();
	var type = "{$type}";
</script>
<!-- {/block} -->
<!-- {block name="main_content"} -->
	<div class="alert alert-info">
		<a class="close" data-dismiss="alert">×</a>
		<strong>温馨提示：</strong>{t}搜索引擎统计主要统计每日搜索引擎蜘蛛抓取页面的次数{/t}
	</div>
	
	<div>
		<h3 class="heading">
			<!-- {if $ur_here}{$ur_here}{/if} -->
			<!-- {if $action_link} -->
			<a class="btn plus_or_reply" id="sticky_a" href='{$action_link.href}&type={$type}{if $month}&month={$month}{/if}'><i class="fontello-icon-download"></i>{$action_link.text}</a>
		    <!-- {/if} -->
		</h3>
	</div>
	
	<div class="row-fluid edit-page">
		<div class="span12">
			<div class="tabbable">
				<ul class="nav nav-tabs">
					<li {if $type eq 2}class="active"{/if}><a class="data-pjax" href='{url path="stats/admin_searchengine_stats/init" args="type=2"}'>{t}今天{/t}</a></li>
					<li {if $type eq 1}class="active"{/if}><a class="data-pjax" href='{url path="stats/admin_searchengine_stats/init" args="type=1"}'>{t}昨天{/t}</a></li>
					<li {if $type eq 3}class="active"{/if}><a class="data-pjax" href='{url path="stats/admin_searchengine_stats/init" args="type=3"}'>{t}本周{/t}</a></li>
					<li {if $type eq 4}class="active"{/if}><a class="data-pjax" href='{url path="stats/admin_searchengine_stats/init" args="type=4"}'>{t}本月{/t}</a></li>
				</ul>
			</div>
		</div>
	</div>
	
	<div class="edit-page">
		<!-- {if $type eq 4} -->
		<div class="row-fluid">
			<div class="choost_list f_r" data-url="{$search_action}">
				<select name="month" class="w100">
					<!-- {foreach from=$month_list item=list} -->
					<option value="{$list}" {if $list == $month}selected{/if}>{t}{$list}月{/t}</option>
					<!-- {/foreach} -->
				</select>
			</div>
		</div>
		<!-- {/if} -->
		
		<!-- 昨天 -->
		<!-- {if $type eq 1} -->
		<div class="general_datas">
			<div id="general_datas" data-url='{RC_Uri::url("stats/admin_searchengine_stats/get_chart_data","type=1")}'>
				<div id="general_loading"><img src="{RC_Uri::admin_url('statics/images/ajax_loader.gif')}" alt="{t}正在加载中……{/t}" /></div>
			</div>
		</div>
		
		<!-- 今天 -->
		<!-- {elseif $type eq 2} -->
		<div class="general_datas">
			<div id="general_datas" data-url='{RC_Uri::url("stats/admin_searchengine_stats/get_chart_data","type=2")}'>
				<div id="general_loading"><img src="{RC_Uri::admin_url('statics/images/ajax_loader.gif')}" alt="{t}正在加载中……{/t}" /></div>
			</div>
		</div>
		
		<!-- 本周 -->
		<!-- {elseif $type eq 3} -->
		<div class="general_datas">
			<div id="general_datas" data-url='{RC_Uri::url("stats/admin_searchengine_stats/get_chart_data","type=3")}'>
				<div id="general_loading"><img src="{RC_Uri::admin_url('statics/images/ajax_loader.gif')}" alt="{t}正在加载中……{/t}" /></div>
			</div>
		</div>
		
		<!-- 本月 -->
		<!-- {elseif $type eq 4} -->
		<div class="general_datas">
			<div id="general_datas" data-url='{RC_Uri::url("stats/admin_searchengine_stats/get_chart_data","type=4{if $month}&month={$month}{/if}")}'>
				<div id="general_loading"><img src="{RC_Uri::admin_url('statics/images/ajax_loader.gif')}" alt="{t}正在加载中……{/t}" /></div>
			</div>
		</div>
		<!-- {/if} -->
	</div>
<!-- {/block} -->