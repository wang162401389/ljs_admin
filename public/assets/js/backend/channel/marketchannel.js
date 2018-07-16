define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'channel/marketchannel/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: '',
                    dragsort_url: '',
                    table: 'AppUser',
                }
            });

            var table = $("#table");
            var tableOptions = {
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                escape: false,
                pk: 'userId',
                sortName: 'createTime',
                columns: [
                    [
                    	{field: 'userId', title: '用户ID', sortable: true},
                        {field: 'userPhone', title: '手机号', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userName', title: '真实姓名', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'createdTime', title: '注册时间', operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'createTime', title: '投资时间', operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'borrowName', title: '标的名称', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'investorCapital', title: '投资金额', operate: 'BETWEEN', sortable: true},
                        {field: 'deductibleMoney', title: '使用投资券金额', operate: 'BETWEEN', sortable: true},
                        {field: 'borrowDurationTxt', title: '投资期限', operate:false},
                        {field: 'payChannelType', title: '通道', formatter: Controller.api.formatter.type, searchList: {'1':'富友',"2":'新浪','3':'华兴'}},
                        {field: 'is_first_invest', title: '是否首次投资', formatter: Controller.api.formatter.isfirst, searchList: {'0':'否',"1":'是'}},
                    ]
                ],
                exportOptions: {
		            mso:{
		                // fileFormat:        'xlsx',
		                 //修复导出数字不显示为科学计数法
		            	onMsoNumberFormat: function (cell, row, col) {
		                   return !isNaN($(cell).text())?'\\@':'';
		            	}
		             }
                },
                search : false
            };
            // 初始化表格
            table.bootstrapTable(tableOptions);

            // 为表格绑定事件
            Table.api.bindevent(table);

            //绑定TAB事件
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                // var options = table.bootstrapTable(tableOptions);
                var typeStr = $(this).attr("href").replace('#','');
                var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    // params.filter = JSON.stringify({type: typeStr});
                    params.type = typeStr;

                    return params;
                };
                table.bootstrapTable('refresh', {});
                return false;

            });

            //必须默认触发shown.bs.tab事件
            // $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger("shown.bs.tab");

        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
	        formatter : {
	        	type : function (value, row, index) {
                    var typenameArr = {'1':'富友',"2":'新浪','3':'华兴'};
                    //渲染状态
                    var html = '<span class="text-primary">' + __(typenameArr[value]) + '</span>';
                    return html;
	        	},
	        	isfirst : function (value, row, index) {
	                var typenameArr = {'0':'否',"1":'是'};
	                //渲染状态
	                var html = '<span class="text-primary">' + __(typenameArr[value]) + '</span>';
	                return html;
	        	}
	        }
        }
    };
    return Controller;
});