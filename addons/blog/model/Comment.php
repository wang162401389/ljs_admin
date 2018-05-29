<?php

namespace addons\blog\model;

use app\common\library\Email;
use think\Exception;
use think\Model;
use think\Validate;

class Comment extends Model
{

    // 表名
    protected $name = 'blog_comment';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];

    protected static function init()
    {

    }

    public function getAvatarAttr($value, $data)
    {
        return isset($data['avatar']) && $data['avatar'] ? $data['avatar'] : "https://secure.gravatar.com/avatar/" . md5($data['email']) . "?s=96&d=&r=X";
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

    public static function postComment($params)
    {
        $config = get_addon_config('blog');
        $request = request();
        $request->filter('strip_tags');
        $post_id = (int)$request->post("post_id");
        $pid = intval($request->post("pid", 0));
        $username = $request->post("username");
        $email = $request->post("email", "");
        $website = $request->post("website", "");
        $content = $request->post("content");
        $avatar = $request->post("avatar", "");
        $subscribe = intval($request->post("subscribe", 0));
        $useragent = $request->server('HTTP_USER_AGENT', '');
        $ip = $request->ip();
        $website = $website != '' && substr($website, 0, 7) != 'http://' && substr($website, 0, 8) != 'https://' ? "http://" . $website : $website;
        $content = nl2br($content);
        $token = $request->post('__token__');

        $post = Post::get($post_id);
        if (!$post || $post['status'] == 'hidden') {
            throw new Exception("日志未找到");
        }

        $rule = [
            'pid'       => 'require|number',
            'username'  => 'require|chsDash|length:3,30',
            'email'     => 'email|length:3,30',
            'website'   => 'url|length:3,50',
            'content'   => 'require|length:3,250',
            '__token__' => 'token',
        ];
        $data = [
            'pid'       => $pid,
            'username'  => $username,
            'email'     => $email,
            'website'   => $website,
            'content'   => $content,
            '__token__' => $token,
        ];
        $validate = new Validate($rule);
        $result = $validate->check($data);
        if (!$result) {
            throw new Exception($validate->getError());
        }

        $lastcomment = self::get(['post_id' => $post_id, 'email' => $email, 'ip' => $ip]);
        if ($lastcomment && time() - $lastcomment['createtime'] < 30) {
            throw new Exception("对不起！您发表评论的速度过快！请稍微休息一下，喝杯咖啡");
        }
        if ($lastcomment && $lastcomment['content'] == $content) {
            throw new Exception("您可能连续了相同的评论，请不要重复提交");
        }
        $data = [
            'pid'       => $pid,
            'post_id'   => $post_id,
            'username'  => $username,
            'email'     => $email,
            'content'   => $content,
            'avatar'    => $avatar,
            'ip'        => $ip,
            'useragent' => $useragent,
            'subscribe' => (int)$subscribe,
            'website'   => $website,
            'status'    => 'normal'
        ];
        self::create($data);

        $post->setInc('comments');
        if ($pid) {
            //查找父评论，是否并发邮件通知
            $parent = self::get($pid);
            if ($parent && $parent['subscribe'] && Validate::is($parent['email'], 'email')) {
                $title = "{$parent['username']}，您发表在《{$post['title']}》上的评论有了新回复 - {$config['name']}";
                $post_url = addon_url("blog/index/post", ['id' => $post['id']], false, true);
                $unsubscribe_url = addon_url("blog/index/unsubscribe", ['id' => $parent['id'], 'key' => md5($parent['id'] . $parent['email'])]);
                $content = "亲爱的{$parent['username']}：<br />您于" . date("Y-m-d H:i:s") .
                    "在《<a href='{$post_url}' target='_blank'>{$post['title']}</a>》上发表的评论<br /><blockquote>{$parent['content']}</blockquote>" .
                    "<br />{$username}发表了回复，内容是<br /><blockquote>{$content}</blockquote><br />您可以<a href='{$post_url}'>点击查看评论详情</a>。" .
                    "<br /><br />如果你不愿意再接受最新评论的通知，<a href='{$unsubscribe_url}'>请点击这里取消</a>";
                $email = new Email;
                $result = $email
                    ->to($parent['email'])
                    ->subject($title)
                    ->message('<div style="min-height:550px; padding: 100px 55px 200px;">' . $content . '</div>')
                    ->send();
            }
        }
    }

    public function sublist()
    {
        return $this->hasMany("Comment", "pid");
    }

}
