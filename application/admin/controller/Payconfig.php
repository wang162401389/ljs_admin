<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Payconfig extends Backend
{
    
    /**
     * PayConfig模型对象
     */
    protected $model = null;
    protected $noNeedRight = ['change'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('PayConfig');

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个方法
     * 因此在当前控制器中可不用编写增删改查的代码,如果需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 切换
     */
    public function change()
    {
        if ($this->request->isPost())
        {
            $id = $this->request->post('ids');
            $params = $this->request->post('params');
            $params_arr = explode('=', $params);
            
            $result = $this->model->save([$params_arr[0] => $params_arr[1]], ['id' => $id]);
            if ($result) 
            {
                $this->success("操作成功");
            }
            else 
            {
                $this->error('操作失败');
            }
        }
    }

}
