<?php

namespace app\admin\controller\appuser;

use app\common\controller\Backend;
use think\Validate;

/**
 * 推荐人审核
 *
 * @icon fa fa-circle-o
 */
class Refereeverify extends Backend
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
            
            $field = 'rl.id as `rl.id`,rl.uid as `rl.uid`,u.userPhone as `u.userPhone`,u.userName as `u.userName`,rl.pre_recommend as `rl.pre_recommend`,rl.now_recommend as `rl.now_recommend`,u.regSource as `u.regSource`,u.createdTime as `u.createdTime`';
            
            $total = \think\Db::table('referee_log')
                    ->alias('rl')
                    ->field($field)
                    ->join('AppUser u','u.userId = rl.uid', 'LEFT')
                    ->where($where)
                    ->where('rl.status', 0)
                    ->order($sort, $order)
                    ->count();

            $list = \think\Db::table('referee_log')
                    ->alias('rl')
                    ->field($field)
                    ->join('AppUser u','u.userId = rl.uid', 'LEFT')
                    ->where($where)
                    ->where('rl.status', 0)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            if (!empty($list)) 
            {
                foreach ($list as &$v) 
                {
                    $v['rl.uid'] = (string)$v['rl.uid'];
                }
            }

            $result = array("total" => $total, "rows" => $list);
    
            return json($result);
        }
        return $this->view->fetch();
    }
    
    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = \think\Db::table('referee_log')->where('id', $ids)->find();
        if (!$row)
        {
            $this->error(__('No Results were found'));
        }
            
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds))
        {
            if (!in_array($row[$this->dataLimitField], $adminIds))
            {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                try
                {
                    $rule = [
                        'now_recommend' => 'regex:/^1\d{10}$/',
                    ];
                    
                    $msg = [
                        'now_recommend' => '推荐人手机号格式不对',
                    ];
                    $data = [
                        'now_recommend' => $params['now_recommend'],
                    ];
                    $validate = new Validate($rule, $msg);
                    $result = $validate->check($data);
                    if (!$result) 
                    {
                        $this->error(__($validate->getError()));
                    }
                    
                    $result = \think\Db::table('AppUser')->where('userPhone', $params['now_recommend'])->where('isBan', 0)->find();
                    if (empty($result))
                    {
                        $this->error('推荐人不存在');
                    }
                    
                    $arr['id'] = $ids;
                    $arr['status'] = 1;
                    $arr['now_recommend'] = $params['now_recommend'];
                    $arr['verify_id'] = $this->auth->id;
                    $arr['remark'] = $params['remark'];
                    $arr['vtime'] = $_SERVER['REQUEST_TIME'];
                    
                    $result = \think\Db::table('referee_log')->update($arr);
                    if ($result !== false)
                    {
                        $this->model->isUpdate(true)->save(['userId' => $row['uid'], 'recommendPhone' => $params['now_recommend']]);
                        $this->success();
                    }
                    else
                    {
                        $this->error($row->getError());
                    }
                }
                catch (\think\exception\PDOException $e)
                {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $row['userPhone'] = $this->model->get($row['uid'])['userPhone'];
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}
