define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'operate/investstatistics/index',
                    add_url: 'operate/investstatistics/add',
                    edit_url: 'operate/investstatistics/edit',
                    del_url: 'operate/investstatistics/del',
                    multi_url: 'operate/investstatistics/multi',
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
                        {field: 'regSource', title: __('Regsource'), formatter: Table.api.formatter.search},
                        {field: 'createdTime', title: __('Createdtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: '', title: '投资次数', operate: 'BETWEEN', sortable: true},
                        {field: '', title: '投资金额', operate: 'BETWEEN', sortable: true},
                        {field: '', title: '充值次数', operate: 'BETWEEN', sortable: true},
                        {field: '', title: '充值金额', operate: 'BETWEEN', sortable: true},
                        {field: '', title: '提现次数', operate: 'BETWEEN', sortable: true},
                        {field: '', title: '提现金额', operate: 'BETWEEN', sortable: true},
                        {field: '', title: '账户余额'}
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