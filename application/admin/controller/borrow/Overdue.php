<?php

namespace app\admin\controller\borrow;

use app\common\controller\Backend;
use sinapay\Weibopay;
use think\Db;

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
    
    protected $noNeedRight = ['investinteresttypelist', 'sinacollecttrade', 'getoldoverdue', 'getnewoverdue'];

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
                'new_overdue_sum' => $new_overdue_sum, 
                'old_overdue_sum' => $old_overdue_sum,
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
            
            $sina['content'] = date('Y-m-d H:i:s').',系统还款';
            $sina['money'] = $money;
            $sina['code'] = '1002';
            $sina['trade_related_no'] = $sina['out_trade_no'] = date('YmdHis').mt_rand(100000, 999999);
            $sina['return_url'] = "http://".$_SERVER['HTTP_HOST']."/manage.php/borrow/overdue?ref=addtabs";
            $sina['notify_url'] = "http://".$_SERVER['HTTP_HOST']."/index/Sinanotify/collecttradenotify";
            
            $this->success('还款成功', null, $this->sinacollecttrade($sina));
        }
    }
    
    private function sinacollecttrade($sina)
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
        //签名方式 MD5
        $data['sign_type'] = $config['sign_type'];
        //交易订单号
        $data['out_trade_no'] = $sina['out_trade_no'];
        //交易码 1001代收投资金，1002代收还款金
        $data['out_trade_code'] = $sina['code'];
        //摘要
        $data['summary'] = $sina['content'];
        //交易关联号
        $data['trade_related_no'] = $sina['trade_related_no'];
        //用户ID
        $data['payer_id'] = '20151008'.$config['uid'];
        //ID类型
        $data['payer_identity_type'] = 'UID';
        //IP
        $data['payer_ip'] = $request->ip();
        //支付方式：支付方式^金额^扩展|支付方式^金额^扩展。扩展信息内容以“，”分隔
        $data['pay_method'] = "online_bank^".$sina['money']."^SINAPAY,DEBIT,C";
        //页面跳转同步返回页面路径
        $data['return_url'] = $sina['return_url'];
        //异步回调通知地址
        $data['notify_url'] = $sina['notify_url'];
        //扩展信息
        $data['extend_param'] = "channel_black_list^online_bank^binding_pay^quick_pay";
        ksort($data);
        //计算签名
        $data['sign'] = $weibopay->getSignMsg($data, $data['sign_type']);
        $setdata = $weibopay->createcurl_data($data);
        //模拟表单提交
        $result = $weibopay->curlPost($config['mas'], $setdata);
        
        $old_overdue = $this->getoldoverdue();
        $new_overdue = $this->getnewoverdue();
        $overdue_total = $old_overdue + $new_overdue;
        
        $ins = [];
        $ins['userId'] = $ins['accountId'] = $config['uid'];
        $ins['payChannelType'] = 2;
        $ins['flowingType'] = 2;
        $ins['relatedUserId'] = $ins['borrowInfoId'] = 0;
        $new_repay = substr(sprintf("%.3f", $sina['money'] / $overdue_total * $new_overdue), 0, -1);
        $ins['transactionAmt'] = $new_repay * 100;
        $ins['handingFee'] = 0;
        $ins['remark'] = '新版本代还款金额：'.$new_repay;
        $ins['transactionStatus'] = 1;
        $ins['transactionType'] = 6;
        $ins['orderId'] = $sina['out_trade_no'];
        $ins['bankCardNo'] = $ins['realName'] = $ins['pid'] = $ins['bankCode'] = $ins['bankName'] = 
        $ins['mobile'] = $ins['userIp'] = $ins['signPay'] = '';
        $ins['payTime'] = $ins['addTime'] = $ins['updateTime'] = date('Y-m-d H:i:s');
        $ins['couponsIdJx'] = $ins['couponsIdTz'] = 0;
        Db::table('AppTransactionFlowing')->insert($ins);
        
        $ins = [];
        $ins['uid'] = $config['uid'];
        $ins['borrow_id'] = 0;
        $ins['type'] = 4;
        $ins['order_no'] = $sina['out_trade_no'];
        $ins['money'] = substr(sprintf("%.3f", $sina['money'] / $overdue_total * $old_overdue), 0, -1);
        $ins['addtime'] = time();
        $ins['sort_order'] = 1;
        $ins['coupons'] = $data['jx_coupons'] = '';
        $ins['is_auto'] = 0;
        Db::connect('old_db')->name('sinalog')->insert($ins);
        
        return $result;
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
    
    public function getnewoverdue()
    {
        $new_overdue_sum = Db::table('AppInvestorRepayment')
                            ->alias('t')
                            ->join('AppBorrowInfo a', 'a.borrowInfoId = t.borrowInfoId')
                            ->join('AppInvestorRecord b', 'b.id = t.borrowInvestorId')
                            ->join('AppBorrowRepayment c', 'c.borrowInfoId = t.borrowInfoId')
                            ->where('t.repaymentStatus', 0)
                            ->where('c.deadline', '<=', date('Y-m-d H:i:s'))
                            ->sum('t.capital');
        
        return $new_overdue_sum / 100;
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
}
