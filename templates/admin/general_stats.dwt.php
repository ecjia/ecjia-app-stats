<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->
<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.general_stats.init();
	{if $is_multi eq ''}
		ecjia.admin.chart.general_data();
	{else if $is_multi eq 1}
		ecjia.admin.chart.general_datas();
	{/if}
</script>
<!-- {/block} -->
<!-- {block name="main_content"} -->
	<!--综合访问量-->
	<div class="alert alert-info">
		<a class="close" data-dismiss="alert">×</a>
		<strong>注：</strong>{t}综合访问量图表默认显示当年每月的访问量{/t}
	</div>
	<div>
		<h3 class="heading">
			<!-- {if $ur_here}{$ur_here}{/if} -->
			<!-- {if !$is_multi} -->
				<!-- {if $action_link} -->
					<a class="btn plus_or_reply" id="sticky_a" href='{$action_link.href}&start_year={$start_year}'><i class="fontello-icon-download"></i>{$action_link.text}</a>
		    	<!-- {/if} -->
		    <!-- {/if} -->
		</h3>
	</div>
	
	<div class="row-fluid edit-page">
		<div class="span12">
			<div class="tabbable">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1" data-toggle="tab">{t}综合访问量{/t}</a></li>
					<li><a class="data-pjax" href='{url path="stats/admin_flow_stats/area_stats" args="type=4"}'>{t}地区分布{/t}</a></li>
					<li><a class="data-pjax" href='{url path="stats/admin_flow_stats/from_stats" args="type=4"}'>{t}来源网站{/t}</a></li>
				</ul>
			</div>
		</div>
	</div>
	
	<!--start综合访问量  -->
	<div class="edit-page">
		<div class="row-fluid">
			<div class="choose_list f_r">
				<form action="{RC_Uri::url('stats/admin_flow_stats/general_stats')}" method="post" name="searchForm">
					<span>按年份查询：</span>
				    <!--{foreach from=$start_year_arr item=sta_year key=key}-->
					    <input type="text" name="start_year" value="{$sta_year}" class="start_year w110"/>
					    <!-- {if $key < 1} --><span class="f_r">或</span><!-- {/if} -->
				    <!--{/foreach}-->
				    <input type="submit" name="submit" value="查询" class="btn screen-btn" />
		 		</form>
		 	</div>
		</div>
	
		<div class="row-fluid">
	 		<div class="choose_list f_r" >
			    <form action="{RC_Uri::url('stats/admin_flow_stats/general_stats')}" method="post" name="selectForm">
			    	<span>按月份查询：</span>
				    <!--{foreach from=$start_date_arr item=sta key=k}-->
					    <input type="text" name="year_month" value="{$sta}" class="year_month w110"/>
					    <!-- {if $k < 1} --><span class="f_r">或</span><!-- {/if} -->
				    <!--{/foreach}-->
				    <input type="hidden" name="is_multi" value="1" />
				    <input type="submit" name="submit" value="查询" class="btn screen-btn1" />
			  	</form>
		  	</div>
		</div>
	  	
		<!-- 按年查询 -->
		<!-- {if $is_multi eq ''} -->
		<div class="general_data">
			<div id="general_data" data-url='{RC_Uri::url("stats/admin_flow_stats/get_general_chart_data","is_multi={$is_multi}&start_year={$start_year}")}'>
				<div id="general_loading"><img src="{RC_Uri::admin_url('statics/images/ajax_loader.gif')}" alt="{t}正在加载中……{/t}" /></div>
			</div>
		</div>
		
		<!-- 按年月查询 -->
		<!-- {elseif $is_multi eq 1} -->
		<div class="general_datas">
			<div id="general_datas" data-url='{RC_Uri::url("stats/admin_flow_stats/get_general_chart_datas","is_multi={$is_multi}&year_month={$year_month}")}'>
				<div id="general_loading"><img src="{RC_Uri::admin_url('statics/images/ajax_loader.gif')}" alt="{t}正在加载中……{/t}" /></div>
			</div>
		</div>
		<!-- {/if} -->
	</div>
	<!--end综合访问量  -->
<!-- {/block} -->