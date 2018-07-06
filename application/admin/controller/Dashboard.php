<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use think\Config;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{
    /**
     * 查看
     */
    public function index()
    {
        $seventtime = \fast\Date::unixtime('day', -7);
        $investlist = $withdrawlist = $chargelist = [];
        for ($i = 0; $i < 7; $i++)
        {
            $day = date("Y-m-d", $seventtime + ($i * 86400));
            $charge_num = Db::table('AppTransactionFlowing')
                            ->where('payChannelType', 2)
                            ->where('transactionType', 1)
                            ->where('transactionStatus', 2)
                            ->whereTime('payTime', 'between', [$day.' 00:00:00', $day.' 23:59:59'])
                            ->sum('transactionAmt');
            $chargelist[$day] = $charge_num * 0.01;
            
            $withdraw_num = Db::table('AppTransactionFlowing')
                            ->where('payChannelType', 2)
                            ->where('transactionType', 2)
                            ->where('transactionStatus', 2)
                            ->whereTime('payTime', 'between', [$day.' 00:00:00', $day.' 23:59:59'])
                            ->sum('transactionAmt');
            $withdrawlist[$day] = $withdraw_num * 0.01;
            
            $invest_num = Db::table('AppTransactionFlowing')
                        ->where('payChannelType', 2)
                        ->where('transactionType', 3)
                        ->where('transactionStatus', 2)
                        ->whereTime('payTime', 'between', [$day.' 00:00:00', $day.' 23:59:59'])
                        ->sum('transactionAmt');
            $investlist[$day] = $invest_num * 0.01;
        }
        
        $total = Db::table('AppUser')->count('userId');
        $new_count = Db::table('AppUser')->whereTime('createdTime', '-7 days')->count('userId');
        
        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
  
        $this->view->assign([
            'first_verify_count' => Db::table('AppBorrowInfo')->where('borrowStatus', 0)->count('borrowInfoId'),
            'second_verify_count' => Db::table('AppBorrowInfo')->where('borrowStatus', 4)->count('borrowInfoId'),
            'repayment_apply_count' => Db::table('AppBorrowRepayment')->where('advanceRepayment', 1)->count('id'),
            'referee_verify_count' => Db::table('referee_log')->where('status', 0)->count('id'),
            'todayusersignup' => Db::table('AppUser')->whereTime('createdTime', 'd')->count('userId'),
            'todayorder' => Db::table('AppInvestorRecord')->whereTime('createTime', 'd')->where('borrowStatus', 'not in', '2,4')->count('id'),
            'unsettleorder' => Db::table('AppInvestorRecord')->where('borrowStatus', 1)->count('id'),
            'sevendnu' => round($new_count / $total * 100, 2).'%',
            'sevendau' => '0%',
            'withdrawlist' => $withdrawlist,
            'chargelist' => $chargelist,
            'investlist' => $investlist,
            'uploadmode' => $uploadmode
        ]);

        return $this->view->fetch();
    }
}