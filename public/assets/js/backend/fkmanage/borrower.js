define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fkmanage/borrower/index',
                    add_url: 'fkmanage/borrower/add',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'fkmanage/borrower/multi',
                    change_status_url : 'fkmanage/borrower/changeLoanStatus',
                    table: 'AppBorrower',
                }
            });

            var table = $("#table");

            //["1000px", "500px"]
            //给添加按钮添加`data-area`属性
            //$(".btn-add").data("area", ["100%", "100%"]);
            
            //当内容渲染完成给编辑按钮添加`data-area`属性
            //table.on('post-body.bs.table', function (e, settings, json, xhr) {
            //    $(".btn-dialog").data("area", ["100%", "100%"]);
            //});

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'borrowUserId',
                sortName: 'createTime',
                columns: [
                    [
                        {field: 'borrowUserId', title: __('BorrowUserId')},
                        {field: 'userName', title: __('UserName'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'realName', title: __('Realname'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'userType', title: __('UserType'), visible:false, searchList: {"1":__('UserType 1'),"2":__('UserType 2')}},
                        {field: 'user_type_text', title: __('UserType'), operate:false},
                        {field: 'isSina', title: __('IsSina'), visible:false, searchList: {"1":__('IsSina 1'),"0":__('IsSina 0'),"2":__('IsSina 2'),"3":__('IsSina 3')}},
                        {field: 'is_sina_text', title: __('IsSina'), operate:false},
                        {field: 'isHuaxing', title: __('IsHuaxing'), visible:false, searchList: {"0":__('IsHuaxing 0'),"1":__('IsHuaxing 1'),"2":__('IsHuaxing 2'),"3":__('IsHuaxing 3')}},
                        {field: 'is_huaxing_text', title: __('IsHuaxing'), operate:false},
                        {field: 'createTime', title: __('Regtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'isLoan', title: __('Isloan'), visible:false, searchList: {"1":__('Isloan 1'),"0":__('Isloan 0')}},
                        {field: 'operate', title: __('Operate'), table: table, events: Controller.api.events.operate, formatter: Controller.api.formatter.operate}
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
            		'click .btn-publish': function (e, value, row, index) {
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
                        var index = Layer.confirm(
                            '是否确定' + row.userName + '为可借款人？',
                            {icon: 3, title: __('Warning'), offset: [top, left], shadeClose: true},
                            function () {
                                var table = $(that).closest('table');
                                var options = table.bootstrapTable('getOptions');
                                Table.api.multi("", row[options.pk], table, that);
                                Layer.close(index);
                            }
                        );
                    },
//                    'click .btn-release' : function(row){
//                        Fast.api.open("fkmanage/borrow/add?id=" + $(this).data("id"), '发布借款');
//                    }
            	}
            },
            formatter : {
            	operate: function (value, row, index) {
                    //this.buttons.splice(0, this.buttons.length);
                    this.buttons = [];
                    if(row.isLoan == 0){
                    	var table = this.table;
                        // 操作配置
                        var options = table ? table.bootstrapTable('getOptions') : {};
                        
                        this.buttons.push({
                            name : 'post',
                            text : '确认可借款',
                            classname : 'btn btn-xs btn-danger btn-publish',
                            extend : "data-url="+$.fn.bootstrapTable.defaults.extend.change_status_url,
                        });
                    }else{
                    	this.buttons.push(
//                    		{
//                    			name: 'addtabs', 
//                    			title: '发布借款', 
//                                text : '发布借款',
//                    			classname: 'btn btn-xs btn-success btn-addtabs', 
//                    			icon: 'fa fa-pencil', 
//                    			url: 'fkmanage/borrow/release'
//                    		}
//                			{
//                    			name: 'addtabs',
//                    			title: '发布借款', 
//                                text : '发布借款',
//                    			classname: 'btn btn-xs btn-success btn-add btn-release', 
//                    			icon: 'fa fa-plus',
//                    			extend : "data-id=" + row.id
//                    		}
                			{
                    			title: '发布借款', 
                                text : '发布借款',
                    			classname: 'btn btn-xs btn-success btn-dialog', 
                    			icon: 'fa fa-plus',
                    			url : 'fkmanage/borrow/add'
                    		}
                    	);
                    }
                	return Table.api.formatter.operate.call(this, value, row, index);
            	}
            }
        }
    };
    return Controller;
});