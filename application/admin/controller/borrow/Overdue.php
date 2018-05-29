<?php

namespace app\admin\controller\borrow;

use app\common\controller\Backend;

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
    
    protected $noNeedRight = ['investinteresttypelist'];

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
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $map['repaymentStatus'] = 0;
            $map['AppBorrowRepayment.deadline'] = ['lt', date('Y-m-d H:i:s')];
            
            $total = $this->model
                    ->with(['borrow', 'borrower'])
                    ->where($where)
                    ->where($map)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['borrow', 'borrower'])
                    ->where($where)
                    ->where($map)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            $list = collection($list)->toArray();
            if (!empty($list)) 
            {
                foreach ($list as &$v) 
                {
                    $v['periods'] = $v['curPeriods'].'/'.$v['totalPeriods'];
                }
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
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
