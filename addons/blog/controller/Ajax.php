<?php

namespace addons\blog\controller;

use addons\blog\model\Comment;
use addons\blog\model\Post;
use app\common\library\Email;
use think\addons\Controller;
use think\Exception;
use think\Validate;

/**
 * Ajax
 */
class Ajax extends Controller
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

}
