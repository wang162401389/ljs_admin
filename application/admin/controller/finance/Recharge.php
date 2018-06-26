<?php

namespace app\admin\controller\finance;

use app\common\controller\Backend;
use think\Db;

/**
 * 充值对账
 */
class Recharge extends Backend
{
    
    /**
     * Recharge模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AppTransactionFlowing');
        $this->view->assign("paychanneltypeList", $this->model->getPaychanneltypeList());
        $this->view->assign("flowingtypeList", $this->model->getFlowingtypeList());
        $this->view->assign("transactionstatusList", $this->model->getTransactionstatusList());
        $this->view->assign("transactiontypeList", $this->model->getTransactiontypeList());
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
            if ($this->request->request('pkey_name'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $field1 = 'tf.id,tf.userId,a.userPhone,a.userName,1 as idname,tf.transactionAmt * 0.01 as transactionAmt,tf.addTime,tf.transactionStatus,
                       tf.payChannelType,tf.orderId,a.regSource,a.marketChannel';
            
            $field2 = "tf.id,tf.userId,b.userName as userPhone,b.realName as userName,2 as idname,tf.transactionAmt * 0.01 as transactionAmt,tf.addTime,
                        tf.transactionStatus,tf.payChannelType,tf.orderId,b.regChannel as regSource,'' as marketChannel";
            
            $subQuery = Db::table('AppTransactionFlowing')
                        ->alias('tf')
                        ->field($field1)
                        ->join('AppUser a','a.userId = tf.userId')
                        ->where('tf.transactionType', 1)
                        ->union(function($query) use ($field2){
                            $query->table('AppTransactionFlowing')
                            ->alias('tf')
                            ->field($field2)
                            ->join('BorrowUser b','b.borrowUserId = tf.userId')
                            ->where('tf.transactionType', 1);
                        }, true)
                        ->buildSql();
            
            $field = 't.id,t.userId,t.userPhone,t.userName,t.idname,t.transactionAmt,t.addTime,t.transactionStatus,t.payChannelType,t.orderId,t.regSource,t.marketChannel';
            $total = Db::table($subQuery.' t')
                    ->where($where)
                    ->order($sort, $order)
                    ->count();
            
            $list = Db::table($subQuery.' t')
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            
            $list = collection($list)->toArray();
            if (!empty($list)) 
            {
                foreach ($list as &$v) 
                {
                    $v['userId'] = ''.$v['userId'];
                }
            }
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }
        return $this->view->fetch();
    }
}
