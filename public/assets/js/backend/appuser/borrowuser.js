define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'appuser/borrowuser/index',
                    add_url: 'appuser/borrowuser/add',
                    edit_url: 'appuser/borrowuser/edit',
                    del_url: 'appuser/borrowuser/del',
                    multi_url: 'appuser/borrowuser/multi',
                    table: 'BorrowUser',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'borrowUserId',
                columns: [
                    [
                        {field: 'u.borrowUserId', title: __('Borrowuserid')},
                        {field: 'u.userName', title: __('Username'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'u.realName', title: __('RealName'), operate: 'LIKE %...%', placeholder: '模糊搜索'},
                        {field: 'u.userType', title: __('Usertype'), visible:false, searchList: {"0":__('Usertype 0'),"1":__('Usertype 1'),"2":__('Usertype 2')}},
                        {field: 'user_type_text', title: __('Usertype'), operate:false},
                        {field: 'u.regChannel', title: __('RegChannel'), formatter: Table.api.formatter.search},
                        {field: 'u.isSina', title: __('IsSina'), visible:false, searchList: {"0":__('IsSina 0'),"1":__('IsSina 1'),"2":__('IsSina 2'),"3":__('IsSina 3')}},
                        {field: 'is_sina_text', title: __('IsSina'), operate:false},
                        {field: 'u.isHuaxing', title: __('IsHuaxing'), visible:false, searchList: {"0":__('IsHuaxing 0'),"1":__('IsHuaxing 1'),"2":__('IsHuaxing 2'),"3":__('IsHuaxing 3')}},
                        {field: 'is_huaxing_text', title: __('IsHuaxing'), operate:false},
                        {field: 'u.createTime', title: __('Regtime'), operate:'RANGE', addclass:'datetimerange', sortable: true},
                        {field: 'total', title: '用户累计借款金额', operate: 'BETWEEN', sortable: true},
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
            }
        }
    };
    return Controller;
});