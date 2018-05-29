<?php

namespace app\admin\model;

use think\Model;

class AppTransactionFlowing extends Model
{
    // 表名
    protected $table = 'AppTransactionFlowing';
    
    // 追加属性
    protected $append = [
        'payChannelType_text',
        'flowingType_text',
        'transactionStatus_text',
        'transactionType_text'
    ];
    
    public function getPaychanneltypeList()
    {
        return ['1' => __('Paychanneltype 1'), '2' => __('Paychanneltype 2'), '3' => __('Paychanneltype 3')];
    }
    
    public function getFlowingtypeList()
    {
        return ['3) unsigne' => __('3) unsigne')];
    }
    
    public function getTransactionstatusList()
    {
        return ['1' => __('Transactionstatus 1'), '2' => __('Transactionstatus 2'), '3' => __('Transactionstatus 3')];
    }
    
    public function getTransactiontypeList()
    {
        return ['3) unsigne' => __('3) unsigne')];
    }
    
    public function getPaychanneltypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['payChannelType'];
        $list = $this->getPaychanneltypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getFlowingtypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['flowingType'];
        $list = $this->getFlowingtypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getTransactionstatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['transactionStatus'];
        $list = $this->getTransactionstatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getTransactiontypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['transactionType'];
        $list = $this->getTransactiontypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function appborrowinfo()
    {
        return $this->belongsTo('app\common\model\Appborrowinfo', 'borrowInfoId', 'borrowInfoId', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function appuser()
    {
        return $this->belongsTo('AppUser', 'userId', 'userId', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function coupon()
    {
        return $this->hasOne('Appcoupon', 'id', 'couponsIdTz', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function borrower()
    {
        return $this->belongsTo('Borrower', 'userId', 'borrowUserId', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function repayment()
    {
        return $this->belongsTo('app\common\model\AppBorrowRepayment', 'innerOrderId');
    }
    
    /**
     * 获取交易金额
     * @param unknown $value
     * @return number
     */
    public function getTransactionAmtAttr($value)
    {
        return $value / 100;
    }
}