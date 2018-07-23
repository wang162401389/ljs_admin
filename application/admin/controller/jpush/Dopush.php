<?php

namespace app\admin\controller\jpush;

use app\common\controller\Backend;
use think\Db;
use think\log;

/**
 * 发送通知
 *
 * @icon fa fa-paper-plane
 */
class Dopush extends Backend
{
    
    public function _initialize()
    {
        parent::_initialize();
    }
    
    /**
     * 查看
     */
    public function index()
    {
        $this->view->selects = [
            'reg' => '注册N天未投资/从未投资的用户',
            'coupon' => '优惠券/注册红包/活动红包N天后失效的用户',
            'recepay' => '回款后N天未投资的用户',
            'birthday' => '今天过生日的用户',
            'rece_this_month' => '当月回款的用户',
            'withdraw_this_month' => '当月提现的用户'
        ];
        return $this->view->fetch();
    }
    
    /**
     * 编辑
     */
    public function grant()
    {
        $post = $this->request->post();
        
        $tmp = $uid_arr = [];
        if (!empty($post['data']))
        {
            foreach ($post['data'] as $v)
            {
                if ($v['name'] == 'uids' && !empty($v['value']))
                {
                    $uid_arr = explode(';', trim($v['value'], ';'));
                }
                elseif ($v['name'] == 'type')
                {
                    $type = $v['value'] == 'touzi' ? 1 : 2;
                }
                elseif ($v['name'] == 'row[cpn_param]')
                {
                    continue;
                }
                else
                {
                    $tmp[$v['name']][] = $v['value'];
                }
            }
            
            $miss = [];
            if (empty($uid_arr))
            {
                $this->error('发放用户不能为空');
            }
            else
            {
                //用户
                foreach ($uid_arr as $uid)
                {
                    $userinfo = Db::table('AppUser')->where('userId', $uid)->whereor('userPhone', $uid)->field('userId,userPhone')->find();
                    if (empty($userinfo['userId']))
                    {
                        $miss[] = $uid;
                    }
                    else
                    {
                        $userid_arr[] = $userinfo['userId'];
                        $userphone[] = $userinfo['userPhone'];
                    }
                }
            }
            
            if (!empty($miss))
            {
                $this->error('未找到用户：('.implode(';', $miss).')');
            }
            
            if (empty($tmp))
            {
                $this->error('未设置优惠券规则');
            }
            foreach ($tmp['money'] as $key => $val)
            {
                if ($val <= 0 || $tmp['num'][$key] <= 0 || $tmp['expiry'][$key] <= 0 || $tmp['limitday'][$key] < 0 || $tmp['limitmoney'][$key] < 0)
                {
                    $this->error('优惠券设置错误');
                }
            }
            
            $miss_grant = [];
            // 启动事务
            Db::startTrans();
            try{
                $tz_money = $jx_num = 0;
                //种类
                foreach ($tmp['num'] as $k => $count)
                {
                    $tz_money += $tmp['money'][$k] * $count;
                    $jx_num += $count;
                    //用户
                    foreach ($userid_arr as $uid)
                    {
                        $ins = [];
                        //数量
                        for ($i = 0; $i < $count; $i++)
                        {
                            $ins[$i]['userId'] = $uid;
                            $ins[$i]['defId'] = 'adminGrant';
                            $ins[$i]['defName'] = '系统发放';
                            $ins[$i]['couponsType'] = $type;
                            $ins[$i]['money'] = $tmp['money'][$k] * 100;
                            $ins[$i]['limitDay'] = $tmp['limitday'][$k];
                            $ins[$i]['limitMoney'] = $tmp['limitmoney'][$k] * 100;
                            $ins[$i]['useTime'] = $ins[$i]['createdTime'] = $ins[$i]['updatedTime'] = date('Y-m-d H:i:s');
                            $ins[$i]['endTime'] = date('Y-m-d H:i:s', strtotime("+{$tmp['expiry'][$k]} days"));
                            $ins[$i]['grantMan'] = $this->auth->id;
                        }
                        $res = Db::table('AppCoupon')->insertAll($ins);
                        if ($res != $count)
                        {
                            $miss_grant[] = $uid;
                        }
                    }
                }
                
                if (!empty($miss_grant))
                {
                    // 回滚事务
                    Db::rollback();
                }
                else
                {
                    $javaapi = new \fast\Javaapi();
                    $req = [];
                    $req['mobile'] = implode(',', $userphone);
                    $req['time'] = $_SERVER['REQUEST_TIME'];
                    if ($type == 1) 
                    {
                        $req['smsCode'] = 'LJS_SENDCOUPONS_TO_USERPHONE_TZQ';
                        $req['args'][] = $tz_money;
                    }
                    else
                    {
                        $req['smsCode'] = 'LJS_SENDCOUPONS_TO_USERPHONE_JXQ';
                        $req['args'][] = $jx_num;
                    }
                    Log::write("发放优惠券发送短信请求参数：".var_export($req, true), 'java');
                    $javaapi->sendSms(['message' => encrypt(json_encode($req))]);
                    Log::write("发放优惠券发送短信请求密文：".encrypt(json_encode($req)), 'java');
                    
                    // 提交事务
                    Db::commit();
                }
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            
            if (!empty($miss_grant))
            {
                $this->error('发放失败,请重发');
            }
            else
            {
                $this->success();
            }
        }
    }
}