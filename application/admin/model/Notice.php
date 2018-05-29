<?php

namespace app\admin\model;

use think\Model;

class Notice extends Model
{
    // 表名
    protected $name = 'notice';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'status_text'
    ];
    
    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    public function getStatusList()
    {
        return ['0' => __('Hidden'),'1' => __('Normal')];
    }

    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    /**
     * 设置图片
     * @param unknown $value
     * @return string
     */
    public function setImageAttr($value)
    {
        return \think\Config::get('upload')['cdnurl'].$value;
    }
    
    /**
     * 获取图片
     * @param unknown $value
     * @return string
     */
    public function getImageAttr($value)
    {
        return str_replace(\think\Config::get('upload')['cdnurl'], '', $value);
    }

    public function column()
    {
        return $this->belongsTo('NoticeColumn', 'column_id')->setEagerlyType(0);
    }
}