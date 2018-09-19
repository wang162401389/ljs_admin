<?php

namespace app\admin\controller\borrow;

use app\common\controller\Backend;
use sinapay\Weibopay;
use think\Db;
use think\Log;

/**
 * 逾期的借款
 *
 * @icon fa fa-circle-o
 */
class Overdue extends Backend
{
    
    /**
     * Appborrowinfo模型对象
     */
    protected $model = null;
    
    protected $noNeedRight = [
        'sinacollecttrade', 
        'getoldoverdue', 
        'getnewoverdue',
        'checksinaerror',
        'getoldoverduebyuid',
        'getnewoverduebyuid',
        'personaldbhandle'
    ];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AppBorrowRepayment');
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $sub_field = "br.borrowInfoId,bi.borrowSn,bi.productType,bi.borrowName,bi.borrowMoney * 0.01 as borrowMoney,bi.investInterestType,
                          bi.borrowInterestRate * 0.01 as borrowInterestRate,bi.createTime,bi.fullTime,bi.payChannelType,bi.borrowDurationTxt,
                          bi.addInterestRate * 0.01 as addInterestRate,(bi.borrowInterestRate+bi.addInterestRate) * 0.01 as rate_total,
                          bi.secondVerifyTime,u.userName,u.realName,br.deadline,concat_ws('/',br.curPeriods,br.totalPeriods) as periods";
            
            $subQuery = Db::table('AppBorrowRepayment')
                        ->alias('br')
                        ->field($sub_field)
                        ->join('BorrowUser u', 'u.borrowUserId = br.userId', 'LEFT')
                        ->join('AppBorrowInfo bi', 'br.borrowInfoId = bi.borrowInfoId', 'LEFT')
                        ->where('br.repaymentStatus', 0)
                        ->where('br.deadline', '<', date('Y-m-d H:i:s'))
                        ->buildSql();
            
            $total = Db::table($subQuery.' t')
                    ->where($where)
                    ->count();
            
            $list = Db::table($subQuery.' t')
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            $list = collection($list)->toArray();
            
            $new_overdue_sum = $this->getnewoverdue();
            $old_overdue_sum = $this->getoldoverdue();
            
            $result = [
                "total" => $total, 
                "rows" => $list, 
                'new_overdue_sum' => number_format($new_overdue_sum, 2), 
                'old_overdue_sum' => number_format($old_overdue_sum, 2),
                'total_sum' => $new_overdue_sum + $old_overdue_sum
            ];

            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 还款
     */
    public function repay()
    {
        if ($this->request->isAjax())
        {
            $param = $this->request->post();
            $money = $param['money'];
            
            $sina['summary'] = date('Y-m-d H:i:s').',系统还款';
            $sina['money'] = $money;
            $sina['code'] = '1002';
            $sina['out_trade_no'] = date('YmdHis').mt_rand(100000, 999999);
            $sina['notify_url'] = "http://".$_SERVER['HTTP_HOST']."/index/Sinanotify/collecttradenotify";
            
            $this->success('还款成功', null, $this->sinacollecttrade($sina));
        }
    }
    
