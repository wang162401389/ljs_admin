define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/operatereport/index',
                    add_url: 'general/operatereport/add',
                    edit_url: 'general/operatereport/edit',
                    del_url: 'general/operatereport/del',
                    multi_url: 'general/operatereport/multi',
                    table: 'AppOperatReport',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'reportYear', title: __('Reportyear'), sortable: true},
                        {field: 'reportMonth', title: __('Reportmonth'), sortable: true},
                        {field: 'reportTitle', title: __('Reporttitle'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'reportUrl', title: __('Reporturl'), formatter: Table.api.formatter.url, operate:false},
                        {field: 'backgroundImg', title: __('Backgroundimg'), formatter: Table.api.formatter.image, operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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