<?php

namespace app\admin\controller\finance;
use app\common\model\Appborrowinfo;
use app\common\controller\Backend;

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
            $this->relationSearch = TRUE;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $total = $this->model
                    ->with('binfo,user')
                    ->where($where)
                    ->order($sort, $order)
                    ->count();
            
            $list = $this->model
                    ->with('binfo,user')
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            
            $list = collection($list)->toArray();
            if (!empty($list))
            {
                foreach ($list as &$v)
                {
                    $v['userId'] = 'ID_'.$v['userId'];
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
