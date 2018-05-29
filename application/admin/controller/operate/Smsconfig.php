<?php

namespace app\admin\controller\operate;

use app\common\controller\Backend;

/**
 * 短信模板
 *
 * @icon fa fa-circle-o
 */
class Smsconfig extends Backend
{
    
    /**
     * Smstemplate模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Smstemplate');
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
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
                    
                    if ($params['sms_cfg'] == 1) 
                    {
                        $restrict = $params['smscfg'];
                        $self_0 = (int)$restrict['self'][0];
                        $self_1 = (int)$restrict['self'][1];
                        $ip_0 = (int)$restrict['ip'][0];
                        $ip_1 = (int)$restrict['ip'][1];
                        $phone_0 = (int)$restrict['phone'][0];
                        $phone_1 = (int)$restrict['phone'][1];
                        if ($self_0 < 0 || $self_1 < 0 || $ip_0 < 0 || $ip_1 < 0 || $phone_0 < 0 || $phone_1 < 0) 
                        {
                            $this->error('短信拦截设置必须都大于等于0');
                        }
                        
                        $type_arr = [
                            [
                                'restrict',
                                'ipRestrict',
                                'mobileRestrict'
                            ],
                            [
                                'verifyRestrict',
                                'verifyIpRestrict',
                                'verifyMobileRestrict'
                            ]
                        ];
                        
                        $params['restrict'] = [
                            [
                                'time' => $self_0 * 1000,
                                'number' => $self_1,
                                'type' => $type_arr[$params['sms_type']][0]
                            ],
                            [
                                'time' => $ip_0 * 1000,
                                'number' => $ip_1,
                                'type' => $type_arr[$params['sms_type']][1]
                            ],
                            [
                                'time' => $phone_0 * 1000,
                                'number' => $phone_1,
                                'type' => $type_arr[$params['sms_type']][2]
                            ]
                        ]; 
                    }
                    
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false)
                    {
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
                    
                    $params['restrict'] = [];
                    if ($params['sms_cfg'] == 1)
                    {
                        $restrict = $params['smscfg'];
                        $self_0 = (int)$restrict['self'][0];
                        $self_1 = (int)$restrict['self'][1];
                        $ip_0 = (int)$restrict['ip'][0];
                        $ip_1 = (int)$restrict['ip'][1];
                        $phone_0 = (int)$restrict['phone'][0];
                        $phone_1 = (int)$restrict['phone'][1];
                        if ($self_0 < 0 || $self_1 < 0 || $ip_0 < 0 || $ip_1 < 0 || $phone_0 < 0 || $phone_1 < 0)
                        {
                            $this->error('短信拦截设置必须都大于等于0');
                        }
                        
                        $type_arr = [
                            [
                                'restrict',
                                'ipRestrict',
                                'mobileRestrict'
                            ],
                            [
                                'verifyRestrict',
                                'verifyIpRestrict',
                                'verifyMobileRestrict'
                            ]
                        ];
                        
                        $params['restrict'] = [
                            [
                                'time' => $self_0 * 1000,
                                'number' => $self_1,
                                'type' => $type_arr[$params['sms_type']][0]
                            ],
                            [
                                'time' => $ip_0 * 1000,
                                'number' => $ip_1,
                                'type' => $type_arr[$params['sms_type']][1]
                            ],
                            [
                                'time' => $phone_0 * 1000,
                                'number' => $phone_1,
                                'type' => $type_arr[$params['sms_type']][2]
                            ]
                        ];
                    }
                    
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false)
                    {
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
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}