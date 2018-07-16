define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'finance/repayment/index',
                    add_url: 'finance/repayment/add',
                    edit_url: 'finance/repayment/edit',
                    del_url: 'finance/repayment/del',
                    multi_url: 'finance/repayment/multi',
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
                        {field: 'borrowSn', title: __('Appborrowinfo.borrowsn'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userId', title: __('UserId'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userName', title: __('Borrower.userName'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'realName', title: __('Borrower.realName'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'capital', title: __('Repayment.Capital'), operate: 'BETWEEN', sortable: true},
                        {field: 'interest', title: __('Repayment.Interest'), operate: 'BETWEEN', sortable: true},
                        {field: 'borrowFee', title: __('Repayment.borrowFee'), operate: 'BETWEEN', sortable: true},
                        {field: '', title: '平台加息金额', operate: 'BETWEEN', sortable: true},
                        {field: '', title: '用户加息券金额', operate: 'BETWEEN', sortable: true},
                        {field: 'transactionAmt', title: __('TransactionAmt'), operate: 'BETWEEN', sortable: true},
                        {field: 'periods', title: '期数', operate: false},
                        {field: 'addTime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'transactionStatus', title: __('Transactionstatus'), formatter: Controller.api.formatter.status_text, searchList: {'1':__('Transactionstatus 1'),"2":__('Transactionstatus 2'),'3':__('Transactionstatus 3')}},
                        {field: 'payChannelType', title: __('Paychanneltype'), formatter: Controller.api.formatter.pay_channel_type_text, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
                        {field: 'orderId', title: __('Orderid'), operate: 'LIKE %...%', placeholder: '模糊搜索'}
                    ]
                ],
                exportOptions: {
		            mso:{
		                // fileFormat:        'xlsx',
		                 //修复导出数字不显示为科学计数法
		            	onMsoNumberFormat: function (cell, row, col) {
		                   return !isNaN($(cell).text())?'\\@':'';
		            	}
		             }
                },
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
            	status_text : function (value, row, index) {
	                var status_textArr = {'1':__('Transactionstatus 1'),"2":__('Transactionstatus 2'),'3':__('Transactionstatus 3')};
	                //渲染状态
	                var html = '<span class="text-primary">' + __(status_textArr[value]) + '</span>';
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