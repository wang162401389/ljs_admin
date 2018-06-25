<?php
namespace app\admin\controller\operate;

use app\common\controller\Backend;
use think\Db;

/**
 * 财富合伙人
 */
class Wealthpartner extends Backend
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
        return $this->view->fetch();
    }
    
    /**
     * 查看
     */
    public function users()
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
            
//             $sql = 'SELECT userPhone,userName,userId,investorCapital,inviteCount,isPartner FROM 
//                     (
//                 		SELECT u.userPhone,u.userName,u.userId,sum(case when i.borrowStatus in(3,5) then investorCapital end) as investorCapital,
//                 			   (SELECT COUNT(userId) FROM appuser WHERE recommendPhone = u.userPhone) inviteCount,
//                 			   CASE 
//                                    WHEN 
//                                        (SELECT count(userId) FROM appuser WHERE recommendPhone = u.userPhone) >= 2 
//                                        AND 
//                                        (SELECT SUM(investorCapital) FROM AppInvestorRecord WHERE borrowStatus in(3,5) AND userId=u.userId) >= 200000 
//                                    THEN 
//                                         1 
//                                    ELSE 
//                                         0 
//                                    END 
//                                isPartner
//                 		FROM AppUser u LEFT JOIN AppInvestorRecord i ON u.userId = i.userId
//                 		WHERE investorCapital > 0
//                 		GROUP BY u.userId
//                     ) t
//                     WHERE investorCapital > 0 or inviteCount > 0 order BY investorCapital DESC';
            
            $subfiled = 'u.userPhone,u.userName,u.userId,sum(case when i.borrowStatus in(3,5) then investorCapital end) * 0.01 as investorCapital,
                        (SELECT COUNT(userId) FROM AppUser WHERE recommendPhone = u.userPhone) inviteCount,
                        CASE WHEN (SELECT count(userId) FROM AppUser WHERE recommendPhone = u.userPhone) >= 2 AND 
                                  (SELECT SUM(investorCapital) FROM AppInvestorRecord WHERE borrowStatus in(3,5) AND userId=u.userId) >= 200000 
                        THEN 1 ELSE 0 END isPartner';
            
            $subQuery = Db::table('AppUser')
                        ->alias('u')
                        ->field($subfiled)
                        ->join('AppInvestorRecord i','u.userId = i.userId', 'LEFT')
                        ->where('investorCapital', 'gt', 0)
                        ->group('u.userId')
                        ->buildSql();
                        
            $field = 'userPhone,userName,userId,investorCapital,inviteCount,isPartner';
                        
            $total = Db::table($subQuery.' t')
                    ->field($field)
                    ->where(function ($query) {
                        $query->where('investorCapital', 'gt', 0)->whereOr('inviteCount', 'gt', 0);
                    })
                    ->where($where)
                    ->count();

            $list = Db::table($subQuery.' t')
                    ->field($field)
                    ->where(function ($query) {
                        $query->where('investorCapital', 'gt', 0)->whereOr('inviteCount', 'gt', 0);
                    })
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
                }
            }
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 计划分红
     */
    public function bonus()
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
            
//             $sql = "SELECT t.userId inviteUserId,t.userName inviteUsername,t.userPhone inviteUserPhone,t.investorCapital,t.investorTime, u.userName partnerUsername,u.userId partnerUserId,u.userPhone partnerUserPhone
//             FROM AppUser u ,
//             (
//             SELECT u.userId,u.userName,u.userPhone,sum(i.investorCapital) investorCapital,investorTime,u.recommendPhone
//             FROM AppUser u,AppInvestorRecord i WHERE i.userId=u.userId AND u.recommendPhone in
            
// 							(
// 								SELECT u.userPhone FROM AppUser u,AppInvestorRecord i WHERE i.userId = u.userId AND (
// 										(SELECT count(1) FROM AppUser WHERE recommendPhone = u.userPhone ) >= 2
// 										 AND (SELECT sum(investorCapital) / 100 FROM AppInvestorRecord WHERE userId = u.userId AND borrowStatus IN (3, 5) ) >= 2000
// 								)
// 								GROUP BY u.userId
// 							)

