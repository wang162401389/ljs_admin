<?php
use think\Db;
use think\Log;

// 公共助手函数

if (!function_exists('__'))
{

    /**
     * 获取语言变量值
     * @param string    $name 语言变量名
     * @param array     $vars 动态变量值
     * @param string    $lang 语言
     * @return mixed
     */
    function __($name, $vars = [], $lang = '')
    {
        if (is_numeric($name) || !$name)
            return $name;
        if (!is_array($vars))
        {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return \think\Lang::get($name, $vars, $lang);
    }

}

if (!function_exists('format_bytes'))
{

    /**
     * 将字节转换为可读文本
     * @param int $size 大小
     * @param string $delimiter 分隔符
     * @return string
     */
    function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 6; $i++)
            $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }

}

if (!function_exists('datetime'))
{

    /**
     * 将时间戳转换为日期时间
     * @param int $time 时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }

}

if (!function_exists('human_date'))
{

    /**
     * 获取语义化时间
     * @param int $time 时间
     * @param int $local 本地时间
     * @return string
     */
    function human_date($time, $local = null)
    {
        return \fast\Date::human($time, $local);
    }

}

if (!function_exists('cdnurl'))
{
    
    /**
     * 获取上传资源的CDN的地址
     * @param string $url 资源相对地址
     * @param boolean $domain 是否显示域名 或者直接传入域名
     * @return string
     */
    function cdnurl($url, $domain = false)
    {
        $url = preg_match("/^https?:\/\/(.*)/i", $url) ? $url : \think\Config::get('upload.cdnurl') . $url;
        if ($domain && !preg_match("/^(http:\/\/|https:\/\/)/i", $url)) {
            if (is_bool($domain)) {
                $public = \think\Config::get('view_replace_str.__PUBLIC__');
                $url = rtrim($public, '/') . $url;
                if (!preg_match("/^(http:\/\/|https:\/\/)/i", $url)) {
                    $url = request()->domain() . $url;
                }
            } else {
                $url = $domain . $url;
            }
        }
        return $url;
    }

}


if (!function_exists('is_really_writable'))
{

    /**
     * 判断文件或文件夹是否可写
     * @param	string $file 文件或目录
     * @return	bool
     */
    function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/')
        {
            return is_writable($file);
        }
        if (is_dir($file))
        {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === FALSE)
            {
                return FALSE;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return TRUE;
        }
        elseif (!is_file($file) OR ( $fp = @fopen($file, 'ab')) === FALSE)
        {
            return FALSE;
        }
        fclose($fp);
        return TRUE;
    }

}

if (!function_exists('rmdirs'))
{

    /**
     * 删除文件夹
     * @param string $dirname 目录
     * @param bool $withself 是否删除自身
     * @return boolean
     */
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname))
            return false;
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo)
        {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if ($withself)
        {
            @rmdir($dirname);
        }
        return true;
    }

}

if (!function_exists('copydirs'))
{

    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest 目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest))
        {
            mkdir($dest, 0755, true);
        }
        foreach (
        $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item
        )
        {
            if ($item->isDir())
            {
                $sontDir = $dest . DS . $iterator->getSubPathName();
                if (!is_dir($sontDir))
                {
                    mkdir($sontDir, 0755, true);
                }
            }
            else
            {
                copy($item, $dest . DS . $iterator->getSubPathName());
            }
        }
    }

}

if (!function_exists('mb_ucfirst'))
{

    function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }

}

