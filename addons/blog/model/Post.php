<?php

namespace addons\blog\model;

use think\Model;

class Post extends Model
{

    // 表名
    protected $name = 'blog_post';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        'url',
        'create_date',
    ];

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $row->save(['weigh' => $row['id']]);
        });
    }

    public function getCreateDateAttr($value, $data)
    {
        return human_date($data['createtime']);
    }

    public function getThumbAttr($value, $data)
    {
        $value = $value ? $value : '/assets/addons/blog/img/thumb.png';
        return cdnurl($value, true);
    }

    public function getImageAttr($value, $data)
    {
        $value = $value ? $value : '/assets/addons/blog/img/thumb.png';
        return cdnurl($value, true);
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getUrlAttr($value, $data)
    {
        return addon_url('blog/index/post', [':id' => $data['id']]);
    }

    public function category()
    {
        return $this->belongsTo('addons\blog\model\Category')->setEagerlyType(1);
    }

}
