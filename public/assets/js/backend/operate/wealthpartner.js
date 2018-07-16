define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init();
            
            //绑定事件
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var panel = $($(this).attr("href"));
                if (panel.size() > 0) {
                    Controller.table[panel.attr("id")].call(this);
                    $(this).on('click', function (e) {
                        $($(this).attr("href")).find(".btn-refresh").trigger("click");
                    });
                }
                //移除绑定的事件
                $(this).unbind('shown.bs.tab');
            });
            
            //必须默认触发shown.bs.tab事件
            $('ul.nav-tabs li.active a[data-toggle="tab"]').trigger("shown.bs.tab");
        },
        table: {
            first: function () {
                // 表格1
                var table1 = $("#table1");
                table1.bootstrapTable({
                    url: 'operate/wealthpartner/users',
                    toolbar: '#toolbar1',
                    sortName: 'investorCapital',
                    search: false,
                    columns: [
                        [
                            {field: 'userId', title: '用户ID'},
                            {field: 'userPhone', title: '手机号'},
                            {field: 'userName', title: '用户姓名'},
                            {field: 'investorCapital', title: '用户累计投资金额', operate: 'BETWEEN', sortable: true},
                            {field: 'inviteCount', title: '用户邀请好友数', operate: 'BETWEEN', sortable: true},
                            {field: 'isPartner', title: '是否为财富合伙人', formatter: Controller.api.formatter.status, searchList: {"0":'否',"1":'是'}}
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
                    }
                });

                // 为表格1绑定事件
                Table.api.bindevent(table1);
            },
            second: function () {
                // 表格2
                var table2 = $("#table2");
                table2.bootstrapTable({
                    url: 'operate/wealthpartner/bonus',
                    extend: {
                        index_url: '',
                        add_url: '',
                        edit_url: '',
                        del_url: '',
                        multi_url: '',
                        table: '',
                    },
                    toolbar: '#toolbar2',
                    sortName: 't.userId',
                    search: false,
                    columns: [
                        [
                        	{field: 't.userId', title: '用户ID'},
                            {field: 't.userPhone', title: '手机号'},
                            {field: 't.userName', title: '用户姓名'},
                            {field: 't.investorCapital', title: '用户单月投资金额', operate: 'BETWEEN', sortable: true},
                            {field: 't.investorTime', title: '用户投资月份', sortable: true},
                            {field: 'u.userPhone', title: '合伙人电话'},
                            {field: 'u.userName', title: '合伙人姓名'},
                            {field: 'u.userId', title: '合伙人ID'},
                            {field: 't.bonus', title: '合伙人当月返利', operate: 'BETWEEN', sortable: true},
                            {field: 't.send_status', title: '返利发放状态', formatter: Controller.api.formatter.send_status, searchList: {"0":'未发放',"1":'已发放'}}
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
                    }
                });

                // 为表格2绑定事件
                Table.api.bindevent(table2);
            }
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
	        	status : function (value, row, index) {
					//颜色状态数组,可使用red/yellow/aqua/blue/navy/teal/olive/lime/fuchsia/purple/maroon
                    var colorArr = {0: 'grey', 1: 'success'};
                    //如果字段列有定义custom
                    if (typeof this.custom !== 'undefined') {
                        colorArr = $.extend(colorArr, this.custom);
                    }
                    value = value === null ? '' : value.toString();
                    var color = value && typeof colorArr[value] !== 'undefined' ? colorArr[value] : 'primary';
                    var statusnameArr = {'0': '否', '1': '是'};
                    //渲染状态
                    var html = '<span class="text-' + color + '">' + __(statusnameArr[value]) + '</span>';
                    return html;
	        	},
	        	send_status : function (value, row, index) {
					//颜色状态数组,可使用red/yellow/aqua/blue/navy/teal/olive/lime/fuchsia/purple/maroon
                    var colorArr = {0: 'grey', 1: 'success'};
                    //如果字段列有定义custom
                    if (typeof this.custom !== 'undefined') {
                        colorArr = $.extend(colorArr, this.custom);
                    }
                    value = value === null ? '' : value.toString();
                    var color = value && typeof colorArr[value] !== 'undefined' ? colorArr[value] : 'primary';
                    var statusnameArr = {'0': '未发放', '1': '已发放'};
                    //渲染状态
                    var html = '<span class="text-' + color + '">' + __(statusnameArr[value]) + '</span>';
                    return html;
	        	}
	        }
        }
    };
    return Controller;
});