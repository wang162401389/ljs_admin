<?php

namespace app\admin\model;

use think\Model;

class OperateReport extends Model
{
    // 表名
    protected $table = 'AppOperatReport';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [

    ];
    
    /**
     * 设置图片
     * @param unknown $value
     * @return string
     */
    public function setBackgroundImgAttr($value)
    {
        return \think\Config::get('upload')['cdnurl'].$value;
    }
    
    /**
     * 获取图片
     * @param unknown $value
     * @return string
     */
    public function getBackgroundImgAttr($value)
    {
        return str_replace(\think\Config::get('upload')['cdnurl'], '', $value);
    }
}