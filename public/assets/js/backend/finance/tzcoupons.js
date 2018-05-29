define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'finance/tzcoupons/index',
                    add_url: 'finance/tzcoupons/add',
                    edit_url: 'finance/tzcoupons/edit',
                    del_url: 'finance/tzcoupons/del',
                    multi_url: 'finance/tzcoupons/multi',
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
                        {field: 'appborrowinfo.borrowSn', title: __('Appborrowinfo.borrowsn'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userId', title: __('UserId'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'appuser.userPhone', title: __('Appuser.userphone'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'appuser.userName', title: __('Appuser.username'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'transactionAmt', title: __('Transactionamt'), operate: 'BETWEEN', sortable: true},
                        {field: 'coupon.money', title: __('Appcoupon.money'), operate: 'BETWEEN', sortable: true},
                        {field: 'coupon.defName', title: __('Appcoupon.defName'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'coupon.grant_man_name', title: __('Appcoupon.grantMan'), operate: false},
                        {field: 'coupon.createdTime', title: __('Appcoupon.createdTime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'coupon.endTime', title: __('Appcoupon.endTime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'addTime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'transactionStatus', title: __('Transactionstatus'), visible:false, searchList: {'1':__('Transactionstatus 1'),"2":__('Transactionstatus 2'),'3':__('Transactionstatus 3')}},
                        {field: 'transactionStatus_text', title: __('Transactionstatus'), operate:false},
                        {field: 'payChannelType', title: __('Paychanneltype'), visible:false, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
                        {field: 'payChannelType_text', title: __('Paychanneltype'), operate:false},
                        {field: 'orderId', title: __('Orderid'), operate: 'LIKE %...%', placeholder: '模糊搜索'}
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