    /**
     * 代收
     * @param unknown $sina
     * @param number $payer 1:借款人 2：对公基本户
     * @param bool $all true：对部分投资账户
     * @return string
     */
    private function sinacollecttrade($sina, $payer = 1, $all = false)
    {
        $config = config('site.sinapay');
        $request = \think\Request::instance();
        $weibopay = new Weibopay();
        
        //接口名称
        $data['service'] = "create_hosting_collect_trade";
        //接口版本
        $data['version'] = $config['version'];
        //请求时间
        $data['request_time'] = date('YmdHis');
        //合作者身份ID
        $data['partner_id'] = $config['partner_id'];
        //网站编码格式
        $data['_input_charset'] = $config['_input_charset'];
        //签名方式
        $data['sign_type'] = $config['sign_type'];
        //交易订单号
        $data['out_trade_no'] = $sina['out_trade_no'];
        //交易码 1001代收投资金，1002代收还款金
        $data['out_trade_code'] = $sina['code'];
        //摘要
        $data['summary'] = $sina['summary'];
        //交易关联号
        $data['trade_related_no'] = $data['out_trade_no'];
        //付款用户、类型
        if ($payer == 1) 
        {
            $data['payer_id'] = '20151008'.$config['uid'];
            $data['payer_identity_type'] = 'UID';
        }
        else 
        {
            $data['payer_id'] = $config['email'];
            $data['payer_identity_type'] = 'EMAIL';
        }
        //IP
        $data['payer_ip'] = $request->ip();
        //支付方式：支付方式^金额^扩展|支付方式^金额^扩展。扩展信息内容以“，”分隔
        if ($payer == 1)
        {
            $data['pay_method'] = "online_bank^".$sina['money']."^SINAPAY,DEBIT,C";
        }
        else
        {
            $data['pay_method'] = "balance^".$sina['money']."^BASIC";
        }
        if (isset($sina['return_url'])) 
        {
            //页面跳转同步返回页面路径
            $data['return_url'] = $sina['return_url'];
        }
        if (isset($sina['notify_url']))
        {
            //异步回调通知地址
            $data['notify_url'] = $sina['notify_url'];
        }
        if ($payer == 1) 
        {
            //扩展信息
            $data['extend_param'] = "channel_black_list^online_bank^binding_pay^quick_pay";
        }
        ksort($data);
        //计算签名
        $data['sign'] = $weibopay->getSignMsg($data, $data['sign_type']);
        $setdata = $weibopay->createcurl_data($data);
        //模拟表单提交
        $result = $weibopay->curlPost($config['mas'], $setdata);
        
        $rs = $this->checksinaerror($result);
        
        if ($all == false) 
        {
            $old_overdue = $this->getoldoverdue();
            $new_overdue = $this->getnewoverdue();
            $overdue_total = $old_overdue + $new_overdue;
            $money = substr(sprintf("%.3f", $sina['money'] / $overdue_total * $old_overdue), 0, -1);
            $new_repay = substr(sprintf("%.3f", $sina['money'] / $overdue_total * $new_overdue), 0, -1);
            
            $ins = [];
            $ins['uid'] = $config['uid'];
            $ins['borrow_id'] = 0;
            $ins['type'] = 4;
            $ins['order_no'] = $data['out_trade_no'];
            $ins['money'] = $money;
            $ins['addtime'] = time();
            $ins['sort_order'] = 1;
            $ins['coupons'] = $ins['jx_coupons'] = '';
            $ins['is_auto'] = 0;
            Db::connect('old_db')->name('sinalog')->insert($ins);
        }
        
        $ins = [];
        $ins['userId'] = $ins['accountId'] = $payer == 2 ? 0 : $config['uid'];
        $ins['payChannelType'] = 2;
        $ins['flowingType'] = 2;
        $ins['relatedUserId'] = $ins['borrowInfoId'] = 0;
        $ins['transactionAmt'] = $all == false ? $new_repay * 100 : $sina['money'] * 100;
        $ins['handingFee'] = 0;
        $ins['remark'] = $all == false ? '新版本代还款金额：'.$new_repay : '对公基本户个人还款总额：'.$sina['money'];
        $ins['transactionStatus'] = 1;
        $ins['transactionType'] = 6;
        $ins['orderId'] = $data['out_trade_no'];
        $ins['bankCardNo'] = $ins['realName'] = $ins['pid'] = $ins['bankCode'] = $ins['bankName'] = $ins['mobile'] = $ins['userIp'] = $ins['signPay'] = '';
        $ins['payTime'] = $ins['addTime'] = $ins['updateTime'] = date('Y-m-d H:i:s');
        $ins['couponsIdJx'] = $ins['couponsIdTz'] = 0;
        Db::table('AppTransactionFlowing')->insert($ins);
        
        return $payer == 1 ? $result : $rs;
    }
    
