<?php

namespace app\admin\model;

use think\Model;

class Appinvestorrecord extends Model
{
    // 表名
    protected $table = 'AppInvestorRecord';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'borrow_status_text',
        'pay_channel_type_text',
    ];
    
    public function binfo()
    {
        return $this->belongsTo('app\common\model\Appborrowinfo', 'borrowInfoId', 'borrowInfoId', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function user()
    {
        return $this->belongsTo('AppUser', 'userId', 'userId', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function getBorrowStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['borrowStatus'];
        $list = $this->getBorrowStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public static function getBorrowStatusList()
    {
        return [
            '0' => __('Borrowstatus 0'), 
            '1' => __('Borrowstatus 1'), 
            '2' => __('Borrowstatus 2'), 
            '3' => __('Borrowstatus 3'),
            '4' => __('Borrowstatus 4'),
            '5' => __('Borrowstatus 5'),
        ];
    }

    public static function getPayChannelTypeList()
    {
        return ['1' => __('Paychanneltype 1'), '2' => __('Paychanneltype 2'), '3' => __('Paychanneltype 3')];
    }
    
    public function getPayChannelTypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['payChannelType'];
        $list = $this->getPayChannelTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    /**
     * 获取投资本金
     * @param unknown $value
     * @return number
     */
    public function getInvestorCapitalAttr($value)
    {
        return $value / 100;
    }
    
    /**
     * 获取抵扣金额
     * @param unknown $value
     * @return number
     */
    public function getDeductibleMoneyAttr($value)
    {
        return $value / 100;
    }
    
    /**
     * 获取加息劵率
     * @param unknown $value
     * @return number
     */
    public function getInterestCcfaxRateAttr($value)
    {
        return $value / 100;
    }
    
    /**
     * 获取预期收益
     * @param unknown $value
     * @return number
     */
    public function getEarningsAttr($value)
    {
        return $value / 100;
    }
}
