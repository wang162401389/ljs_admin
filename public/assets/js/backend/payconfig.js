define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'payconfig/index',
                    add_url: 'payconfig/add',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'payconfig/multi',
                    table: 'AppPayConfig',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {field: 'name', title: __('Name')},
                        {field: 'title', title: __('Title')},
                        {field: 'is_open', title: __('Is_open'), formatter: Controller.api.formatter.to_open},
                        {field: 'is_disable_charge', title: __('Is_disable_charge'), formatter: Controller.api.formatter.to_dis},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                //禁用默认搜索
                search : false,
                //浏览模式
                showToggle : false,
                //显示隐藏列
                showColumns : false,
                //导出
                showExport : false,
                //通用搜索
                commonSearch : false
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
            formatter: {//渲染的方法
            	to_open : function (value, row, index) {
                    //添加上btn-change可以自定义请求的URL进行数据处理
                    return '<a class="btn-change text-success" data-url="payconfig/change" data-id="' + row.id + 
                    '" data-params="is_open=' + (value ? 0 : 1) + '"><i class="fa ' + (value == 0 ? 'fa-toggle-off' : 'fa-toggle-on') + ' fa-2x"></i></a>';
                },
                to_dis : function (value, row, index) {
                    //添加上btn-change可以自定义请求的URL进行数据处理
                    return '<a class="btn-change text-success" data-url="payconfig/change" data-id="' + row.id + 
                    '" data-params="is_disable_charge=' + (value ? 0 : 1) + '"><i class="fa ' + (value == 0 ? 'fa-toggle-off' : 'fa-toggle-on') + ' fa-2x"></i></a>';
                }
            }
        }
    };
    return Controller;
});