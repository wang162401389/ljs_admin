<?php

namespace app\admin\model;

use think\Model;

/**
 * 抽奖日志模型
 */
class LuckydrawLog Extends Model
{

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;



}
