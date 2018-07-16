define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fkmanage/borrow/index',
                    add_url: '',
                    edit_url: 'fkmanage/borrow/edit',
                    del_url: '',
                    multi_url: '',
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
                        {field: 'borrowUid', title: '用户ID', operate: 'LIKE %...%', placeholder: '模糊搜索', sortable: true},
                        {field: 'userName', title: '手机号', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'realName', title: '借款人姓名', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrowSn', title: __('Borrowsn'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrowInfoId', title: '标号ID', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrowName', title: '借款名称', operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrowMoney', title: __('Borrowmoney'), operate: 'BETWEEN', sortable: true},
                        {field: 'createTime', title: __('createTime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'borrowDurationTxt', title: __('Borrowdurationtxt'), operate:false},
                        {field: 'borrowStatus', title: '初审状态', formatter: Controller.api.formatter.status, searchList: {"0":__('Borrowstatus 0'),'1':__('Borrowstatus 1'),'2':__('Borrowstatus 2'),'3':__('Borrowstatus 3')}},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Controller.api.formatter.operate}
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
        verify : function () {
        	Form.events.datetimepicker($("#verify-form"));
            Controller.api.bindevent();
        },
        verifylog : function () {
        	Form.events.datetimepicker($("#log-form"));
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
                $(document).on('click', "input[name='row[type]']", function () {
                    var type = $(this).val();
                    if (type == 1) {
                    	$("#repaymenttype").hide();
                    } else if (type == 2) {
                        $("#repaymenttype").show();
                    }
                });
            },
            formatter : {
            	operate: function (value, row, index) {
            		this.buttons = [];
            		var that = $.extend({}, this);
            		var table = $(that.table).clone(true);
                    if(row.borrowStatus != 0)
                    {
                    	//审核通过/不通过
            			$(table).data("operate-edit", null);
            			
            			this.buttons.push({
            				name: '查看',
            				text: '查看',
            				icon: 'fa fa-list',
            				classname: 'btn btn-info btn-xs btn-detail btn-dialog',
            				url: 'fkmanage/borrow/verifyLog'
            			});
                    }
                    else
                	{
                    	this.buttons.push({
            				name: '审核',
            				text: '审核',
            				classname: 'btn btn-danger btn-xs btn-detail btn-dialog',
            				url: 'fkmanage/borrow/verify'
            			});
                	}
                    that.table = table;
            		return Table.api.formatter.operate.call(that, value, row, index);
            	},
            	status : function (value, row, index) {
	                var statusnameArr = {"0":__('Borrowstatus 0'),'1':__('Borrowstatus 1'),'2':__('Borrowstatus 2'),'3':__('Borrowstatus 3')};
	                //渲染状态
	                var html = '<span class="text-primary">' + __(statusnameArr[value]) + '</span>';
	                return html;
	        	},
            }
        }
    };
    return Controller;
});