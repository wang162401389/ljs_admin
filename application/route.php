<?php
use think\Route;

Route::pattern([
    'id' => '\d+',
]);

Route::rule([
    //'gg/:id' => 'index/Notice/detail',
]);

return [
    //别名配置,别名只能是映射到控制器且访问时必须加上请求的方法
    '__alias__'   => [
    ],
    //变量规则
    '__pattern__' => [
    ],
//        域名绑定到模块
//        '__domain__'  => [
//            'admin' => 'admin',
//            'api'   => 'api',
//        ],
];
