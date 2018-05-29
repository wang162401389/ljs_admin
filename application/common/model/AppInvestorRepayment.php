<?php
namespace app\common\model;
use think\Model;
use think\Db;

class AppInvestorRepayment extends Model
{
    //表名
    protected $table = 'AppInvestorRepayment';
    
    public static function insert_repayment_record($item, $borrow_info, $deadline_last) {
        switch ($borrow_info->investInterestType) 
        {
            case 1:
                //按天到期还款
                
                $data['borrowInvestorId'] = $item->id;
                //标id
                $data['borrowInfoId'] = $item->borrowInfoId;
                //投资人ID
                $data['investorUid'] = $item->investorUid;
                //借款人ID
                $data['borrowUid'] = $borrow_info->borrowUid;
                //本金
                $data['capital'] = $item->investorCapital;
                //利息
                $data['interest'] = $borrow_info->borrowInterestRate * $item->investorCapital * $borrow_info->borrowDuration / 36000;
                //平台加息
                $data['interestCcfax'] = $borrow_info->addInterestRate * $item->investorCapital * $borrow_info->borrowDuration / 36000;
                //加息券加息
                $data['interestCoupons'] = $item->interestCouponsRate * $item->investorCapital * $borrow_info->borrowDuration / 36000 / 100;
                //当前期数
                $data['curPeriods'] = 1;
                //总期数
                $data['totalPeriods'] = 1;
                //待还款时间
                $data['deadline'] = date('Y-m-d H:i:s', $deadline_last);
                //创建时间
                $data['createTime'] = date('Y-m-d H:i:s');
                
                Db::table('AppInvestorRepayment')->insert($data);
                
                break;
                
            case 2:
                //按月等额本息
                
                $monthDataDetail['money'] = $item->investorCapital / 100;
                $monthDataDetail['year_apr'] = $borrow_info->borrowInterestRate;
                $monthDataDetail['duration'] = $borrow_info->borrowDuration;
                $repay_list = equal_month($monthDataDetail);
                
                $jx_repay_list = $coupons_repay_list = [];
                if ($borrow_info->addInterestRate > 0) 
                {
                    $monthDataDetail['year_apr'] = $borrow_info->addInterestRate;
                    $jx_repay_list = equal_month($monthDataDetail);
                }
                
                if ($item->interestCouponsRate > 0)
                {
                    $monthDataDetail['year_apr'] = $item->interestCouponsRate / 100;
                    $coupons_repay_list = equal_month($monthDataDetail);
                }
                
                if (!empty($repay_list)) 
                {
                    foreach ($repay_list as $k => $v) 
                    {
                        $data = [];
                        $data['borrowInvestorId'] = $item->id;
                        //标id
                        $data['borrowInfoId'] = $item->borrowInfoId;
                        //投资人ID
                        $data['investorUid'] = $item->investorUid;
                        //借款人ID
                        $data['borrowUid'] = $borrow_info->borrowUid;
                        //本金
                        $data['capital'] = $v['capital'] * 100;
                        //利息
                        $data['interest'] = $v['interest'] * 100;
                        //平台加息
                        $data['interestCcfax'] = !empty($jx_repay_list) ? $jx_repay_list[$k]['interest'] * 100 : 0;
                        //加息券加息
                        $data['interestCoupons'] = !empty($coupons_repay_list) ? $coupons_repay_list[$k]['interest'] * 100 : 0;
                        //当前期数
                        $data['curPeriods'] = $k + 1;
                        //总期数
                        $data['totalPeriods'] = $borrow_info->borrowDuration;
                        //待还款时间
                        $data['deadline'] = date('Y-m-d H:i:s', $v['repayment_time']);
                        //创建时间
                        $data['createTime'] = date('Y-m-d H:i:s');
                        
                        Db::table('AppInvestorRepayment')->insert($data);
                    }
                }
                
                break;
                
            case 3:
                //按季分期还款（等额本息）
                
                $monthDataDetail['month_times'] = $borrow_info->borrowDuration;
                $monthDataDetail['account'] = $item->investorCapital / 100;
                $monthDataDetail['year_apr'] = $borrow_info->borrowInterestRate;
                $repay_list = equal_season($monthDataDetail);
                
                $jx_repay_list = $coupons_repay_list = [];
                if ($borrow_info->addInterestRate > 0)
                {
                    $monthDataDetail['year_apr'] = $borrow_info->addInterestRate;
                    $jx_repay_list = equal_season($monthDataDetail);
                }
                
                if ($item->interestCouponsRate > 0)
                {
                    $monthDataDetail['year_apr'] = $item->interestCouponsRate / 100;
                    $coupons_repay_list = equal_season($monthDataDetail);
                }
                
                if (!empty($repay_list))
                {
                    foreach ($repay_list as $k => $v)
                    {
                        $data = [];
                        $data['borrowInvestorId'] = $item->id;
                        //标id
                        $data['borrowInfoId'] = $item->borrowInfoId;
                        //投资人ID
                        $data['investorUid'] = $item->investorUid;
                        //借款人ID
                        $data['borrowUid'] = $borrow_info->borrowUid;
                        //本金
                        $data['capital'] = $v['capital'] * 100;
                        //利息
                        $data['interest'] = $v['interest'] * 100;
                        //平台加息
                        $data['interestCcfax'] = !empty($jx_repay_list) ? $jx_repay_list[$k]['interest'] * 100 : 0;
                        //加息券加息
                        $data['interestCoupons'] = !empty($coupons_repay_list) ? $coupons_repay_list[$k]['interest'] * 100 : 0;
                        //当前期数
                        $data['curPeriods'] = $k + 1;
                        //总期数
                        $data['totalPeriods'] = $borrow_info->borrowDuration;
                        //待还款时间
                        $data['deadline'] = date('Y-m-d H:i:s', $v['repayment_time']);
                        //创建时间
                        $data['createTime'] = date('Y-m-d H:i:s');
                        
                        Db::table('AppInvestorRepayment')->insert($data);
                    }
                }
                
                break;
                
            case 4:
                //每月还息到期还本
                
                $monthDataDetail['month_times'] = $borrow_info->borrowDuration;
                $monthDataDetail['account'] = $item->investorCapital / 100;
                $monthDataDetail['year_apr'] = $borrow_info->borrowInterestRate;
                $repay_list = equal_end_month($monthDataDetail);
                
                $jx_repay_list = $coupons_repay_list = [];
                if ($borrow_info->addInterestRate > 0)
                {
                    $monthDataDetail['year_apr'] = $borrow_info->addInterestRate;
                    $jx_repay_list = equal_end_month($monthDataDetail);
                }
                
                if ($item->interestCouponsRate > 0)
                {
                    $monthDataDetail['year_apr'] = $item->interestCouponsRate / 100;
                    $coupons_repay_list = equal_end_month($monthDataDetail);
                }
                
                if (!empty($repay_list))
                {
                    foreach ($repay_list as $k => $v)
                    {
                        $data = [];
                        $data['borrowInvestorId'] = $item->id;
                        //标id
                        $data['borrowInfoId'] = $item->borrowInfoId;
                        //投资人ID
                        $data['investorUid'] = $item->investorUid;
                        //借款人ID
                        $data['borrowUid'] = $borrow_info->borrowUid;
                        //本金
                        $data['capital'] = $v['capital'] * 100;
                        //利息
                        $data['interest'] = $v['interest'] * 100;
                        //平台加息
                        $data['interestCcfax'] = !empty($jx_repay_list) ? $jx_repay_list[$k]['interest'] * 100 : 0;
                        //加息券加息
                        $data['interestCoupons'] = !empty($coupons_repay_list) ? $coupons_repay_list[$k]['interest'] * 100 : 0;
                        //当前期数
                        $data['curPeriods'] = $k + 1;
                        //总期数
                        $data['totalPeriods'] = $borrow_info->borrowDuration;
                        //待还款时间
                        $data['deadline'] = date('Y-m-d H:i:s', $v['repayment_time']);
                        //创建时间
                        $data['createTime'] = date('Y-m-d H:i:s');
                        
                        Db::table('AppInvestorRepayment')->insert($data);
                    }
                }
                
                break;
                
            case 5:
                //一次性还款
                
                $monthDataDetail['month_times'] = $borrow_info->borrowDuration;
                $monthDataDetail['account'] = $item->investorCapital / 100;
                $monthDataDetail['year_apr'] = $borrow_info->borrowInterestRate;
                $monthDataDetail['type'] = "all";
                $repay_list = equal_end_month_only($monthDataDetail);
                
                $jx_repay_list = $coupons_repay_list = [];
                if ($borrow_info->addInterestRate > 0)
                {
                    $monthDataDetail['year_apr'] = $borrow_info->addInterestRate;
                    $jx_repay_list = equal_end_month_only($monthDataDetail);
                }
                
                if ($item->interestCouponsRate > 0)
                {
                    $monthDataDetail['year_apr'] = $item->interestCouponsRate / 100;
                    $coupons_repay_list = equal_end_month_only($monthDataDetail);
                }
                
                if (!empty($repay_list))
                {
                    $data['borrowInvestorId'] = $item->id;
                    //标id
                    $data['borrowInfoId'] = $item->borrowInfoId;
                    //投资人ID
                    $data['investorUid'] = $item->investorUid;
                    //借款人ID
                    $data['borrowUid'] = $borrow_info->borrowUid;
                    //本金
                    $data['capital'] = $repay_list['capital'] * 100;
                    //利息
                    $data['interest'] = $repay_list['interest'] * 100;
                    //平台加息
                    $data['interestCcfax'] = $jx_repay_list['interest'] * 100;
                    //加息券加息
                    $data['interestCoupons'] = $coupons_repay_list['interest'] * 100;
                    //当前期数
                    $data['curPeriods'] = 1;
                    //总期数
                    $data['totalPeriods'] = 1;
                    //待还款时间
                    $data['deadline'] = date('Y-m-d H:i:s', $deadline_last);
                    //创建时间
                    $data['createTime'] = date('Y-m-d H:i:s');
                    
                    Db::table('AppInvestorRepayment')->insert($data);
                }
                
                break;
                
            case 7:
                //按月等本降息
                
                $monthDataDetail['money'] = $item->investorCapital / 100;
                $monthDataDetail['year_apr'] = $borrow_info->borrowInterestRate;
                $monthDataDetail['duration'] = $borrow_info->borrowDuration;
                $repay_list = equal_month_cut_interest($monthDataDetail);
                
                $jx_repay_list = $coupons_repay_list = [];
                if ($borrow_info->addInterestRate > 0)
                {
                    $monthDataDetail['year_apr'] = $borrow_info->addInterestRate;
                    $jx_repay_list = equal_month_cut_interest($monthDataDetail);
                }
                
                if ($item->interestCouponsRate > 0)
                {
                    $monthDataDetail['year_apr'] = $item->interestCouponsRate / 100;
                    $coupons_repay_list = equal_month_cut_interest($monthDataDetail);
                }
                
                if (!empty($repay_list))
                {
                    foreach ($repay_list as $k => $v)
                    {
                        $data = [];
                        $data['borrowInvestorId'] = $item->id;
                        //标id
                        $data['borrowInfoId'] = $item->borrowInfoId;
                        //投资人ID
                        $data['investorUid'] = $item->investorUid;
                        //借款人ID
                        $data['borrowUid'] = $borrow_info->borrowUid;
                        //本金
                        $data['capital'] = $v['capital'] * 100;
                        //利息
                        $data['interest'] = $v['interest'] * 100;
                        //平台加息
                        $data['interestCcfax'] = !empty($jx_repay_list) ? $jx_repay_list[$k]['interest'] * 100 : 0;
                        //加息券加息
                        $data['interestCoupons'] = !empty($coupons_repay_list) ? $coupons_repay_list[$k]['interest'] * 100 : 0;
                        //当前期数
                        $data['curPeriods'] = $k + 1;
                        //总期数
                        $data['totalPeriods'] = $borrow_info->borrowDuration;
                        //待还款时间
                        $data['deadline'] = date('Y-m-d H:i:s', $v['repayment_time']);
                        //创建时间
                        $data['createTime'] = date('Y-m-d H:i:s');
                        
                        Db::table('AppInvestorRepayment')->insert($data);
                    }
                }
                
                break;
        }     
    }
}