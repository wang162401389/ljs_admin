define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'operate/act/act201807/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: '',
                    table: 'AppUser',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'userId',
                sortName: 'investorTime',
                columns: [
                    [
                        {field: 'userId', title: '用户ID', sortable: true},
                        {field: 'userPhone', title: '手机号'},
                        {field: 'userName', title: '姓名'},
                        {field: 'borrowSn', title: '标号'},
                        {field: 'investorTime', title: '投资时间', operate: false, sortable: true},
                        {field: 'investorCapital', title: '投资金额', operate: 'BETWEEN', sortable: true},
                        {field: 'has_send', title: '状态', formatter: Controller.api.formatter.send_status, searchList: {"0":'未发放',"1":'已发放'}}
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
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
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
	        	send_status : function (value, row, index) {
                    var statusnameArr = {'0': '未发放', '1': '已发放'};
                    //渲染状态
                    var html = '<span class="text-primary">' + __(statusnameArr[value]) + '</span>';
                    return html;
	        	}
	        }
        }
    };
    return Controller;
});