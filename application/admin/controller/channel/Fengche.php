<?php

namespace app\admin\controller\channel;

use app\common\controller\Backend;

/**
 * 风车投资数据统计
 *
 * @icon fa fa-circle-o
 */
class Fengche extends Backend
{
    
    /**
     * AppUser模型对象
     */
    protected $model = null;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('AppUser');
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
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('pkey_name'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $field = 'ir.id as `ir.id`,ir.userId as `ir.userId`,u.userPhone as `u.userPhone`,u.userName as `u.userName`,u.createdTime as `u.createdTime`,
                    ir.createTime as `ir.createTime`,bi.borrowName as `bi.borrowName`,ir.investorCapital * 0.01 as `ir.investorCapital`,
                    ir.deductibleMoney * 0.01 as `ir.deductibleMoney`,bi.borrowDurationTxt as `bi.borrowDurationTxt`,bi.payChannelType as `bi.payChannelType`';
            
            $map['u.regSource'] = 'fengc';
            
            $total = \think\Db::table('AppInvestorRecord')
                    ->alias('ir')
                    ->field($field)
                    ->join('AppUser u','u.userId = ir.userId', 'LEFT')
                    ->join('AppBorrowInfo bi','ir.borrowInfoId = bi.borrowInfoId', 'LEFT')
                    ->where($where)
                    ->where($map)
                    ->order($sort, $order)
                    ->count();
            
            $list = \think\Db::table('AppInvestorRecord')
                    ->alias('ir')
                    ->field($field)
                    ->join('AppUser u','u.userId = ir.userId', 'LEFT')
                    ->join('AppBorrowInfo bi','ir.borrowInfoId = bi.borrowInfoId', 'LEFT')
                    ->where($where)
                    ->where($map)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            
            if (!empty($list)) 
            {
                foreach ($list as &$v) 
                {
                    $min_id = \think\Db::table('AppInvestorRecord')->where('investorUid', $v['ir.userId'])->min('id');
                    $v['ir.userId'] = (string)$v['ir.userId'];
                    $v['pay_channel_type_text'] = model('Appborrowinfo')->getPayChannelTypeList()[$v['bi.payChannelType']];
                    $v['is_first_invest_text'] = $min_id == $v['ir.id'] ? '是' : '否';
                }
            }
            
            $result = array("total" => $total, "rows" => $list);
    
            return json($result);
        }
        return $this->view->fetch();
    }
}