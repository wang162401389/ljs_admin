<?php

namespace app\admin\model;

use think\Model;

class Smstemplate extends Model
{
    // 表名
    protected $table = 'SmsTemplate';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'type_text',
        'status_text'
    ];
    
    protected $type = [
        'restrict' => 'json'
    ];
    
    public function setRestrictAttr($value)
    {
        return !empty($value) ? json_encode($value) : '';
    }
    
    public function getTypeList()
    {
        return ['0' => __('Type 0'), '1' => __('Type 1')];
    }     

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }     


    public function getTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['type'];
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
}