<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'sms_send' => 
    array (
      0 => 'clsms',
    ),
    'sms_notice' => 
    array (
      0 => 'clsms',
    ),
    'sms_check' => 
    array (
      0 => 'clsms',
    ),
    'send_msg' => 
    array (
      0 => 'jpush',
    ),
    'testhook' => 
    array (
      0 => 'jpush',
    ),
    'leesignhook' => 
    array (
      0 => 'leesign',
    ),
    'login_init' => 
    array (
      0 => 'loginbg',
    ),
  ),
  'route' => 
  array (
    '/blog$' => 'blog/index/index',
    '/blog/p/[:id]' => 'blog/index/post',
    '/blog/c/[:id]' => 'blog/index/category',
    '/blog/archieve' => 'blog/index/archieve',
    '/example$' => 'example/index/index',
    '/example/d/[:name]' => 'example/demo/index',
    '/example/d1/[:name]' => 'example/demo/demo1',
    '/example/d2/[:name]' => 'example/demo/demo2',
    '/leesign$' => 'leesign/index/index',
    '/qrcode$' => 'qrcode/index/index',
    '/qrcode/build$' => 'qrcode/index/build',
    '/third$' => 'third/index/index',
    '/third/connect/[:platform]' => 'third/index/connect',
    '/third/callback/[:platform]' => 'third/index/callback',
  ),
);