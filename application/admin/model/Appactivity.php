<?php

namespace app\admin\model;

use think\Model;

class Appactivity extends Model
{
    // 表名
    protected $table = 'AppActivity';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [

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
        return ['1' => __('Normal'), '0' => __('Hidden')];
    }
    
    /**
     * 设置图片
     * @param unknown $value
     * @return string
     */
    public function setImgUrlAttr($value)
    {
        return \think\Config::get('upload')['cdnurl'].$value;
    }
    
    /**
     * 获取图片
     * @param unknown $value
     * @return string
     */
    public function getImgUrlAttr($value)
    {
        return str_replace(\think\Config::get('upload')['cdnurl'], '', $value);
    }
}