if (!function_exists('addtion'))
{

    /**
     * 附加关联字段数据
     * @param array $items 数据列表
     * @param mixed $fields 渲染的来源字段
     * @return array
     */
    function addtion($items, $fields)
    {
        if (!$items || !$fields)
            return $items;
        $fieldsArr = [];
        if (!is_array($fields))
        {
            $arr = explode(',', $fields);
            foreach ($arr as $k => $v)
            {
                $fieldsArr[$v] = ['field' => $v];
            }
        }
        else
        {
            foreach ($fields as $k => $v)
            {
                if (is_array($v))
                {
                    $v['field'] = isset($v['field']) ? $v['field'] : $k;
                }
                else
                {
                    $v = ['field' => $v];
                }
                $fieldsArr[$v['field']] = $v;
            }
        }
        foreach ($fieldsArr as $k => &$v)
        {
            $v = is_array($v) ? $v : ['field' => $v];
            $v['display'] = isset($v['display']) ? $v['display'] : str_replace(['_ids', '_id'], ['_names', '_name'], $v['field']);
            $v['primary'] = isset($v['primary']) ? $v['primary'] : '';
            $v['column'] = isset($v['column']) ? $v['column'] : 'name';
            $v['model'] = isset($v['model']) ? $v['model'] : '';
            $v['table'] = isset($v['table']) ? $v['table'] : '';
            $v['name'] = isset($v['name']) ? $v['name'] : str_replace(['_ids', '_id'], '', $v['field']);
        }
        unset($v);
        $ids = [];
        $fields = array_keys($fieldsArr);
        foreach ($items as $k => $v)
        {
            foreach ($fields as $m => $n)
            {
                if (isset($v[$n]))
                {
                    $ids[$n] = array_merge(isset($ids[$n]) && is_array($ids[$n]) ? $ids[$n] : [], explode(',', $v[$n]));
                }
            }
        }
        $result = [];
        foreach ($fieldsArr as $k => $v)
        {
            if ($v['model'])
            {
                $model = new $v['model'];
            }
            else
            {
                $model = $v['name'] ? \think\Db::name($v['name']) : \think\Db::table($v['table']);
            }
            $primary = $v['primary'] ? $v['primary'] : $model->getPk();
            $result[$v['field']] = $model->where($primary, 'in', $ids[$v['field']])->column("{$primary},{$v['column']}");
        }

        foreach ($items as $k => &$v)
        {
            foreach ($fields as $m => $n)
            {
                if (isset($v[$n]))
                {
                    $curr = array_flip(explode(',', $v[$n]));

                    $v[$fieldsArr[$n]['display']] = implode(',', array_intersect_key($result[$n], $curr));
                }
            }
        }
        return $items;
    }
}

if (!function_exists('var_export_short'))
{
    
    /**
     * 返回打印数组结构
     * @param string $var   数组
     * @param string $indent 缩进字符
     * @return string
     */
    function var_export_short($var, $indent = "")
    {
        switch (gettype($var))
        {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value)
                {
                    $r[] = "$indent    "
                    . ($indexed ? "" : var_export_short($key) . " => ")
                    . var_export_short($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, TRUE);
        }
    }
    
}

/**
 * 等额本息法
 * 贷款本金×月利率×（1+月利率）还款月数/[（1+月利率）还款月数-1]
 * a*[i*(1+i)^n]/[(1+I)^n-1]
 * （a×i－b）×（1＋i）
 * @param array $data money,year_apr,duration,borrow_time(用来算还款时间的),type(==all时，返回还款概要)
 * @return string|number[]|NULL[]|unknown[]
 */
function equal_month($data = [])
{
    if (isset($data['money']) && $data['money'] > 0) 
    {
        $account = $data['money'];
    } 
    else 
    {
        return "";
    }

    if (isset($data['year_apr']) && $data['year_apr'] > 0) 
    {
        $year_apr = $data['year_apr'];
    } 
    else 
    {
        return "";
    }

    if (isset($data['duration']) && $data['duration'] > 0) 
    {
        $duration = $data['duration'];
    }
    $borrow_time = time();
    if (isset($data['borrow_time']) && $data['borrow_time'] > 0) 
    {
        $borrow_time = $data['borrow_time'];
    }
    $month_apr = $year_apr / (12 * 100);
    $_li = pow((1+$month_apr), $duration);
    $repayment = round($account * ($month_apr * $_li) / ($_li - 1), 4);
    
    $_result = [];
    if (isset($data['type']) && $data['type'] == "all") 
    {
        $_result['repayment_money'] = $repayment * $duration;
        $_result['monthly_repayment'] = $repayment;
        $_result['month_apr'] = round($month_apr * 100, 4);
    } 
    else 
    {
        for ($i = 0;$i < $duration;$i++) 
        {
            if ($i == 0) 
            {
                $interest = round($account * $month_apr, 4);
            } 
            else 
            {
                $_lu = pow((1+$month_apr), $i);
                $interest = round(($account * $month_apr - $repayment) * $_lu + $repayment, 4);
            }
            $_result[$i]['repayment_money'] = get_float_value($repayment, 2);
            $_result[$i]['repayment_time'] = get_times(['time' => $borrow_time, 'num' => $i + 1]);
            $_result[$i]['interest'] = get_float_value($interest, 2);
            $_result[$i]['capital'] = get_float_value($repayment - $interest, 2);
        }
    }
    return $_result;
}

