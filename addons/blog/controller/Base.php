<?php

namespace addons\blog\controller;
/**
 * Blog控制器基类
 */
class Base extends \think\addons\Controller
{

    // 初始化
    public function __construct()
    {
        parent::__construct();
        $config = get_addon_config('blog');
        $config['indexurl'] = addon_url('blog/index/index', [], false);
        \think\Config::set('blog', $config);
    }

    public function _initialize()
    {
        parent::_initialize();
    }

}
