define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'finance/investment/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: '',
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
                        {field: 'borrowSn', title: '标号', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userId', title: __('UserId')},
                        {field: 'userPhone', title: '用户手机号', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userName', title: '投资人姓名', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'recommendPhone', title: '推荐人', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'regSource', title: '注册渠道', formatter: Table.api.formatter.search},
                        {field: 'createdTime', title: '注册时间', operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'native_place', title: '籍贯', formatter: Table.api.formatter.search, operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'investorCapital', title: __('Investorcapital'), operate: 'BETWEEN', sortable: true},
                        {field: 'investInterestType', title: '还款方式', formatter: Controller.api.formatter.invest_interest_type_text, searchList: $.getJSON('finance/investment/investinteresttypelist')},
                        {field: 'borrowDurationTxt', title: '标的期限', operate:false},
                        {field: 'investorTime', title: __('Investortime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'deductibleMoney', title: '红包金额', operate: 'BETWEEN', sortable: true},
                        {field: 'interestCcfaxRate', title: '加息券利率', operate: 'BETWEEN', sortable: true},
                        {field: 'borrowStatus', title: __('Borrowstatus'), formatter: Controller.api.formatter.borrow_status_text, searchList: {'0':__('Borrowstatus 0'),'1':__('Borrowstatus 1'),'2':__('Borrowstatus 2'),'3':__('Borrowstatus 3'),'4':__('Borrowstatus 4'),'5':__('Borrowstatus 5')}},
                        {field: 'payChannelType', title: __('Paychanneltype'), formatter: Controller.api.formatter.pay_channel_type_text, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
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
            },
            formatter : {
            	borrow_status_text : function (value, row, index) {
	                var borrow_status_textArr = {'0':__('Borrowstatus 0'),'1':__('Borrowstatus 1'),'2':__('Borrowstatus 2'),'3':__('Borrowstatus 3'),'4':__('Borrowstatus 4'),'5':__('Borrowstatus 5')};
	                //渲染状态
	                var html = '<span class="text-primary">' + __(borrow_status_textArr[value]) + '</span>';
	                return html;
	        	},
	        	invest_interest_type_text : function (value, row, index) {
	                var invest_interest_type_textArr = {'1': __('InvestInterestType 1'), '2': __('InvestInterestType 2'), '3': __('InvestInterestType 3'), '4': __('InvestInterestType 4'), '5': __('InvestInterestType 5'), '7': __('InvestInterestType 7')};
	                //渲染状态
	                var html = '<span class="text-primary">' + __(invest_interest_type_textArr[value]) + '</span>';
	                return html;
	        	},
	        	pay_channel_type_text : function (value, row, index) {
	                var pay_channel_type_textArr = {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')};
	                //渲染状态
	                var html = '<span class="text-primary">' + __(pay_channel_type_textArr[value]) + '</span>';
	                return html;
	        	}
            }
        }
    };
    return Controller;
});