/**
 * 按季等额本息法
 * @param array $data
 * @return boolean|string|number[]|NULL[]|unknown[]
 */
function equal_season($data = [])
{
    //借款的月数
    if (isset($data['month_times']) && $data['month_times'] > 0) 
    {
        $month_times = $data['month_times'];
    }
    //按季还款必须是季的倍数
    if ($month_times % 3 != 0) 
    {
        return false;
    }
    //借款的总金额
    if (isset($data['account']) && $data['account'] > 0) 
    {
        $account = $data['account'];
    } 
    else 
    {
        return "";
    }
    //借款的年利率
    if (isset($data['year_apr']) && $data['year_apr'] > 0) 
    {
        $year_apr = $data['year_apr'];
    } 
    else 
    {
        return "";
    }

    //借款的时间 --- 什么时候开始借款，计算还款的
    $borrow_time = time();
    if (isset($data['borrow_time']) && $data['borrow_time'] > 0) 
    {
        $borrow_time = $data['borrow_time'];
    } 
    $season_apr = $year_apr / (4 * 100);
    //得到总季数
    $_season = $month_times / 3;
    $_li = pow((1+$season_apr), $_season);

    $repayment = round($account * ($season_apr * $_li) / ($_li - 1), 2);
    $_result = [];
    if (isset($data['type']) && $data['type'] == "all") 
    {
        $_result['repayment_money'] = $repayment * $_season;
        $_result['monthly_repayment'] = $repayment;
        $_result['month_apr'] = round($season_apr * 100, 4);
        $_result['interest'] = $_result['repayment_money'] - $account;
    } 
    else 
    {
        $_yes_account=0;
        $repayment_account = 0;//总还款额
        for ($i = 0;$i < $month_times;$i++) 
        {
            //应还的金额
            $repay = $account - $_yes_account;
            //利息等于应还金额乘季利率
            $interest = round(($repay*$season_apr) / 3, 2);
            //总还款额+利息
            $repayment_account = $repayment_account + $interest;
            $capital = 0;
            if ($i % 3 == 2) 
            {
                //本金只在第三个月还，本金等于借款金额除季度
                $capital = $repayment - $interest * 3;
                $_yes_account = $_yes_account + $capital;
                $repay = $account - $_yes_account;
                //总还款额+本金
                $repayment_account = $repayment_account + $capital;
            }

            $_result[$i]['repayment_money'] = get_float_value($interest + $capital, 2);
            $_result[$i]['repayment_time'] = get_times(['time' => $borrow_time, 'num' => $i + 1]);
            $_result[$i]['interest'] = get_float_value($interest, 2);
            $_result[$i]['capital'] = get_float_value($capital, 2);
        }
    }
    return $_result;
}

/**
 * 到期还本，按月付息
 * @param array $data
 * @return string|number|unknown
 */
function equal_end_month($data = [])
{

    //借款的月数
    if (isset($data['month_times']) && $data['month_times'] > 0) 
    {
        $month_times = $data['month_times'];
    }

    //借款的总金额
    if (isset($data['account']) && $data['account'] > 0) 
    {
        $account = $data['account'];
    } else {
        return "";
    }

    //借款的年利率
    if (isset($data['year_apr']) && $data['year_apr'] > 0) 
    {
        $year_apr = $data['year_apr'];
    } else {
        return "";
    }

    $borrow_time = time();
    //借款的时间
    if (isset($data['borrow_time']) && $data['borrow_time'] > 0) 
    {
        $borrow_time = $data['borrow_time'];
    }

    //月利率
    $month_apr = $year_apr / (12 * 100);

    $_yes_account = 0;
    //总还款额
    $repayment_account = 0;
    $_all_interest = 0;

    //利息等于应还金额乘月利率
    $interest = round($account * $month_apr, 2);
    for ($i = 0;$i < $month_times;$i++) 
    {
        $capital = 0;
        if ($i+1 == $month_times) 
        {
            //本金只在最后一个月还，本金等于借款金额除季度
            $capital = $account;
        }

        $_result[$i]['repayment_money'] = $interest + $capital;
        $_result[$i]['repayment_time'] = get_times(['time' => $borrow_time, 'num' => $i + 1]);
        $_result[$i]['interest'] = $interest;
        $_result[$i]['capital'] = $capital;
        $_all_interest += $interest;
    }
    if (isset($data['type']) && $data['type'] == "all") 
    {
        $_resul['repayment_account'] = $account + $interest * $month_times;
        $_resul['monthly_repayment'] = $interest;
        $_resul['month_apr'] = round($month_apr * 100, 4);
        $_resul['interest'] = $_all_interest;
        return $_resul;
    } 
    else 
    {
        return $_result;
    }
}

