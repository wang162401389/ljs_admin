<?php

namespace app\admin\model;

use think\Model;

class Banner extends Model
{
    // 表名
    protected $table = 'AppHomeBanner';
    
    // 追加属性
    protected $append = [
        'type_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['sort' => $row[$pk]]);
        });
    }

    
    public function getTypeList()
    {
        return ['0' => __('Type 0'), '1' => __('Type 1'), '2' => __('Type 2')];
    }


    public function getTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['type'];
        $list = $this->getTypeList();
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