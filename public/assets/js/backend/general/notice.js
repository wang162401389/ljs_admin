define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/notice/index',
                    add_url: 'general/notice/add',
                    edit_url: 'general/notice/edit',
                    del_url: 'general/notice/del',
                    multi_url: 'general/notice/multi',
                    table: 'notice',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'publish_username', title: '发布人姓名', formatter: Table.api.formatter.search},
                        {field: 'column.name', title: '栏目', formatter: Table.api.formatter.search},
                        {field: 'title', title: __('Title'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'source', title: __('Source'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'image', title: __('Image'), formatter: Table.api.formatter.image, operate:false},
                        {field: 'keywords', title: __('Keywords'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'weigh', title: __('Weigh'), operate:false},
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
            },
	        formatter : {
	        	status : function (value, row, index) {
					//颜色状态数组,可使用red/yellow/aqua/blue/navy/teal/olive/lime/fuchsia/purple/maroon
                    var colorArr = {1: 'success', 0: 'grey'};
                    //如果字段列有定义custom
                    if (typeof this.custom !== 'undefined') {
                        colorArr = $.extend(colorArr, this.custom);
                    }
                    value = value === null ? '' : value.toString();
                    var color = value && typeof colorArr[value] !== 'undefined' ? colorArr[value] : 'primary';
                    var statusnameArr = {'0': '隐藏', '1': '正常'};
                    //渲染状态
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i> ' + __(statusnameArr[value]) + '</span>';
                    return html;
	        	}
	        }
        }
    };
    return Controller;
});