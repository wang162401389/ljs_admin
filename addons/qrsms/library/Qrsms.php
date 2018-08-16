<?php

namespace addons\qrsms\library;

/**
 * 启瑞云SMS短信发送
 */
class Qrsms
{

    private $_params = [];
    protected $error = '';
    protected $config = [];

    public function __construct($options = [])
    {
        if ($config = get_addon_config('qrsms')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->config = array_merge($this->config, is_array($options) ? $options : []);
    }

    /**
     * 单例
     * @param array $options 参数
     * @return Qrsms
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * 立即发送短信
     *
     * @return boolean
     */
    public function send()
    {
        $this->error = '';
        $requestData = array(
            'un' => $this->config['APIKey'],
            'pw' => $this->config['APISecret'],
            'sm' => $this->config['sign'] . $this->_params['msg'],
            'da' => $this->_params['mobile'],
            'rd' => 1,
            'dc' => 15,
            'rf' => 2,
            'tf' => 3,
        );

        $url = $this->config['gateway'] . '?' . http_build_query($requestData);
        $redata = $this->request($url);
        $rejson = json_decode($redata);
        if (isset($rejson->r) && $rejson->r > 0) {
            return false;
        }
        if (isset($rejson->id) && $rejson->id > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 请求发送
     * @return string 返回发送状态
     */
    private function request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 接收手机
     * @param   string $mobile 手机号码
     * @return Qrsms
     */
    public function mobile($mobile = '')
    {
        $this->_params['mobile'] = $mobile;
        return $this;
    }

    /**
     * 短信内容
     * @param   string $msg 短信内容
     * @return Qrsms
     */
    public function msg($msg = '')
    {
        $this->_params['msg'] = $msg;
        return $this;
    }

}