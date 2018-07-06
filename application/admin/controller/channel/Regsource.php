<?php

namespace app\admin\controller\channel;

use app\common\controller\Backend;
use think\Db;

/**
 * 注册来源别
 *
 * @icon fa fa-list
 */
class Regsource extends Backend
{
    
    protected $model = null;
    protected $typelist = [];
    
    public function _initialize()
    {
        parent::_initialize();
        $typelist = config('site.regsourcetype');
        $this->typelist = $typelist;
        $this->view->assign("typeList", $typelist);
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
        if ($this->request->isAjax())
        {
            $type = $this->request->request("type");
            reset($this->typelist);
            
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $sub_field = 'ir.id,ir.userId,u.userPhone,u.userName,u.createdTime,ir.createTime,bi.borrowName,
                    ir.investorCapital * 0.01 as `investorCapital`,ir.deductibleMoney * 0.01 as `deductibleMoney`,bi.borrowDurationTxt,bi.payChannelType,
                    CASE WHEN (SELECT min(id) FROM AppInvestorRecord WHERE investorUid = ir.userId) = ir.id THEN 1 ELSE 0 END as is_first_invest';
            
            $type = $type == null ? key($this->typelist) : $type;
            $map['u.regSource'] = $type;
            
            $subQuery = Db::table('AppInvestorRecord')
                        ->alias('ir')
                        ->field($sub_field)
                        ->join('AppUser u','u.userId = ir.userId', 'LEFT')
                        ->join('AppBorrowInfo bi','ir.borrowInfoId = bi.borrowInfoId', 'LEFT')
                        ->where($map)
                        ->buildSql();

            $total = Db::table($subQuery.' t')
                    ->where($where)
                    ->count();

            $list = Db::table($subQuery.' t')
                    ->field('*')
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
    
            if (!empty($list))
            {
                foreach ($list as &$v)
                {
                    $v['userId'] = ''.$v['userId'];
                }
            }
        
            $result = array("total" => $total, "rows" => $list);
        
            return json($result);
        }
        return $this->view->fetch();
    }
}