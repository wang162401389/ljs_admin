<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;

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
//         $arr = [
//             'mobile' => '12312312311',
//             'args0' => 'ZJB123',
//             'smsCode' => 'LJS_TO_REIMBURSE_MSG',
//             'args1' => '123',
//             'args2' => '12'
//         ];
        
//         halt(encrypt(json_encode($arr)));
        
        $this->view->assign([
            'first_verify_count' => Db::table('AppBorrowInfo')->where('borrowStatus', 0)->count('borrowInfoId'),
            'second_verify_count' => Db::table('AppBorrowInfo')->where('borrowStatus', 4)->count('borrowInfoId'),
            'repayment_apply_count' => Db::table('AppBorrowRepayment')->where('advanceRepayment', 1)->count('id'),
            'referee_verify_count' => Db::table('referee_log')->where('status', 0)->count() 
        ]);

        return $this->view->fetch();
    }
}