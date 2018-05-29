define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
        index: function () {
            $(document).on('click', "#grant", function () {
            	if($("#uids").val() == ''){
            		Layer.alert('发放用户不能为空');
            		return;
            	}

            	var str = $("#uids").val(); 
            	var strs = new Array();
            	
        		str = str.replace(new RegExp('^\\;+|\\;+$', 'g'), '');
            	strs = str.split(";");
            	for (i = 0; i < strs.length; i++) 
            	{ 
            		if(isNaN(strs[i])){
                		Layer.alert('发放用户为用户ID或手机号');
                		return;
                	}
            	}
            	
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
            
            var refresh_type = function () {
                if ($('input[name=type]:checked').val() == 'touzi') {
                    $(".show_name").html('投资');
                    $(".show_unit").html('金额（元）');
                } else {
                    $(".show_name").html('加息');
                    $(".show_unit").html('利率（%）');
                }
            };
            
            $(document).on('change', 'input[name=type]', function () {
                refresh_type();
            });
            
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});