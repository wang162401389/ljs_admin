<?php

namespace app\admin\model;

use think\Model;

class BorrowEnterpriseInfoSina extends Model
{
    // 表名
    protected $table = 'BorrowEnterpriseInfoSina';
    
    // 追加属性
    protected $append = [

    ];

    public function borrowuser()
    {
        return $this->hasOne('Borrower', 'borrowUserId', 'borrowUserId', [], 'LEFT')->setEagerlyType(0);
    }
}
