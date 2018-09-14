<?php

namespace addons\blog\controller;

use addons\blog\model\Category;
use addons\blog\model\Comment;
use addons\blog\model\Post;

/**
 * 博客
 */
class Index extends Base
{

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    // 博客首页
    public function index()
    {
        $postlist = Post::where(['status' => 'normal'])
            ->with('category')
            ->order('weigh desc,id desc')
            ->paginate($this->view->config['listpagesize']);
        $page = \think\paginator::getCurrentPage();
        $urls = $postlist->getUrlRange($page - 1, $page + 1);
        $prevurl = $page == 1 ? '' : array_shift($urls);
        $nexturl = $page == $postlist->lastPage() ? '' : array_pop($urls);

        $this->view->assign("postlist", $postlist);
        $this->view->assign('prevurl', $prevurl);
        $this->view->assign('nexturl', $nexturl);
        return $this->view->fetch();
    }

    // 分类日志列表
    public function category()
    {
        $id = (int)$this->request->param('id');
        $category = Category::get($id);
        if (!$category || $category['status'] == 'hidden') {
            $this->error("分类未找到");
        }

        $postlist = Post::where(['status' => 'normal'])
            ->where('category_id', $category['id'])
            ->with('category')
            ->order('weigh desc,id desc')
            ->paginate($this->view->config['listpagesize']);
        $page = \think\paginator::getCurrentPage();
        $urls = $postlist->getUrlRange($page - 1, $page + 1);
        $prevurl = $page == 1 ? '' : array_shift($urls);
        $nexturl = $page == $postlist->lastPage() ? '' : array_pop($urls);

        $this->view->assign("postlist", $postlist);
        $this->view->assign('prevurl', $prevurl);
        $this->view->assign('nexturl', $nexturl);
        $this->view->assign('category', $category);
        return $this->view->fetch();
    }

    // 日志详情
    public function post()
    {
        $id = (int)$this->request->param('id');
        $post = Post::get($id);
        if (!$post || $post['status'] == 'hidden') {
            $this->error("日志未找到");
        }
        $post->setInc('views');

        $category = Category::get($post['category_id']);

        $commentlist = Comment::where(['post_id' => $id, 'pid' => 0, 'status' => 'normal'])
            ->with('sublist')
            ->order('id desc')
            ->paginate($this->view->config['commentpagesize']);

        $nextpost = Post::where('id', '>', $post['id'])->where('status', 'normal')->find();
        $this->view->assign("post", $post);
        $this->view->assign("nextpost", $nextpost);
        $this->view->assign("category", $category);
        $this->view->assign("commentlist", $commentlist);
        return $this->view->fetch();
    }

    // 分类列表
    public function categorylist()
    {
        return $this->view->fetch();
    }

    // 日志归档
    public function archieve()
    {
        $postlist = Post::where('status', 'normal')->field('id,title,createtime')->cache(3600 * 365)->select();
        $yearlist = [];
        foreach ($postlist as $k => $v) {
            $yearlist[date("Y", $v['createtime'])][] = ['id' => $v['id'], 'title' => $v['title'], 'url' => $v['url']];
        }
        $this->view->assign('yearlist', $yearlist);
        return $this->view->fetch();
    }

    // 取消订阅评论
    public function unsubscribe()
    {
        $id = (int)$this->request->param('id');
        $key = $this->request->param('key');
        $comment = Comment::get($id);
        if (!$comment) {
            $this->error("日志评论未找到");
        }
        if ($key !== md5($comment['id'] . $comment['email'])) {
            $this->error("无法进行该操作");
        }
        if (!$comment['subscribe']) {
            $this->error("评论已经取消订阅，请勿重复操作");
        }
        $comment->subscribe = 0;
        $comment->save();
        $this->success('取消评论订阅成功');
    }

}
