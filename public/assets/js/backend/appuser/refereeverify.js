define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'appuser/refereeverify/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'appuser/refereeverify/multi',
                    table: 'AppUser',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'rl.id',
                sortName: 'rl.id',
                columns: [
                    [
                        {field: 'rl.uid', title: __('Userid'), sortable: true},
                        {field: 'u.userPhone', title: __('Userphone')},
                        {field: 'u.userName', title: __('Username')},
                        {field: 'u.regSource', title: __('Regsource'), formatter: Table.api.formatter.search},
                        {field: 'u.createdTime', title: __('Createdtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'rl.pre_recommend', title: '原推荐人'},
                        {field: 'rl.now_recommend', title: __('Recommendphone')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, 
                        	buttons: [
                        		{
                    				name: '审核',
                    				text: '审核',
                    				classname: 'btn btn-danger btn-xs btn-detail btn-dialog',
                    				url: 'appuser/refereeverify/edit',
                    			}
                    		],
                        	formatter: Table.api.formatter.operate
                        }
                    ]
                ],
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