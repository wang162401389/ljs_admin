define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'operate/investstatistics/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: '',
                    saving_url : 'operate/investstatistics/querysaving',
                    table: 'AppUser',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'u.userId',
                sortName: 'u.createdTime',
                columns: [
                    [
                        {field: 'u.userId', title: __('Userid'), sortable: true},
                        {field: 'u.userPhone', title: __('Userphone')},
                        {field: 'u.userName', title: __('Username')},
                        {field: 'u.regSource', title: __('Regsource'), formatter: Table.api.formatter.search},
                        {field: 'u.createdTime', title: __('Createdtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'ir.touzicount', title: '投资次数', operate: 'BETWEEN', sortable: true},
                        {field: 'ir.totalmoney', title: '投资金额', operate: 'BETWEEN', sortable: true},
                        {field: 'fl.charge_total', title: '充值次数', operate: 'BETWEEN', sortable: true},
                        {field: 'fl.charge_money_total', title: '充值金额', operate: 'BETWEEN', sortable: true},
                        {field: 'fl.withdraw_total', title: '提现次数', operate: 'BETWEEN', sortable: true},
                        {field: 'fl.withdraw_money_total', title: '提现金额', operate: 'BETWEEN', sortable: true},
                        {field: 'operate', title: '账户余额', table: table, events: Controller.api.events.operate, formatter: Controller.api.formatter.operate}
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
            		'click .btn-saving': function (e, value, row, index) {
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
            				name: '还款信息',
            				text: '还款信息',
            				classname: 'btn btn-info btn-xs btn-saving btn-dialog',
            				url: 'fkmanage/borrow/saving_url'
            			}
                	);
                	return Table.api.formatter.operate.call(this, value, row, index);
            	}
            }
        }
    };
    return Controller;
});