<?php

namespace app\admin\controller\borrow;

use app\common\controller\Backend;

/**
 * 复审未通过的借款
 *
 * @icon fa fa-circle-o
 */
class Secondfail extends Backend
{
    
    /**
     * Appborrowinfo模型对象
     */
    protected $model = null;
    
    protected $noNeedRight = ['investinteresttypelist'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Appborrowinfo');
        $this->view->assign("investinteresttypeList", $this->model->getInvestinteresttypeList());
        $this->view->assign("producttypeList", $this->model->getProducttypeList());
        $this->view->assign("borrowstatusList", $this->model->getBorrowstatusList());
        $this->view->assign("borrowuseList", $this->model->getBorrowuseList());
        $this->view->assign("borrowinteresttypeList", $this->model->getBorrowinteresttypeList());
        $this->view->assign("paychanneltypeList", $this->model->getPaychanneltypeList());
        $this->view->assign("feetypeList", $this->model->getFeetypeList());
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
            
            $map['borrowStatus'] = 6;
            
            $total = $this->model
                    ->with(['borrower', 'secondverify'])
                    ->where($where)
                    ->where($map)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['borrower', 'secondverify'])
                    ->where($where)
                    ->where($map)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 搜索下拉列表
     */
    public function investinteresttypelist()
    {
        $typelist = $this->model->getInvestInterestTypeList();
        $searchlist = [];
        if (!empty($typelist))
        {
            foreach ($typelist as $tid => $tname) {
                $searchlist[] = ['id' => $tid, 'name' => $tname];
            }
        }
        $this->success('', null, ['searchlist' => $searchlist]);
    }
    
    /**
     * 查看
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
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
        $this->view->assign('productTypeList', build_select('row[productType]', $this->model->getProductTypeList(), $row['productType'], ['class' => 'form-control selectpicker']));
        $this->view->assign('investInterestTypeList', build_select('row[investInterestType]', $this->model->getInvestInterestTypeList(), $row['investInterestType'], ['class' => 'form-control selectpicker']));
        $this->view->assign('borrowInterestTypeList', build_select('row[borrowInterestType]', $this->model->getBorrowInterestTypeList(), $row['borrowInterestType'], ['class' => 'form-control selectpicker']));
        $this->view->assign('borrowUseList', build_select('row[borrowUse]', $this->model->getBorrowUseList(), $row['borrowUse'], ['class' => 'form-control selectpicker']));
        $this->view->assign('guaranteeCompanyList', build_select('row[danbao]', $this->model->getGuaranteeCompanyList(), $row['danbao'], ['class' => 'form-control selectpicker']));
        $this->view->assign('feeTypeList', build_select('row[feeType]', $this->model->getFeeTypeList(), $row['feeType'], ['class' => 'form-control selectpicker']));
        
        return $this->view->fetch();
    }
}
