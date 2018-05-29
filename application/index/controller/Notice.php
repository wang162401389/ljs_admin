<?php

namespace app\index\controller;

use app\common\controller\Frontend;

class Notice extends Frontend
{
    
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';
    
    public function _initialize()
    {
        parent::_initialize();
    }
    
    public function index()
    {
        return $this->view->fetch();
    }
    
    public function detail($id = NULL)
    {
        $row = \think\Db::name('notice')->where(['id' => $id])->find();
        if (!$row)
        {
            $this->error('没有找到记录');
        }
        
        $this->view->assign('row', $row);
        return $this->view->fetch();
    }
}
