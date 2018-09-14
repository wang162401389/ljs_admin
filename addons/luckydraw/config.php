<?php

return array(
    0 =>
        array(
            'name'    => 'score',
            'title'   => '抽奖积分',
            'type'    => 'string',
            'content' =>
                array(),
            'value'   => '100',
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '抽奖所需要的积分',
            'ok'      => '',
            'extend'  => '',
        ),
    1 =>
        array(
            'name'    => 'copies',
            'title'   => '抽奖次数',
            'type'    => 'string',
            'content' =>
                array(),
            'value'   => '50',
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '抽奖剩余的奖品余数，每抽一次-1',
            'ok'      => '',
            'extend'  => '',
        ),
    2 =>
        array(
            'name'    => 'award_item',
            'title'   => '奖品内容',
            'type'    => 'array',
            'content' =>
                array(),
            'value'   =>
                array(
                    '一等奖' => '10000元',
                    '二等奖' => '5000元',
                    '三等奖' => '2000元',
                    '四等奖' => '500元',
                    '五等奖' => '100元',
                    '六等奖' => '10元',
                ),
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '',
            'ok'      => '',
            'extend'  => '',
        ),
    3 =>
        array(
            'name'    => 'winning_rate',
            'title'   => '中奖几率',
            'type'    => 'array',
            'content' =>
                array(),
            'value'   =>
                array(
                    '一等奖中奖比例' => '0',
                    '二等奖中奖比例' => '1',
                    '三等奖中奖比例' => '2',
                    '四等奖中奖比例' => '15',
                    '五等奖中奖比例' => '50',
                    '六等奖中奖比例' => '80',
                ),
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '中奖比例（%）',
            'ok'      => '',
            'extend'  => '',
        ),
    4 =>
        array(
            'name'    => 'rule',
            'title'   => '注意事项',
            'type'    => 'string',
            'content' =>
                array(),
            'value'   => '奖品内容与中奖几率设置对应,缺一不可',
            'rule'    => 'required',
            'msg'     => '',
            'tip'     => '',
            'ok'      => '',
            'extend'  => '',
        ),
);
