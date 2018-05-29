<?php

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