/**
 * 到期还本，按月付息
 * @param array $data
 * @return string|unknown
 */
function equal_end_month_only($data = [])
{
    //借款的月数
    if (isset($data['month_times']) && $data['month_times'] > 0) 
    {
        $month_times = $data['month_times'];
    }

    //借款的总金额
    if (isset($data['account']) && $data['account'] > 0) 
    {
        $account = $data['account'];
    } else {
        return "";
    }

    //借款的年利率
    if (isset($data['year_apr']) && $data['year_apr'] > 0) 
    {
        $year_apr = $data['year_apr'];
    } else {
        return "";
    }

    //月利率
    $month_apr = $year_apr / (12 * 100);

    //利息等于应还金额*月利率*借款月数
    $interest = get_float_value($account * $month_apr * $month_times, 2);

    if (isset($data['type']) && $data['type'] == "all") 
    {
        $_resul['repayment_account'] = $account + $interest;
        $_resul['monthly_repayment'] = $interest;
        $_resul['month_apr'] = round($month_apr * 100, 4);
        $_resul['interest'] = $interest;
        $_resul['capital'] = $account;
        return $_resul;
    }
}

/**
 * 等本降息
 * @param array $data money,year_apr,duration,borrow_time(用来算还款时间的),type(==all时，返回还款概要)
 * @return string|number[]|NULL[]|unknown[]
 */
function equal_month_cut_interest($data = [])
{
    if (isset($data['money']) && $data['money'] > 0) 
    {
        $account = $data['money'];
    } else {
        return "";
    }

    if (isset($data['year_apr']) && $data['year_apr'] > 0) 
    {
        $year_apr = $data['year_apr'];
    } else {
        return "";
    }

    if (isset($data['duration']) && $data['duration'] > 0) 
    {
        $duration = $data['duration'];
    }
    $borrow_time = time();
    if (isset($data['borrow_time']) && $data['borrow_time'] > 0) 
    {
        $borrow_time = $data['borrow_time'];
    }
    $month_apr = $year_apr / (12 * 100);
    $month_money = $account / $duration;
    $repayment = round(($account * $duration - $month_money * (($duration*($duration-1)) / 2)) * $month_apr, 4);
    $_result = [];
    if (isset($data['type']) && $data['type'] == "all") 
    {
        $_result['repayment_money'] = $month_money * $duration + $repayment;
        $_result['monthly_repayment'] = $repayment;
        $_result['month_apr'] = round($month_apr * 100, 4);
    } 
    else 
    {
        for ($i = 0;$i < $duration;$i++) 
        {
            if ($i == 0) 
            {
                $interest = round($account * $month_apr, 4);
            } 
            else 
            {
                $interest = round(($account - $month_money * $i) * $month_apr, 4);
            }
            $_result[$i]['repayment_money'] = get_float_value($month_money + $interest, 4);
            $_result[$i]['repayment_time'] = get_times(['time' => $borrow_time, 'num' => $i + 1]);
            $_result[$i]['interest'] = get_float_value($interest, 2);
            $_result[$i]['capital'] = get_float_value($month_money, 2);
        }
    }
    return $_result;
}

/**
 * 格式化数字
 * @param unknown $f
 * @param unknown $len
 * @return string
 */
function get_float_value($f, $len)
{
    return number_format($f, $len, '.', '');
}

/**
 * 获得时间天数
 * @param array $data
 * @return string|unknown
 */
