define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'borrow/repaymenting/index',
                    add_url: 'borrow/repaymenting/add',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'borrow/repaymenting/multi',
                    repayment_url : 'fkmanage/borrow/repaymentinfo',
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
                        {field: 'borrower.userName', title: __('borrower.username'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: '', title: '法人手机号', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrower.realName', title: __('borrower.realname'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'productType', title: __('Producttype'), visible:false, searchList: $.getJSON('borrow/waitverify/producttypelist')},
                        {field: 'product_type_text', title: __('Producttype'), operate:false},
                        {field: 'borrowName', title: __('Borrowname'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrowMoney', title: __('Borrowmoney'), operate: 'BETWEEN', sortable: true},
                        {field: 'investInterestType', title: __('Investinteresttype'), visible:false, searchList: $.getJSON('borrow/waitmoney/investinteresttypelist')},
                        {field: 'invest_interest_type_text', title: __('Investinteresttype'), operate:false},
                        {field: 'borrowDurationTxt', title: __('Borrowdurationtxt'), operate:false},
                        {field: 'borrowInterestRate', title:__('BorrowInterestRate'), operate:false},
                        {field: 'secondVerifyTime', title: __('Secondverifytime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'last_deadline', title: '最近还款时间', operate:false, addclass:'datetimerange', sortable: true},
                        {field: 'payChannelType', title: __('Paychanneltype'), visible:false, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
                        {field: 'pay_channel_type_text', title: __('Paychanneltype'), operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Controller.api.events.operate, 
//                        	buttons: [
//                        		{
//                    				name: '查看',
//                    				text: '查看',
//                    				icon: 'fa fa-list',
//                    				classname: 'btn btn-info btn-xs btn-detail btn-dialog',
//                    				url: 'borrow/repaymenting/edit'
//                    			},
//                        		{
//	                                name: 'addtabs',
//	                                text: '投资记录',
//	                                icon: 'fa fa-list',
//	                                classname: 'btn btn-xs btn-warning btn-addtabs',
//	                                url: 'borrow/history/index'
//                        		},
//                        		{
//                    				name: '还款信息',
//                    				text: '还款信息',
//                    				classname: 'btn btn-info btn-xs btn-repaymentinfo btn-dialog',
//                    				url: 'fkmanage/borrow/repaymentInfo'
//                    			}
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
            events : {
            	operate: {
            		'click .btn-repaymentinfo': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var that = this;
                        var table = $(that).closest('table');
                        var options = table ? table.bootstrapTable('getOptions') : {};
                        Fast.api.ajax({
							url: options.extend.repayment_url,
							data: {'id': row.borrowInfoId},
              		  	}, function (data, ret) {
              		  		//逾期的借款
							if(ret.code == 1){
								Layer.alert(ret.data.msg);
							}
							return false;
						}, function (data, ret) {
							Layer.alert(ret.msg);
						});
                    }
            	}
            },
            formatter : {
            	operate: function (value, row, index) {
                    this.buttons = [];
                	this.buttons.push(
            			{
            				name: '查看',
            				text: '查看',
            				icon: 'fa fa-list',
            				classname: 'btn btn-info btn-xs btn-detail btn-dialog',
            				url: 'borrow/repaymenting/edit'
            			},
                		{
                            name: 'addtabs',
                            text: '投资记录',
                            icon: 'fa fa-list',
                            classname: 'btn btn-xs btn-warning btn-addtabs',
                            url: 'borrow/history/index?borrowInfoId=' + row.borrowInfoId
                		},
                		{
            				name: '还款信息',
            				text: '还款信息',
            				classname: 'btn btn-info btn-xs btn-repaymentinfo btn-dialog',
            				url: 'fkmanage/borrow/repaymentInfo'
            			}
                	);
                	return Table.api.formatter.operate.call(this, value, row, index);
            	}
            }
        }
    };
    return Controller;
});