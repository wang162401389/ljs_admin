<?php

namespace addons\jpush\controller;

use think\addons\Controller;

class Index extends Controller
{

    public function index()
    {
        return $this->view->fetch();
    }
    
    public function send()
    {
        $mobile = $this->request->post('mobile');
        $alert = $this->request->post('alert');
        $jpush = new \addons\jpush\library\Jpush();
        $ret = $jpush->platform('all')->mobile($mobile)->alert($alert)->send();
//         Array
//         (
//             [body] => Array
//             (
//                 [sendno] => 343545483
//                 [msg_id] => 58546795264977810
//                 )
            
//             [http_code] => 200
//             [headers] => Array
//             (
//                 [0] => HTTP/1.1 200 OK
//                 [Server] => nginx
//                 [Date] => Fri, 20 Jul 2018 05:54:28 GMT
//                 [Content-Type] => application/json
//                 [Content-Length] => 51
//                 [Connection] => keep-alive
//                 [X-Rate-Limit-Limit] => 1200
//                 [X-Rate-Limit-Remaining] => 1199
//                 [X-Rate-Limit-Reset] => 60
//                 [X-Jpush-Msgid] => 58546795264977810
//                 [X-Jpush-Timestamp] => 1532066068473
//             )
        
//         )
        if (isset($ret['http_code']) && $ret['http_code'] == 200)
        {
            $this->success("发送成功");
        }
        else
        {
            $this->error("发送失败！失败原因：" . $jpush->getError());
        }
    }

}
