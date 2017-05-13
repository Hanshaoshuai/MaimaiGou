<?php

/**
 *
 * User: LF
 * Date: 2017/3/27
 */
class ThirdPartyModel {

    private static $_instance;
    private $db;

    private function __construct() {
        $this->init();
    }

    private function init() {
        //$this->db = M("yaf_user_bankcard", "yaf_mmg");
    }

    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    function checkCardNo($params) {
        $cardNo=trim($params['cardNo']);
        $userName=trim($params['userName']);
        $host = "http://aliyun.id98.cn";
        $path = "/idcard";
        $method = "GET";
        $appcode = "e7edb75ec5634a498684494e1901d0a0";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "cardno=" . $cardNo . "&name=" . urlencode($userName) . "";
        $bodys = "";
        $url = $host . $path . "?" . $querys;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $rs = curl_exec($curl);
        return $rs;
    }

}