    public function checksinaerror($data)
    {
        $config = config('site.sinapay');
        $weibopay = new Weibopay();
        
        $deresult = urldecode($data);
        $splitdata = (array)json_decode($deresult, true);
        //对签名参数据排序
        ksort($splitdata); 
        
        if ($weibopay->checkSignMsg($splitdata, $splitdata["sign_type"])) 
        {
            return $splitdata;
        } 
        else 
        {
            return "sing error!" ;
            exit();
        }
    }
    
    public function getoldoverdue()
    {
        return Db::connect('old_db')
                ->name('investor_detail')
                ->alias('ide')
                ->join('borrow_info b', 'ide.borrow_id = b.id')
                ->where('ide.status', 7)
                ->where('ide.repayment_time', 0)
                ->where('ide.is_debt', 0)
                ->where('b.test', 0)
                ->whereTime('ide.deadline', 'between', ['2018-01-01 00:00:00', date('Y-m-d H:i:s')])
                ->sum('ide.capital-ide.substitute_money');
    }
    
    private function getoldoverduebyuid($uid)
    {
        $old_overdue = Db::connect('old_db')
                        ->name('investor_detail')
                        ->alias('ide')
                        ->join('borrow_info b', 'ide.borrow_id = b.id')
                        ->where('investor_uid', $uid)
                        ->where('ide.status', 7)
                        ->where('ide.repayment_time', 0)
                        ->where('ide.is_debt', 0)
                        ->where('b.test', 0)
                        ->whereTime('ide.deadline', 'between', ['2018-01-01 00:00:00', date('Y-m-d H:i:s')])
                        ->sum('ide.capital-ide.substitute_money');
        
        return $old_overdue;
    }
    
    public function getnewoverdue()
    {
        $new_overdue_sum = Db::table('AppInvestorRepayment')
                            ->alias('t')
                            ->join('AppBorrowInfo a', 'a.borrowInfoId = t.borrowInfoId')
                            ->join('AppInvestorRecord b', 'b.id = t.borrowInvestorId')
                            ->join('AppBorrowRepayment c', 'c.borrowInfoId = t.borrowInfoId')
                            ->where('t.repaymentStatus', 0)
                            ->where('a.testFlag', 0)
                            ->where('c.deadline', '<=', date('Y-m-d H:i:s'))
                            ->sum('b.realityMoney-t.receiveMoney');
        
        return $new_overdue_sum / 100;
    }
    
    private function getnewoverduebyuid($uid)
    {
        $new_overdue = Db::table('AppInvestorRepayment')
                        ->alias('t')
                        ->join('AppBorrowInfo a', 'a.borrowInfoId = t.borrowInfoId')
                        ->join('AppInvestorRecord b', 'b.id = t.borrowInvestorId')
                        ->join('AppBorrowRepayment c', 'c.borrowInfoId = t.borrowInfoId')
                        ->where('t.repaymentStatus', 0)
                        ->where('a.testFlag', 0)
                        ->where('c.deadline', '<=', date('Y-m-d H:i:s'))
                        ->where('t.userId', $uid)
                        ->sum('b.realityMoney-t.receiveMoney');
        
        return $new_overdue / 100;
    }
    
    /**
     * 查看
     */
    public function edit($ids = NULL)
    {
        $row = model('Appborrowinfo')->get($ids);
        if (!$row)
        {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
        }
        
        $this->view->assign("row", $row);
        $this->view->assign('productTypeList', build_select('row[productType]', model('Appborrowinfo')->getProductTypeList(), $row['productType'], ['class' => 'form-control selectpicker']));
        $this->view->assign('investInterestTypeList', build_select('row[investInterestType]', model('Appborrowinfo')->getInvestInterestTypeList(), $row['investInterestType'], ['class' => 'form-control selectpicker']));
        $this->view->assign('borrowInterestTypeList', build_select('row[borrowInterestType]', model('Appborrowinfo')->getBorrowInterestTypeList(), $row['borrowInterestType'], ['class' => 'form-control selectpicker']));
        $this->view->assign('borrowUseList', build_select('row[borrowUse]', model('Appborrowinfo')->getBorrowUseList(), $row['borrowUse'], ['class' => 'form-control selectpicker']));
        $this->view->assign('guaranteeCompanyList', build_select('row[danbao]', model('Appborrowinfo')->getGuaranteeCompanyList(), $row['danbao'], ['class' => 'form-control selectpicker']));
        $this->view->assign('feeTypeList', build_select('row[feeType]', model('Appborrowinfo')->getFeeTypeList(), $row['feeType'], ['class' => 'form-control selectpicker']));
        
        return $this->view->fetch();
    }
    
