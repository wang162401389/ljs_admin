define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'luckydraw/index',
                    add_url: '',
                    edit_url: '',
                    del_url: 'luckydraw/del',
                    multi_url: '',
                    table: '',
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
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'user_id', title: __('中奖用户ID'), operate: false},
                        {
                            field: 'username', title: __('中奖用户'), operate: 'LIKE %...%',
                            placeholder: '模糊搜索'
                        },
                        {
                            field: 'price',
                            sortable: false,
                            title: __('中奖名称'),
                            operate: 'LIKE %...%',
                            placeholder: '模糊搜索'
                        },
                        {field: 'rank', title: __('奖品等级'), operate: false},
                        {
                            field: 'createtime',
                            title: __('抽奖时间'),
                            formatter: Table.api.formatter.datetime,
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            sortable: true
                        },
                        {
                            field: 'redeemtime',
                            title: __('兑换时间'),
                            formatter: Table.api.formatter.datetime,
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            sortable: true
                        },
                        {
                            field: 'status',
                            title: __("状态"),
                            formatter: Controller.api.formatter.status,
                            searchList: {'0': __('未兑换'), '1': __('已兑换'), '2': __('作废')}
                        },
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    title: __('兑换'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-magic',
                                    url: 'luckydraw/exchange',
                                    success: function (data, ret) {
                                        if (ret.code == 1) {
                                            Layer.alert(ret.msg);
                                            setTimeout(function () {
                                                window.location.reload();
                                            }, 2000);
                                        }else{
                                            Layer.alert(ret.msg);
                                        }
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                status: function (value, row, index) {
                    var text = '未兑换';
                    if (value == 1) {
                        text = '已兑换';
                    } else if (value == 2) {
                        text = '<span style="color: #FD482C">已作废</span>';
                    }
                    return text;
                }
            }
        }
    };
    return Controller;
});