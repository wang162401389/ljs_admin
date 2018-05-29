<?php

namespace addons\upload;

use think\Addons;

/**
 * 示例上传插件
 */
class Upload extends Addons
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
     * 必须实现此行为，用于渲染上传所提交的数据
     */
    public function uploadConfigInit(&$upload)
    {
        $config = $this->getConfig();

        $upload = [
            'cdnurl'    => $config['cdnurl'],
            'uploadurl' => $config['uploadurl'],
            'bucket'    => $config['bucket'],
            'maxsize'   => $config['maxsize'],
            'mimetype'  => $config['mimetype'],
            'multipart' => [],
            'multiple'  => $config['multiple'] ? true : false,
        ];
    }

}
