<?php

namespace app\admin\controller\blog;

use app\common\controller\Backend;
use think\Controller;
use think\Request;

/**
 * 博客分类管理
 *
 * @icon fa fa-circle-o
 */
class Category extends Backend
{

    protected $noNeedRight = ['selectpage'];

    /**
     * BlogCategory模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('BlogCategory');
        $this->view->assign("flagList", $this->model->getFlagList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    public function selectpage()
    {
        return parent::selectpage();
    }

}
