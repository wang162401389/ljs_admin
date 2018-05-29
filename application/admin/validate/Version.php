<?php

namespace app\admin\validate;

use think\Validate;

class Version extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'versionCode' => '>=:0',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'versionCode' => '版本号不能小于0',
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => [],
        'edit' => [],
    ];
    
}
