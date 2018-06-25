define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'channel/investlist/index',
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
                pk: 'ir.userId',
                sortName: 'ir.createTime',
                commonSearch: false,
                columns: [
                    [
                        {field: 'ir.userId', title: __('Userid'), sortable: true},
                        {field: 'u.userPhone', title: __('Userphone'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'u.userName', title: __('Username'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'u.createdTime', title: __('Createdtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'ir.createTime', title: __('InvestTime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'bi.borrowName', title: __('BorrowName'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'ir.investorCapital', title: __('InvestorCapital'), operate: 'BETWEEN', sortable: true},
                        {field: 'ir.deductibleMoney', title: __('DeductibleMoney'), operate: 'BETWEEN', sortable: true},
                        {field: 'bi.borrowDurationTxt', title: __('BorrowDurationTxt'), operate:false},
                        {field: 'bi.payChannelType', title: __('PayChannelType'), formatter: Controller.api.formatter.type, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype'),'3':__('Paychanneltype 3')}},
                        {field: 'is_first_invest', title: __('IsFirstInvest'), formatter: Controller.api.formatter.isfirst, searchList: {'0':'否',"1":'是'}},
                    ]
                ],
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
                    var typenameArr = {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')};
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