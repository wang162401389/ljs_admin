<?php

namespace addons\blog\controller\wxapp;

use addons\blog\model\Category;
use addons\blog\model\Post as PostModel;

/**
 * 单页
 */
class Page extends Base
{

    protected $noNeedLogin = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 关于我
     */
    public function aboutme()
    {
        $id = 1;
        $post = PostModel::get($id);
        if (!$post) {
            $this->error(__('No specified article found'));
        }
        $category = Category::get($post['category_id']);
        if (!$category) {
            $this->error(__('No specified channel found'));
        }
        $post->setInc("views", 1);

        $my = [
            'avatar'     => 'https://cdn.fastadmin.net/assets/img/avatar.png',
            'name'       => 'FastAdmin',
            'career' => '专职设计师',
        ];
        $this->success('', ['my' => $my, 'pageInfo' => $post, 'categoryInfo' => $category]);
    }

}
