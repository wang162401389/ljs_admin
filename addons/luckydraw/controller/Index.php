<?php

namespace addons\luckydraw\controller;

use app\common\model\ScoreLog;
use app\common\model\User;
use think\addons\Controller;
use think\Db;

class Index extends Controller
{

    protected $model = null;
    protected $config = null;
    protected $userinfo = null;

    public function _initialize()
    {
        parent::_initialize();
        if (!$this->isMobile()) {
            $this->error(__('请通过手机端进行访问！'));
        }
        if ($this->auth->getUser()) {
            $this->userinfo = $this->auth->getUserinfo();
            $this->assign('userinfo', $this->userinfo);
        }
        //读取抽奖配置
        $this->config = get_addon_config('luckydraw');
        $this->assign('rule', $this->config);
        $this->model = model('addons\luckydraw\model\LuckydrawLog');
    }

    public function index()
    {
        $record_line = $this->model->order('createtime desc')->limit(50)->select();
        $this->assign('record_line', $record_line);
        return $this->fetch();
    }

    /**
     * 获取大转盘奖项内容
     */
    public function getPrizeinfo()
    {
        $info = $this->config['award_item'];
        $html = "";
        foreach ($info as $k => $v) {
            $html .= "'" . $k . '\n' . $v . "',";
        }
        $html = rtrim($html, ',');
        //$info = "'" . join("','", array_values($info)) . "'";
        echo "var restaraunts=[" . $html . "];";

    }

    /**
     * 获取大转盘中奖率
     */
    public function getPrizerate()
    {
        $info = $this->config['winning_rate'];
        $info = join(",", array_values($info));
        echo "var zjl=[" . $info . "];";
    }

    /**
     * 获取奖品项目
     */
    public function getPrizetext()
    {
        $info = $this->config['award_item'];
        $html = '<div class="jiang" style="width:64%;text-align: left;margin-left:18%">';
        foreach ($info as $k => $v) {
            $html .= $k . ':' . '<span>' . $v . '</span></br>';
        }
        $html = substr($html, 0, -5);
        echo $html;
    }

    /**
     * 抽奖的处理方法
     * 添加抽奖记录
     * 扣除会员积分
     * 抽奖次数减一
     */
    public function addPrize()
    {
        if ($this->request->isPost()) {
            $score = $this->config['score']; //获取抽奖每次所用的积分
            Db::startTrans();
            $data['rank'] = input('post.rank');  //获取中间的级别
            $data['price'] = input('post.prize'); //获取中间内容
            $data['user_id'] = $this->auth->id;
            $data['username'] = $this->userinfo['username'];
            $data['createtime'] = time();
            $data['status'] = 0;
            $result = $this->model->insert($data);
            if ($result) {
                #处理剩余奖品数量
                self::updateCopies();
                self::score($score, $this->auth->id, '抽奖大转盘扣除积分');
                Db::commit();
                $json = ['code' => 0, 'msg' => $data['price']];
                echo json_encode($json);
                die;
            } else {
                Db::rollback();
                $json = ['code' => 1, 'msg' => '系统错误，请刷新后再试'];
                echo json_encode($json);
                die;
            }
        }
    }

    /**
     * 变更会员积分
     * @param int $score 积分
     * @param int $user_id 会员ID
     * @param string $memo 备注
     */
    public static function score($score, $user_id, $memo)
    {
        $user = User::get($user_id);
        if ($user) {
            $before = $user->score;
            $after = $user->score - $score;
            //$level = self::nextlevel($after);
            //更新会员信息
            $user->save(['score' => $after]);
            //写入日志
            ScoreLog::create(['user_id' => $user_id, 'score' => $score, 'before' => $before, 'after' => $after, 'memo' => $memo]);
        }
    }

    /**
     * 更新配置项的奖品剩余数量
     * @param int $value
     * @return int
     */
    public static function updateCopies($value = 1)
    {
        $config = get_addon_fullconfig('luckydraw');
        foreach ($config as $key => $val) {
            if ($config[$key]['name'] == 'copies' && intval($config[$key]['value']) > 0) {
                $config[$key]['value'] = $config[$key]['value'] - $value;
            }
        }
        set_addon_fullconfig('luckydraw', $config);
    }


    public function log()
    {
        return $this->fetch();
    }

    /**
     * 获取中奖纪录
     */
    public function getJson()
    {
        $args = $this->request->param();
        $list = $this->model->get_list($args, $this->auth->id);
        echo json_encode($list, true);
    }

    protected function isMobile()
    {
        $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';
        function CheckSubstrs($substrs, $text)
        {
            foreach ($substrs as $substr)
                if (false !== strpos($text, $substr)) {
                    return true;
                }
            return false;
        }

        $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
        $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');

        $found_mobile = CheckSubstrs($mobile_os_list, $useragent_commentsblock) ||
            CheckSubstrs($mobile_token_list, $useragent);

        if ($found_mobile) {
            return true;
        } else {
            return false;
        }
    }

}