function get_times($data = [])
{
    if (isset($data['time']) && $data['time'] != "")
    {
        //时间
        $time = $data['time'];
    }
    elseif (isset($data['date']) && $data['date'] != "")
    {
        //日期
        $time = strtotime($data['date']);
    }
    else
    {
        //现在时间
        $time = time();
    }
    $type = "month";
    if (isset($data['type']) && $data['type'] != "")
    {
        //时间转换类型，有day week month year
        $type = $data['type'];
    }
    $num = 1;
    if (isset($data['num']) && $data['num'] != "")
    {
        $num = $data['num'];
    }

    if ($type == "month")
    {
        $month = date("m", $time);
        $year = date("Y", $time);
        $_result = strtotime("$num month", $time);
        $_month = (int)date("m", $_result);
        if ($month + $num > 12)
        {
            $_num = $month + $num - 12;
            $year = $year + 1;
        }
        else
        {
            $_num = $month + $num;
        }

        if ($_num != $_month)
        {
            $_result = strtotime("-1 day",strtotime("{$year}-{$_month}-01"));
        }
    }
    else
    {
        $_result = strtotime("$num $type", $time);
    }
    if (isset($data['format']) && $data['format'] != "")
    {
        return date($data['format'], $_result);
    }
    else
    {
        return $_result;
    }
}

/**
 * 获取期限天数
 * @param unknown $day_string
 * @return number
 */
function get_day($day_string)
{
    $day_array = explode("+", $day_string);
    $day = intval(mb_strcut($day_array[0], 0, mb_strlen($day_array[0]) - 1));
    if (count($day_array) == 2) {
        $day2 = intval(mb_strcut($day_array[1], 0, mb_strlen($day_array[0]) - 1));
        $day += $day2;
    }
    if (mb_strpos($day_string, "月")) {
        $day = $day * 30;
    }

    return $day;
}

function encrypt($str)
{
    $size = mcrypt_get_block_size ( MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC );
    $pad = $size - (strlen ( $str ) % $size);
    $str .= str_repeat ( chr ( $pad ), $pad );
    
    $data = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, 'LjsAuthorizeSeed', $str, MCRYPT_MODE_CBC, 'HelloThisIsWorld');
    
    return base64_encode($data);
}

/**
 * 处理新版本数据
 * @param number $new_repay
 * @param string $orderId
 * @param array $uid
 * @return boolean|array
 */
