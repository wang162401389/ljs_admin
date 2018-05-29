<?php

return array (
  'name' => 
  array (
    'name' => 'name',
    'title' => '博客名称',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '极速后台的博客',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'enname' => 
  array (
    'name' => 'enname',
    'title' => '博客英文名称',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'FastAdmin\'s Blog',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'keywords' => 
  array (
    'name' => 'keywords',
    'title' => '关键字',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'FastAdmin,FastAdmin博客,后台博客,开发博客',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'description' => 
  array (
    'name' => 'description',
    'title' => '描述',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '基于ThinkPHP5和Bootstrap的极速后台开发框架',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'intro' => 
  array (
    'name' => 'intro',
    'title' => '个人简介',
    'type' => 'text',
    'content' => 
    array (
    ),
    'value' => 'FastAdmin是一款基于ThinkPHP5和Bootstrap的极速后台开发框架，其强大的CRUD一键生成功能和丰富的插件扩展，让你的后台开发更加简单、快速。',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'listpagesize' => 
  array (
    'name' => 'listpagesize',
    'title' => '列表每页显示数量',
    'type' => 'number',
    'content' => 
    array (
    ),
    'value' => '10',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'commentpagesize' => 
  array (
    'name' => 'commentpagesize',
    'title' => '评论每页显示数量',
    'type' => 'number',
    'content' => 
    array (
    ),
    'value' => '10',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'avatar' => 
  array (
    'name' => 'avatar',
    'title' => '头像',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => '/assets/addons/blog/img/avatar.png',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  'background' => 
  array (
    'name' => 'background',
    'title' => '背景图片',
    'type' => 'image',
    'content' => 
    array (
    ),
    'value' => 'https://images.pexels.com/photos/325193/pexels-photo-325193.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  0 => 
  array (
    'name' => 'domain',
    'title' => '绑定二级域名前缀',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'rewrite',
    'title' => '伪静态',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'index/index' => '/blog$',
      'index/post' => '/blog/p/[:id]',
      'index/category' => '/blog/c/[:id]',
      'index/archieve' => '/blog/archieve',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);
