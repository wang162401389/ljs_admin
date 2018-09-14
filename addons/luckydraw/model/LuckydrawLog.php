<?php

namespace addons\luckydraw\model;

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

    protected $dateFormat = 'Y/m/d H:i:s';
    // 追加属性
    protected $append = [
    ];
    protected static $config = [];

    //自定义初始化
    protected static function init()
    {
        $config = get_addon_config('luckydraw');
        self::$config = $config;
    }

    public function getRedeemtimeAttr($value, $data)
    {
        if (empty($value)) {
            return '未兑换';
        } else {
            return '已兑换';
        }
    }

    /**
     * @param $args
     * @param int $uid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_list($args, $uid)
    {
        $map = array();
        //分頁的
        $limit = isset($args['limit']) ? $args['limit'] : 20;
        $offset = isset($args['offset']) ? $args['offset'] * $args['limit'] : 0;
        $order = isset($args['sortOrder']) ? $args['sortOrder'] : "desc";
        $orderSql = 'createtime' . " {$order}";
        $map['user_id'] = $uid;
        $field = "*";
        $list = $this->field($field)
            ->order($orderSql)
            ->where($map)
            ->limit($offset, $limit)
            ->select();
        #echo $this->getLastSql();die;
        $total = $this->field($field)
            ->order($orderSql)
            ->where($map)
            ->count();
        $datas = array(
            "code"  => 200,
            "msg"   => "ok",
            "total" => $total,
            "rows"  => $list,
        );
        return $datas;
    }

}
