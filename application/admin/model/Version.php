<?php

namespace app\admin\model;

use think\Model;

class Version extends Model
{
//     // 表名
//     protected $table = 'AppVersionUpgrade';
    
//     // 自动写入时间戳字段
//     protected $autoWriteTimestamp = 'datetime';

//     // 定义时间戳字段名
//     protected $createTime = 'publishDate';
//     protected $updateTime = false;
    
//     // 追加属性
//     protected $append = [
//         'force_text',
//         'auditStatus_text',
//         'status_text'
//     ];
    
//     public function getForceList()
//     {
//         return ['0' => __('Force 0'), '1' => __('Force 1')];
//     }

//     public function getAuditstatusList()
//     {
//         return ['0' => __('Auditstatus 0'), '1' => __('Auditstatus 1'), '2' => __('Auditstatus 2')];
//     }

//     public function getStatusList()
//     {
//         return ['0' => __('Status 0'), '1' => __('Status 1')];
//     }

//     public function getForceTextAttr($value, $data)
//     {        
//         $value = $value ? $value : $data['force'];
//         $list = $this->getForceList();
//         return isset($list[$value]) ? $list[$value] : '';
//     }


//     public function getAuditstatusTextAttr($value, $data)
//     {        
//         $value = $value ? $value : $data['auditStatus'];
//         $list = $this->getAuditstatusList();
//         return isset($list[$value]) ? $list[$value] : '';
//     }


//     public function getStatusTextAttr($value, $data)
//     {        
//         $value = $value ? $value : $data['status'];
//         $list = $this->getStatusList();
//         return isset($list[$value]) ? $list[$value] : '';
//     }
}