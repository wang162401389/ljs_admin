<?php
namespace app\common\model;
use think\Model;

class AppBorrowRepayment extends Model
{
    // 表名
    protected $table = 'AppBorrowRepayment';
    
    public function borrow()
    {
        return $this->belongsTo('Appborrowinfo', 'borrowInfoId', 'borrowInfoId', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function borrower()
    {
        return $this->belongsTo('app\admin\model\Borrower', 'userId', 'borrowUserId', [], 'LEFT')->setEagerlyType(0);
    }
    
    /**
     * 获取利息
     * @param unknown $value
     * @return number
     */
    public function getInterestAttr($value)
    {
        return $value / 100;
    }
    
    /**
     * 获取本金
     * @param unknown $value
     * @return number
     */
    public function getCapitalAttr($value)
    {
        return $value / 100;
    }
    
    /**
     * 获取服务费
     * @param unknown $value
     * @return number
     */
    public function getBorrowFeeAttr($value)
    {
        return $value / 100;
    }
}