<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use sinapay\Weibopay;
use think\Log;
use think\Db;

class Sinanotify extends Frontend
{
    
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';
    
    public function _initialize()
    {
        parent::_initialize();
    }
    
    public function collecttradenotify()
    {
        Log::write('还款回调 ：' . var_export($_REQUEST, true), 'java');
        $orderId = $_REQUEST['outer_trade_no'];
        
        $status = Db::table('AppTransactionFlowing')->where('orderId', $orderId)->where('transactionType', 6)->value('transactionStatus');
        
        if ($_REQUEST['trade_status'] == 'TRADE_FINISHED' && $status == 1)
        {
            //输入总还款
            $trade_amount = $_REQUEST['trade_amount'];
            //新版本总逾期额度
            $new_overdue = $this->getnewoverdue();
            //旧版本总逾期额度
            $old_overdue = $this->getoldoverdue();
            //新版本分配还款额度
            $new_repay = substr(sprintf("%.3f", $trade_amount / ($new_overdue + $old_overdue) * $new_overdue), 0, -1);
            //旧版本分配还款额度
            $old_repay = substr(sprintf("%.3f", $trade_amount / ($new_overdue + $old_overdue) * $old_overdue), 0, -1);
            
            // 启动事务
            Db::startTrans();
            
            try{
                Log::write('新版本总逾期额度：'.$new_overdue.'，新版本分配还款额度：'.$new_repay.'，旧版本总逾期额度：'.$old_overdue.'，旧版本分配还款额度'.$old_repay, 'java');
                
                $new_return = handle_new($new_overdue, $new_repay, $orderId);
                
                $old_return = handle_old($old_overdue, $old_repay, $orderId);
                
                Log::write('新版本handle return：'.json_encode($new_return), 'java');
                Log::write('旧版本handle return：'.json_encode($old_return), 'java');
                
                if ($new_return && $old_return)
                {
                    Log::write("正在发奖", 'java');
                    foreach ($new_return['trade_list'] as $k => $list)
                    {
                        $res1 = $this->batchpay($list, $new_return['batch_orderid_arr'][$k], 1, $new_return['act_receive_arr'][$k]);
                        Log::write("新版本批量回款结果 ： " . var_export($res1, true), 'java');
                    }
                    
                    foreach ($old_return['trade_list'] as $k => $list)
                    {
                        $res2 = $this->batchpay($list, '', 2, $old_return['act_receive_arr'][$k]);
                        Log::write("旧版本批量回款结果 ： " . var_export($res2, true), 'java');
                    }
                    
                    if ($res1['response_code'] == 'APPLY_SUCCESS' && $res2['response_code'] == 'APPLY_SUCCESS')
                    {
                        Log::write("发奖成功", 'java');
                        
                        $upd = [];
                        $upd['payTime'] = $upd['updateTime'] = date('Y-m-d H:i:s');
                        $upd['transactionStatus'] = 2;
                        Db::table('AppTransactionFlowing')->where('orderId', $orderId)->update($upd);
                        
                        $upd = [];
                        $upd['status'] = 2;
                        $upd['completetime'] = time();
                        Db::connect("old_db")->name("sinalog")->where('order_no', $orderId)->where('type', 4)->update($upd);
                        
                        Log::write("新版本投资用户 ： " . json_encode($new_return['userinfo']), 'java');
                        Log::write("旧版本投资用户 ： " . json_encode($old_return['userinfo']), 'java');
                        
                        $this->sendsms($new_return['userinfo'], $old_return['userinfo']);
                        
                        echo 'success';
                    }
                    
                    // 提交事务
                    Db::commit();
                }
                else
                {
                    // 回滚事务
                    Db::rollback();
                }
                    
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            
            echo 'success';
        }
        elseif ($_REQUEST['trade_status'] == 'PAY_FINISHED')
        {
            echo 'success';
        }
        else
        {
            echo 'success';
        }
        die();
    }
    
    public function basiccollecttradenotify()
    {
        Log::write('还款回调 ：' . var_export($_REQUEST, true), 'java');
        $orderId = $_REQUEST['outer_trade_no'];
        
        $status = Db::table('AppTransactionFlowing')->where('orderId', $orderId)->where('transactionType', 6)->value('transactionStatus');
        
        if ($_REQUEST['trade_status'] == 'TRADE_FINISHED' && $status == 1)
        {
            $return = require RUNTIME_PATH . 'log' . DS . date('Ym') . DS . $orderId . '.php';
            
            // 启动事务
            Db::startTrans();
        
            try{
        
                $new_return = handle_new($orderId, array_column($return, 'userId'));
        
                $old_return = handle_old($orderId, array_column($return, 'userId'));
        
                Log::write('新版本handle return：'.json_encode($new_return), 'java');
                Log::write('旧版本handle return：'.json_encode($old_return), 'java');
        
                if ($new_return && $old_return)
                {
                    Log::write("正在发奖", 'java');
                    foreach ($new_return['trade_list'] as $k => $list)
                    {
                        $res1 = $this->batchpay($list, $new_return['batch_orderid_arr'][$k], 1, $new_return['act_receive_arr'][$k]);
                        Log::write("新版本批量回款结果 ： " . var_export($res1, true), 'java');
                    }
        
                    foreach ($old_return['trade_list'] as $k => $list)
                    {
                        $res2 = $this->batchpay($list, '', 2, $old_return['act_receive_arr'][$k]);
                        Log::write("旧版本批量回款结果 ： " . var_export($res2, true), 'java');
                    }
        
                    if ($res1['response_code'] == 'APPLY_SUCCESS' && $res2['response_code'] == 'APPLY_SUCCESS')
                    {
                        Log::write("发奖成功", 'java');
        
                        $upd = [];
                        $upd['payTime'] = $upd['updateTime'] = date('Y-m-d H:i:s');
                        $upd['transactionStatus'] = 2;
                        Db::table('AppTransactionFlowing')->where('orderId', $orderId)->update($upd);
        
                        $upd = [];
                        $upd['status'] = 2;
                        $upd['completetime'] = time();
                        Db::connect("old_db")->name("sinalog")->where('order_no', $orderId)->where('type', 4)->update($upd);
        
                        Log::write("新版本投资用户 ： " . json_encode($new_return['userinfo']), 'java');
                        Log::write("旧版本投资用户 ： " . json_encode($old_return['userinfo']), 'java');
        
                        $this->sendsms($new_return['userinfo'], $old_return['userinfo']);
        
                        echo 'success';
                    }
        
                    // 提交事务
                    Db::commit();
                }
                else
                {
                    // 回滚事务
                    Db::rollback();
                }
        
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
        
            echo 'success';
        }
        elseif ($_REQUEST['trade_status'] == 'PAY_FINISHED')
        {
            echo 'success';
        }
        else
        {
            echo 'success';
        }
        die();
    }
    
    private function sendsms($new, $old)
    {
        if (!empty($new) || !empty($old))
        {
            $mergeuserinfo = [];
            if (!empty($new))
            {
                foreach ($new as $phone => $repaymoney)
                {
                    if (!isset($mergeuserinfo[$phone]))
                    {
                        $mergeuserinfo[$phone] = $repaymoney;
                    }
                    else
                    {
                        $mergeuserinfo[$phone] += $repaymoney;
                    }
                }
            }
            
            if (!empty($old))
            {
                foreach ($old as $phone => $repaymoney)
                {
                    if (!isset($mergeuserinfo[$phone]))
                    {
                        $mergeuserinfo[$phone] = $repaymoney;
                    }
                    else
                    {
                        $mergeuserinfo[$phone] += $repaymoney;
                    }
                }
            }
            
            if (!empty($mergeuserinfo))
            {
                foreach ($mergeuserinfo as $phone => $money)
                {
                    $javaapi = new \fast\Javaapi();
                    $req = [];
                    $req['mobile'] = $phone;
                    $req['time'] = $_SERVER['REQUEST_TIME'];
                    $req['smsCode'] = 'LJS_TO_SCALE_MSG';
                    $req['args'][] = (string)$money;
                    $javaapi->sendSms(['message' => encrypt(json_encode($req))]);
                }
            }
        }
    }
        
    public function getnewoverdue()
    {
        $new_overdue_sum = Db::table('AppInvestorRepayment')
                            ->alias('t')
                            ->join('AppBorrowInfo a', 'a.borrowInfoId = t.borrowInfoId')
                            ->join('AppInvestorRecord b', 'b.id = t.borrowInvestorId')
                            ->join('AppBorrowRepayment c', 'c.borrowInfoId = t.borrowInfoId')
                            ->where('t.repaymentStatus', 0)
                            ->where('c.deadline', 'between', ['2018-01-01 00:00:00', date('Y-m-t', strtotime('-1 month'))])
                            ->sum('b.realityMoney');
        
        return $new_overdue_sum / 100;
    }
        
    public function getoldoverdue()
    {
        return Db::connect('old_db')
                ->name('investor_detail')
                ->alias('ide')
                ->join('borrow_info b', 'ide.borrow_id = b.id')
                ->where('ide.status', 7)
                ->where('ide.repayment_time', 0)
                ->where('ide.status', '<>', -1)
                ->where('ide.is_debt', 0)
                ->where('b.test', 0)
                ->whereTime('ide.deadline', 'between', ['2018-01-01 00:00:00', date('Y-m-t', strtotime('-1 month'))])
                ->sum('ide.capital');
    }
        
    public function batchpay($trade_list, $batch_orderId = '', $version = 1, $money)
    {
        $config = config('site.sinapay');
        $request = \think\Request::instance();
        $weibopay = new Weibopay();
        //接口名称
        $data['service'] = "create_batch_hosting_pay_trade";
        //接口版本
        $data['version'] = $config['version'];
        //请求时间
        $data['request_time'] = date('YmdHis');
        //用户IP地址
        $data['user_ip'] = $request->ip();
        //合作者身份ID
        $data['partner_id'] = $config['partner_id'];
        //网站编码格式
        $data['_input_charset'] = $config['_input_charset'];
        //签名方式 MD5
        $data['sign_type'] = $config['sign_type'];
        //交易订单号
        $data['out_pay_no'] = !empty($batch_orderId) ? $batch_orderId : buildorderno();
        //交易码 2001代付借款金 2002代付（本金/收益）金
        $data['out_trade_code'] = '2002';
        //交易列表
        $data['trade_list'] = $trade_list;
        //通知方式：single_notify: 交易逐笔通知 batch_notify: 批量通知
        $data['notify_method'] = 'batch_notify';
        //异步回调地址
        if ($version == 1)
        {
            $data['notify_url'] = "http://".$_SERVER['HTTP_HOST']."/index/Sinanotify/new_batchpaynotify";
        }
        else
        {
            $data['notify_url'] = "http://".$_SERVER['HTTP_HOST']."/index/Sinanotify/old_batchpaynotify";
        }
        ksort($data);
        //计算签名
        $data['sign'] = $weibopay->getSignMsg($data, $data['sign_type']);
        $setdata = $weibopay->createcurl_data($data);
        //模拟表单提交
        $result = $weibopay->curlPost($config['mas'], $setdata);
        $rs = $this->checksinaerror($result);
        
        $ins = [];
        if ($version == 1)
        {
            $ins['accountId'] = '';
            $ins['userId'] = 0;
            $ins['payChannelType'] = 2;
            $ins['flowingType'] = 1;
            $ins['relatedUserId'] = $ins['borrowInfoId'] = 0;
            $ins['transactionAmt'] = $money;
            $ins['handingFee'] = 0;
            $ins['remark'] = '代还款分账给投资人';
            $ins['transactionStatus'] = 1;
            $ins['transactionType'] = 4;
            $ins['orderId'] = $data['out_pay_no'];
            $ins['bankCardNo'] = $ins['realName'] = $ins['pid'] = $ins['bankCode'] = $ins['bankName'] =
            $ins['mobile'] = $ins['userIp'] = $ins['signPay'] = '';
            $ins['payTime'] = $ins['addTime'] = $ins['updateTime'] = date('Y-m-d H:i:s');
            $ins['couponsIdJx'] = $ins['couponsIdTz'] = 0;
            Db::table('AppTransactionFlowing')->insert($ins);
        }
        else
        {
            $ins['uid'] = 1;
            $ins['borrow_id'] = 0;
            $ins['type'] = 4;
            $ins['order_no'] = $data['out_pay_no'];
            $ins['money'] = $money;
            $ins['addtime'] = time();
            $ins['sort_order'] = '';
            $ins['coupons'] = '';
            $ins['is_auto'] = 0;
            $ins['jx_coupons'] = '';
            Db::connect('old_db')->name('sinalog')->insert($ins);
        }
        return $rs;
    }
    
    public function new_batchpaynotify()
    {
        Log::write("新版本回款回调 ： " . var_export($_REQUEST, true), 'java');
        
        $status = Db::table('AppTransactionFlowing')->where('orderId', $_REQUEST['outer_batch_no'])->where('transactionType', 4)->value('transactionStatus');
        
        Log::write('新版本回款回调 select：'.Db::table('AppTransactionFlowing')->getLastSql(), 'java');
        Log::write('新版本回款回调  status ：'.$status, 'java');
        
        if ($_REQUEST['batch_status'] == 'FINISHED' && $status == 1)
        {
            $upd = [];
            $upd['transactionStatus'] = 2;
            $upd['payTime'] = $upd['updateTime'] = date('Y-m-d H:i:s');
            $upd['transactionAmt'] = (int)($_REQUEST['batch_amount'] * 100);
            $res = Db::table('AppTransactionFlowing')->where('orderId', $_REQUEST['outer_batch_no'])->where('transactionType', 4)->update($upd);
            Log::write('新版本回款回调 sql：'.Db::table('AppTransactionFlowing')->getLastSql(), 'java');
            Log::write('新版本回款回调 sql res ：'.var_export($res, true), 'java');
            
            echo 'success';
        }
        else
        {
            Log::write('新版本回款回调 没有进来：', 'java');
            echo 'success';
        }
    }
        
    public function old_batchpaynotify()
    {
        Log::write("旧版本回款回调 ： " . var_export($_REQUEST, true), 'java');
        
        $status = Db::connect('old_db')->name('sinalog')->where('order_no', $_REQUEST['outer_batch_no'])->where('type', 4)->value('status');
        
        Log::write('旧版本回款回调 select：'.Db::connect('old_db')->name('sinalog')->getLastSql(), 'java');
        Log::write('旧版本回款回调  status ：'.$status, 'java');
        
        if ($_REQUEST['batch_status'] == 'FINISHED' && $status == 1)
        {
            $upd = [];
            $upd['status'] = 2;
            $upd['completetime'] = time();
            $upd['money'] = floatval($_REQUEST['batch_amount']);
            $res = Db::connect('old_db')->name('sinalog')->where('order_no', $_REQUEST['outer_batch_no'])->where('type', 4)->update($upd);
            Log::write('旧版本回款回调 sql：'.Db::connect('old_db')->name('sinalog')->getLastSql(), 'java');
            Log::write('旧版本回款回调 sql res ：'.var_export($res, true), 'java');
            
            echo 'success';
        }
        else
        {
            Log::write('旧版本回款回调 没有进来：', 'java');
            echo 'success';
        }
    }
        
    //验证新浪接口响应信息
    public function checksinaerror($data)
    {
        $weibopay = new Weibopay();
        $deresult = urldecode($data);
        $splitdata = [];
        $splitdata = json_decode($deresult, true);
        //对签名参数据排序
        ksort($splitdata);
        
        if ($weibopay->checkSignMsg($splitdata, $splitdata["sign_type"]))
        {
            return $splitdata;
        }
        else
        {
            return "sing error!" ;
        }
    }
}