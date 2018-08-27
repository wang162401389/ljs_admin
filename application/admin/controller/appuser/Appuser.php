<?php

namespace app\admin\controller\appuser;

use app\common\controller\Backend;
use think\Db;

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
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $sub_field = 'u.userId,u.userPhone,u.userName,u.pid,u.recommendPhone,u.regSource,u.marketChannel,u.createdTime,
                          sum(case when ir.borrowStatus not in (2,4) then ir.investorCapital else 0 end) * 0.01 as total';
            
            $subQuery = Db::table('AppUser')
                        ->alias('u')
                        ->field($sub_field)
                        ->join('AppInvestorRecord ir','u.userId = ir.userId', 'LEFT')
                        ->group('u.userId')
                        ->buildSql();
            
            $total = Db::table($subQuery.' t')
                    ->where($where)
                    ->count();
            
            $list = Db::table($subQuery.' t')
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
                    
            if (!empty($list)) 
            {
                foreach ($list as &$v) 
                {
                    $v['userId'] = ''.$v['userId'];
                    $v['pid'] = $v['pid'] ? ''.$v['pid'] : '';
                }
            }
            
            $result = array("total" => $total, "rows" => $list);
    
            return json($result);
        }
        return $this->view->fetch();
    }
}
