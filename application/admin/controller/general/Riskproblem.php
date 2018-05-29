<?php

namespace app\admin\controller\general;

use app\common\controller\Backend;

use think\Controller;
use think\Request;

/**
 * 风险评估问题
 *
 * @icon fa fa-circle-o
 */
class Riskproblem extends Backend
{
    
    /**
     * Riskproblem模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Riskproblem');
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个方法
     * 因此在当前控制器中可不用编写增删改查的代码,如果需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            
            if ($params)
            {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill)
                {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    
                    $ans_arr = $params['adi_param']['field'];
                    $sco_arr = $params['adi_param']['value'];
                    
                    $answers_arr = [];
                    foreach ($ans_arr as $k => $v) 
                    {
                        if (!empty($v) && isset($sco_arr[$k]) && $sco_arr[$k] > 0) 
                        {
                            $answers_arr[$k]['answer'] = $v;
                            $answers_arr[$k]['score'] = $sco_arr[$k];
                        }    
                    }
                    
                    $result = $this->model->allowField(true)->save($params);
                    
                    if ($result !== false)
                    {
                        // 批量增加关联数据
                        $this->model->answers()->saveAll($answers_arr);
                        
                        $this->success();
                    }
                    else
                    {
                        $this->error($this->model->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }
    
    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    
                    $ans_arr = $params['adi_param']['field'];
                    $sco_arr = $params['adi_param']['value'];
                    
                    $answers_arr = [];
                    foreach ($ans_arr as $k => $v)
                    {
                        if (!empty($v) && isset($sco_arr[$k]) && $sco_arr[$k] > 0)
                        {
                            $answers_arr[$k]['answer'] = $v;
                            $answers_arr[$k]['score'] = $sco_arr[$k];
                        }
                    }
                    
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false)
                    {
                        $row->answers()->delete();
                        // 批量增加关联数据
                        $row->answers()->saveAll($answers_arr);
                        $this->success();
                    }
                    else
                    {
                        $this->error($row->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        
        $this->view->assign('answers', collection($row->answers)->toArray());
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids)
        {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds))
            {
                $count = $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();
            $count = 0;
            foreach ($list as $k => $v)
            {
                $count += $v->delete();
                $v->answers()->delete();
            }
            if ($count)
            {
                $this->success();
            }
            else
            {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }
}
