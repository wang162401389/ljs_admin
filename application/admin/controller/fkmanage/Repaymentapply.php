<?php

namespace app\admin\controller\fkmanage;

use app\common\controller\Backend;
use think\Request;

/**
 * 标的管理
 *
 * @icon fa fa-circle-o
 */
class Repaymentapply extends Backend
{
    
    /**
     * Appborrowinfo模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AppBorrowRepayment');
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
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
            
            $map['advanceRepayment'] = 1;
            
            $total = $this->model
                    ->with(['borrow', 'borrower'])
                    ->where($where)
                    ->where($map)
                    ->order($sort, $order)
                    ->count();
            
            $list = $this->model
                    ->with(['borrow', 'borrower'])
                    ->where($where)
                    ->where($map)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            
            $list = collection($list)->toArray();
            if (!empty($list)) 
            {
                foreach ($list as &$v)
                {
                    $v['userId'] = (string)$v['userId'];
                    $v['total'] = $v['capital'] + $v['interest'] + $v['borrowFee'];
                }
            }
            
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 还款申请
     */
    public function changeRepaymentStatus()
    {
        if ($this->request->isAjax())
        {
            $param = $this->request->post();
            $id = $param['id'];
            $status = $param['status'];
            $row = $this->model->get($id);
            if ($id < 0 || !$row)
            {
                $this->error(__('No Results were found'));
            }
                
            $info = $this->model->get($id);
            if ($info->advanceRepayment != 1) {
                $this->error('不在申请中状态');
            }
            
            $result = $row->save(['advanceRepayment' => $status, 'id' => $id]);
            if ($result !== false)
            {
                $javaapi = new \fast\Javaapi();
                $req = [];
                
                $mobile = \think\Db::table('BorrowUser')->where('borrowUserId', $row->userId)->value('userName');
                $borrowsn = \think\Db::table('AppBorrowInfo')->where('borrowInfoId', $row->borrowInfoId)->value('borrowSn');
                if (!empty($mobile) && !empty($borrowsn)) 
                {
                    $req['mobile'] = $mobile;
                    $req['time'] = $_SERVER['REQUEST_TIME'];
                    $req['args'][] = $borrowsn;
                    
                    if ($status == 2)
                    {
                        if ($row->totalPeriods == 1)
                        {
                            $req['smsCode'] = 'LJS_TO_REIMBURSE_MSG';
                            $req['args'][] = $row->capital + $row->interest + $row->borrowFee;
                            $req['args'][] = $row->borrowFee;
                        }
                        else
                        {
                            $req['smsCode'] = 'LJS_TO_INSTALL_REIMBURSE_MSG';
                            $req['args'][] = $row->curPeriods;
                            $req['args'][] = $row->capital + $row->interest + $row->borrowFee;
                            $req['args'][] = $row->borrowFee;
                        }
                    }
                    elseif ($status == 3)
                    {
                        if ($row->totalPeriods == 1)
                        {
                            $req['smsCode'] = 'LJS_TO_NO_REIMBURSE_MSG';
                        }
                        else
                        {
                            $req['smsCode'] = 'LJS_TO_NO_INSTALL_REIMBURSE_MSG';
                            $req['args'][] = $row->curPeriods;
                        }
                    }
                    
                    \think\Log::write("还款申请发送短信请求参数：".var_export($req, true), 'java');
                    $javaapi->sendSms(['message' => encrypt(json_encode($req))]);
                    \think\Log::write("还款申请发送短信请求密文：".encrypt(json_encode($req)), 'java');
                }
                
                $this->success('操作成功');
            }
            else 
            {
                $this->error('修改失败');
            }
        }
    }
}
