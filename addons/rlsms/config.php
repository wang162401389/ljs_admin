<?php

return array(
    0 =>
        array(
            'name'    => 'accountSid',
            'title'   => '主账号Sid',
            'type'    => 'string',
            'content' =>
                array(),
            'value'   => '',
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '请在https://www.yuntongxun.com/member/main中进行获取',
            'ok'      => '',
            'extend'  => '',
        ),
    1 =>
        array(
            'name'    => 'accountToken',
            'title'   => '主账号Token',
            'type'    => 'string',
            'content' =>
                array(),
            'value'   => '',
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '',
            'ok'      => '',
            'extend'  => '',
        ),
    2 =>
        array(
            'name'    => 'appId',
            'title'   => '应用ID',
            'type'    => 'string',
            'content' =>
                array(),
            'value'   => '',
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '',
            'ok'      => '',
            'extend'  => '',
        ),
    3 =>
        array(
            'name'    => 'template',
            'title'   => '短信模板',
            'type'    => 'array',
            'content' =>
                array(),
            'value'   =>
                array(
                    'register'  => '199986',
                    'resetpwd'  => '199986',
                    'changepwd' => '199986',
                    'profile'   => '199986',
                ),
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '',
            'ok'      => '',
            'extend'  => '',
        ),
);
