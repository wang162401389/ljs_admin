<?php

namespace app\admin\model;

use think\Model;

class Appcoupon extends Model
{
    // 表名
    protected $table = 'AppCoupon';
    
    // 追加属性
    protected $append = [
        'grant_man_name'
    ];
    
    public function getGrantManNameAttr($value, $data)
    {
        $name = '系统派送';
        if ($data['grantMan'])
        {
            $name = \think\Db::table('admin')->where('id', $data['grantMan'])->value('username');
        }
        return $name;
    }
    
    /**
     * 获取抵扣额
     * @param unknown $value
     * @return number
     */
    public function getMoneyAttr($value)
    {
        return $value / 100;
    }
}
