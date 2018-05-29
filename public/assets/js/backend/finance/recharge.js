define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'finance/recharge/index',
                    add_url: 'finance/recharge/add',
                    edit_url: 'finance/recharge/edit',
                    del_url: 'finance/recharge/del',
                    multi_url: 'finance/recharge/multi',
                    table: 'AppTransactionFlowing',
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
                        {field: 'userId', title: __('UserId'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userphone', title: '用户手机号', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'realname', title: __('Realname'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'idname', title: '身份'},
                        {field: 'transactionAmt', title: __('Transactionamt'), operate: 'BETWEEN', sortable: true},
                        {field: 'addTime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'transactionStatus', title: __('Transactionstatus'), visible:false, searchList: {'1':__('Transactionstatus 1'),"2":__('Transactionstatus 2'),'3':__('Transactionstatus 3')}},
                        {field: 'transactionStatus_text', title: __('Transactionstatus'), operate:false},
                        {field: 'payChannelType', title: __('Paychanneltype'), visible:false, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
                        {field: 'payChannelType_text', title: __('Paychanneltype'), operate:false},
                        {field: 'orderId', title: __('Orderid'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
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