function handle_new($orderId, $uid = [])
{
    if (!empty($uid)) 
    {
        $newoverduelist = getnewoverduelistbyuid($uid);
    }
    else 
    {
        $newoverduelist = getnewoverduelist();
    }
    if (!empty($newoverduelist))
    {
        $i = $k = $j = $c = $d = 0;
        $trade_list = $userinfo = $batch_orderId_arr = $act_receive_arr = [];

        foreach ($newoverduelist as $v)
        {
            //按比例还给投资者的钱
            $receive_money = substr(sprintf("%.3f", $v['realityMoney'] * 0.048), 0, -1);
            $t_money = $receive_money * 100;
            //肯定是小于等于 投资的钱
            Log::write('新：'.($j * 300 + $i + 1).'、receive_money：'.$receive_money.'，realityMoney：'.$v['realityMoney'], 'java');
            if ($receive_money <= ($v['realityMoney'] - $v['receiveMoney']))
            {
                $phone = Db::table('AppUser')->where('userId', $v['userId'])->value('userPhone');
                if (isset($userinfo[$phone]))
                {
                    $userinfo[$phone] += $receive_money;
                }
                else
                {
                    $userinfo[$phone] = $receive_money;
                }

                $upd = [];
                //还清了
                if ($v['realityMoney'] - $v['receiveMoney'] == $receive_money)
                {
                    $upd['repaymentStatus'] = 3;
                }
                $upd['receiveMoney'] = Db::raw('receiveMoney+'.$t_money);
                $upd['repaymentTime'] = $upd['updateTime'] = Db::raw('now()');
                $investor = Db::table('AppInvestorRepayment')->where('id', $v['id'])->update($upd);
                if (!$investor)
                {
                    return false;
                }

                $upd = [];
                //还清了
                if ($v['realityMoney'] - $v['receiveMoney'] == $receive_money)
                {
                    $upd['repaymentStatus'] = 1;
                    $upd['applyAgentRepaymentStatus'] = 2;
                }
                $upd['repaymentMoney'] = Db::raw('repaymentMoney+'.$t_money);
                $upd['substituteMoney'] = Db::raw('substituteMoney+'.$t_money);
                $upd['repaymentTime'] = $upd['substituteTime'] = $upd['updateTime'] = Db::raw('now()');
                $borrow = Db::table('AppBorrowRepayment')->where('borrowInfoId', $v['borrowInfoId'])->update($upd);
                if (!$borrow)
                {
                    return false;
                }

                $money_before = Db::table('AppAccount')->where('userId', $v['userId'])->value('moneyCollect');

                $upd = [];
                $upd['moneyCollect'] = Db::raw('moneyCollect-'.$t_money);
                $upd['updatedTime'] = Db::raw('now()');

                $appaccountinfo = Db::table('AppAccount')->where('userId', $v['userId'])->field('id')->find();
                if (!empty($appaccountinfo))
                {
                    $account = Db::table('AppAccount')->where('userId', $v['userId'])->where('moneyCollect', '>', 0)->update($upd);
                    if (!$account)
                    {
                        return false;
                    }
                }

                $money_after = Db::table('AppAccount')->where('userId', $v['userId'])->value('moneyCollect');

                $ins = [];
                $ins['userId'] = $ins['acno'] = $v['userId'];
                $ins['payChannelType'] = 2;
                $ins['moneyBefore'] = $money_before;
                $ins['moneyAfter'] = $money_after;
                $ins['createdTime'] = $ins['updatedTime'] = date('Y-m-d H:i:s');
                $ins['changeAmt'] = '-'.$t_money;
                $account_detail = Db::table('AppAccountDetail')->insert($ins);
                if (!$account_detail)
                {
                    return false;
                }

                if ($v['realityMoney'] - $v['receiveMoney'] == $receive_money)
                {
                    $upd = [];
                    $upd['borrowStatus'] = 10;
                    $upd['updateTime'] = date('Y-m-d H:i:s');
                    $upbinfo = Db::table('AppBorrowInfo')->where('borrowInfoId', $v['borrowInfoId'])->update($upd);
                    if (!$upbinfo)
                    {
                        return false;
                    }
                }
            }

            if ($i < 300)
            {
                $person_orderId = buildorderno();
                if ($k === 0)
                {
                    $batch_orderId_arr[$j] = buildorderno();
                    //实际还款总额
                    $act_receive_arr[$j] = $t_money;
                    $trade_list[$j] = $person_orderId.'~20151008'.$v['userId'].'~UID~SAVING_POT~'.$receive_money.'~~新版本标号：'.$v['borrowSn'].'系统还款~~~~~'.$orderId;
                    $k++;
                }
                else
                {
                    $act_receive_arr[$j] += $t_money;
                    $trade_list[$j] .= '$'.$person_orderId.'~20151008'.$v['userId'].'~UID~SAVING_POT~'.$receive_money.'~~新版本标号：'.$v['borrowSn'].'系统还款~~~~~'.$orderId;
                }
                $i++;

                $ins = [];
                $ins['accountId'] = '';
                $ins['userId'] = $v['userId'];
                $ins['payChannelType'] = 2;
                $ins['flowingType'] = 1;
                $ins['borrowInfoId'] = $v['borrowInfoId'];
                $ins['relatedUserId'] = 0;
                $ins['transactionAmt'] = $t_money;
                $ins['handingFee'] = 0;
                $ins['remark'] = '新浪回款标号：'.$v['borrowSn'].'，还款金额'.$receive_money;
                $ins['transactionStatus'] = 2;
                $ins['transactionType'] = 4;
                $ins['orderId'] = $person_orderId;
                $ins['batchOrderId'] = $batch_orderId_arr[$j];
                $ins['innerOrderId'] = $v['id'];
                $ins['outerOrderId'] = '';
                $ins['bankCardNo'] = $ins['realName'] = $ins['pid'] = $ins['bankCode'] = $ins['bankName'] = $ins['mobile'] = $ins['userIp'] = $ins['signPay'] = '';
                $ins['payTime'] = $ins['addTime'] = $ins['updateTime'] = date('Y-m-d H:i:s');
                $ins['couponsIdJx'] = $ins['couponsIdTz'] = 0;
                $res = Db::table('AppTransactionFlowing')->insert($ins);
                if (!$res)
                {
                    return false;
                }

                if ($i === 300)
                {
                    $i = $k = 0;
                    $j++;
                }
            }
        }

        return ['trade_list' => $trade_list, 'batch_orderid_arr' => $batch_orderId_arr, 'act_receive_arr' => $act_receive_arr, 'userinfo' => $userinfo];
    }

    return true;
}

