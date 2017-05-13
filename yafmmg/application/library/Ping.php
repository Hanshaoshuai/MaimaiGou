<?php

class Ping {

    // api_key 获取方式：登录 [Dashboard](https://dashboard.pingxx.com)->点击管理平台右上角公司名称->企业设置->开发设置->Live/Test Secret Key
    const APP_KEY = 'sk_live_eH0uTGmfv1qDubPWz9iDqv9K';
// app_id 获取方式：登录 [Dashboard](https://dashboard.pingxx.com)->应用卡片下方
    const APP_ID = 'app_vvbDiHvPGaH4vz1a';

    public function __construct() {
        $this->init();
        require(dirname(__FILE__) . '/ping/lib/Error/ParamsError.php');
    }

    private function init() {
        require "ping/init.php";
        \Pingpp\Pingpp::setApiKey(self::APP_KEY);        
        \Pingpp\Pingpp::setPrivateKeyPath(__DIR__ . '/ping/example/your_rsa_private_key.pem');
    }

    /**
     * 支付接口
     * @return type 
     */
    public function create($input_data) {
        $data="";
        $code="1000"; 
        $msg="";
        try {
            if (empty($input_data['channel']) || empty($input_data['amount']) || empty($input_data['orderno'])) {                
                throw new \Pingpp\Error\ParamsError("channel or amount or orderno  empty");
            }
            
            $channel = strtolower($input_data['channel']);           
            $extra = $this->getExtra($channel);
            if(!$extra){
                throw new \Pingpp\Error\ParamsError("channel 参数有误请查看帮助手册");                
            }
            $data = \Pingpp\Charge::create(
                            array(
                                //请求参数字段规则，请参考 API 文档：https://www.pingxx.com/api#api-c-new
                                'subject' => 'Your Subject',
                                'body' => 'Your Body',
                                'amount' => $input_data['amount']*100, //订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
                                'order_no' => $input_data['orderno'], // 推荐使用 8-20 位，要求数字或字母，不允许其他字符
                                'currency' => 'cny',
                              //  'extra' => $extra,
                                'channel' => $channel, // 支付使用的第三方支付渠道取值，请参考：https://www.pingxx.com/api#api-c-new
                                'client_ip' => $_SERVER['REMOTE_ADDR'], // 发起支付请求客户端的 IP 地址，格式为 IPV4，如: 127.0.0.1
                                'app' => array('id' => self::APP_ID)
                            )
            );

        } catch (\Pingpp\Error\Base $e) {
            // 捕获报错信息
            if ($e->getHttpStatus() != null) {
                header('Status: ' . $e->getHttpStatus());
                $msg = $e->getHttpBody();
            } else {
                $msg = $e->getMessage();
            }
            $code="1001";
        }
		return array("code" => $code, "data" => $data, "msg" => $msg);
    }

    private function getExtra($channel) {        
        switch ($channel) {
            case 'alipay_wap':
                $extra = array(
                    // success_url 和 cancel_url 在本地测试不要写 localhost ，请写 127.0.0.1。URL 后面不要加自定义参数
                    'success_url' => '127.0.0.1',
                    'cancel_url' => '127.0.0.1'
                );
                break;
            case 'bfb_wap':
                $extra = array(
                    'result_url' => 'http://example.com/result', // 百度钱包同步回调地址
                    'bfb_login' => true// 是否需要登录百度钱包来进行支付
                );
                break;
            case 'upacp_wap':
                $extra = array(
                    'result_url' => 'http://example.com/result'// 银联同步回调地址
                );
                break;
            case 'wx_pub':
                $extra = array(
                    'open_id' => 'openidxxxxxxxxxxxx'// 用户在商户微信公众号下的唯一标识，获取方式可参考 pingpp-php/lib/WxpubOAuth.php
                );
                break;
            case 'wx_pub_qr':
                $extra = array(
                    'product_id' => 'Productid'// 为二维码中包含的商品 ID，1-32 位字符串，商户可自定义
                );
                break;
            case 'yeepay_wap':
                $extra = array(
                    'product_category' => '1', // 商品类别码参考链接 ：https://www.pingxx.com/api#api-appendix-2
                    'identity_id' => 'your identity_id', // 商户生成的用户账号唯一标识，最长 50 位字符串
                    'identity_type' => 1, // 用户标识类型参考链接：https://www.pingxx.com/api#yeepay_identity_type
                    'terminal_type' => 1, // 终端类型，对应取值 0:IMEI, 1:MAC, 2:UUID, 3:other
                    'terminal_id' => 'your terminal_id', // 终端 ID
                    'user_ua' => 'your user_ua', // 用户使用的移动终端的 UserAgent 信息
                    'result_url' => 'http://example.com/result'// 前台通知地址
                );
                break;
            case 'jdpay_wap':
                $extra = array(
                    'success_url' => 'http://example.com/success', // 支付成功页面跳转路径
                    'fail_url' => 'http://example.com/fail', // 支付失败页面跳转路径
                    /**
                     * token 为用户交易令牌，用于识别用户信息，支付成功后会调用 success_url 返回给商户。
                     * 商户可以记录这个 token 值，当用户再次支付的时候传入该 token，用户无需再次输入银行卡信息
                     */
                    'token' => 'dsafadsfasdfadsjuyhfnhujkijunhaf' // 选填
                );
                break;
            case 'upacp':
                $extra = array(
                    'pay_type' => '0004', // 支付成功页面跳转路径
                    'acc_no' => '6228480063208212918', // 支付失败页面跳转路径
                    'pay_card_type' => '00' // 选填
                );
                break;      
            case 'alipay':
                $extra = array(
                    'pay_type' => '0004', // 支付成功页面跳转路径
                    'acc_no' => '6228480063208212918', // 支付失败页面跳转路径
                    'pay_card_type' => '00' // 选填
                );
                break;               
            default :
                $extra=false;
        }
        return $extra;
    }

    public function checkOrder($charge_id) {
        //$charge_id = 'ch_L8qn10mLmr1GS8e5OODmHaL4';
        try {
            $charge = \Pingpp\Charge::retrieve($charge_id);
            return $charge;
        } catch (\Pingpp\Error\Base $e) {
            if ($e->getHttpStatus() != null) {
                header('Status: ' . $e->getHttpStatus());
                echo $e->getHttpBody();exit;
            } else {
                echo $e->getMessage();exit;
            }
        }
    }

    public function getH5PingPath() {
        return __DIR__ . '/pingH5/dist/pingpp.js';
    }

}
