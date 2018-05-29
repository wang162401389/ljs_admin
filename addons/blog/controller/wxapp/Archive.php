<?php

namespace addons\blog\controller\wxapp;

use addons\blog\model\Category;
use addons\blog\model\Post;
use app\common\model\Addon;

/**
 * 归档
 */
class Archive extends Base
{

    protected $noNeedLogin = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 首页
     */
    public function index()
    {
        $postlist = Post::where('status', 'normal')
            ->field('id,title,createtime')
            ->cache(3600 * 365)
            ->order('weigh desc,id desc')
            ->select();
        $list = [];
        foreach ($postlist as $k => $v) {
            $list[date("Y", $v['createtime'])][] = ['id' => $v['id'], 'title' => $v['title']];
        }
        $archiveList = [];
        foreach ($list as $index => $item) {
            $archiveList[] = [
                'title'    => $index . '年',
                'postList' => $item
            ];
        }
        $data = [
            'archiveList' => $archiveList
        ];
        $this->success('', $data);

    }


}