//             GROUP BY u.userId,date_format(investorTime,'%Y-%m') ORDER BY date_format(investorTime,'%Y-%m')DESC
//             )
// 						t WHERE t.recommendPhone = u.userPhone";
            
            $subsql1_1 = '(SELECT count(1) FROM AppUser WHERE recommendPhone = u.userPhone)';
            $subsql1_2 = '(SELECT sum(investorCapital) FROM AppInvestorRecord WHERE userId = u.userId AND borrowStatus IN (3, 5))';
                        
            $subfield = "u.userId,u.userName,u.userPhone,sum(i.investorCapital) * 0.01 as investorCapital,
                        date_format(i.investorTime,'%Y-%m') as investorTime,u.recommendPhone,
                        CASE WHEN sum(i.investorCapital) < 50000000 then truncate(sum(i.investorCapital) * 0.8 * 0.01 * 0.01 / 12, 2) 
                      WHEN sum(i.investorCapital) >= 50000000 and sum(i.investorCapital) <= 100000000 THEN truncate(sum(i.investorCapital) * 1.0 * 0.01 * 0.01 / 12, 2)  
                      WHEN sum(i.investorCapital) >= 100000000 and sum(i.investorCapital) <= 200000000 then truncate(sum(i.investorCapital) * 1.2 * 0.01 * 0.01 / 12, 2) 
                      WHEN sum(i.investorCapital) >= 200000000 and sum(i.investorCapital) <= 500000000 then truncate(sum(i.investorCapital) * 1.5 * 0.01 * 0.01 / 12, 2)  
                      WHEN sum(i.investorCapital) > 500000000 then truncate(sum(i.investorCapital) * 2.0 * 0.01 * 0.01 / 12, 2)  ELSE 0 END bonus,
                      CASE WHEN (SELECT count(id) FROM AppDividend WHERE userId = u.userId and date_format(dividendTime,'%Y-%m') = date_format(i.investorTime,'%Y-%m')) >= 1
                        THEN 1 ELSE 0 END send_status";
            
            $subsql1 = Db::table('AppUser')
                    ->alias('u')
                    ->join('AppInvestorRecord i', 'u.userId = i.userId')
                    ->field($subfield)
                    ->whereIn('u.recommendPhone', function($query) use($subsql1_1, $subsql1_2){
                        $query->table('AppUser')->alias('u')
                              ->field('u.userPhone')
                              ->join('AppInvestorRecord i','u.userId = i.userId')
                              ->where($subsql1_1 . ' >= 2')
                              ->where($subsql1_2 . ' >= 200000')
                              ->group('u.userId');
                    })
                    ->group("u.userId,date_format(investorTime,'%Y-%m')")
                    //->order("date_format(investorTime,'%Y-%m')", 'DESC')
                    ->buildSql();
                    
            $field = 't.userId as `t.userId`,t.userName as `t.userName`,t.userPhone as `t.userPhone`,t.investorCapital as `t.investorCapital`,
                      t.investorTime as `t.investorTime`,u.userName as `u.userName`,u.userId as `u.userId`,u.userPhone as `u.userPhone`,
                      t.bonus as `t.bonus`,t.send_status as `t.send_status`';
        
            $total = Db::table('AppUser')
                    ->alias('u')
                    ->join([$subsql1 => 't'], 't.recommendPhone = u.userPhone')
                    ->field($field)
                    ->where($where)
                    ->count();

            $list = Db::table('AppUser')
                    ->alias('u')
                    ->join([$subsql1 => 't'], 't.recommendPhone = u.userPhone')
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
                    $v['t.userId'] = ''.$v['t.userId'];
                    $v['u.userId'] = ''.$v['u.userId'];
                }
            }
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }
        return $this->view->fetch();
    }
}