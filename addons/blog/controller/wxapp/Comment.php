<?php

namespace addons\blog\controller\wxapp;

use think\Config;
use think\Exception;

/**
 * 评论
 */
class Comment extends Base
{

    protected $noNeedLogin = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 评论列表
     */
    public function index()
    {
        $post_id = (int)$this->request->request('post_id');
        $page = (int)$this->request->request('page');
        Config::set('paginate.page', $page);
        $commentList = \addons\blog\model\Comment::where('post_id', $post_id)->where('status', 'normal')
            ->page($page)->select();
        $this->success('', ['commentList' => $commentList]);
    }

    /**
     * 发表评论
     */
    public function post()
    {
        try {
            $params = $this->request->post();
            \addons\blog\model\Comment::postComment($params);
        } catch (Exception $e) {
            $this->error($e->getMessage(), null, ['token' => $this->request->token()]);
        }
        $this->success(__('评论成功'));
    }

}
