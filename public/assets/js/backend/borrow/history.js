define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'borrow/history/index',
                    add_url: 'borrow/history/add',
                    edit_url: 'borrow/history/edit',
                    del_url: 'borrow/history/del',
                    multi_url: 'borrow/history/multi',
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
                        {field: 'borrowInfoId', title: __('Borrowinfoid'), visible:false},
                        {field: 'investorUid', title: __('Investoruid')},
                        {field: 'user.userPhone', title: __('AppUser.userPhone'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'user.userName', title: __('AppUser.userName'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'binfo.borrowName', title: __('binfo.borrowName'), operate: false},
                        {field: 'investorCapital', title: __('Investorcapital'), operate: 'BETWEEN', sortable: true},
                        {field: 'earnings', title: __('Earnings'), operate: 'BETWEEN', sortable: true},
                        {field: 'binfo.borrowDurationTxt', title: __('binfo.borrowDurationTxt'), operate:false},
                        {field: 'binfo.investInterestType', title: __('binfo.investInterestType'), visible:false, operate:false},//searchList:$.getJSON('finance/investment/investinteresttypelist')
                        {field: 'binfo.invest_interest_type_text', title: __('binfo.investInterestType'), operate:false},
                        {field: '', title: '投标方式', operate:false},
                        {field: 'deductibleMoney', title: __('Deductiblemoney'), operate: 'BETWEEN', sortable: true},
                        {field: 'interestCcfaxRate', title: __('Interestccfaxrate'), operate: 'BETWEEN', sortable: true},
                        {field: 'investorTime', title: __('Investortime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
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