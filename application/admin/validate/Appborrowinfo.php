<?php

namespace app\admin\validate;

use think\Validate;

class Appborrowinfo extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'borrowDuration' => '>:0',
        'borrowMoney' => '>:0',
        'orderInterestRate' => '>:0',
        'addInterestRate' => '>=:0',
        'collectDay' => '>:0',
        'borrowMin' => '>:0',
        'borrowMax' => '>:0',
        'serviceCharge' => '>:0',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        //'borrowMin' => '最低投标金额必须大于0且被标的总额整除',
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => [],
        'edit' => [],
    ];
    
    // 自定义验证规则
    protected function checkBorrowMin($value,$data)
    {
        return $value > 0 && !($data['borrowMoney'] % $value) ? true : false;
    }
}
