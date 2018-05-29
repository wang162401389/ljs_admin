<?php

namespace addons\ucloud\controller;

use app\common\model\Attachment;
use think\addons\Controller;

/**
 * Ucloud
 *
 */
class Index extends Controller
{

    public function index()
    {
        $this->error("当前插件暂无前台页面");
    }

    public function token()
    {
        $config = get_addon_config('ucloud');
        $bucket = $config['bucket'];
        $method = $this->request->post('method');
        $name = $this->request->post('name');
        $md5 = $this->request->post('md5');
        $type = $this->request->post('type');
        $suffix = substr($name, stripos($name, '.') + 1);
        $search = ['{year}', '{mon}', '{month}', '{day}', '{filemd5}', '{suffix}', '{.suffix}'];
        $replace = [date("Y"), date("m"), date("m"), date("d"), $md5, $suffix, '.' . $suffix];
        $filename = ltrim(str_replace($search, $replace, $config['savekey']), '/');

        $auth = new \addons\ucloud\library\Auth($config['public_key'], $config['private_key']);
        $token = $auth->token($method, $bucket, $filename, $md5, $type);
        $this->success('', null, ['token' => $token, 'filename' => $filename]);
        return;
    }

    public function notify()
    {
        $config = get_addon_config('ucloud');
        $bucket = $config['bucket'];
        $method = $this->request->post('method');
        $size = $this->request->post('size');
        $name = $this->request->post('name');
        $md5 = $this->request->post('md5');
        $type = $this->request->post('type');
        $token = $this->request->post('token');
        $url = $this->request->post('url');
        $filename = ltrim($url, '/');
        $suffix = substr($name, stripos($name, '.') + 1);
        $auth = new \addons\ucloud\library\Auth($config['public_key'], $config['private_key']);
        if ($token == $auth->token($method, $bucket, $filename, $md5, $type)) {
            $attachment = Attachment::getBySha1($md5);
            if (!$attachment) {
                $params = array(
                    'admin_id'    => (int)session('admin.id'),
                    'user_id'     => (int)cookie('uid'),
                    'filesize'    => $size,
                    'imagewidth'  => 0,
                    'imageheight' => 0,
                    'imagetype'   => $suffix,
                    'imageframes' => 0,
                    'mimetype'    => $type,
                    'url'         => $url,
                    'uploadtime'  => time(),
                    'storage'     => 'ucloud',
                    'sha1'        => $md5,
                );
                Attachment::create($params);
            }
            $this->success();
        } else {
            $this->error(__('You have no permission'));
        }
        return;
    }

}
