define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'finance/withdraw/index',
                    add_url: 'finance/withdraw/add',
                    edit_url: 'finance/withdraw/edit',
                    del_url: 'finance/withdraw/del',
                    multi_url: 'finance/withdraw/multi',
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
                        {field: 'userId', title: __('Accountid')},
                        {field: 'userPhone', title: '用户手机号'},
                        {field: 'userName', title: __('Realname')},
                        {field: 'idname', title: '身份', formatter: Controller.api.formatter.idname, searchList: {'1':'投资人',"2":'借款人'}},
                        {field: 'transactionAmt', title: __('Transactionamt'), operate: 'BETWEEN', sortable: true},
                        {field: 'addTime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'payTime', title: '完成时间', operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'transactionStatus', title: __('Transactionstatus'), formatter: Controller.api.formatter.status, searchList: {'1':__('Transactionstatus 1'),"2":__('Transactionstatus 2'),'3':__('Transactionstatus 3')}},
                        {field: 'payChannelType', title: __('Paychanneltype'), formatter: Controller.api.formatter.type, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
                        {field: 'orderId', title: __('Orderid')},
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
	        	idname : function (value, row, index) {
                    var idnameArr = {'1': '投资人', '2': '借款人'};
                    //渲染状态
                    var html = '<span class="text-primary">' + idnameArr[value] + '</span>';
                    return html;
	        	},
	        	status : function (value, row, index) {
                    var statusnameArr = {'1':__('Transactionstatus 1'),"2":__('Transactionstatus 2'),'3':__('Transactionstatus 3')};
                    //渲染状态
                    var html = '<span class="text-primary">' + __(statusnameArr[value]) + '</span>';
                    return html;
	        	},
	        	type : function (value, row, index) {
                    var typenameArr = {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')};
                    //渲染状态
                    var html = '<span class="text-primary">' + __(typenameArr[value]) + '</span>';
                    return html;
	        	}
	        }
        }
    };
    return Controller;
});