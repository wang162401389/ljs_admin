<?php

namespace app\admin\controller\appuser;

use app\common\controller\Backend;
use think\Db;
use think\Validate;

/**
 * 推荐人修改
 *
 * @icon fa fa-circle-o
 */
class Refereeapply extends Backend
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
            
            $field = 'u.userId as `u.userId`,u.userPhone as `u.userPhone`,u.userName as `u.userName`,rl.pre_recommend,u.recommendPhone as `u.recommendPhone`,u.regSource as `u.regSource`,u.createdTime as `u.createdTime`';
            
            $subsql = Db::table('referee_log')->where(['status' => 1])->field('uid,pre_recommend')->order('id desc')->limit(1)->buildSql();
            
            $total = Db::table('AppUser')
                    ->alias('u')
                    ->field($field)
                    ->join([$subsql => 'rl'], 'u.userId = rl.uid', 'LEFT')
                    ->where($where)
                    ->order($sort, $order)
                    ->group('u.userId')
                    ->count();
            
            $list = Db::table('AppUser')
                    ->alias('u')
                    ->field($field)
                    ->join([$subsql => 'rl'], 'u.userId = rl.uid', 'LEFT')
                    ->where($where)
                    ->order($sort, $order)
                    ->group('u.userId')
                    ->limit($offset, $limit)
                    ->select();
            
            if (!empty($list)) 
            {
                foreach ($list as &$v) 
                {
                    $v['u.userId'] = (string)$v['u.userId'];
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
        $row = $this->model->get($ids);
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
                    $result = Db::table('referee_log')->where('uid', $ids)->where('status', 0)->find();
                    if (!empty($result)) 
                    {
                        $this->error('你有一个修改申请正在审核中，审核通过之前不能重复提交');
                    }
                    
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
                    
                    $result = Db::table('AppUser')->where('userPhone', $params['now_recommend'])->where('isBan', 0)->find();
                    if (empty($result)) 
                    {
                        $this->error('推荐人不存在');
                    }

                    $arr['uid'] = $ids;
                    $arr['pre_recommend'] = $params['pre_recommend'];
                    $arr['now_recommend'] = $params['now_recommend'];
                    $arr['apply_id'] = $this->auth->id;
                    $arr['remark'] = $params['remark'];
                    $arr['ctime'] = $_SERVER['REQUEST_TIME'];
                    
                    $result = Db::table('referee_log')->insert($arr);
                    if ($result !== false)
                    {
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
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    
    /**
     * 查看
     */
    public function applylog($ids)
    {
        $row = $this->model->get($ids);
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
        
        $result = Db::table('referee_log')->where('uid', $ids)->where('status', 1)->select();
        if (!empty($result)) 
        {
            foreach ($result as &$v)
            {
                $v['apply_name'] = Db::table('admin')->where('id', $v['apply_id'])->value('username');
                $v['verify_name'] = Db::table('admin')->where('id', $v['verify_id'])->value('username');
            }
        }
        $this->view->assign("list", $result);
        return $this->view->fetch();
    }
}