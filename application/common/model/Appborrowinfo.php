<?php

namespace app\common\model;
use think\Model;

class Appborrowinfo extends Model
{
    // 表名
    protected $table = 'AppBorrowInfo';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'datetime';

    // 定义时间戳字段名
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
    
    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            //生成标号
            $borrowSn = '';
            $productType = self::where($pk, $row[$pk])->value('productType');
            $table = $prefix = '';
            switch ($productType)
            {
                case 1:
                    $table = 'AppBorrowPledge';
                    $prefix = 'ZJ';
                    break;
                    
                case 2:
                    $table = 'AppBorrowFinance';
                    $prefix = 'RJ';
                    break;
                    
                case 3:
                    $table = 'AppBorrowInstallment';
                    $prefix = 'FQG';
                    break;
                    
                case 4:
                    $table = 'AppBorrowCredit';
                    $prefix = 'XJ';
                    break;
                    
                case 5:
                    $table = 'AppBorrowOptimal';
                    $prefix = 'YJ';
                    break;
                    
                case 6:
                    $table = 'AppBorrowGuarantee';
                    $prefix = 'BJ';
                    break;
                    
                case 7:
                    $table = 'AppBorrowCashLoan';
                    $prefix = 'XJD';
                    break;
                    
                case 8:
                    $table = 'AppBorrowAssets';
                    $prefix = 'ZJB';
                    break;
            }
            
            if (!empty($table) && !empty($prefix)) {
                $inc_id = \think\Db::table($table)->insertGetId(['borrow_id' => $row[$pk]]);
                $borrowSn = $prefix . $inc_id;
            }
            
