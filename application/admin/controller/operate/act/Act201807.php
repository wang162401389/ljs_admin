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
                
            $subQuery1 = Db::table('AppInvestorRecord')
                        ->alias('ir')
                        ->join('AppBorrowInfo b', 'b.borrowInfoId = ir.borrowInfoId', 'LEFT')
                        ->field('ir.userId,max(ir.investorTime) as investorTime,b.borrowSn,ir.borrowInfoId')
                        ->where('b.isNew', 0)
                        ->whereNotIn('b.borrowStatus', '0,1,2,3,6,11')
                        ->group('ir.borrowInfoId')
                        ->buildSql();
        
            $map['record.investorCapital'] = ['egt', 300000];
            $map['record.investorTime'] = ['between', ['2018-07-02 00:00:00', '2018-08-01 00:00:00']];
                        
            $subQuery2 = Db::table($subQuery1.' t')
                        ->field('record.userId,u.userPhone,u.userName,t.borrowSn,t.investorTime,record.investorCapital * 0.01 as investorCapital,CASE WHEN tf.transactionStatus = 2 then 1 ELSE 0 END as has_send')
                        ->join('AppInvestorRecord record','record.borrowInfoId = t.borrowInfoId AND record.investorTime = t.investorTime')
                        ->join('AppUser u','u.userId = record.userId', 'LEFT')
                        ->join('AppTransactionFlowing tf', "tf.borrowInfoId = t.borrowInfoId AND tf.userId = record.userId AND tf.transactionType = 7 AND tf.remark LIKE '%号标满标奖励%'", 'LEFT')
                        ->where($map)
                        ->buildSql();
            
            $total = Db::table($subQuery2.' t1')
                    ->where($where)
                    ->count();

            $field = 't1.userId,t1.userPhone,t1.userName,t1.borrowSn,t1.investorTime,t1.investorCapital,t1.has_send';
            $list = Db::table($subQuery2.' t1')
                    ->field($field)
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            $list = collection($list)->toArray();

            if (!empty($list))
            {
                foreach ($list as &$v) 
                {
                    $v['userId'] = ''.$v['userId'];
                    $v['prize'] = 30;
                }
            }
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }
        return $this->view->fetch();
    }
}