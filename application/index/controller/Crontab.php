<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Db;
use think\Log;

/**
 * 定时任务列表
 * @internal
 */
class Crontab extends Frontend
{
    
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $layout = '';
    
    /**
     * 注册实名同步新老版本数据库
     */
    public function synchronous($id = null)
    {
        if ($id === null) 
        {
            return 'id empty!';
        }
        
        $row = Db::table('crontab')->where('id', $id)->field('executes,executetime')->find();
        if (empty($row))
        {
            return 'no crontab!';
        }
        
        if ($row['executes'] == 1)
        {
            $starttime = '2018-06-01 00:00:00';
        }
        else 
        {
            $starttime = date('Y-m-d H:i:s', $row['executetime'] - 60);
        }
        $endtime = date('Y-m-d H:i:s');
        
        $upd_data = Db::table('AppUser')->whereTime('updatedTime', 'between', [$starttime, $endtime])->field('userId,userPhone,userName,pid,userPass')->select();
        
        if (!empty($upd_data))
        {
            Log::write("userid_upd ： ". var_export(array_column($upd_data, 'userId'), true), 'crontab');
            foreach ($upd_data as $v) 
            {
                //members_status
                $ms_data = Db::connect("old_db")->name("members_status")->where('uid', $v['userId'])->field('id_status')->find();
                if (!empty($ms_data))
                {
                    if ($ms_data['id_status'] != 1 && !empty($v['userName']))
                    {
                        $upd = [];
                        $upd['sina_member_status'] = $upd['sina_phone'] = $upd['id_status'] = 1;
                        $res = Db::connect("old_db")->name("members_status")->where('uid', $v['userId'])->update($upd);
                        Log::write("userid ： ". $v['userId'] ."，更新members_status结果 ：".(bool)$res, 'crontab');
                    }
                }
                else
                {
                    $ins = [];
                    $ins['uid'] = $v['userId'];
                    $ins['phone_status'] = 1;
                    if (!empty($v['userName']))
                    {
                        $ins['sina_member_status'] = $ins['sina_phone'] = $ins['id_status'] = 1;
                    }
                    $res = Db::connect("old_db")->name("members_status")->insert($ins);
                    Log::write("userid ： ". $v['userId'] ."，添加members_status结果 ：".(bool)$res, 'crontab');
                }
                
                //member_info
                $mi_data = Db::connect("old_db")->name("member_info")->where('uid', $v['userId'])->field('uid')->find();
                if (!empty($mi_data))
                {
                    if (!empty($v['userName']))
                    {
                        $upd = [];
                        $upd['real_name'] = $v['userName'];
                        $upd['idcard'] = $v['pid'];
                        $upd['up_time'] = time();
                        $res = Db::connect("old_db")->name("member_info")->where('uid', $v['userId'])->update($upd);
                        Log::write("userid ： ". $v['userId'] ."，更新member_info结果 ：".(bool)$res, 'crontab');
                    }
                }
                else
                {
                    $ins = [];
                    $ins['uid'] = $v['userId'];
                    $ins['cell_phone'] = $v['userPhone'];
                    $ins['real_name'] = $v['userName'];
                    $ins['idcard'] = $v['pid'];
                    $ins['up_time'] = time();
                    $res = Db::connect("old_db")->name("member_info")->insert($ins);
                    Log::write("userid ： ". $v['userId'] ."，添加member_info结果 ：".(bool)$res, 'crontab');
                }
                
                //member
                $m_data = Db::connect("old_db")->name("members")->where('id', $v['userId'])->field('id,user_pass')->find();
                if (!empty($m_data) && $m_data['user_pass'] != $v['userPass'])
                {
                    $upd = [];
                    $upd['user_pass'] = $v['userPass'];
                    Db::connect("old_java_db")->name("members")->where('id', $v['userId'])->update($upd);
                    $res = Db::connect("old_db")->name("members")->where('id', $v['userId'])->update($upd);
                    Log::write("userid ： ". $v['userId'] ."，更新member结果 ：".(bool)$res, 'crontab');
                }
            }
        }

        $new_data = Db::table('AppUser')->whereTime('createdTime', 'between', [$starttime, $endtime])->select();

        if (!empty($new_data))
        {
            $insall = [];
            Log::write("new_userid_add ： ". var_export(array_column($new_data, 'userId'), true), 'crontab');
            foreach ($new_data as $val) 
            {
                $m_data = Db::connect("old_java_db")->name("members")->where('id', $val['userId'])->field('id')->find();
                if (empty($m_data))
                {
                    $ins = [];
                    $ins['id'] = $val['userId'];
                    $ins['user_name'] = $val['userPhone'];
                    $ins['user_pass'] = $val['userPass'];
                    $ins['user_phone'] = $val['userPhone'];
                    $ins['reg_time'] = $val['createdTime'];
                    $ins['reg_ip'] = $val['regIp'];
                    if (!empty($val['recommendPhone']))
                    {
                        $ins['recommend_id'] = Db::table('AppUser')->where('userPhone', $val['recommendPhone'])->value('userId');
                    }
                    $ins['last_log_ip'] = $val['lastLoginIp'];
                    $ins['last_log_time'] = $val['lastLoginTime'];
                    $ins['equipment'] = $val['regSource'];
                    $insall[] = $ins;
                }
            }

            if (!empty($insall))
            {
                $res = Db::connect("old_java_db")->name("members")->insertAll($insall);
                Log::write("userid ： ". $v['userId'] ."，添加member结果 ：".(bool)$res, 'crontab');
            }
        }
    }
    
