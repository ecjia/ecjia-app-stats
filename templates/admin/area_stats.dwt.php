<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->
<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.area_stats.init();
	ecjia.admin.chart.area();
</script>
<!-- {/block} -->
<!-- {block name="main_content"} -->
	<!--地区分布-->
	<div>
		<h3 class="heading">
			<!-- {if $ur_here}{$ur_here}{/if} -->
			<!-- {if $action_link} -->
				<a class="btn plus_or_reply" id="sticky_a" href='{$action_link.href}{if $type}&type={$type}{/if}&start_date={$start_date}&end_date={$end_date}'><i class="fontello-icon-download"></i>{$action_link.text}</a>
	    	<!-- {/if} -->
		</h3>
	</div>
	
	
	
	<div class="row-fluid edit-page">
		<div class="span12">
			<div class="tabbable">
				<ul class="nav nav-tabs">
					<li><a class="data-pjax" href='{url path="stats/admin_flow_stats/general_stats"}'>{t}综合访问量{/t}</a></li>
					<li class="active"><a href="#tab2" data-toggle="tab">{t}地区分布{/t}</a></li>
					<li><a class="data-pjax" href='{url path="stats/admin_flow_stats/from_stats" args="type=4"}'>{t}来源网站{/t}</a></li>
				</ul>
			</div>
		</div>
	</div>
	
	<div class="edit-page">
		<div class="row-fluid">
			<div class="choose_list f_l">
				<ul class="nav nav-pills">
					<li class="{if $type eq 1}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/area_stats" args="type=1"}'>今天 <span class="badge badge-info"></span></a></li>
					<li class="{if $type eq 2}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/area_stats" args="type=2"}'>昨天 <span class="badge badge-info"></span></a></li>
					<li class="{if $type eq 3}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/area_stats" args="type=3"}'>最近7天 <span class="badge badge-info"></span></a></li>
					<li class="{if $type eq 4}active{/if}"><a class="data-pjax" href='{url path="stats/admin_flow_stats/area_stats" args="type=4"}'>最近30天<span class="badge badge-info"></span></a></li>
				</ul>
			</div>
			
			<div class="choose_list f_r">
				<form class="f_r" action="{RC_Uri::url('stats/admin_flow_stats/area_stats')}" method="post" name="searchForm">
					<span class="f_r">开始时间：</span>
					<input class="w110 start_date" type="text" name="start_date" value="{$start_date}" placeholder="开始时间"/>
					<span class="f_r">结束时间：</span>
					<input class="w110 end_date" type="text" name="end_date" value="{$end_date}" placeholder="结束时间"/>
					<input type="submit" name="submit" value="查询" class="btn screen-btn" />
		 		</form>
		 	</div>
	 	</div>
	 	
	 	
		<!-- {if $area_data.current_page lt 2} -->
		<!--start地区分布  -->
		<div class="row-fluid mt_10 m_b20">
			<h3 class="text-center">
				 地区分布TOP15图表
			</h3>
		</div>
		
		<div class="row-fluid">
          	<div class="area_data">
				<div id="area_data" data-url='{RC_Uri::url("stats/admin_flow_stats/get_area_chart_data","start_date={$start_date}&end_date={$end_date}&type={$type}")}'>
					<div id="area_loading"><img src="{RC_Uri::admin_url('statics/images/ajax_loader.gif')}" alt="{t}正在加载中……{/t}" /></div>
				</div>
			</div>
		</div>
		
		<div>
			<h3 class="heading">{t}地区分布列表{/t}</h3>
		</div>
		<!-- {/if} -->
		<div class="row-fluid">
			<table class="table table-striped" id="smpl_tbl">
				<thead>
					<tr>
						<th>{t}地区{/t}</th>
						<th class="w150">{t}数量{/t}</th>
					</tr>
				</thead>
				<tbody>
					<!-- {foreach from=$area_data.item item=list} -->
					<tr>
						<td>{$list.area}</td>
						<td>{$list.access_count}</td>
					</tr>
					<!-- {foreachelse} -->
			    	<tr><td class="dataTables_empty" colspan="2">没有找到任何记录</td></tr>
			  		<!-- {/foreach} -->
				</tbody>
			</table>
			<!-- {$area_data.page} -->
		</div>
		<!--end地区分布  -->
	</div>
<!-- {/block} -->