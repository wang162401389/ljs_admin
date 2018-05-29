<?php

namespace app\admin\controller\operate;

use app\common\controller\Backend;

/**
 * 发放优惠券
 *
 * @icon fa fa-user
 */
class Grantcoupon extends Backend
{
    
    protected $noNeedRight = ['grant'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 查看
     */
    public function index()
    {
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
            
            if (empty($uid_arr)) 
            {
                $this->error('发放用户不能为空');
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
            
            $miss = $miss_grant = [];
            //种类
            foreach ($tmp['num'] as $k => $count) 
            {
                //用户
                foreach ($uid_arr as $uid)
                {
                    $userid = \think\Db::table('AppUser')->where('userId', $uid)->whereor('userPhone', $uid)->value('userId');
                    if (!empty($userid))
                    {
                        $ins = [];
                        //数量
                        for ($i = 0; $i < $count; $i++)
                        {
                            $ins[$i]['userId'] = $userid;
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
                        $res = \think\Db::table('AppCoupon')->insertAll($ins);
                        if ($res != $count) 
                        {
                            $miss_grant[] = $uid;
                        }
                    }
                    else
                    {
                        $miss[] = $uid;
                    } 
                }
            }
            
            if (!empty($miss) || !empty($miss_grant)) 
            {
                $msg = [];
                if (!empty($miss)) 
                {
                    $msg[] = '未找到用户：('.implode(';', $miss).')';
                }
                if (!empty($miss_grant))
                {
                    $msg[] = '发放优惠券失败用户：('.implode(';', $miss_grant).')';
                }
                $this->error(implode(',', $msg));
            }
            else 
            {
                $this->success();
            }            
        }
    }
}