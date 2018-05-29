define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fkmanage/repaymentapply/index',
                    add_url: 'fkmanage/repaymentapply/add',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'fkmanage/repaymentapply/multi',
                    change_status_url : 'fkmanage/repaymentapply/changeRepaymentStatus',
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
                        {field: 'borrow.borrowSn', title: __('borrow.Borrowsn'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userId', title: __('UserId')},
                        {field: 'borrower.userName', title: __('borrower.UserName'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrower.realName', title: __('borrower.RealName'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrow.borrowDurationTxt', title: __('borrow.Borrowdurationtxt'), operate:false},
                        {field: 'borrow.borrowInterestRate', title: __('borrow.Borrowinterestrate') + '（%）', operate: 'BETWEEN', sortable: true},
                        {field: 'capital', title: __('Capital'), operate: 'BETWEEN', sortable: true},
                        {field: 'interest', title: __('Interest'), operate: 'BETWEEN', sortable: true},
                        {field: 'borrowFee', title: __('BorrowFee'), operate: 'BETWEEN', sortable: true},
                        {field: 'total', title: '截止今日还款总额', operate:false},
                        {field: 'repaymentTime', title: __('RepaymentTime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'borrow.payChannelType', title: __('Paychanneltype'), visible:false, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
                        {field: 'borrow.pay_channel_type_text', title: __('Paychanneltype'), operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Controller.api.events.operate, formatter: Controller.api.formatter.operate}
                    ]
                ],
                search : false,
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
            		'click .btn-apply': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var that = this;
                        var top = $(that).offset().top - $(window).scrollTop();
                        var left = $(that).offset().left - $(window).scrollLeft() - 260;
                        if (top + 154 > $(window).height()) {
                            top = top - 154;
                        }
                        if ($(window).width() < 480) {
                            top = left = undefined;
                        }
                        
                        var table = $(that).closest('table');
                        var options = table ? table.bootstrapTable('getOptions') : {};
                        var index = layer.open({
                        	  content: '通过提前还款审请？',
                        	  btn : ['通过', '拒绝'],
                        	  icon : 3,
                        	  title : __('Warning'),
                        	  offset : [top, left],
                        	  shadeClose : true,
                        	  yes : function(){
                        		  Fast.api.ajax({
                        			  url: options.extend.change_status_url,
									  data: {'id': row.id, "status" : 2},
                        		  }, function (data, ret) {
                        			  if(ret.code == 1){
                        				  Layer.closeAll();
                        				  Toastr.success(ret.msg);
                        				  table.bootstrapTable('refresh');
                        			  }else{
                        				  Layer.alert(ret.msg);
                    				  }
                        			  return false;//这里很关键，如果不加会弹出两次提示
                        		  }, function (data, ret) {
                        			  Layer.alert(ret.msg);
                        		  });
                    		  },
                    		  btn2 : function(){
                    			  Fast.api.ajax({
                        			  url: options.extend.change_status_url,
									  data: {'id': row.id, "status" : 3},
                        		  }, function (data, ret) {
                        			  if(ret.code == 1){
                        				  Layer.closeAll();
                        				  Toastr.success(ret.msg);
                        				  table.bootstrapTable('refresh');
                        			  }else{
                        				  Layer.alert(ret.msg);
                    				  }
                        			  return false;//这里很关键，如果不加会弹出两次提示
                        		  }, function (data, ret) {
                        			  Layer.alert(ret.msg);
                        		  });
                    		  }
                		});
                    }
            	}
            },
            formatter : {
            	operate: function (value, row, index) {
            		this.buttons = [];
            		var table = this.table;
                    
                    this.buttons.push({
                        name : 'post',
                        text : '审核',
                        classname : 'btn btn-xs btn-danger btn-apply',
                        extend : "data-url="+$.fn.bootstrapTable.defaults.extend.change_status_url,
                    });
                    
                    return Table.api.formatter.operate.call(this, value, row, index);
            	}
            }
        }
    };
    return Controller;
});