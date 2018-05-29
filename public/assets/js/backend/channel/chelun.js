define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'channel/chelun/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'channel/chelun/multi',
                    table: 'AppUser',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'ir.userId',
                sortName: 'ir.createTime',
                columns: [
                    [
                        {field: 'ir.userId', title: __('Userid'), sortable: true},
                        {field: 'u.userPhone', title: __('Userphone'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'u.userName', title: __('Username'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'u.createdTime', title: __('Createdtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'ir.createTime', title: __('InvestTime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'bi.borrowName', title: __('BorrowName'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'ir.investorCapital', title: __('InvestorCapital'), operate: 'BETWEEN', sortable: true},
                        {field: 'ir.deductibleMoney', title: __('DeductibleMoney'), operate: 'BETWEEN', sortable: true},
                        {field: 'bi.borrowDurationTxt', title: __('BorrowDurationTxt'), operate:false},
                        {field: 'bi.payChannelType', title: __('PayChannelType'), visible:false, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype'),'3':__('Paychanneltype 3')}},
                        {field: 'pay_channel_type_text', title: __('PayChannelType'), operate:false},
                        //{field: 'is_first_invest', title: __('IsFirstInvest'), visible:false, searchList: {'not in':__('IsFirstInvest 0'),'in':__('IsFirstInvest 1')}},
                        {field: 'is_first_invest_text', title: __('IsFirstInvest'), operate:false},
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