<?php

namespace app\admin\controller\finance;

use app\common\controller\Backend;
use think\Db;

/**
 * 加息券对账
 *
 * @icon fa fa-circle-o
 */
class Jxcoupons extends Backend
{
    
    /**
     * Tzcoupons模型对象
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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $sub_field = "tf.id,b.borrowSn,tf.userId,u.userPhone,u.userName,tf.transactionAmt * 0.01 as transactionAmt,tf.orderId,b.borrowDurationTxt,
                          c.money * 0.01 as money,c.defName,c.createdTime,c.endTime,tf.addTime,tf.transactionStatus,tf.payChannelType,
                          case when c.grantMan > 0 then (select username from admin where id = c.grantMan) else '系统派送' end as grantmanname";
            
            $map['tf.transactionType'] = 3;
            $map['tf.couponsIdJx'] = ['gt', 0];
            
            $subQuery = Db::table('AppTransactionFlowing')
                        ->alias('tf')
                        ->field($sub_field)
                        ->join('AppBorrowInfo b','b.borrowInfoId = tf.borrowInfoId', 'LEFT')
                        ->join('AppUser u','u.userId = tf.userId', 'LEFT')
                        ->join('AppCoupon c','c.id = tf.couponsIdTz', 'LEFT')
                        ->where($map)
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
