define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'appuser/refereeapply/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'appuser/refereeapply/multi',
                    table: 'AppUser',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'u.userId',
                sortName: 'u.createdTime',
                columns: [
                    [
                        {field: 'u.userId', title: __('Userid'), sortable: true},
                        {field: 'u.userPhone', title: __('Userphone')},
                        {field: 'u.userName', title: __('Username')},
                        {field: 'u.regSource', title: __('Regsource'), formatter: Table.api.formatter.search},
                        {field: 'u.createdTime', title: __('Createdtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'pre_recommend', title: '原推荐人'},
                        {field: 'u.recommendPhone', title: __('Recommendphone')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, 
                        	buttons: [
                        		{
                    				name: '修改推荐人',
                    				text: '修改推荐人',
                    				classname: 'btn btn-info btn-xs btn-danger btn-dialog',
                    				url: 'appuser/refereeapply/edit',
                    			},
                        		{
                    				name: '操作记录',
                    				text: '操作记录',
                    				icon: 'fa fa-list',
                    				classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                    				url: 'appuser/refereeapply/applylog'
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