    /**
     * 基本户针对个人还款
     * @return string
     */
    public function publicpersonalrepay()
    {
        if ($this->request->isPost()) 
        {
            $params = $this->request->post();
            if ($params) 
            {
                foreach ($params['phone'] as $v) 
                {
                    if (empty($v)) 
                    {
                        $this->error('手机号不能为空！');
                    }
                    $userId = Db::table('AppUser')->where('userPhone', $v)->value('userId');
                    if(!$userId)
                    {
                        $this->error('手机号'.$v.'不存在！');
                    }
                    $userId_arr[] = $userId;
                }
                foreach ($params['money'] as &$v)
                {
                    if ($v <= 0)
                    {
                        $this->error('还款金额必须大于0！');
                    }
                    $v = substr(sprintf("%.3f", $v), 0, -1);
                }
            
                $repay_userinfo = array_combine($userId_arr, $params['money']);
                $uid_money_arr = [];
                foreach ($repay_userinfo as $uid => $money) 
                {
                    $uid_money_arr[$uid] += $money;
                }
                
                foreach ($uid_money_arr as $uid => $money) 
                {
                    $old_overdue = $this->getoldoverduebyuid($uid);
                    
                    $new_overdue = $this->getnewoverduebyuid($uid);
                    
                    if ($money > $old_overdue + $new_overdue)
                    {
                        $this->error('实际还款金额必须小于待还金额！（uid:'.$uid.'）');
                    }
                }
                
                $sina['summary'] = date('Y-m-d H:i:s').',后台管理员还款操作';
                $sina['money'] = array_sum($uid_money_arr);
                $sina['code'] = '1002';
                $sina['out_trade_no'] = date('YmdHis').mt_rand(100000, 999999);
                
                $rs = $this->sinacollecttrade($sina, 2, true);
                
                Log::write("基本户代收结果 ： " . var_export($rs, true), 'java');
            
                if ($rs['response_code'] == 'APPLY_SUCCESS') 
                {
                    //这里是处理数据表的逻辑
                    //启动事务
                    Db::startTrans();
                    
                    try{
                        
                        foreach ($uid_money_arr as $uid => $money)
                        {
                            $res = $this->personaldbhandle($uid, $money, $sina['out_trade_no']);
                            if (!$res) 
                            {
                                // 回滚事务
                                Db::rollback();
                                
                                $this->error('操作数据库失败');
                            }
                        }
                        
                    }catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();
                    }
                    //这里是处理数据表的逻辑
                    
                    $i = $k = $j = 0;
                    $trade_list = ""; //新浪的交易列表
                    foreach ($uid_money_arr as $uid => $money)
                    {
                        if ($i < 300) 
                        {
                            if ($k === 0) 
                            {
                                $trade_list[$j] = date('YmdHis').mt_rand(100000, 999999).'~20151008'.$uid.'~UID~SAVING_POT~'.$money.'~~对公基本户个人还款金额'.$money.'~~~~~'.$sina['out_trade_no'];
                                $k++;
                            } 
                            else 
                            {
                                $trade_list[$j] .= '$'.date('YmdHis').mt_rand(100000, 999999).'~20151008'.$uid.'~UID~SAVING_POT~'.$money.'~~对公基本户个人还款金额'.$money.'~~~~~'.$sina['out_trade_no'];
                            }
                            $i++;
                            if ($i === 300) 
                            {
                                $i = $k = 0;
                                $j++;
                            }
                        }
                    }
                    
                    $weibopay = new Weibopay();
                    $request = \think\Request::instance();
                    $config = config('site.sinapay');
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
                    //签名方式
                    $data['sign_type'] = $config['sign_type'];
                    //交易订单号
                    $data['out_pay_no'] = date('YmdHis').mt_rand(100000, 999999);
                    //交易码 2001代付借款金 2002代付（本金/收益）金
                    $data['out_trade_code'] = '2002';
                    //交易列表
                    $data['trade_list'] = $trade_list[0];
                    //通知方式：single_notify: 交易逐笔通知 batch_notify: 批量通知
                    $data['notify_method'] = 'batch_notify';
                    ksort($data);
                    //计算签名
                    $data['sign'] = $weibopay->getSignMsg($data, $data['sign_type']);
                    $setdata = $weibopay->createcurl_data($data);
                    //模拟表单提交
                    $result = $weibopay->curlPost($config['mas'], $setdata);
                    $rs = $this->checksinaerror($result);
                    
                    Log::write("基本户代付结果 ： " . var_export($rs, true), 'java');
                    
                    if ($rs['response_code'] == 'APPLY_SUCCESS')
                    {
                        $this->success($rs['response_message']);
                    }
                    else
                    {
                        $this->error($rs['response_message']);
                    }
                }
                else 
                {
                    $this->error($rs['response_message']);
                }
            }
            
            $this->error(__('Parameter %s can not be empty', ''));
        }
        
