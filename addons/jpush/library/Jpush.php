<?php

namespace addons\jpush\library;

/**
 * 极光推送
 * 如有问题，请加微信  wch586wsh  QQ:510461252
 */
class Jpush
{

    private $_params = [];
    protected $error = '';
    protected $config = [];

    public function __construct($options = [])
    {
        if ($config = get_addon_config('jpush'))
        {
            $this->config = array_merge($this->config, $config);
        }
        $this->config = array_merge($this->config, is_array($options) ? $options : []);
    }

    /**
     * 单例
     * @param array $options 参数
     * @return Jpush
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance))
        {
            self::$instance = new static($options);
        }
        return self::$instance;
    }

    /**
     * 立即推送
     * @return boolean
     */
    public function send()
    {
        $this->error = '';
        $params = $this->_params;
        vendor('jpush.jpush.autoload');
        
        $app_key = $this->config['AppKey'];
        $master_secret = $this->config['MasterSecret'];
        
        $client = new \JPush\Client($app_key, $master_secret);
        
        try {
            $response = $client->push()
            //['all', 'android', 'ios', 'winphone']
            ->setPlatform($params['platform'])
            // 一般情况下，关于 audience 的设置只需要调用 addAlias、addTag、addTagAnd  或 addRegistrationId
            // 这四个方法中的某一个即可，这里仅作为示例，当然全部调用也可以，多项 audience 调用表示其结果的交集
            // 即是说一般情况下，下面三个方法和没有列出的 addTagAnd 一共四个，只适用一个便可满足大多数的场景需求
            
            ->addAlias($params['mobile'])
            //->addTag(['tag1', 'tag2'])
            // ->addRegistrationId($registration_id)
            
            ->setNotificationAlert($params['alert'])
//             ->iosNotification($params['ios_alert'], [
//                 'sound' => 'sound.caf',
//                 // 'badge' => '+1',
//                 // 'content-available' => true,
//                 // 'mutable-content' => true,
//                 'category' => 'jiguang',
//                 'extras' => [
//                     'key' => 'value',
//                     'jiguang'
//                 ],
//             ])
//             ->androidNotification($params['android_alert'], [
//                 'title' => 'hello jpush',
//                 // 'builder_id' => 2,
//                 'extras' => [
//                     'key' => 'value',
//                     'jiguang'
//                 ],
//             ])
//             ->message('message content', [
//                 'title' => 'hello jpush',
//                 // 'content_type' => 'text',
//                 'extras' => [
//                     'key' => 'value',
//                     'jiguang'
//                 ],
//             ])
            ->options([
//                 // sendno: 表示推送序号，纯粹用来作为 API 调用标识，
//                 // API 返回时被原样返回，以方便 API 调用方匹配请求与返回
//                 // 这里设置为 100 仅作为示例
                
//                 // 'sendno' => 100,
                
//                 // time_to_live: 表示离线消息保留时长(秒)，
//                 // 推送当前用户不在线时，为该用户保留多长时间的离线消息，以便其上线时再次推送。
//                 // 默认 86400 （1 天），最长 10 天。设置为 0 表示不保留离线消息，只有推送当前在线的用户可以收到
//                 // 这里设置为 1 仅作为示例
                
//                 // 'time_to_live' => 1,
                
//                 // apns_production: 表示APNs是否生产环境，
//                 // True 表示推送生产环境，False 表示要推送开发环境；如果不指定则默认为推送生产环境
                
                'apns_production' => true,
                
//                 // big_push_duration: 表示定速推送时长(分钟)，又名缓慢推送，把原本尽可能快的推送速度，降低下来，
//                 // 给定的 n 分钟内，均匀地向这次推送的目标用户推送。最大值为1400.未设置则不是定速推送
//                 // 这里设置为 1 仅作为示例
                
//                 // 'big_push_duration' => 1
            ])
            ->send();
            
            return $response;
            
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            // try something here
            $this->error = 'InvalidConnect';
            //return $e;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            // try something here
            $this->error = 'InvalidRequest';
            //return $e;
        }
    }

    /**
     * 获取错误信息
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 接收手机
     * @param   string  $mobile     手机号码
     * @return Jpush
     */
    public function mobile($mobile = '')
    {
        $this->_params['mobile'] = $mobile;
        return $this;
    }

    /**
     * 推送内容
     * @param   string  $alert        推送内容
     * @return Jpush
     */
    public function alert($alert = '')
    {
        $this->_params['alert'] = $alert;
        return $this;
    }

    /**
     * 平台
     * @param array $platform
     * @return Jpush
     */
    public function platform($platform = 'all')
    {
        $this->_params['platform'] = $platform;
        return $this;
    }
}
