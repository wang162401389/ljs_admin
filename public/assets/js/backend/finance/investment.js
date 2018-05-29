define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'finance/investment/index',
                    add_url: 'finance/investment/add',
                    edit_url: 'finance/investment/edit',
                    del_url: 'finance/investment/del',
                    multi_url: 'finance/investment/multi',
                    table: 'AppInvestorRecord',
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
                        {field: 'binfo.borrowSn', title: '标号', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userId', title: __('UserId')},
                        {field: 'user.userPhone', title: '用户手机号', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'user.userName', title: '投资人姓名', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'investorCapital', title: __('Investorcapital'), operate: 'BETWEEN', sortable: true},
                        {field: 'binfo.investInterestType', title: '还款方式', visible:false, searchList: $.getJSON('finance/investment/investinteresttypelist')},
                        {field: 'binfo.invest_interest_type_text', title: '还款方式', operate:false},
                        {field: 'binfo.borrowDurationTxt', title: '标的期限', operate:false},
                        {field: 'investorTime', title: __('Investortime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'borrowStatus', title: __('Borrowstatus'), visible:false, searchList: $.getJSON('finance/investment/borrowstatuslist')},
                        {field: 'borrow_status_text', title: __('Borrowstatus'), operate:false},
                        {field: 'payChannelType', title: __('Paychanneltype'), visible:false, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
                        {field: 'pay_channel_type_text', title: __('Paychanneltype'), operate:false},
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