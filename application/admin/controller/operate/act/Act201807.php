<?php
namespace app\admin\controller\operate\act;

use app\common\controller\Backend;
use think\Db;

/**
 * 2018.07活动
 */
class Act201807 extends Backend
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
                
            $subQuery = Db::table('AppInvestorRecord')
                        ->alias('ir')
                        ->join('AppTransactionFlowing tf',"tf.borrowInfoId = ir.borrowInfoId AND tf.userId = ir.userId AND tf.transactionType = 7 AND tf.remark LIKE '%号标满标奖励%'", 'LEFT')
                        ->field('max(ir.id) as id,ir.userId,ir.investorTime,ir.investorCapital * 0.01 as investorCapital,ir.borrowInfoId,CASE WHEN tf.transactionStatus = 2 then 1 ELSE 0 END has_send')
                        ->where('investorCapital', 'egt', 300000)
                        ->whereTime('investorTime', 'between', ['2018-07-02 00:00:00', '2018-08-01 00:00:00'])
                        ->group('borrowInfoId')
                        ->buildSql();
        
            $total = Db::table($subQuery.' t')
                    ->where($where)
                    ->join('AppBorrowInfo bi','bi.borrowInfoId = t.borrowInfoId AND bi.isNew = 0', 'LEFT')
                    ->join('AppUser u','u.userId = t.userId', 'LEFT')
                    ->order($sort, $order)
                    ->count();

            $field = 't.userId as `t.userId`,u.userPhone as `u.userPhone`,u.userName as `u.userName`,bi.borrowSn as `bi.borrowSn`,
                    t.investorTime,t.investorCapital as `t.investorCapital`,t.has_send as `t.has_send`';
            $list = Db::table($subQuery.' t')
                    ->field($field)
                    ->where($where)
                    ->join('AppBorrowInfo bi','bi.borrowInfoId = t.borrowInfoId AND bi.isNew = 0', 'LEFT')
                    ->join('AppUser u','u.userId = t.userId', 'LEFT')
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            $list = collection($list)->toArray();
            if (!empty($list))
            {
                foreach ($list as &$v) 
                {
                    $v['t.userId'] = ''.$v['t.userId'];
                    $v['prize'] = 30;
                }
            }
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }
        return $this->view->fetch();
    }
}