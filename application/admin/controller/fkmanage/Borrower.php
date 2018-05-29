<?php

namespace app\admin\controller\fkmanage;

use app\common\controller\Backend;

use think\Controller;

/**
 * 借款用户管理
 *
 * @icon fa fa-circle-o
 */
class Borrower extends Backend
{
    
    /**
     * Borrower模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Borrower');
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个方法
     * 因此在当前控制器中可不用编写增删改查的代码,如果需要自己控制这部分逻辑
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
            $total = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->count();
            
            $list = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            
            
            $list = collection($list)->toArray();
            foreach ($list as &$v)
            {
                $v['borrowUserId'] = (string)$v['borrowUserId'];
            }
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 发布
     */
    public function changeLoanStatus($ids = "")
    {
        if ($ids)
        {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds))
            {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            
            $info = $this->model->get($ids);
            if ($info->isLoan == 1) {
                $this->error('请不要重复确认！');
            }
            
            $info->isLoan = 1;
            
            if ($info->save())
            {
                $this->success('修改成功');
            }
            else
            {
                $this->error('修改失败');
            }
        }
        
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }
}