    public function pickData()
    {
        $list = Db::connect('old_db')->name('transfer_wrong_remark')->where('is_done', 0)->select();
        
        if (!empty($list))
        {
            foreach ($list as $v) 
            {
                $old_members = Db::connect('old_db')->name('members')->where('id', $v['uid'])->find();
                
                if (!empty($old_members))
                {
                    $phone_exist_in_new = Db::table('AppUser')->where('userPhone', $old_members['user_phone'])->field('userId')->find();
                    $old_memberinfo = Db::connect('old_db')->name('member_info')->where('uid', $v['uid'])->find();
                    
                    if (empty($phone_exist_in_new))
                    {
                        $ins = [];
                        $ins['userId'] = $v['uid'];
                        $ins['userPhone'] = $old_members['user_phone'];
                        $ins['userPass'] = $old_members['user_pass'];
                        $ins['userName'] = !empty($old_memberinfo['real_name']) ? $old_memberinfo['real_name'] : '';
                        $ins['userEmail'] = !empty($old_members['user_email']) ? $old_members['user_email'] : '';
                        $ins['pid'] = !empty($old_memberinfo['idcard']) ? $old_memberinfo['idcard'] : '';
                        $ins['recommendPhone'] = '';
                        if (!empty($old_members['recommend_id']))
                        {
                            $rc_phone = Db::connect('old_db')->name('members')->where('id', $old_members['recommend_id'])->value('user_phone');
                            $ins['recommendPhone'] = $rc_phone !== null ? $rc_phone : '';
                        }
                        $ins['regSource'] = $old_members['equipment'];
                        $ins['createdTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                        $ins['updatedTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                        $ins['lastLoginIp'] = !empty($old_members['last_log_ip']) ? $old_members['last_log_ip'] : '';
                        $ins['lastLoginTime'] = !empty($old_members['last_log_time']) ? date('Y-m-d H:i:s', $old_members['last_log_time']) : date('Y-m-d H:i:s');
                        $ins['marketChannel'] = '';
                        $ins['regIp'] = '';
                        $res = Db::table('AppUser')->insert($ins);
                        
                        if (!empty($old_memberinfo) && !empty($old_memberinfo['real_name']))
                        {
                            $has_account = Db::table('appaccount')->where('userId', $v['uid'])->find();
                            
                            if (empty($has_account))
                            {
                                $ins = [];
                                $ins['userId'] = $ins['acno'] = $v['uid'];
                                $ins['payChannelType'] = 2;
                                $ins['createdTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                                $ins['updatedTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                                Db::table('appaccount')->insert($ins);
                            }
                        }
                        
                        if ($res)
                        {
                            Db::connect('old_db')->name('transfer_wrong_remark')->where('id', $v['id'])->update(['is_done' => 1]);
                        }
                    }
                    else
                    {
                        if ($phone_exist_in_new['userId'] == $v['uid']) 
                        {
                            if ($v['type'] == 2)
                            {
                                $upd = [];
                                $upd['userName'] = !empty($old_memberinfo['real_name']) ? $old_memberinfo['real_name'] : '';
                                $upd['pid'] = !empty($old_memberinfo['idcard']) ? $old_memberinfo['idcard'] : '';
                                
                                $res = Db::table('AppUser')->where('userId', $v['uid'])->update($upd);
                                
                                $has_account = Db::table('appaccount')->where('userId', $v['uid'])->find();
                                
                                if (empty($has_account))
                                {
                                    $ins = [];
                                    $ins['userId'] = $ins['acno'] = $v['uid'];
                                    $ins['payChannelType'] = 2;
                                    $ins['createdTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                                    $ins['updatedTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                                    Db::table('appaccount')->insert($ins);
                                }
                                
                                if ($res)
                                {
                                    Db::connect('old_db')->name('transfer_wrong_remark')->where('id', $v['id'])->update(['is_done' => 1]);
                                }
                            }
                        }
                        else 
                        {
                            $conflict_uid[] = $v['uid'];
                        }
                    }
                }
            }
        }
    }
    
    public function unPickData() 
    {
        $uid_str = '80672,80674,80779,74096,80795,80812,81077,79671,81173,81191,81195,81203,81218,81299,81331,81332,81345,81354,81360,81374,81378,81389,81391,81399,81409,81437,81439,81480,81485,81511,81530,81537,81548,81559,81564,81573,81583,81603,81606,81627,81667,81681,81694,81722,81727,81742,81754,81789,81798,81823,81824,81854,81866,81919,81930,81956,81986,81987,82009,82018,82040,82047,82052,82080,82091,82103,82166,82168,82169,82196,82222,82230,82252,82255,82305,82326,82367,82384,82406,82408,82415,82420,82429,80057,82473,82483,82485,82503,82510,82515,82521,82523,82544,82548,82556,82562,82584,82586,82587,82588,82589,82590,82591,82595,82597,82596,82594,82598,82599,82601,82609,82632,82644,82653,82669,82730,82731,82737,82752,82783,82835,82839,82846,82860,82869,4180609063001071931,80672';
        
        $uid_arr = explode(',', $uid_str);
        
        foreach ($uid_arr as $uid)
        {
            $old_members = Db::connect('old_db')->name('members')->where('id', $uid)->find();
        
            if (!empty($old_members))
            {
                $phone_exist_in_new = Db::table('AppUser')->where('userPhone', $old_members['user_phone'])->field('userId')->find();
                $old_memberinfo = Db::connect('old_db')->name('member_info')->where('uid', $uid)->find();
        
                if (empty($phone_exist_in_new))
                {
                    $ins = [];
                    $ins['userId'] = $uid;
                    $ins['userPhone'] = $old_members['user_phone'];
                    $ins['userPass'] = $old_members['user_pass'];
                    $ins['userName'] = !empty($old_memberinfo['real_name']) ? $old_memberinfo['real_name'] : '';
                    $ins['userEmail'] = !empty($old_members['user_email']) ? $old_members['user_email'] : '';
                    $ins['pid'] = !empty($old_memberinfo['idcard']) ? $old_memberinfo['idcard'] : '';
                    $ins['recommendPhone'] = '';
                    if (!empty($old_members['recommend_id']))
                    {
                        $rc_phone = Db::connect('old_db')->name('members')->where('id', $old_members['recommend_id'])->value('user_phone');
                        $ins['recommendPhone'] = $rc_phone !== null ? $rc_phone : '';
                    }
                    $ins['regSource'] = $old_members['equipment'];
                    $ins['createdTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                    $ins['updatedTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                    $ins['lastLoginIp'] = !empty($old_members['last_log_ip']) ? $old_members['last_log_ip'] : '';
                    $ins['lastLoginTime'] = !empty($old_members['last_log_time']) ? date('Y-m-d H:i:s', $old_members['last_log_time']) : date('Y-m-d H:i:s');
                    $ins['marketChannel'] = '';
                    $ins['regIp'] = '';
                    Db::table('AppUser')->insert($ins);
        
                    if (!empty($old_memberinfo['real_name']))
                    {
                        $has_account = Db::table('appaccount')->where('userId', $uid)->find();
        
                        if (empty($has_account))
                        {
                            $ins = [];
                            $ins['userId'] = $ins['acno'] = $uid;
                            $ins['payChannelType'] = 2;
                            $ins['createdTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                            $ins['updatedTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                            Db::table('appaccount')->insert($ins);
                        }
                    }
                }
                else
                {
                    if ($phone_exist_in_new['userId'] == $uid)
                    {
                        if (!empty($old_memberinfo['real_name']))
                        {
                            $upd = [];
                            $upd['userName'] = $old_memberinfo['real_name'];
                            $upd['pid'] = !empty($old_memberinfo['idcard']) ? $old_memberinfo['idcard'] : '';
                            
                            $res = Db::table('AppUser')->where('userId', $uid)->update($upd);
                            
                            $has_account = Db::table('appaccount')->where('userId', $uid)->find();
                            
                            if (empty($has_account))
                            {
                                $ins = [];
                                $ins['userId'] = $ins['acno'] = $uid;
                                $ins['payChannelType'] = 2;
                                $ins['createdTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                                $ins['updatedTime'] = date('Y-m-d H:i:s', $old_members['reg_time']);
                                Db::table('appaccount')->insert($ins);
                            }
                        }
                    }
                    else
                    {
                        $conflict_uid[] = $uid;
                    }
                }
            }
        }
    }
    
    public function sendcoupons()
    {
        $list = [
            ['money' => '28', 'use_money' => 5000, 'min_investrange' => 30, 'expired' => 20],
            ['money' => '58', 'use_money' => 10000, 'min_investrange' => 30, 'expired' => 20],
            ['money' => '168', 'use_money' => 30000, 'min_investrange' => 60, 'expired' => 20],
            ['money' => '258', 'use_money' => 50000, 'min_investrange' => 60, 'expired' => 20],
            ['money' => '576', 'use_money' => 100000, 'min_investrange' => 90, 'expired' => 20]
        ];
        
        $send_wrg_uid = [];
        
        Db::connect('old_db')->name('borrow_investor')->field('id,investor_uid')->where('status', '<>', 3)->group('investor_uid')->chunk(100, function($investors) use($list) {
            
            foreach ($investors as $uid_info) 
            {
                $phone = Db::connect('old_db')->name('members')->where('id', $uid_info['investor_uid'])->value('user_phone');
                if (!empty($phone))
                {
                    $now = time();
                    foreach ($list as $v)
                    {
                        $coup = [];
                        $coup['user_phone']      = $phone;
                        $coup['money']           = $v['money'];
                        $coup['use_money']       = $v['use_money'];
                        $coup['min_investrange'] = $v['min_investrange'];
                        $coup['endtime']         = $v['expired'] * 24 * 3600 + $now;
                        $coup['status']          = 0;
                        $coup['serial_number']   = date('YmdHis').mt_rand(100000, 999999);
                        $coup['type']            = 1;
                        $coup['name']            = '庆平台增资';
                        $coup['addtime']         = date("Y-m-d H:i:s");
                        $coup['isexperience']    = 0;
                        $res = Db::connect('old_db')->name('coupons')->insert($coup);
                        if (!$res)
                        {
                            $send_wrg_uid[] = $uid_info['investor_uid'];
                        }
                    }
                }
            }
        }, 'investor_uid', 'desc');
        
        if (!empty($send_wrg_uid))
        {
            Log::write("定时发放投资券错误用户 ： " . json_encode($send_wrg_uid), 'crontab');
        }
    }
}