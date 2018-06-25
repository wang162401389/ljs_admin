<?php

namespace app\admin\controller\operate;

use app\common\controller\Backend;
use think\Db;

/**
 * 用户投资统计
 *
 * @icon fa fa-circle-o
 */
class Investstatistics extends Backend
{
    
    /**
     * AppUser模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AppUser');
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
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $field = "u.userId as `u.userId`,u.userPhone as `u.userPhone`,u.userName as `u.userName`,u.regSource as `u.regSource`,
                      u.createdTime as `u.createdTime`,ir.touzicount as `ir.touzicount`,ir.totalmoney as `ir.totalmoney`,fl.charge_total as `fl.charge_total`,
                      fl.charge_money_total as `fl.charge_money_total`,fl.withdraw_total as `fl.withdraw_total`,fl.withdraw_money_total as `fl.withdraw_money_total`";
            
            $subsql1_field = "userId,count(case when transactionType = 1 and transactionStatus = 2 then 'charge' end) charge_total,
                              count(case when transactionType = 2 and transactionStatus = 2 then 'withdraw' end) withdraw_total,
                              sum(case when transactionType = 1 and transactionStatus = 2 then transactionAmt end) * 0.01 as charge_money_total,
                              sum(case when transactionType = 2 and transactionStatus = 2 then transactionAmt end) * 0.01 as withdraw_money_total";
            
            $subsql1 = Db::table('AppTransactionFlowing')
                        ->field($subsql1_field)
                        ->where('transactionStatus', 2)
                        ->whereIn('transactionType', '1,2')
                        ->group('userId')
                        ->buildSql();
                        
            $subsql2_field = 'userId,count(*) as touzicount,sum(investorCapital) * 0.01 as totalmoney';
                        
            $subsql2 = Db::table('AppInvestorRecord')
                        ->field($subsql2_field)
                        ->group('userId')
                        ->buildSql();
            
            $total = Db::table('AppUser')
                    ->alias('u')
                    ->field($field)
                    ->join([$subsql1 => 'fl'], 'fl.userId = u.userId', 'LEFT')
                    ->join([$subsql2 => 'ir'], 'ir.userId = u.userId', 'LEFT')
                    ->where($where)
                    ->where(function ($query) {
                        $query->where('ir.touzicount', 'gt', 0)->whereOr('fl.charge_total', 'gt', 0)->whereOr('fl.withdraw_total', 'gt', 0);
                    })
                    ->order($sort, $order)
                    ->count();
            
            $list = Db::table('AppUser')
                    ->alias('u')
                    ->field($field)
                    ->join([$subsql1 => 'fl'], 'fl.userId = u.userId', 'LEFT')
                    ->join([$subsql2 => 'ir'], 'ir.userId = u.userId', 'LEFT')
                    ->where($where)
                    ->where(function ($query) {
                        $query->where('ir.touzicount', 'gt', 0)->whereOr('fl.charge_total', 'gt', 0)->whereOr('fl.withdraw_total', 'gt', 0);
                    })
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
                    
            if (!empty($list))
            {
                foreach ($list as &$v)
                {
                    $v['u.userId'] = ''.$v['u.userId'];
                }
            }
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }
        return $this->view->fetch();
    }
}
