<?php

namespace app\admin\model;

use think\Model;

class ChannelMenu extends Model
{
    // 表名
    protected $table = 'AppChannelMenuBar';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'channel_type_text','isjump_text'
    ];
    
    public static function getChannelTypeList()
    {
        return ['1' => __('Channeltype 1'), '2' => __('Channeltype 2'), '3' => __('Channeltype 3')];
    }
    
    public function getChannelTypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['channelType'];
        $list = $this->getChannelTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public static function getIsjumpList()
    {
        return ['0' => __('Isjump 0'),'1' => __('Isjump 1')];
    }
    
    public function getIsjumpTextAttr($value, $data)
    {
        $value = $value ? $value : $data['isJump'];
        $list = $this->getIsjumpList();
        return isset($list[$value]) ? $list[$value] : '';
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