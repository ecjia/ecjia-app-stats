<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->
<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.general_stats.init();
	{if !$type}
		ecjia.admin.chart.general_data();
	{else}
		ecjia.admin.chart.general_datas();
	{/if}
</script>
<!-- {/block} -->
<!-- {block name="main_content"} -->
<!--综合访问量-->
<div class="alert alert-info">
	<a class="close" data-dismiss="alert">×</a>
	<strong>{lang key='stats::flow_stats.tips'}</strong>{lang key='stats::flow_stats.general_tips'}
</div>
<div>
	<h3 class="heading">
		<!-- {if $ur_here}{$ur_here}{/if} -->
		<!-- {if !$type} -->
			<!-- {if $action_link} -->
				<a class="btn plus_or_reply" id="sticky_a" href='{$action_link.href}&start_year={$start_year}&end_year={$end_year}'><i class="fontello-icon-download"></i>{$action_link.text}</a>
	    	<!-- {/if} -->
	    <!-- {/if} -->
	</h3>
</div>
	
<div class="row-fluid edit-page">
	<div class="span12">
		<div class="tabbable">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab1" data-toggle="tab">{lang key='stats::flow_stats.tab_general'}</a></li>
				<li><a class="data-pjax" href='{url path="stats/admin_flow_stats/area_stats" args="type=4"}'>{lang key='stats::flow_stats.tab_area'}</a></li>
				<li><a class="data-pjax" href='{url path="stats/admin_flow_stats/from_stats" args="type=4"}'>{lang key='stats::flow_stats.tab_from'}</a></li>
			</ul>
		</div>
	</div>
</div>
	
<!--start综合访问量  -->
<div class="edit-page">
	<div class="row-fluid">
		<div class="choose_list f_r">
			<form action="{RC_Uri::url('stats/admin_flow_stats/general_stats')}" method="post" name="searchForm">
				<span>{lang key='stats::flow_stats.select_year'}</span>
				    <input class="w110 year_date" type="text" name="start_year" value="{$start_year}" />
				    <span class="f_r">{lang key='stats::flow_stats.or'}</span>
					<input class="w110 year_date" type="text" name="end_year" value="{$end_year}" />
			    <input type="submit" name="submit" value="{lang key='stats::flow_stats.inquire'}" class="btn screen-btn" />
	 		</form>
	 	</div>
	</div>

	<div class="row-fluid">
 		<div class="choose_list f_r" >
		    <form action="{RC_Uri::url('stats/admin_flow_stats/general_stats')}" method="post" name="selectForm">
		    	<span>{lang key='stats::flow_stats.select_month'}</span>
				    <input class="w110 month_date" type="text" name="start_month" value="{$start_month}" />
				    <span class="f_r">{lang key='stats::flow_stats.or'}</span>
				    <input class="w110 month_date" type="text" name="end_month" value="{$end_month}" />
				    <input type="hidden" name="type" value="1" />
			    <input type="submit" name="submit" value="{lang key='stats::flow_stats.inquire'}" class="btn screen-btn1" />
		  	</form>
	  	</div>
	</div>
  	
	<!-- 按年查询 -->
	<!-- {if !$type} -->
	<div class="general_data">
		<div id="general_data" data-url='{RC_Uri::url("stats/admin_flow_stats/get_general_chart_data", "start_year={$start_year}&end_year={$end_year}")}'>
			<div id="general_loading"><img src="{$ajax_loader}" alt="{lang key='stats::flow_stats.loading'}" /></div>
		</div>
	</div>
	
	<!-- 按年月查询 -->
	<!-- {else} -->
	<div class="general_datas">
		<div id="general_datas" data-url='{RC_Uri::url("stats/admin_flow_stats/get_general_chart_datas", "&type={$type}&start_month={$start_month}&end_month={$end_month}")}'>
			<div id="general_loading"><img src="{$ajax_loader}" alt="{lang key='stats::flow_stats.loading'}" /></div>
		</div>
	</div>
	<!-- {/if} -->
</div>
<!--end综合访问量  -->
<!-- {/block} -->