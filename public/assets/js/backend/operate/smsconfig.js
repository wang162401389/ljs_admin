define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'operate/smsconfig/index',
                    add_url: 'operate/smsconfig/add',
                    edit_url: 'operate/smsconfig/edit',
                    del_url: 'operate/smsconfig/del',
                    multi_url: 'operate/smsconfig/multi',
                    table: 'SmsTemplate',
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
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'code', title: __('Code')},
                        {field: 'name', title: __('Name')},
                        {field: 'type', title: __('Type'), visible:false, searchList: {"0":__('Type 0'),'1':__('Type 1')}},
                        {field: 'type_text', title: __('Type'), operate:false},
                        {field: 'content', title: __('Content'), operate:false},
                        {field: 'status', title: __('Status'), formatter: Controller.api.formatter.status, searchList: {"0":__('Status 0'),'1':__('Status 1')}},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
                
                /*$(document).on('click', ".btn-jsoneditor", function () {
                    $(".fieldlist").toggle();
                    $("#c-value").toggleClass("hide");
                    $("input[name='row[mode]']").val($("#c-value").is(":visible") ? "textarea" : "json");
                });*/
                
                $(document).on('click', "input[name='row[sms_cfg]']", function () {
                	$(".sms_cfg_area").toggleClass("hide");
                });
            },
	        formatter : {
	        	status : function (value, row, index) {
					//颜色状态数组,可使用red/yellow/aqua/blue/navy/teal/olive/lime/fuchsia/purple/maroon
                    var colorArr = {1: 'grey', 0: 'success'};
                    //如果字段列有定义custom
                    if (typeof this.custom !== 'undefined') {
                        colorArr = $.extend(colorArr, this.custom);
                    }
                    value = value === null ? '' : value.toString();
                    var color = value && typeof colorArr[value] !== 'undefined' ? colorArr[value] : 'primary';
                    var statusnameArr = {'0': '启用', '1': '禁用'};
                    //渲染状态
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(statusnameArr[value]) + '</span>';
                    return html;
	        	}
	        }
        }
    };
    return Controller;
});