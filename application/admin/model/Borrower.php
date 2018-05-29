<?php

namespace app\admin\model;

use think\Model;

class Borrower extends Model
{
    // 表名
    protected $table = 'BorrowUser';
    
    // 追加属性
    protected $append = [
        'user_type_text',
        'is_sina_text',
        'is_huaxing_text',
        'is_loan_text',
        'reg_channel_text'
    ];
    
    public static function getRegChannellist()
    {
        return [
            '1' => __('RegChannel 1'),
            '2' => __('RegChannel 2'),
            '3' => __('RegChannel 3'),
            '4' => __('RegChannel 4'),
            '5' => __('RegChannel 5'),
            '6' => __('RegChannel 6'),
            '7' => __('RegChannel 7'),
            '8' => __('RegChannel 8'),
        ];
    }
    
    public function getRegChannelTextAttr($value, $data)
    {
        $value = $value ? $value : $data['regChannel'];
        $list = $this->getRegChannellist();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getUserTypeList()
    {
        return ['0' => __('UserType 0'), '1' => __('UserType 1'), '2' => __('UserType 2')];
    }
    
    public function getUserTypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['userType'];
        $list = $this->getUserTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getIsSinaList()
    {
        return ['0' => __('IsSina 0'), '1' => __('IsSina 1'), '2' => __('IsSina 2'), '3' => __('IsSina 3')];
    }
    
    public function getIsSinaTextAttr($value, $data)
    {
        $value = $value ? $value : $data['isSina'];
        $list = $this->getIsSinaList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getIsHuaxingList()
    {
        return ['0' => __('IsHuaxing 0'), '1' => __('IsHuaxing 1'), '2' => __('IsHuaxing 2'), '3' => __('IsHuaxing 3')];
    }
    
    public function getIsHuaxingTextAttr($value, $data)
    {
        $value = $value ? $value : $data['isHuaxing'];
        $list = $this->getIsHuaxingList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getIsLoanList()
    {
        return ['1' => __('Isloan 1'), '0' => __('Isloan 0')];
    }

    public function getIsLoanTextAttr($value, $data)
    {
        $value = $value ? $value : $data['isLoan'];
        $list = $this->getIsLoanList();
        return isset($list[$value]) ? $list[$value] : '';
    }
}