function getnewoverduelistbyuid($uid)
{
    $new_overdue_list = Db::table('AppInvestorRepayment')
                        ->alias('t')
                        ->join('AppBorrowInfo a', 'a.borrowInfoId = t.borrowInfoId and t.receiveMoney < t.capital')
                        ->join('AppInvestorRecord b', 'b.id = t.borrowInvestorId')
                        ->join('AppBorrowRepayment c', 'c.borrowInfoId = t.borrowInfoId')
                        ->whereIn('t.userId', $uid)
                        ->where('t.repaymentStatus', 0)
                        ->where('a.testFlag', 0)
                        ->where('c.deadline', 'between', ['2018-07-19 00:00:00', '2018-09-01 00:00:00'])
                        ->field('t.id,b.realityMoney * 0.01 as realityMoney,t.receiveMoney * 0.01 as receiveMoney,t.userId,t.borrowInfoId,a.borrowSn')
                        ->select();

    return $new_overdue_list;
}

function getnewoverduelist()
{
    $new_overdue_list = Db::table('AppInvestorRepayment')
                        ->alias('t')
                        ->join('AppBorrowInfo a', 'a.borrowInfoId = t.borrowInfoId and t.receiveMoney < t.capital')
                        ->join('AppInvestorRecord b', 'b.id = t.borrowInvestorId')
                        ->join('AppBorrowRepayment c', 'c.borrowInfoId = t.borrowInfoId')
                        ->where('t.repaymentStatus', 0)
                        ->where('a.testFlag', 0)
                        ->where('c.deadline', 'between', ['2018-01-01 00:00:00', date('Y-m-t', strtotime('-1 month'))])
                        ->field('t.id,b.realityMoney * 0.01 as realityMoney,t.receiveMoney * 0.01 as receiveMoney,t.userId,t.borrowInfoId,a.borrowSn')
                        ->select();

    return $new_overdue_list;
}

/**
 * 处理旧版本数据
 * @param float $old_overdue
 * @param float $old_repay
 * @param string $orderId
 * @param array $uid
 * @return boolean|array
 */
