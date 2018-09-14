<?php
/**
 * 房间控制器
 * User: ysongyang
 * Date: 2018/9/1
 * Time: 21:17
 */

namespace app\admin\controller;

use app\common\controller\Backend;

class Luckydraw extends Backend
{

    /**
     * LeeSign模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('LuckydrawLog');
    }

    /**
     * 查看
     * @return string|\think\response\Json
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 兑换
     * @param $ids
     */
    public function exchange($ids)
    {
        if ($ids) {
            $where['id'] = $ids;
            $ret = $this->model->where($where)->update(['status' => 1, 'redeemtime' => time()]);
            if ($ret) {
                $this->success('兑换成功');
            }
        }
        $this->error('兑换失败');
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $delIds = [];
            foreach (explode(',', $ids) as $k => $v) {
                $delIds[] = $v;
            }
            $delIds = array_unique($delIds);
            $count = $this->model->where('id', 'in', $delIds)->update(['status' => 2]);
            if ($count) {
                $this->success();
            }
        }
        $this->error();
    }
}