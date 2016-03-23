<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->
<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.keywords.init();
</script>
<!-- {/block} -->
<!-- {block name="main_content"} -->
	<!--搜索引擎-->
	<div>
		<h3 class="heading">
			<!-- {if $ur_here}{$ur_here}{/if} -->
			<!-- {if $action_link} --><a class="btn plus_or_reply"  id="sticky_a" href="{$action_link.href}&start_date={$start_date}&end_date={$end_date}&filter={$filter}"><i class="fontello-icon-download"></i>{t}{$action_link.text}{/t}</a><!-- {/if} -->
		</h3>
	</div>
	
	<div class="row-fluid">
		<form action="{$search_action}" method="post" name="theForm">
			<div class="row-fluid">
				<div class="choose_list f_r">
				<span class="f_r">过滤结果：</span>
				<!-- {foreach from=$keywords key=sename item=val} -->
					<div class="f_l">
						<input type="checkbox" name="filter" value="{$sename}" {if $val}checked{/if} >
					</div>
					<span class="l_h25">{$sename}</span>
				<!-- {/foreach} -->
				</div>
			</div>
			<div class="row-fluid">
				<div class="choose_list f_r">
					<span class="f_r">开始时间：</span>
					<input class="start_date f_l w110" name="start_date" type="text" placeholder="开始时间" value="{$start_date}">
					<span class="f_r">结束时间：</span>
					<input class="end_date f_l w110" name="end_date" type="text" placeholder="结束时间" value="{$end_date}">
					<input class="btn screen-btn" type="submit" value="搜索">
				</div>
			</div>
		</form>
	</div>
	
	<div class="row-fluid">
		<table class="table table-striped" id="smpl_tbl">
			<thead>
				<tr>
					<th>{t}关键词{/t}</th>
					<th class="w150">{t}搜索引擎{/t}</th>
					<th class="w120">{t}搜索次数{/t}</th>
					<th class="w100">{t}日期{/t}</th>
				</tr>
			</thead>
			<tbody>
				<!-- {foreach from=$keywords_data.item item=list} -->
				<tr>
					<td>{$list.keyword}</td>
					<td>{$list.searchengine}</td>
					<td>{$list.count}</td>
					<td>{$list.date}</td>
				</tr>
				<!-- {foreachelse} -->
		    	<tr><td class="dataTables_empty" colspan="4">没有找到任何记录</td></tr>
		  		<!-- {/foreach} -->
			</tbody>
		</table>
		<!-- {$keywords_data.page} -->
	</div>
	
<!-- {/block} -->