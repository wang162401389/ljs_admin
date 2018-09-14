<?php

namespace addons\yunpian\library;

class yunpian
{
    private static $instance;
    private $_params = [];
    public $error = '';
    protected $config = [];

    public function __construct($options = [])
    {
        if ($config = get_addon_config('yunpian')) {
            $this->config = array_merge($this->config, $config);
        }
        $this->config = array_merge($this->config, is_array($options) ? $options : []);
    }

    /**
     * 单例
     * @param array $options 参数
     * @return yunpian
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
     * 接收手机
     * @param   string $mobile 手机号码
     * @return yunpian
     */
    public function mobile($mobile = '')
    {
        $this->_params['mobile'] = $mobile;
        return $this;
    }

    /**
     * 设置模板
     * @param string $tpl_id 短信模板
     * @return yunpian
     */
    public function template($tpl_id = '')
    {
        $this->_params['tpl_id'] = $tpl_id;
        return $this;
    }

    /**
     * 设置参数
     * @param array $param
     * @return yunpian
     */
    public function param(array $param = [])
    {
        $param['company'] = $this->config['company'];
        $tpl_value = '';
        foreach ($param as $k => &$v) {
            if ($k == 'mobile' || $k == 'tpl_id') continue;
            $tpl_value .= urlencode('#' . $k . '#') . '=' . urlencode($v) . '&';
        }
        $tpl_value = rtrim($tpl_value, '&');
        unset($v);

        $this->_params['tpl_value'] = $tpl_value;

        return $this;
    }

    /**
     * 立即发送
     * @return boolean
     */
    public function send()
    {
        $this->error = '';
        $params = $this->_params();

        $options = [
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded; charset=utf-8'
            )
        ];
        $response = \fast\Http::sendRequest('https://sms.yunpian.com/v2/sms/tpl_single_send.json', $params, 'POST', $options);
        if ($response['ret']) {
            $res = (array)json_decode($response['msg'], TRUE);
            if (isset($res['code']) && $res['code'] == '0')
                return TRUE;
            $this->error = isset($res['detail']) ? $res['detail'] : $res['msg'];
        }
        return FALSE;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    private function _params()
    {
        return array_merge([
            'apikey' => $this->config['apikey']
        ], $this->_params);
    }
}