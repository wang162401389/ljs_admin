<?php
namespace fast;

use think\Config;
use fast\Http;

/**
* PHP 连接 JAVA 接口类
* @author wangchaohong
*/

class Javaapi
{
    public $url;
    
    public function __construct() 
    {
        $this->url = Config::get('site.javaapi');
    }
    
    public function getInvestInterest($data = [])
    {
        return Http::post($this->url."/any/borrow/findListInterest.do", $data);
    }
    
    public function getRepaymentList($data = [])
    {
        return Http::post($this->url.'/any/borrow/findList.do', $data);
    }
    
    /**
     * 标的录入（新浪）
     * @param array $data
     * @return string|mixed
     */
    public function releaseBorrowToSina($data = [])
    {
        return Http::post($this->url.'/any/sina/create_bid_info.do', $data);
    }
    
    /**
     * 标的录入（华兴）
     * @param array $data
     * @return string|mixed
     */
    public function releaseBorrowToHuaXing($data = [])
    {
        return Http::post($this->url.'/any/huaxing/setBiddingInf.do', $data);
    }
    
    /**
     * 新浪代收完成
     * @param array $data
     * @return string|mixed
     */
    public function finishPreAuthTrade($data = [])
    {
        return Http::post($this->url.'/any/contract/gnerate.do', $data);
    }
    
    /**
     * 新浪标的复审拒绝
     * @param array $data
     * @return string|mixed
     */
    public function sinaReviewRefuse($data = [])
    {
        return Http::post($this->url.'/any/sina/reviewFailure.do', $data);
    }
    
    /**
     * 华兴标的复审拒绝
     * @param array $data
     * @return string|mixed
     */
    public function huaXingReviewRefuse($data = [])
    {
        return Http::post($this->url.'/any/huaxing/reviewFailure.do', $data);
    }
    
    /**
     * 华兴标的复审通过
     * @param array $data
     * @return string|mixed
     */
    public function huaXingReviewPass($data = [])
    {
        return Http::post($this->url.'/any/huaxing/reviewSuccess.do', $data);
    }
    
    /**
     * 账户详情接口
     * @param array $data
     * @return string|mixed
     */
    public function accountInfo($data = [])
    {
        return Http::post($this->url.'/any/user/account.do', $data);
    }
    
    /**
     * 发送短信接口
     * @param array $data
     * @return string|mixed
     */
    public function sendSms($data = [])
    {
        return Http::post($this->url.'/any/php/sendSms.do', $data);
    }
}