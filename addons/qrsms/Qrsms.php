<?php

namespace addons\qrsms;

use think\Addons;

/**
 * 启瑞云短信插件
 */
class Qrsms extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 短信发送行为
     * @param   array $params
     * @return  boolean
     */
    public function smsSend(&$params)
    {
        $qrsms = new library\Qrsms();
        $result = $qrsms->mobile($params['mobile'])
            ->msg("你的短信验证码是：{$params['code']}")
            ->send();
        return $result;
    }

    /**
     * 短信发送通知
     * @param   array $params
     * @return  boolean
     */
    public function smsNotice(&$params)
    {
        $qrsms = new library\Qrsms();
        $result = $qrsms->mobile($params['mobile'])
            ->msg($params['msg'])
            ->send();
        return $result;
    }

    /**
     * 检测验证是否正确
     * @param   array $params
     * @return  boolean
     */
    public function smsCheck(&$params)
    {
        return TRUE;
    }
}