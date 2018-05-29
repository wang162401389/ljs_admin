<?php

namespace app\admin\model;

use think\Model;

class Riskproblem extends Model
{
    // 表名
    protected $table = 'RiskProblem';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [

    ];
    
    public function answers()
    {
        return $this->hasMany('Riskanswer', 'problemId');
    }
    
    public function getStatusList()
    {
        return ['1' => __('Normal'), '0' => __('Hidden')];
    }
}