function handle_old($orderId, $uid = [])
{
    if (!empty($uid))
    {
        $oldoverduelist = getoldoverduelistbyuid($uid);
    }
    else
    {
        $oldoverduelist = getoldoverduelist();
    }
    if (!empty($oldoverduelist))
    {
        $i = $k = $j = 0;
        $trade_list = $userinfo = $act_receive_arr = [];
        foreach ($oldoverduelist as $v)
        {
            //按比例还给投资者的钱
            $receive_money = substr(sprintf("%.3f", $v['capital'] * 0.048), 0, -1);
            //肯定是小于等于 投资的钱
            Log::write('旧：'.($j * 300 + $i + 1).'、receive_money：'.$receive_money.'capital：'.$v['capital'], 'java');

            //肯定是小于等于 投资的钱
            if ($receive_money <= ($v['capital'] - $v['receive_capital']))
            {
                $phone = Db::connect('old_db')->name('members')->where('id', $v['investor_uid'])->value('user_phone');
                if (!empty($phone))
                {
                    if (isset($userinfo[$phone]))
                    {
                        $userinfo[$phone] += $receive_money;
                    }
                    else
                    {
                        $userinfo[$phone] = $receive_money;
                    }
                }

                $upd = [];
                //还清了
                if (($v['capital'] - $v['receive_capital']) == $receive_money)
                {
                    $upd['repayment_time'] = time();
                    $upd['status'] = 4;
                }
                $upd['substitute_money'] = Db::raw('substitute_money+'.$receive_money);
                $upd['receive_capital'] = Db::raw('receive_capital+'.$receive_money);
                $upd['substitute_time'] = time();
                $res = Db::connect('old_db')->name('investor_detail')->where('id', $v['id'])->update($upd);
                if (!$res)
                {
                    return false;
                }

                $binfo = Db::connect('old_db')->name('borrow_info')->where('id', $v['borrow_id'])->field('borrow_money,repayment_money')->find();

                $upd = [];
                //还清了
                if ($receive_money + $binfo['repayment_money'] == $binfo['borrow_money'])
                {
                    //网站代还款完成
                    $upd['borrow_status'] = 9;
                }
                $upd['repayment_money'] = Db::raw('repayment_money+'.$receive_money);
                $upd['substitute_money'] = Db::raw('substitute_money+'.$receive_money);
                $res = Db::connect('old_db')->name('borrow_info')->where('id', $v['borrow_id'])->update($upd);
                if (!$res)
                {
                    return false;
                }

                $investinfo = Db::connect('old_db')->name('borrow_investor')->where('id', $v['invest_id'])->field('investor_capital,receive_capital')->find();
                $upd = [];
                //还清了
                if ($receive_money + $investinfo['receive_capital'] == $investinfo['investor_capital'])
                {
                    //网站代还完成
                    $upd['status'] = 6;
                }
                $upd['receive_capital'] = Db::raw('receive_capital+'.$receive_money);
                $upd['substitute_money'] = Db::raw('substitute_money+'.$receive_money);
                $res = Db::connect('old_db')->name('borrow_investor')->where('id', $v['invest_id'])->update($upd);
                if (!$res)
                {
                    return false;
                }
            }

            if ($i < 300)
            {
                $borrowsn = Db::connect('old_db')->name('borrow_assets')->where('borrow_id', $v['borrow_id'])->value('id');
                $borrowsn = !empty($borrowsn) ? 'ZJB'.$borrowsn : '未知';
                if ($k === 0)
                {
                    //实际还款总额
                    $act_receive_arr[$j] = $receive_money;
                    $trade_list[$j] = buildorderno().'~20151008'.$v['investor_uid'].'~UID~SAVING_POT~'.$receive_money.'~~旧版本标号：'.$borrowsn.'系统还款~~~~~'.$orderId;
                    $k++;
                }
                else
                {
                    $act_receive_arr[$j] += $receive_money;
                    $trade_list[$j] .= '$'.buildorderno().'~20151008'.$v['investor_uid'].'~UID~SAVING_POT~'.$receive_money.'~~旧版本标号：'.$borrowsn.'系统还款~~~~~'.$orderId;
                }
                $i++;
                if ($i === 300)
                {
                    $i = $k = 0;
                    $j++;
                }
            }
        }

        return ['trade_list' => $trade_list, 'act_receive_arr' => $act_receive_arr, 'userinfo' => $userinfo];
    }

    return true;
}

function getoldoverduelistbyuid($uid)
{
    $old_overdue_list = Db::connect('old_db')
                        ->name('investor_detail')
                        ->alias('ide')
                        ->join('borrow_info b', 'ide.borrow_id = b.id')
                        ->whereIn('ide.investor_uid', $uid)
                        ->where('ide.status', 7)
                        ->where('ide.repayment_time', 0)
                        ->where('ide.is_debt', 0)
                        ->where('b.test', 0)
                        ->whereTime('ide.deadline', 'between', ['2018-07-19 00:00:00', '2018-09-01 00:00:00'])
                        ->field('ide.id,ide.capital,ide.borrow_id,ide.receive_capital,ide.invest_id,ide.investor_uid')
                        ->select();

    return $old_overdue_list;
}

function getoldoverduelist()
{
    $old_overdue_list = Db::connect('old_db')
                        ->name('investor_detail')
                        ->alias('ide')
                        ->join('borrow_info b', 'ide.borrow_id = b.id')
                        ->where('ide.status', 7)
                        ->where('ide.repayment_time', 0)
                        ->where('ide.is_debt', 0)
                        ->where('b.test', 0)
                        ->whereTime('ide.deadline', 'between', ['2018-01-01 00:00:00', date('Y-m-t', strtotime('-1 month'))])
                        ->field('ide.id,ide.capital,ide.borrow_id,ide.receive_capital,ide.invest_id,ide.investor_uid')
                        ->select();

    return $old_overdue_list;
}

function buildorderno()
{
    $order_date = date('Y-m-d');

    $order_id_main = date('YmdHis') . rand(10000000, 99999999);

    $order_id_len = strlen($order_id_main);

    $order_id_sum = 0;

    for($i = 0; $i < $order_id_len; $i++)
    {
        $order_id_sum += (int)(substr($order_id_main,$i,1));
    }

    return $order_id_main . str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);
}