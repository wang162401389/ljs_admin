<?php
namespace app\admin\controller\finance;

use app\common\model\Appborrowinfo;
use app\common\controller\Backend;
use think\Db;

/**
 * 投标记录
 *
 * @icon fa fa-circle-o
 */
class Investment extends Backend
{
    
    /**
     * Appinvestorrecord模型对象
     */
    protected $model = null;
    
    protected $noNeedRight = ['borrowstatuslist', 'investinteresttypelist'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Appinvestorrecord');
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
            
            $subfield = 'ir.id,b.borrowSn,ir.userId,u.userPhone,u.userName,u.recommendPhone,u.regSource,u.createdTime,ir.investorCapital * 0.01 as investorCapital,
                        b.investInterestType,b.borrowDurationTxt,ir.investorTime,ir.deductibleMoney * 0.01 as deductibleMoney,u.marketChannel,
                        ir.interestCcfaxRate * 0.01 as interestCcfaxRate,ir.borrowStatus,ir.payChannelType,ir.orderId';
            
            $subfield .= ",case left(u.pid,2)
                    when '11' then '北京市'
                    when '12' then '天津市'
                    when '13' then '河北省'
                    when '14' then '山西省'
                    when '15' then '内蒙古自治区'
                    when '21' then '辽宁省'
                    when '22' then '吉林省'
                    when '23' then '黑龙江省'
                    when '31' then '上海市'
                    when '32' then '江苏省'
                    when '33' then '浙江省'
                    when '34' then '安徽省'
                    when '35' then '福建省'
                    when '36' then '江西省'
                    when '37' then '山东省'
                    when '41' then '河南省'
                    when '42' then '湖北省'
                    when '43' then '湖南省'
                    when '44' then '广东省'
                    when '45' then '广西壮族自治区'
                    when '46' then '海南省'
                    when '50' then '重庆市'
                    when '51' then '四川省'
                    when '52' then '贵州省'
                    when '53' then '云南省'
                    when '54' then '西藏自治区'
                    when '61' then '陕西省'
                    when '62' then '甘肃省'
                    when '63' then '青海省'
                    when '64' then '宁夏回族自治区'
                    when '65' then '新疆维吾尔自治区'
                    when '71' then '台湾省'
                    when '81' then '香港特别行政区'
                    when '82' then '澳门特别行政区'
                    else ''
                    end as native_place";
            
            $subQuery = Db::table('AppInvestorRecord')
                        ->alias('ir')
                        ->join('AppBorrowInfo b', 'b.borrowInfoId = ir.borrowInfoId', 'LEFT')
                        ->join('AppUser u', 'u.userId = ir.userId', 'LEFT')
                        ->field($subfield)
                        ->buildSql();
            
            $total = Db::table($subQuery.' t')
                    ->where($where)
                    ->count();
            
            $list = Db::table($subQuery.' t')
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
     * 搜索下拉列表
     */
    public function borrowstatuslist()
    {
        $borrowstatuslist = $this->model->getBorrowStatusList();
        $searchlist = [];
        if (!empty($borrowstatuslist))
        {
            foreach ($borrowstatuslist as $sid => $sname) {
                $searchlist[] = ['id' => $sid, 'name' => $sname];
            }
        }
        $this->success('', null, ['searchlist' => $searchlist]);
    }
    
    /**
     * 搜索下拉列表
     */
    public function investinteresttypelist()
    {
        $typelist = Appborrowinfo::getInvestInterestTypeList();
        $searchlist = [];
        if (!empty($typelist))
        {
            foreach ($typelist as $tid => $tname) {
                $searchlist[] = ['id' => $tid, 'name' => $tname];
            }
        }
        $this->success('', null, ['searchlist' => $searchlist]);
    }
}
