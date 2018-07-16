define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'appuser/appuser/index',
                    add_url: 'appuser/appuser/add',
                    edit_url: 'appuser/appuser/edit',
                    del_url: 'appuser/appuser/del',
                    multi_url: 'appuser/appuser/multi',
                    table: 'AppUser',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'userId',
                sortName: 'createdTime',
                columns: [
                    [
                        {field: 'userId', title: __('Userid'), sortable: true},
                        {field: 'userPhone', title: __('Userphone')},
                        {field: 'userName', title: __('Username')},
                        {field: 'pid', title: __('Pid')},
                        {field: 'recommendPhone', title: __('Recommendphone')},
                        {field: 'regSource', title: __('Regsource'), formatter: Table.api.formatter.search},
                        {field: 'marketChannel', title: __('MarketChannel'), formatter: Table.api.formatter.search},
                        {field: 'createdTime', title: __('Createdtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'total', title: '用户累计投资金额', operate: 'BETWEEN', sortable: true}
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
            }
        }
    };
    return Controller;
});