<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'app_init' => 
    array (
      0 => 'epay',
    ),
    'send_msg' => 
    array (
      0 => 'jpush',
    ),
    'testhook' => 
    array (
      0 => 'jpush',
      1 => 'luckydraw',
    ),
    'admin_login_init' => 
    array (
      0 => 'loginbg',
    ),
    'sms_send' => 
    array (
      0 => 'yunpian',
    ),
    'sms_notice' => 
    array (
      0 => 'yunpian',
    ),
    'sms_check' => 
    array (
      0 => 'yunpian',
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
    '/qrcode$' => 'qrcode/index/index',
    '/qrcode/build$' => 'qrcode/index/build',
  ),
);