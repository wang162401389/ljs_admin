define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
        	Table.config.dragsortfield = 'sort',
        	
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/banner/index',
                    add_url: 'general/banner/add',
                    edit_url: 'general/banner/edit',
                    del_url: 'general/banner/del',
                    multi_url: 'general/banner/multi',
                    table: 'AppHomeBanner',
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
                        {field: 'type', title: __('Type'), visible:false, searchList: {'0':__('Type 0'),"1":__('Type 1'),"2":__('Type 2')}},
                        {field: 'type_text', title: __('Type'), operate:false},
                        {field: 'imgUrl', title: __('imgUrl'), formatter: Table.api.formatter.image, operate:false},
                        {field: 'summary', title: __('summary'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'jumpUrl', title: __('jumpUrl'), formatter: Table.api.formatter.url, operate:false},
                        {field: 'sort', title: __('sort'), operate:false},
                        {field: 'status', title: __('Status'), formatter: Controller.api.formatter.status, searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
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