<?php

namespace addons\yunpian\controller;

use think\addons\Controller;

class Index extends Controller
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    // 
    public function index()
    {
        return $this->view->fetch();
        //$this->error("当前插件暂无前台页面");
    }

    public function send()
    {
        $mobile = $this->request->post('mobile');
        $tpl_id = $this->request->post('tpl_id');
        $param = [];
        for ($i = 1; $i <= 5; $i++) {
            $k = $this->request->post('k' . $i);
            $v = $this->request->post('v' . $i);
            if (empty($k) || empty($v)) continue;
            $param[$k] = $v;
        }
        $sms = new \addons\yunpian\library\Yunpian();
        $ret = $sms->mobile($mobile)
            ->template($tpl_id)
            ->param($param)
            ->send();

        if ($ret) {
            $this->success("发送成功");
        } else {
            $this->error("发送失败！失败原因：" . $sms->getError());
        }
    }
}
