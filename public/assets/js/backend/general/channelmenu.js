define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
        	Table.config.dragsortfield = 'sort',
        	
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/channelmenu/index',
                    add_url: 'general/channelmenu/add',
                    edit_url: 'general/channelmenu/edit',
                    del_url: 'general/channelmenu/del',
                    multi_url: 'general/channelmenu/multi',
                    table: 'AppChannelMenuBar',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'sort',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'channelType', title: __('Channeltype'), visible:false, searchList: {'1':__('Channeltype 1'),"2":__('Channeltype 2'),'3':__('Channeltype 3')}},
                        {field: 'channel_type_text', title: __('Channeltype'), operate:false},
                        {field: 'imgUrl', title: __('Imgurl'), formatter: Table.api.formatter.image, operate:false},
                        {field: 'jumpUrl', title: __('Jumpurl'), formatter: Table.api.formatter.url, operate:false},
                        {field: 'sort', title: __('Sort'), operate:false},
                        {field: 'status', title: __('Status'), formatter: Controller.api.formatter.status, searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}},
                        {field: 'summary', title: __('Summary'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'isJump', title: __('isJump'), visible:false, searchList: {"0":__('isJump 0'),'1':__('isJump 1')}},
                        {field: 'isjump_text', title: __('isJump'), operate:false},
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
            },
	        formatter : {
	        	status : function (value, row, index) {
					//颜色状态数组,可使用red/yellow/aqua/blue/navy/teal/olive/lime/fuchsia/purple/maroon
                    var colorArr = {0: 'grey', 1: 'success'};
                    //如果字段列有定义custom
                    if (typeof this.custom !== 'undefined') {
                        colorArr = $.extend(colorArr, this.custom);
                    }
                    value = value === null ? '' : value.toString();
                    var color = value && typeof colorArr[value] !== 'undefined' ? colorArr[value] : 'primary';
                    var statusnameArr = {'0': __('Status 0'), '1': __('Status 1'), '2': __('Status 2')};
                    //渲染状态
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(statusnameArr[value]) + '</span>';
                    return html;
	        	}
	        }
        }
    };
    return Controller;
});