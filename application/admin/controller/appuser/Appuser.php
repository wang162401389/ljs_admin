<?php

namespace app\admin\controller\appuser;

use app\common\controller\Backend;

/**
 * APP用户表
 *
 * @icon fa fa-circle-o
 */
class Appuser extends Backend
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
            
            $field = 'u.userId as `u.userId`,u.userPhone as `u.userPhone`,u.userName as `u.userName`,u.pid as `u.pid`,u.recommendPhone as `u.recommendPhone`,
                      u.regSource as `u.regSource`,u.marketChannel as `u.marketChannel`,u.createdTime as `u.createdTime`,
                    sum(case when ir.borrowStatus not in (2,4) then ir.investorCapital end) * 0.01 as total';
            
            $total = \think\Db::table('AppUser')
                    ->alias('u')
                    ->field($field)
                    ->join('AppInvestorRecord ir','u.userId = ir.userId', 'LEFT')
                    ->where($where)
                    ->order($sort, $order)
                    ->group('u.userId')
                    ->count();

            $list = \think\Db::table('AppUser')
                    ->alias('u')
                    ->field($field)
                    ->join('AppInvestorRecord ir','u.userId = ir.userId', 'LEFT')
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->group('u.userId')
                    ->select();
                    
            if (!empty($list)) 
            {
                foreach ($list as &$v) 
                {
                    $v['u.userId'] = 'ID_'.$v['u.userId'];
                    $v['u.pid'] = $v['u.pid'] ? 'PID_'.$v['u.pid'] : '';
                }
            }
            
            $result = array("total" => $total, "rows" => $list);
    
            return json($result);
        }
        return $this->view->fetch();
    }
}
