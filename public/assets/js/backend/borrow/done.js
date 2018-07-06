define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'borrow/done/index',
                    add_url: 'borrow/done/add',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'borrow/done/multi',
                    table: 'AppBorrowInfo',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'borrowInfoId',
                sortName: 'borrowInfoId',
                columns: [
                    [
                        {field: 'borrowSn', title: __('Borrowsn'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userName', title: __('borrower.username'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'realName', title: __('borrower.realname'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrowName', title: __('Borrowname'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrowMoney', title: __('Borrowmoney'), operate: 'BETWEEN', sortable: true},
                        {field: 'repayd_total', title: '已还金额', operate: 'BETWEEN', sortable: true},
                        {field: 'interest_total', title: '已还利息', operate: 'BETWEEN', sortable: true},
                        {field: 'investInterestType', title: __('Investinteresttype'), formatter: Controller.api.formatter.invest_interest_type_text, searchList: $.getJSON('borrow/waitmoney/investinteresttypelist')},
                        {field: 'borrowDurationTxt', title: __('Borrowdurationtxt'), operate:false},
                        {field: 'rate_total', title:__('BorrowInterestRate'), formatter: Controller.api.formatter.rate_text, operate:'BETWEEN', sortable: true},
                        {field: 'secondVerifyTime', title: __('Secondverifytime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'last_deadline', title: '还款最终时间', operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'last_repaymentTime', title: '提前还款时间', operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'payChannelType', title: __('Paychanneltype'), formatter: Controller.api.formatter.pay_channel_type_text, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, 
//                        	buttons: [
//                        		{
//	                                name: 'addtabs',
//	                                text: '投资记录',
//	                                icon: 'fa fa-list',
//	                                classname: 'btn btn-xs btn-warning btn-addtabs',
//	                                url: 'fkmanage/borrow/history'
//                        		}
//                    		],
                        	formatter: Controller.api.formatter.operate
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
            },
            formatter : {
            	operate: function (value, row, index) {
                    this.buttons = [];
                	this.buttons.push(
            			{
                            name: 'addtabs',
                            text: '投资记录',
                            icon: 'fa fa-list',
                            classname: 'btn btn-xs btn-warning btn-addtabs',
                            url: 'borrow/history/index?borrowInfoId=' + row.borrowInfoId
                		}
                	);
                	return Table.api.formatter.operate.call(this, value, row, index);
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
	        	},
	        	rate_text : function (value, row, index) {
	                //渲染状态
	                var html = row.borrowInterestRate + '%';
	                if(row.addInterestRate > 0){
	                	html += '+' + row.addInterestRate + '%'; 
	                }
	                return html;
	        	}
            }
        }
    };
    return Controller;
});