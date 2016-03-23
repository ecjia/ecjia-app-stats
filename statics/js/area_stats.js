// JavaScript Document
;
(function(app, $) {
	app.area_stats = {
		init : function() {
			app.area_stats.searchForm();
		},
		searchForm : function () {
			$(".start_date,.end_date").datepicker({
				format: "yyyy-mm-dd"
			});
			$('.screen-btn').on('click', function(e) {
				e.preventDefault();
				$("#area_loading").css('display','block');
				var start_date      = $("input[name=start_date]").val();//开始时间
				var end_date      	= $("input[name=end_date]").val();	//结束时间
				var url				= $("form[name='searchForm']").attr('action'); //请求链接
				
				
				var start_time = (new Date(start_date.replace(/-/g,'/')).getTime())/1000;
				var end_time   = (new Date(end_date.replace(/-/g,'/')).getTime())/1000;

				if (start_date == '') {
		        	var data = {
							message : "查询的开始时间不能为空！",
							state : "error",
					};
					ecjia.admin.showmessage(data);
					return false;
		        } else if (end_date == '') {
		        	var data = {
							message : "查询的结束时间不能为空！",
							state : "error",
					};
					ecjia.admin.showmessage(data);
					return false;
		        } else if (start_date > end_date) {
		        	var data = {
							message : "查询的开始时间不能超于结束时间！",
							state : "error",
					};
					ecjia.admin.showmessage(data);
					return false;
		        } else if (end_time - start_time > 86400*90) {
		        	var data = {
							message : "非常抱歉，时间查询范围不能超出90天!",
							state : "error",
					};
					ecjia.admin.showmessage(data);
					return false;
		        }
		        
				if(start_date	== 'undefind')start_date='';
				if(end_date     == 'undefind')end_date='';
				if(url        	== 'undefind')url='';
				ecjia.pjax(url+'&start_date='+start_date+'&end_date='+end_date);
			});
		}
	};
})(ecjia.admin, jQuery);
// end
