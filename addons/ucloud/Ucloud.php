<?php

namespace addons\ucloud;

use think\Addons;

/**
 * UCloud插件
 */
class Ucloud extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 加载配置
     */
    public function uploadConfigInit(&$upload)
    {
        $config = $this->getConfig();
        if ($config['uploadmode'] === 'client')
        {
            $upload = [
                'cdnurl'    => $config['cdnurl'],
                'uploadurl' => $config['uploadurl'],
                'bucket'    => $config['bucket'],
                'maxsize'   => $config['maxsize'],
                'mimetype'  => $config['mimetype'],
                'multipart' => [],
                'multiple'  => $config['multiple'] ? true : false,
                'storage'   => 'ucloud'
            ];
        }
        else
        {
            $upload = array_merge($upload, [
                'maxsize'  => $config['maxsize'],
                'mimetype' => $config['mimetype'],
                'multiple' => $config['multiple'] ? true : false,
            ]);
        }
    }

    /**
     * 上传成功后
     */
    public function uploadAfter($attachment)
    {
        $config = $this->getConfig();
        if ($config['uploadmode'] === 'server')
        {
            $file = ROOT_PATH . 'public' . str_replace('/', DIRECTORY_SEPARATOR, $attachment->url);

            $name = basename($file);
            $md5 = md5_file($file);

            $suffix = substr($name, stripos($name, '.') + 1);
            $search = ['{year}', '{mon}', '{month}', '{day}', '{filemd5}', '{suffix}', '{.suffix}'];
            $replace = [date("Y"), date("m"), date("m"), date("d"), $md5, $suffix, '.' . $suffix];
            $filename = ltrim(str_replace($search, $replace, $config['savekey']), '/');

            $auth = new \addons\ucloud\library\Auth($config['public_key'], $config['private_key']);
            $token = $auth->token('POST', $config['bucket'], $filename, $md5, $attachment->mimetype);
            $multipart = [
                [
                    'name'     => 'FileName',
                    'contents' => $filename
                ],
                [
                    'name'     => 'Authorization',
                    'contents' => $token,
                ],
                [
                    'name'     => 'file',
                    'contents' => fopen($file, 'r'),
                    'filename' => $name,
                ]
            ];
            try
            {
                $client = new \GuzzleHttp\Client();
                $res = $client->request('POST', $config['uploadurl'], [
                    'multipart' => $multipart,
                    'headers'   => [
                        'Content-MD5' => $md5
                    ],
                ]);
                $code = $res->getStatusCode();
                //成功不做任何操作
            }
            catch (\GuzzleHttp\Exception\ClientException $e)
            {
                $attachment->delete();
                unlink($file);
                echo json_encode(['code' => 0, 'msg' => '无法上传到远程服务器，错误:' . $e->getMessage()]);
                exit;
            }
        }
    }

}
