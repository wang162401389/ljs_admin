define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'borrow/overdue/index',
                    add_url: 'borrow/overdue/add',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'borrow/overdue/multi',
                    repayment_url : 'fkmanage/borrow/repaymentinfo',
                    repay_url : 'borrow/overdue/repay',
                    table: 'AppBorrowInfo',
                }
            });

            var table = $("#table");
            
            //当表格数据加载完成时
            table.on('load-success.bs.table', function (e, data) {
                //这里我们手动设置底部的值
                $("#total_money").text(data.total_sum);
                $("#old_sum").text(data.old_overdue_sum);
                $("#new_sum").text(data.new_overdue_sum);
            });
            
            //还款
            $(document).on("click", ".btn-repay", function () {
            	layer.prompt({title: '请输入还款金额', formType: 3}, function(pass, index){
            		var val = parseInt(pass);
        		    if(!isNaN(val)){
        		        if(val % 100 == 0){
        		        	Layer.confirm('代还款：' + pass + '元，确定进行代还操作？', {
	                        	btn: ['确定', '取消']
	                    	}, function(index){
	                            var options = table ? table.bootstrapTable('getOptions') : {};
	                    		Fast.api.ajax({
	    							url: options.extend.repay_url,
	    							data: {'money': pass},
	                  		  	}, function (data, ret) {
		                  		  	if(ret.code == 1){
		            					document.write(ret.data);
		            				}else{
		            					
		            				}
	    						}, function (data, ret) {
	    							
	    						});
	                    	},function (){
                    			
                            });
        		        }else{
        		        	layer.alert("必须是100整数倍");
        		        }
        		    } else{
        		    	layer.alert("输入必须是数字");
        		    }
            		layer.close(index);
        		});
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'borrowInfoId',
                sortName: 'borrowInfoId',
                columns: [
                    [
                        {field: 'borrowSn', title: __('Borrow.Borrowsn'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userName', title: __('borrower.username'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'realName', title: __('borrower.realname'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'productType', title: __('Producttype'), formatter: Controller.api.formatter.product_type_text, searchList: $.getJSON('borrow/waitverify/producttypelist')},
                        {field: 'borrowName', title: __('Borrowname'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'borrowMoney', title: __('Borrowmoney'), operate: 'BETWEEN', sortable: true},
                        {field: 'investInterestType', title: __('Investinteresttype'), formatter: Controller.api.formatter.invest_interest_type_text, searchList: $.getJSON('borrow/waitmoney/investinteresttypelist')},
                        {field: 'borrowDurationTxt', title: __('Borrowduration'), operate:false},
                        {field: 'rate_total', title:__('BorrowInterestRate'), formatter: Controller.api.formatter.rate_text, operate:'BETWEEN', sortable: true},
                        {field: 'periods', title: '期数', operate:false},
                        {field: 'secondVerifyTime', title: __('Borrow.Secondverifytime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'deadline', title: __('Deadline'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'payChannelType', title: __('Paychanneltype'), formatter: Controller.api.formatter.pay_channel_type_text, searchList: {'1':__('Paychanneltype 1'),"2":__('Paychanneltype 2'),'3':__('Paychanneltype 3')}},
                        {field: 'operate', title: __('Operate'), table: table, events: Controller.api.events.operate, 
//                        	buttons: [
//                        		{
//                    				name: '查看',
//                    				text: '查看',
//                    				icon: 'fa fa-list',
//                    				classname: 'btn btn-info btn-xs btn-detail btn-dialog',
//                    				url: 'borrow/overdue/edit'
//                    			},
//                        		{
//	                                name: 'addtabs',
//	                                text: '投资记录',
//	                                icon: 'fa fa-list',
//	                                classname: 'btn btn-xs btn-warning btn-addtabs',
//	                                url: 'fkmanage/borrow/history'
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
								Layer.confirm(ret.data.msg, {
		                        	btn: ['代借款人还款', '关闭']
		                    	}, function(index){
		                    		var confirm = Layer.confirm(
		                                '确定对此逾期标的进行代还操作？',
		                                {icon: 3, title: __('Warning'), shadeClose: true},
		                                function () {
		                                	Layer.alert('有钱就可以任性！');
		                                	table.bootstrapTable('refresh');
		                                },function (){
		                                	Layer.alert('自己都穷得叮当响了！');
		                                	table.bootstrapTable('refresh');
		                                }
		                            );
		                    	});
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
            				url: 'borrow/overdue/edit'
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
            	},
            	product_type_text : function (value, row, index) {
	                var product_type_textArr = {'1': __('Producttype 1'), '2': __('Producttype 2'), '3': __('Producttype 3'), '4': __('Producttype 4'), '5': __('Producttype 5'), '6': __('Producttype 6'), '7': __('Producttype 7'), '8': __('Producttype 8')};
	                //渲染状态
	                var html = '<span class="text-primary">' + __(product_type_textArr[value]) + '</span>';
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