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
					<li><a class="data-pjax" href='{url path="stats/admin_flow_stats/general_stats"}'>{t}综合访问量{/t}</a></li>
					<li><a class="data-pjax" href='{url path="stats/admin_flow_stats/area_stats" args="type=4"}'>{t}地区分布{/t}</a></li>
					<li class="active"><a href="#tab3" data-toggle="tab">{t}来源网站{/t}</a></li>
				</ul>
			</div>
		</div>
	</div>
	
	<div>
		<div class="row-fluid">
			<div class="choose_list f_l">
				<ul class="nav nav-pills">
					<li class="{if $type eq 1}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/from_stats" args="type=1{if $from_type}&from_type={$from_type}{/if}"}'>今天 <span class="badge badge-info"></span></a></li>
					<li class="{if $type eq 2}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/from_stats" args="type=2{if $from_type}&from_type={$from_type}{/if}"}'>昨天 <span class="badge badge-info"></span></a></li>
					<li class="{if $type eq 3}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/from_stats" args="type=3{if $from_type}&from_type={$from_type}{/if}"}'>最近7天 <span class="badge badge-info"></span></a></li>
					<li class="{if $type eq 4}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/from_stats" args="type=4{if $from_type}&from_type={$from_type}{/if}"}'>最近30天<span class="badge badge-info"></span></a></li>
				</ul>
			</div>
			
			<div class="choose_list f_r">
				<form class="f_r" action="{RC_Uri::url('stats/admin_flow_stats/from_stats')}" method="post" name="searchForm">
					<select name="from_type" class="w130">
						<option value="1"	{if $from_type == "1"}	selected{/if}>全部来源</option>
						<option value="2"	{if $from_type == "2"}	selected{/if}>外部链接</option>
						<option value="3"	{if $from_type == "3"}	selected{/if}>搜索引擎</option>
					</select>
					<span class="f_r">开始时间：</span>
					<input class="w110 start_date" type="text" name="start_date" value="{$start_date}" placeholder="开始时间"/>
					<span class="f_r">结束时间：</span>
					<input class="w110 end_date" type="text" name="end_date" value="{$end_date}" placeholder="结束时间"/>
					<input type="submit" name="submit" value="查询" class="btn screen-btn" />
		 		</form>
		 	</div>
		</div>
	
		<!-- {if $from_data.current_page lt 2} -->
		
		<div class="row-fluid mt_10 m_b20">
			<h3 class="text-center">
				 来源网站TOP15图表
			</h3>
		</div>
		
		<div class="row-fluid">
        	<div class="from_data">
				<div id="from_data" data-url='{RC_Uri::url("stats/admin_flow_stats/get_from_chart_data","start_date={$start_date}&end_date={$end_date}{if $type}&type={$type}{/if}{if $from_type}&from_type={$from_type}{/if}")}'>
					<div id="from_loading"><img src="{RC_Uri::admin_url('statics/images/ajax_loader.gif')}" alt="{t}正在加载中……{/t}" /></div>
				</div>
			</div>
		</div>
		
		<div>
			<h3 class="heading">{t}来源网站列表{/t}</h3>
		</div>
		<!-- {/if} -->
		
		<div class="row-fluid">
			<table class="table table-striped" id="smpl_tbl">
				<thead>
					<tr>
						<th>{t}来源类型{/t}</th>
						<th class="w100">{t}浏览量{/t}</th>
						<th class="w100">{t}浏览量占比{/t}</th>
						<th class="w100">{t}访问次数{/t}</th>
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
			    	<tr><td class="dataTables_empty" colspan="10">没有找到任何记录</td></tr>
			  		<!-- {/foreach} -->
				</tbody>
			</table>
			<!-- {$from_data.page} -->
		</div>
	</div>
<!-- {/block} -->