        return $this->view->fetch();
    }
    
    /**
     * 基本户对个人还款数据库处理
     * @param int $uid
     * @param float $money 总还款
     * @param string $orderid 订单号
     * @return boolean
     */
    public function personaldbhandle($uid, $money, $orderid)
    {
        //新版本逾期额度
        $new_overdue = $this->getnewoverduebyuid($uid);
        //旧版本逾期额度
        $old_overdue = $this->getoldoverduebyuid($uid);
        //新版本分配还款额度
        $new_repay = substr(sprintf("%.3f", $money / ($new_overdue + $old_overdue) * $new_overdue), 0, -1);
        //旧版本分配还款额度
        $old_repay = substr(sprintf("%.3f", $money / ($new_overdue + $old_overdue) * $old_overdue), 0, -1);

        Log::write('uid:'.$uid.'，新版本逾期额度：'.$new_overdue.'，新版本分配还款额度：'.$new_repay.'，旧版本逾期额度：'.$old_overdue.'，旧版本分配还款额度'.$old_repay, 'java');
        
        $new_return = handle_new($new_overdue, $new_repay, $orderid, $uid);
        
        $old_return = handle_old($old_overdue, $old_repay, $orderid, $uid);
        
        Log::write('新版本handle return：'.json_encode($new_return), 'java');
        Log::write('旧版本handle return：'.json_encode($old_return), 'java');
        
        return !$new_return || !$old_return ? false : true;
    }
    
    /**
     * 基本户对所有人还款
     */
    public function publicallrepay()
    {
        if ($this->request->isAjax())
        {
            $param = $this->request->post();
            $money = $param['money'];
            
            $sina['summary'] = date('Y-m-d H:i:s').',对公基本户系统对这个月'.date('Y-m').'之前所有待还投资者还款';
            $sina['money'] = $money;
            $sina['code'] = '1002';
            $sina['out_trade_no'] = date('YmdHis').mt_rand(100000, 999999);
            $sina['notify_url'] = "http://".$_SERVER['HTTP_HOST']."/index/Sinanotify/collecttradenotify";
            $rs = $this->sinacollecttrade($sina, 2);
            Log::write("基本户针对所有人代收结果 ： " . var_export($rs, true), 'java');
            if ($rs['response_code'] == 'APPLY_SUCCESS')
            {
                $this->success($rs['response_message']);
            }
            else
            {
                $this->error($rs['response_message']);
            }
        }
    }
}