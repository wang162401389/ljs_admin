define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'borrow/waitverify/index',
                    add_url: 'borrow/waitverify/add',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'borrow/waitverify/multi',
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
                        {field: 'borrower.userName', title: '借款人手机', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: '', title: '法人手机号', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrower.realName', title: '借款人姓名', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'productType', title: __('Producttype'), visible:false, searchList: $.getJSON('borrow/waitverify/producttypelist')},
                        {field: 'product_type_text', title: __('Producttype'), operate:false},
                        {field: 'borrowName', title: __('Borrowname'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrowMoney', title: __('Borrowmoney'), operate: 'BETWEEN', sortable: true},
                        {field: 'borrowDurationTxt', title: __('Borrowdurationtxt'), operate:false},
                        {field: 'borrowInterestRate', title:__('BorrowInterestRate'), operate:false},
                        {field: 'createTime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'fullTime', title: __('Fulltime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'payChannelType', title: __('Paychanneltype'), visible:false, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
                        {field: 'pay_channel_type_text', title: __('Paychanneltype'), operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Controller.api.formatter.operate}
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
                				name: '查看',
                				text: '查看',
                				icon: 'fa fa-list',
                				classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                				url: 'borrow/waitverify/edit'
                			},
		        			{
		        				name: '审核',
		        				text: '审核',
		        				classname: 'btn btn-danger btn-xs btn-detail btn-dialog',
		    					url : 'fkmanage/borrow/verify'
		        			}
        			);
            		return Table.api.formatter.operate.call(this, value, row, index);
            	}
            }
        }
    };
    return Controller;
});