<?php

namespace app\admin\controller\finance;

use app\common\controller\Backend;

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
            
            $map['transactionType'] = 1;
            
            $total = $this->model
                    ->where($map)
                    ->where($where)
                    ->order($sort, $order)
                    ->count();
            
            $list = $this->model
                    ->where($map)
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            
            $list = collection($list)->toArray();
            if (!empty($list)) 
            {
                foreach ($list as &$v) 
                {
                    $v['userId'] = (string)$v['userId'];
                    $is_investor = \think\Db::table('AppUser')->field('userPhone,userName')->where('userId', $v['userId'])->find();
                    $v['userphone'] = $v['realname'] = '';
                    $v['idname'] = '未知';
                    if (!empty($is_investor)) 
                    {
                        $v['userphone'] = $is_investor['userPhone'];
                        $v['realname'] = $is_investor['userName'];
                        $v['idname'] = '投资人';
                    }
                    else
                    {
                        $is_borrower = \Think\Db::table('BorrowUser')->field('userName,realName')->where('borrowUserId', $v['userId'])->find();
                        if (!empty($is_borrower))
                        {
                            $v['userphone'] = $is_investor['userName'];
                            $v['realname'] = $is_investor['realName'];
                            $v['idname'] = '借款人';
                        }
                    }
                }
            }
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }
        return $this->view->fetch();
    }
}
