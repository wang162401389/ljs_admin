<?php

namespace app\admin\controller\fkmanage;

use app\common\controller\Backend;
use app\common\model\Appborrowinfo as BrrowInfoModel;
use think\Db;
use think\Log;

/**
 * 标的管理
 *
 * @icon fa fa-circle-o
 */
class Borrow extends Backend
{
    
    /**
     * Appborrowinfo模型对象
     */
    protected $model = null;
    
    protected $noNeedRight = ['verify', 'verifylog', 'release_borrow_to_sina', 'release_borrow_to_huaxing', 'examine', 'repaymentinfo', 'canloan'];
    
    /**
     * 是否开启Validate验证
     */
    protected $modelValidate = true;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Appborrowinfo');
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个方法
     * 因此在当前控制器中可不用编写增删改查的代码,如果需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            $this->relationSearch = TRUE;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('pkey_name'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $map['borrowStatus'] = ['in', [0, 1, 2, 3]];
            
            $total = $this->model
                    ->with('Borrower')
                    ->where($where)
                    ->where($map)
                    ->order($sort, $order)
                    ->count();
            
            $list = $this->model
                    ->with('Borrower')
                    ->where($where)
                    ->where($map)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            
            if (!empty($list))
            {
                foreach ($list as &$v)
                {
                    $v['borrowUid'] = (string)$v['borrowUid'];
                }
            }
                    
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 发布借款
     */
    public function add($ids = NULL)
    {
        if (!$ids)
        {
            $this->error(__('Parameter %s can not be empty', 'id'));
        }

        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill)
                {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    
                    $loan_status = $this->canloan($ids, $params['payChannelType']);
                    
                    if ($loan_status) 
                    {
                        //借款人ID
                        $params['borrowUid'] = $ids;
                        $result = $this->model->allowField(true)->save($params);
                        if ($result !== false)
                        {
                            $this->success();
                        }
                        else
                        {
                            $this->error($this->model->getError());
                        }
                    }
                    else
                    {
                        $this->error('没有设置支付密码或开户或绑卡');
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        
        $this->view->assign('productTypeList', build_select('row[productType]', BrrowInfoModel::getProductTypeList(), '', ['class' => 'form-control selectpicker']));
        $this->view->assign('investInterestTypeList', build_select('row[investInterestType]', BrrowInfoModel::getInvestInterestTypeList(), '', ['class' => 'form-control selectpicker']));
        $this->view->assign('borrowInterestTypeList', build_select('row[borrowInterestType]', BrrowInfoModel::getBorrowInterestTypeList(), '', ['class' => 'form-control selectpicker']));
        $this->view->assign('borrowUseList', build_select('row[borrowUse]', BrrowInfoModel::getBorrowUseList(), '', ['class' => 'form-control selectpicker']));
        $this->view->assign('guaranteeCompanyList', build_select('row[danbao]', BrrowInfoModel::getGuaranteeCompanyList(), '', ['class' => 'form-control selectpicker']));
        $this->view->assign('feeTypeList', build_select('row[feeType]', BrrowInfoModel::getFeeTypeList(), '', ['class' => 'form-control selectpicker']));
        return $this->view->fetch();
    }
    
    /**
     * 是否可借款
     * @param unknown $uid
     * @param unknown $paychanneltype
     * @return boolean
     */
    public function canloan($uid, $paychanneltype)
    {
        $loan_status = true;
        $javaapi = new \fast\Javaapi();
        $req = [];
        $req['userId'] = $uid;
        $req['accountType'] = $paychanneltype;
        Log::write("发布借款请求参数：".var_export($req,true), 'java');
        $res = json_decode($javaapi->accountInfo($req), true);
        Log::write("发布借款返回参数：".var_export($res,true), 'java');
        
        $sina_status = $paychanneltype == 2 && !$res['result']['sinaHasSetPayPwd'];
        $huaxing_status = $paychanneltype == 3 && !($res['result']['ifAccount'] && $res['result']['ifBank']);
        
        if ($res['status'] != 'ok' || $sina_status || $huaxing_status) 
        {
            $loan_status = false;
        }
        
        return $loan_status;
    }
    
    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
        {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error($row->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        
        $this->view->assign("row", $row);
        $this->view->assign('productTypeList', build_select('row[productType]', BrrowInfoModel::getProductTypeList(), $row['productType'], ['class' => 'form-control selectpicker']));
        $this->view->assign('investInterestTypeList', build_select('row[investInterestType]', BrrowInfoModel::getInvestInterestTypeList(), $row['investInterestType'], ['class' => 'form-control selectpicker']));
        $this->view->assign('borrowInterestTypeList', build_select('row[borrowInterestType]', BrrowInfoModel::getBorrowInterestTypeList(), $row['borrowInterestType'], ['class' => 'form-control selectpicker']));
        $this->view->assign('borrowUseList', build_select('row[borrowUse]', BrrowInfoModel::getBorrowUseList(), $row['borrowUse'], ['class' => 'form-control selectpicker']));
        $this->view->assign('guaranteeCompanyList', build_select('row[danbao]', BrrowInfoModel::getGuaranteeCompanyList(), $row['danbao'], ['class' => 'form-control selectpicker']));
        $this->view->assign('feeTypeList', build_select('row[feeType]', BrrowInfoModel::getFeeTypeList(), $row['feeType'], ['class' => 'form-control selectpicker']));
        
        return $this->view->fetch();
    }
    
    /**
     * 查看
     */
    public function verifylog($ids)
    {
        $row = $this->model->get($ids);
        if (!$row)
        {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            $result = true;
            if (!empty($params) && isset($params['cancel'])) 
            {
                $result = Db::table('AppBorrowInfo')->where('borrowInfoId', $ids)->update(['borrowStatus' => 2]);
            }
            if ($result !== false)
            {
                $this->success();
            }
            else
            {
                $this->error($row->getError());
            }
                
            $this->error(__('Parameter %s can not be empty', ''));
        }
        
        $this->view->assign('cancel_status', strtotime($row->bidTime) > time() && $row->borrowStatus == 3 ? 1 : 0);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    /**
     * 审核
     */
    public function verify($ids)
    {
        $row = $this->model->get($ids);
        if (!$row)
        {
            $this->error(__('No Results were found'));
        }
        
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
        }
        
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                try
                {
                    //是否采用模型验证
                    if ($this->modelValidate)
                    {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }

                    if ($params['borrowStatus'] == 0) 
                    {
                        if ($row['borrowStatus'] == 0)
                        {
                            //初审不通过
                            $result = $this->examine($params, $ids, 1, 0);
                        }
                        else 
                        {
                            //复审不通过
                            $result = $this->examine($params, $ids, 2, 0);
                        }
                    }
                    else 
                    {
                        if ($row['borrowStatus'] == 0) 
                        {
                            //初审通过
                            $result = $this->examine($params, $ids, 1, 1);
                        }
                        else 
                        {
                            //复审通过
                            $result = $this->examine($params, $ids, 2, 1);
                        }
                    }
                    
                    if ($result['result'] !== false)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->error($result['msg']);
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    /**
     * 新浪标的录入
     * @param unknown $bidtime
     * @param unknown $borrow_info
     * @return boolean
     */
    private function release_borrow_to_sina($bidtime, $borrow_info) {
        
        if ($borrow_info['investInterestType'] == 1)
        {
            $repay_type = 'REPAY_CAPITAL_WITH_INTEREST';
        }
        elseif ($borrow_info['investInterestType'] == 2 || $borrow_info['investInterestType'] == 3)
        {
            $repay_type = 'AVERAGE_CAPITAL_PLUS_INTERES';
        }
        elseif ($borrow_info['investInterestType'] == 4)
        {
            $repay_type = 'SCHEDULED_INTEREST_PAYMENTS_DUE';
        }
        elseif ($borrow_info['investInterestType'] == 5)
        {
            $repay_type = 'AVERAGE_CAPITAL';
        }
        else
        {
            $repay_type = 'OTHER';
        }
        
        $borrow_phone = Db::table('BorrowUser')->where('borrowUserId', $borrow_info['borrowUid'])->value('userName');
        
        $req = [];
        //商户网站标的号
        $req['out_bid_no'] = $borrow_info['borrowInfoId'];
        //网站名称/平台名称
        $req['web_site_name'] = '链金所';
        //标的名称
        $req['bid_name'] = $borrow_info['borrowName'];
        //标的类型 HOUSING_LOAN 房贷类 CREDIT信用MORTGAGE抵押ASSIGNMENT_DEBT债权转让OTHER其他
        $req['bid_type'] = 'CREDIT';
        //发标金额
        $req['bid_amount'] = $borrow_info['borrowMoney'] / 100;
        //年化收益率 Number(8,2) 年化收益率（%）
        $req['bid_year_rate'] = $borrow_info['borrowInterestRate'] / 100;
        //借款期限 Number(10) 借款期限，精确到天
        $req['bid_duration'] = $borrow_info['borrowDuration'];
        //还款方式 还款方式 REPAY_CAPITAL_WITH_INTEREST 一次还本付息 AVERAGE_CAPITAL 等额本金 AVERAGE_CAPITAL_PLUS_INTERES 等额本息 SCHEDULED_INTEREST_PAYMENTS_DUE 按期付息到期还本 OTHER 其他
        $req['repay_type'] = $repay_type;
        //标的开始时间 String(14) 格式：yyyyMMddHHmmss
        $req['begin_date'] = date('YmdHis', strtotime($bidtime));
        //还款期限 String(14) 格式：yyyyMMddHHmmss
        $req['term'] = date('YmdHis', strtotime($bidtime) + $borrow_info['borrowDuration'] * 24 * 3600);
        //担保方式 String(64) 担保方式，例如：企业担保， xx保险担保，银行担保，无担保 等等，具体内容商户可自行定义
        $req['guarantee_method'] = '企业担保';
        //借款人id
        $req['user_id'] = $borrow_info['borrowUid'];
        //用途
        $req['use_purpose'] = '小额融资';
        //借款人手机号码
        $req['phone'] = $borrow_phone;
        
        $javaapi = new \fast\Javaapi();
        Log::write("新浪标的初审录入请求参数：".var_export($req,true), 'java');
        $res = json_decode($javaapi->releaseBorrowToSina($req), true);
        Log::write("新浪标的初审录入返回结果：".var_export($res,true), 'java');
        return $res;
    }
    
    /**
     * 华兴标的录入
     * @param unknown $borrow_info
     * @return string|mixed
     */
    private function release_borrow_to_huaxing($borrow_info)
    {
        $req = [];
        $req['appId'] = 'PC';
        $req['borrowSn'] = $borrow_info['borrowSn'];
        
        $javaapi = new \fast\Javaapi();
        Log::write("华兴标的初审请求参数：".var_export($req,true), 'java');
        $res = $javaapi->releaseBorrowToHuaXing($req);
        Log::write("华兴标的初审：".$res, 'java');
        return $res;
    }
    
    /**
     * 审核
     * @param unknown $post 表单提交数据
     * @param unknown $ids 标ID
     * @param unknown $type 1:初审 2：复审
     * @param unknown $status 0：拒绝 1：通过
     * @return boolean
     */
    public function examine($post, $ids, $type, $status)
    {
        $done = false;
        $msg = '审核失败了';
        if (!$ids) {
            return ['result' => $done, 'msg' => '缺少标的ID'];
        }

        $borrow_info = Db::table('AppBorrowInfo')->where('borrowInfoId', $ids)
                        ->field('borrowInfoId,borrowStatus,investInterestType,borrowName,borrowMoney,borrowInterestRate,borrowDuration,borrowUid,borrowSn,payChannelType,bidTime,collectTime,borrowDurationTxt')
                        ->find();
        if (empty($borrow_info))
        {
            return ['result' => $done, 'msg' => '查找不到标的信息'];
        }
        
        if ($type == 1) 
        {
            $params['firstVerifyTime'] = date("Y-m-d H:i:s");
            $params['firstVerfiyId'] = $this->auth->id;
            $params['bidTime'] = $post['bidTime'];
            $params['firstVerfiyRemarks'] = $post['firstVerfiyRemarks'];
            if ($status == 0) 
            {
                $params['borrowStatus'] = 1;
                $done = Db::table('AppBorrowInfo')->where('borrowInfoId', $ids)->update($params);
                if (!$done) 
                {
                    $msg = '数据库修改失败';
                }
            }
            elseif ($status == 1)
            {
                if ($borrow_info['payChannelType'] == 2)
                {
                    $res = $this->release_borrow_to_sina($params['bidTime'], $borrow_info);
                    if ($res['status'] == 'ok') 
                    {
                        $done = true;
                        $msg = '初审成功';
                    }
                    else 
                    {
                        $msg = $res['msg'];
                    }
                }
                elseif ($borrow_info['payChannelType'] == 3)
                {
                    $msg = $this->release_borrow_to_huaxing($borrow_info);
                    $done = $msg == '成功' ? true : false;
                }
                elseif ($borrow_info['payChannelType'] == 1)
                {
                    $msg = '富友支付渠道暂无审核标准';
                }
                if ($done)
                {
                    Db::table('AppBorrowInfo')->where('borrowInfoId', $ids)->update($params);
                }
            }
        }
        elseif ($type == 2)
        {
            if ($borrow_info['borrowStatus'] == 5)
            {
                $done = false;
                $msg = '复审正在处理中';
            }
            else 
            {
                $params['secondVerifyTime'] = date("Y-m-d H:i:s");
                $params['secondVerfiyId'] = $this->auth->id;
                $params['secondVerfiyRemarks'] = $post['secondVerfiyRemarks'];
                
                if ($status != 1)
                {
                    $params['borrowStatus'] = 5;
                }
                $upd = Db::table('AppBorrowInfo')->where('borrowInfoId', $ids)->update($params);
                
                if ($upd)
                {
                    $javaapi = new \fast\Javaapi();
                    $req = [];
                    if ($status == 0)
                    {
                        if ($borrow_info['payChannelType'] == 2)
                        {
                            $req['borrowInfoId'] = $ids;
                            Log::write("新浪标的复审拒绝请求参数：".var_export($req,true), 'java');
                            $res = $javaapi->sinaReviewRefuse($req);
                            Log::write("新浪标的复审拒绝：".$res, 'java');
                        }
                        elseif ($borrow_info['payChannelType'] == 3)
                        {
                            $req['borrowInfoId'] = $ids;
                            Log::write("华兴标的复审拒绝请求参数：".var_export($req,true), 'java');
                            $res = $javaapi->huaXingReviewRefuse($req);
                            Log::write("华兴标的复审拒绝：".$res, 'java');
                        }
                    }
                    elseif ($status == 1)
                    {
                        if ($borrow_info['payChannelType'] == 2)
                        {
                            $req['borrow_sn'] = $borrow_info['borrowSn'];
                            $req['user_id'] = $this->auth->id;
                            Log::write("新浪标的复审通过请求参数：".var_export($req,true), 'java');
                            $res = $javaapi->finishPreAuthTrade($req);
                            Log::write("新浪标的复审通过：".$res, 'java');
                        }
                        elseif ($borrow_info['payChannelType'] == 3)
                        {
                            $req['borrowInfoId'] = $ids;
                            Log::write("华兴标的复审通过请求参数：".var_export($req,true), 'java');
                            $res = $javaapi->huaXingReviewPass($req);
                            Log::write("华兴标的复审通过：".$res, 'java');
                        }
                    }
                    $done = $res == 'success' ? true : false;
                    if (!$done)
                    {
                        $msg = ($res !== null && !empty($res)) ? $res : '请求成功，请确认标的状态，若状态未变，请重试';
                    }
                }
            }
        }

        return ['result' => $done, 'msg' => $msg];
    }
    
    /**
     * 还款信息
     * @param unknown $ids
     * @return string
     */
    public function repaymentinfo()
    {
        if ($this->request->isAjax())
        {
            $param = $this->request->post();
            $id = $param['id'];
            
            $row = $this->model->get($id);
            if ($id < 0 || !$row)
            {
                $this->error(__('No Results were found'));
            }
            
            $result = true;
            if ($result !== false)
            {
                $total = $row->borrowMoney + $row->investInterest + $row->serviceCharge;
                
                $msg = '应还：<br>本金（'.$row->borrowMoney.'）+ 利息（'.$row->investInterest.'）+ 综合服务费（'.$row->serviceCharge.'）= '.$total.'<br>';
                
                $return['msg'] = $msg;
                $this->success('申请成功', '', $return);
            }
            else
            {
                $this->error('修改失败');
            }
        }
    }
}