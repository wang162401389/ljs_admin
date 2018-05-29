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
                        {field: 'bi.borrowSn', title: __('Borrowsn'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'u.userName', title: __('borrower.username'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: '', title: '法人手机号', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'u.realName', title: __('borrower.realname'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'bi.borrowName', title: __('Borrowname'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'bi.borrowMoney', title: __('Borrowmoney'), operate: 'BETWEEN', sortable: true},
                        {field: 'repayd_total', title: '已还金额', operate: 'BETWEEN', sortable: true},
                        {field: '', title: '已还利息', operate: 'BETWEEN', sortable: true},
                        {field: 'bi.investInterestType', title: __('Investinteresttype'), visible:false, searchList: $.getJSON('borrow/waitmoney/investinteresttypelist')},
                        {field: 'invest_interest_type_text', title: __('Investinteresttype'), operate:false},
                        {field: 'bi.borrowDurationTxt', title: __('Borrowdurationtxt'), operate:false},
                        {field: 'bi.secondVerifyTime', title: __('Secondverifytime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: '', title: '还款最终时间', operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: '', title: '提前还款时间', operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'bi.payChannelType', title: __('Paychanneltype'), visible:false, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
                        {field: 'pay_channel_type_text', title: __('Paychanneltype'), operate:false},
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
            	}
            }
        }
    };
    return Controller;
});