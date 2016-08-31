<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->
<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.from_stats.init();
	ecjia.admin.chart.from();
</script>
<!-- {/block} -->
<!-- {block name="main_content"} -->	
<!--来源网站-->
<div>
	<h3 class="heading">
		<!-- {if $ur_here}{$ur_here}{/if} -->
		<!-- {if $action_link} -->
			<a class="btn plus_or_reply" id="sticky_a" href='{$action_link.href}&type={$type}&from_type={$from_type}&start_date={$start_date}&end_date={$end_date}'><i class="fontello-icon-download"></i>{$action_link.text}</a>
    	<!-- {/if} -->
	</h3>
</div>

<div class="row-fluid edit-page">
	<div class="span12">
		<div class="tabbable">
			<ul class="nav nav-tabs">
				<li><a class="data-pjax" href='{url path="stats/admin_flow_stats/general_stats"}'>{lang key='stats::flow_stats.tab_general'}</a></li>
				<li><a class="data-pjax" href='{url path="stats/admin_flow_stats/area_stats" args="type=4"}'>{lang key='stats::flow_stats.tab_area'}</a></li>
				<li class="active"><a href="#tab3" data-toggle="tab">{lang key='stats::flow_stats.tab_from'}</a></li>
			</ul>
		</div>
	</div>
	
	<div class="choose_list f_l">
		<ul class="nav nav-pills">
			<li class="{if $type eq 1}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/from_stats" args="type=1{if $from_type}&from_type={$from_type}{/if}"}'>{lang key='stats::flow_stats.tody'}<span class="badge badge-info"></span></a></li>
			<li class="{if $type eq 2}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/from_stats" args="type=2{if $from_type}&from_type={$from_type}{/if}"}'>{lang key='stats::flow_stats.yesterday'} <span class="badge badge-info"></span></a></li>
			<li class="{if $type eq 3}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/from_stats" args="type=3{if $from_type}&from_type={$from_type}{/if}"}'>{lang key='stats::flow_stats.last_7'} <span class="badge badge-info"></span></a></li>
			<li class="{if $type eq 4}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/from_stats" args="type=4{if $from_type}&from_type={$from_type}{/if}"}'>{lang key='stats::flow_stats.last_30'}<span class="badge badge-info"></span></a></li>
		</ul>
	</div>
	
	<div class="choose_list f_r">
		<form action="{RC_Uri::url('stats/admin_flow_stats/from_stats')}" method="post" name="searchForm">
			<div class="f_l">
				<select name="from_type" class="w130">
					<option value="1"	{if $from_type == "1"}	selected{/if}>{lang key='stats::flow_stats.all_sources'}</option>
					<option value="2"	{if $from_type == "2"}	selected{/if}>{lang key='stats::flow_stats.external_link'}</option>
					<option value="3"	{if $from_type == "3"}	selected{/if}>{lang key='stats::flow_stats.search_engine'}</option>
				</select>
			</div>
			<span class="f_r">{lang key='stats::flow_stats.start_date_two'}</span>
			<input class="w110 start_date" type="text" name="start_date" value="{$start_date}" placeholder="{lang key='stats::flow_stats.start_date'}"/>
			<span class="f_r">{lang key='stats::flow_stats.end_date_two'}</span>
			<input class="w110 end_date" type="text" name="end_date" value="{$end_date}" placeholder="{lang key='stats::flow_stats.end_date'}"/>
			<input type="submit" name="submit" value="{lang key='stats::flow_stats.inquire'}" class="btn screen-btn" />
 		</form>
 	</div>
</div>
	
<!-- {if $from_data.current_page lt 2} -->
<div class="row-fluid mt_10 m_b20">
	<h3 class="text-center">
		 {lang key='stats::flow_stats.from_top15'}
	</h3>
</div>
		
<div class="row-fluid">
	<div class="from_data">
		<div id="from_data" data-url='{RC_Uri::url("stats/admin_flow_stats/get_from_chart_data","start_date={$start_date}&end_date={$end_date}{if $type}&type={$type}{/if}{if $from_type}&from_type={$from_type}{/if}")}'>
			<div id="from_loading"><img src="{$ajax_loader}" alt="{lang key='stats::flow_stats.loading'}" /></div>
		</div>
	</div>
</div>
		
<div>
	<h3 class="heading">{lang key='stats::flow_stats.from_web_list'}</h3>
</div>
<!-- {/if} -->
		
<div class="row-fluid">
	<table class="table table-striped" id="smpl_tbl">
		<thead>
			<tr>
				<th>{lang key='stats::flow_stats.from_type'}</th>
				<th class="w100">{lang key='stats::flow_stats.pageviews'}</th>
				<th class="w150">{lang key='stats::flow_stats.pageviews_account'}</th>
				<th class="w100">{lang key='stats::flow_stats.quantity'}</th>
			</tr>
		</thead>
		<tbody>
			<!-- {foreach from=$from_data.item item=list} -->
			<tr>
				<td>{$list.referer_domain}</td>
				<td>{$list.access_count}</td>
				<td>{$list.percent}</td>
				<td>{$list.visit_count}</td>
			</tr>
			<!-- {foreachelse} -->
	    	<tr><td class="dataTables_empty" colspan="10">{lang key='system::system.no_records'}</td></tr>
	  		<!-- {/foreach} -->
		</tbody>
	</table>
	<!-- {$from_data.page} -->
</div>
<!-- {/block} -->