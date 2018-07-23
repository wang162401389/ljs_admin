define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
        index: function () {
        	$(document).on('click', ".btn-command", function () {
        		if($("#pushtext").val() == ''){
            		Layer.alert('推送内容不能为空');
            		return;
            	}
            });
            $(document).on('click', ".btn-execute", function () {
            	if($("#pushtext").val() == ''){
            		Layer.alert('推送内容不能为空');
            		return;
            	}
            });
            $(document).on('click', "#grant", function () {

            	var str = $("#uids").val(); 
            	
            	var confirm = Layer.confirm(
                    '确定发放吗？',
                    {icon: 3, title: __('Warning'), shadeClose: true},
                    function () {
                    	$.post("operate/grantcoupon/grant", {data : $("#add-form").serializeArray()}, function (ret) {
                            var msg = ret.hasOwnProperty("msg") && ret.msg != "" ? ret.msg : "";
                            if (ret.code == 1) {
                            	Layer.close(confirm);
                                Backend.api.toastr.success('发放成功！');
                            } else {
                            	Layer.alert(msg);
                                //Backend.api.toastr.error(msg ? msg : __('Operation failed'));
                            }
                        }, 'json');
                    }
                );
            });
            
            $(document).on('change', 'select[name=select_event]', function () {
            	var selected = $("#select_event option:selected").val();
            	
            	var day_input = $.inArray(selected, ['reg', 'coupon', 'recepay']);
            	
            	if (day_input > -1) {
                    $(".day-input").show();
                } else {
                    $(".day-input").hide();
                }
            });
            
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});