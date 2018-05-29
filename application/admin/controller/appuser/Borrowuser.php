<?php

namespace app\admin\controller\appuser;
use app\common\controller\Backend;

/**
 * 借款人管理
 * @icon fa fa-circle-o
 */
class Borrowuser extends Backend
{
    
    /**
     * Borrowuser模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Borrower');
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
            
            $field = "u.userName as `u.userName`,
                      u.regChannel as `u.regChannel`,u.isSina as `u.isSina`,u.isHuaxing as `u.isHuaxing`,u.createTime as `u.createTime`,
                      u.borrowUserId as `u.borrowUserId`,u.realName as `u.realName`,u.userType as `u.userType`,sum(bi.borrowMoney) as total,
                    sum(case when bi.payChannelType = 2 and bi.borrowStatus not in ('1','2','6') then bi.borrowMoney end) * 0.01 as sina_total,
                    sum(case when bi.payChannelType = 3 and bi.borrowStatus not in ('1','2','6') then bi.borrowMoney end) * 0.01 as huaxing_total";

            $total = \think\Db::table('BorrowUser')
                    ->alias('u')
                    ->field($field)
                    ->join('AppBorrowInfo bi','u.borrowUserId = bi.borrowUid', 'LEFT')
                    ->where($where)
                    ->order($sort, $order)
                    ->group('u.borrowUserId')
                    ->count();
            
            $list = \think\Db::table('BorrowUser')
                    ->alias('u')
                    ->field($field)
                    ->join('AppBorrowInfo bi','u.borrowUserId = bi.borrowUid', 'LEFT')
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->group('u.borrowUserId')
                    ->select();
                    
            if (!empty($list))
            {
                foreach ($list as &$v)
                {
                    $v['u.borrowUserId'] = (string)$v['u.borrowUserId'];
                    $v['total'] = $v['sina_total'] + $v['huaxing_total'];
                    $v['is_sina_text'] = $this->model->getIsSinaList()[$v['u.isSina']];
                    $v['is_huaxing_text'] = $this->model->getIsHuaxingList()[$v['u.isHuaxing']];
                    $v['user_type_text'] = $this->model->getUserTypeList()[$v['u.userType']];
                }
            }
                    
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }
        return $this->view->fetch();
    }
}
