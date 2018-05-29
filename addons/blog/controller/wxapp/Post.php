<?php

namespace addons\blog\controller\wxapp;

use addons\blog\model\Category;
use addons\blog\model\Post as PostModel;
use addons\blog\model\Comment;

/**
 * 日志
 */
class Post extends Base
{

    protected $noNeedLogin = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 日志列表
     */
    public function index()
    {
        $params = [];
        $category = (int)$this->request->request('channel');
        $page = (int)$this->request->request('page');

        if ($category) {
            $params['category_id'] = $category;
        }
        $page = max(1, $page);

        $postList = PostModel::
        with('category')
            ->where('status', 'normal')
            ->field('id,category_id,title,summary,image,createtime')
            ->page($page)
            ->order('weigh desc,id desc')
            ->select();
        foreach ($postList as $index => &$item) {
            $item['summary'] = mb_substr(trim(strip_tags($item['summary'])), 0, 100);
        }
        $this->success('', ['postList' => $postList]);
    }

    /**
     * 日志详情
     */
    public function detail()
    {
        $id = $this->request->request('id', '');
        $post = PostModel::get($id);
        if (!$post || $post['status'] == 'hidden') {
            $this->error(__('No specified article found'));
        }
        $category = Category::get($post['category_id']);
        if (!$category) {
            $this->error(__('No specified channel found'));
        }
        $post->setInc("views", 1);

        $commentList = Comment::where('post_id', $post->id)->where('status', 'normal')->limit(10)->select();

        $this->success('', ['postInfo' => $post, 'categoryInfo' => $category, 'commentList' => $commentList]);
    }

}