            $row->getQuery()->where($pk, $row[$pk])->update(['borrowSn' => $borrowSn]);
        });
    }
    
    // 追加属性
    protected $append = [
        'borrow_status_text',
        'pay_channel_type_text',
        'product_type_text',
        'invest_interest_type_text',
        'first_verify_man'
    ];
    
    protected $type = [
        'borrowDuration' => 'integer',
        'borrowMoney' => 'integer',
        'borrowInterestRate' => 'integer',
        'addInterestRate' => 'integer',
        'productType' => 'integer',
        'investInterestType' => 'integer',
        'borrowInterestType' => 'integer',
        'borrowUse' => 'integer',
        'collectDay' => 'integer',
        'borrowMin' => 'integer',
        'danbao' => 'integer',
        'testFlag' => 'integer',
        'isNew' => 'integer',
        'payChannelType' => 'integer',
        'borrowDetails' => 'json'
    ];
    
    protected $auto = ['investInterestRate', 'borrowDurationTxt', 'investStage', 'borrowStage', 'investInterest', 'borrowDetails'];
    
    protected $insert = ['collectTime', 'bidTime', 'fullTime', 'deadline', 'firstVerifyTime', 'secondVerifyTime'];
    
    public function borrower()
    {
        return $this->belongsTo('app\admin\model\Borrower', 'borrowUid', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function firstverify()
    {
        return $this->belongsTo('app\admin\model\Admin', 'firstVerfiyId', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function secondverify()
    {
        return $this->belongsTo('app\admin\model\Admin', 'secondVerfiyId', [], 'LEFT')->setEagerlyType(0);
    }
    
    /**
     * 设置商品图片
     * @param unknown $value
     * @return string
     */
    public function setAddImageListAttr($value)
    {
        $pic_arr = array_map(function ($cell){return \think\Config::get('upload')['cdnurl'].$cell;}, explode(',', $value));
        return implode('|', $pic_arr);
    }
    
    /**
     * 获取商品图片
     * @param unknown $value
     * @return string
     */
    public function getAddImageListAttr($value)
    {
        $pic_arr = array_map(function ($cell){return str_replace(\think\Config::get('upload')['cdnurl'], '', $cell);}, explode('|', $value));
        return implode(',', $pic_arr);
    }
    
    /**
     * 可投标时间
     * @param unknown $value
     */
    public function setBidTimeAttr()
    {
        return date('Y-m-d H:i:s');
    }
    
    /**
     * 满标时间
     * @param unknown $value
     */
    public function setFullTimeAttr()
    {
        return date('Y-m-d H:i:s');
    }
    
    /**
     * 到期还款时间
     * @param unknown $value
     */
    public function setDeadlineAttr()
    {
        return date('Y-m-d H:i:s');
    }
    
    /**
     * 初审时间
     * @param unknown $value
     */
    public function setFirstVerifyTimeAttr()
    {
        return date('Y-m-d H:i:s');
    }
    
    /**
     * 复审时间
     * @param unknown $value
     */
    public function setSecondVerifyTimeAttr()
    {
        return date('Y-m-d H:i:s');
    }
    
    /**
     * 借款金额100倍入库
     * @param unknown $value
     * @return number
     */
    public function setBorrowMoneyAttr($value)
    {
        return $value * 100;
    }
    
    /**
     * 获取借款金额
     * @param unknown $value
     * @return number
     */
    public function getBorrowMoneyAttr($value)
    {
        return $value / 100;
    }
    
    /**
     * 设置加息利率
     * @param unknown $value
     * @return number
     */
    public function setAddInterestRateAttr($value)
    {
        return $value * 100;
    }
    
    /**
     * 获取加息利率
     * @param unknown $value
     * @return number
     */
    public function getAddInterestRateAttr($value)
    {
        return $value / 100;
    }
    
    /**
     * 设置借款人利率
     * @param unknown $value
     * @return number
     */
    public function setBorrowInterestRateAttr($value)
    {
        return $value * 100;
    }
    
    /**
     * 获取借款人利率
     * @param unknown $value
     * @return number
     */
    public function getBorrowInterestRateAttr($value)
    {
        return $value / 100;
    }

    /**
     * 设置最小投资金额
     * @param unknown $value
     * @return number
     */
    public function setBorrowMinAttr($value)
    {
        return $value * 100;
    }
    
    /**
     * 获取最小投资金额
     * @param unknown $value
     * @return number
     */
    public function getBorrowMinAttr($value)
    {
        return $value / 100;
    }
    
    /**
     * 设置最高投资金额
     * @param unknown $value
     * @return number
     */
    public function setborrowMaxAttr($value)
    {
        return $value * 100;
    }
    
    /**
     * 获取最高投资金额
     * @param unknown $value
     * @return number
     */
    public function getborrowMaxAttr($value)
    {
        return $value / 100;
    }
    
    /**
     * 设置综合服务费
     * @param unknown $value
     * @return number
     */
    public function setserviceChargeAttr($value)
    {
        return $value * 100;
    }
    
    /**
     * 获取综合服务费
     * @param unknown $value
     * @return number
     */
    public function getserviceChargeAttr($value)
    {
        return $value / 100;
    }
    
    /**
     * 获取投资人利息
     * @param unknown $value
     * @return number
     */
    public function getInvestInterestAttr($value)
    {
        return $value / 100;
    }
    
    /**
     * 标的详情
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function setBorrowDetailsAttr($value, $data)
    {
        $json_arr = [
            ['label' => '借款方','val' => $data['debtor']],
            ['label' => '业务类型','val' => $data['businessType']],
            ['label' => '担保方式','val' => $data['guaranteeWay']],
            ['label' => '托管机构','val' => $data['depositoryInstitution']],
            ['label' => '还款保障','val' => $data['ensure']],
            ['label' => '借款方信息','val' => $data['debtorInfo']],
            ['label' => '质押物信息','val' => $data['pledge']],
            ['label' => '还款来源','val' => $data['payment']],
            ['label' => '项目状况','val' => $data['projectDetail']],
            ['label' => '风控措施','val' => $data['riskControl']],
            ['label' => '质押物真实性','val' => $data['pledgeAuthenticity']],
            ['label' => '信息透明度','val' => $data['transparency']],
        ];
        
        $tmp = [];
        foreach ($json_arr as $v)
        {
            if (empty($v['val'])) 
            {
                continue;
            }
            $tmp[] = $v;
        }
        
        return json_encode(['detail' => $tmp]);
    }
    
    /**
     * 标的详情字段
     * @return string[][]
     */
    public static function borrowDetailField()
    {
        return [
            'debtor' => ['label' => '借款方', 'type' => 'text'],
            'businessType' => ['label' => '业务类型', 'type' => 'text'],
            'guaranteeWay' => ['label' => '担保方式', 'type' => 'text'],
            'depositoryInstitution' => ['label' => '托管机构', 'type' => 'text'],
            'ensure' => ['label' => '还款保障', 'type' => 'text'],
            'debtorInfo' => ['label' => '借款方信息', 'type' => 'textarea'],
            'pledge' => ['label' => '质押物信息', 'type' => 'textarea'],
            'payment' => ['label' => '还款来源', 'type' => 'textarea'],
            'projectDetail' => ['label' => '项目状况', 'type' => 'textarea'],
            'riskControl' => ['label' => '风控措施', 'type' => 'textarea'],
            'pledgeAuthenticity' => ['label' => '质押物真实性', 'type' => 'textarea'],
            'transparency' => ['label' => '信息透明度', 'type' => 'textarea'],
        ];
    }
    
    /**
     * 还款类型
     * @param unknown $value
     * @param unknown $data
     * @return number|unknown
     */
    public function setInvestInterestTypeAttr($value, $data)
    {
        return $data['type'] == 1 ? 1 : $data['investInterestType'];
    }
    
    /**
     * 投资人回款总期数
     * @param unknown $value
     * @param unknown $data
     * @return number|unknown
     */
    public function setInvestStageAttr($value, $data)
    {
        return $data['type'] == 1 ? 1 : $data['borrowDuration'];
    }
    
    /**
     * 借款人总期数
     * @param unknown $value
     * @param unknown $data
     * @return number|unknown
     */
    public function setBorrowStageAttr($value, $data)
    {
        return $data['type'] == 1 ? 1 : $data['borrowDuration'];
    }
    
    /**
     * 投资人利率（借款人利率 + 平台加息利率）
     * @param unknown $value
     * @param unknown $data
     * @return number
     */
    public function setInvestInterestRateAttr($value, $data)
    {
        return $data['borrowInterestRate'] + $data['addInterestRate'];
    }
    
    /**
     * 募集到期时间
     * @param unknown $name
     * @param unknown $value
     * @return string
     */
    public function setCollectTimeAttr($value, $data)
    {
        return date("Y-m-d H:i:s", strtotime("+{$data['collectDay']} day"));
    }
    
    /**
     * 借款时间的文字描述
     * @param unknown $value
     * @param unknown $data
     */
    public function setBorrowDurationTxtAttr($value, $data)
    {
        return $data['borrowDuration'].($data['type'] == 1 ? '天' : '个月'); 
    }
    
    /**
     * 投资人利息
     * @param unknown $value
     * @param unknown $data
     */
    public function setInvestInterestAttr($value, $data)
    {
        //本金
        $amount = $data['borrowMoney'] / 100;
        //还款类型1:天标 2:按月等额本息 3:按季分期还款（等额本息） 4:每月还息到期还本 5:一次性还款 7:按月等本降息
        $type = $data['investInterestType'];
        //期数
        $periods = $data['borrowDuration'];
        //年利率
        $rate = ($data['borrowInterestRate'] + $data['addInterestRate']) / 100;
        $interest = self::getBorrowInterest($type, $amount, $periods, $rate);
        return $interest * 100;
    }
    
    public static function getBorrowInterest($type, $amount, $periods, $rate)
    {
        $interest = '';
        switch ($type) {
            case 1:
                //按天到期还款
                $day_rate =  $rate / 36000;//计算出天标的天利率
                $interest = number_format($amount * $day_rate * $periods, 2, '.', ''); //字数字格式化保留小数点后2位
                break;
                
            case 2:
                //按月分期还款
                $parm['duration'] = $periods;
                $parm['money'] = $amount;
                $parm['year_apr'] = $rate;
                $parm['type'] = "all";
                $intre = equal_month($parm);
                $interest = ($intre['repayment_money'] - $amount);
                break;
                
            case 3:
                //按季分期还款
                $parm['month_times'] = $periods;
                $parm['account'] = $amount;
                $parm['year_apr'] = $rate;
                $parm['type'] = "all";
                $intre = equal_season($parm);
                $interest = $intre['interest'];
                break;
                
            case 4:
                //每月还息到期还本
                $parm['month_times'] = $periods;
                $parm['account'] = $amount;
                $parm['year_apr'] = $rate;
                $parm['type'] = "all";
                $intre = equal_end_month($parm);
                $interest = $intre['interest'];
                break;
                
            case 5:
                //一次性到期还款
                $parm['month_times'] = $periods;
                $parm['account'] = $amount;
                $parm['year_apr'] = $rate;
                $parm['type'] = "all";
                $intre = equal_end_month_only($parm);
                $interest = $intre['interest'];
                break;
                
            case 7:
                //按月等本降息
                $parm['duration'] = $periods;
                $parm['money'] = $amount;
                $parm['year_apr'] = $rate;
                $parm['type'] = "all";
                $intre = equal_month_cut_interest($parm);
                $interest = ($intre['repayment_money'] - $amount);
                break;
        
        }
        return $interest;
    }
    
    /**
     * 获取还款时间
     * @param unknown $repayment
     * @param unknown $duration
     * @return number
     */
    public static function getDeadline($repayment, $duration)
    {
        //到期还款时钟，暂定为当天的23:59:59
        $endTime = strtotime(date("Y-m-d 23:59:59"));
        if ($repayment == 1)
        {
            //天标
            $deadline_last = strtotime("+".($duration - 1)." day", $endTime);
        }
        else
        {
            //月标
            $deadline_last = strtotime("+{$duration} month", $endTime);
        }

        return $deadline_last;
    }
    
    public function getBorrowStatusList()
    {
        return ['0' => __('Borrowstatus 0'), '1' => __('Borrowstatus 1'), '2' => __('Borrowstatus 2'), '3' => __('Borrowstatus 3')];
    }
    
    public static function getProductTypeList()
    {
        return [
            '1' => __('Producttype 1'), 
            '2' => __('Producttype 2'), 
            '3' => __('Producttype 3'),
            '4' => __('Producttype 4'),
            '5' => __('Producttype 5'),
            '6' => __('Producttype 6'),
            '7' => __('Producttype 7'),
            '8' => __('Producttype 8'),
        ];
    }
    
    public static function getInvestInterestTypeList()
    {
        return [
            '1' => __('InvestInterestType 1'),
            '2' => __('InvestInterestType 2'),
            '3' => __('InvestInterestType 3'),
            '4' => __('InvestInterestType 4'),
            '5' => __('InvestInterestType 5'),
            '7' => __('InvestInterestType 7'),
        ];
    }
    
    public static function getBorrowInterestTypeList()
    {
        return [
            '1' => __('BorrowInterestType 1'),
            '2' => __('BorrowInterestType 2'),
            '3' => __('BorrowInterestType 3'),
            '4' => __('BorrowInterestType 4'),
            '5' => __('BorrowInterestType 5'),
            '6' => __('BorrowInterestType 6'),
            '7' => __('BorrowInterestType 7'),
        ];
    }
    
    public static function getBorrowUseList()
    {
        return [
            '1' => __('Borrowuse 1'),
            '2' => __('Borrowuse 2'),
            '3' => __('Borrowuse 3'),
            '4' => __('Borrowuse 4'),
            '5' => __('Borrowuse 5'),
            '6' => __('Borrowuse 6'),
            '7' => __('Borrowuse 7'),
            '8' => __('Borrowuse 8'),
            '100' => __('Borrowuse 100'),
        ];
    }
    
    public static function getGuaranteeCompanyList()
    {
        return [
            '0' => '无',
            '1' => '深圳市链金所互联网金融服务有限公司',
            '2' => '深圳市腾讯计算机系统有限公司',
            '3' => '北京百度在线网络技术有限公司'
        ];
    }
    
    /**
     * 手续费付款类型
     * @return array
     */
    public static function getFeeTypeList()
    {
        return [
            '0' =>  __('FeeType 0'),
            '1' =>  __('FeeType 1')
        ];
    }
    
    public function getBorrowStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['borrowStatus'];
        $list = $this->getBorrowStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public static function getPayChannelTypeList()
    {
        return ['1' => __('Paychanneltype 1'), '2' => __('Paychanneltype 2'), '3' => __('Paychanneltype 3')];
    }
    
    public function getPayChannelTypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['payChannelType'];
        $list = $this->getPayChannelTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getProductTypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['productType'];
        $list = $this->getProductTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getInvestInterestTypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['investInterestType'];
        $list = $this->getInvestInterestTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getFirstVerifyManAttr($value, $data)
    {
        $name = '';
        if ($data['firstVerfiyId']) 
        {
            $name = \think\Db::table('admin')->where('id', $data['firstVerfiyId'])->value('username');
        }
        return $name;
    }
}