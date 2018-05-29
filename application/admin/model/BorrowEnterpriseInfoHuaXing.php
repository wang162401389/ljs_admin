<?php

namespace app\admin\model;

use think\Model;

class BorrowEnterpriseInfoHuaXing extends Model
{
    // 表名
    protected $table = 'BorrowEnterpriseInfoHuaXing';
    
    // 追加属性
    protected $append = [

    ];

    public function borrowuser()
    {
        return $this->hasOne('Borrower', 'borrowUserId', 'borrowUserId', [], 'LEFT')->setEagerlyType(0);
    }
}
