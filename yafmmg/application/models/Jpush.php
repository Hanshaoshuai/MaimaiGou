<?php

class JpushModel extends BaseModel{

    private $app_key = 'c3c1c4c7246ef2eba42f936a';       //待发送的应用程序(appKey)，只能填一个。
    private $master_secret = 'd6abf40558b85b3f674e9842';      //主密码
    private $url = "https://api.jpush.cn/v3/push";    //推送的地址

    public function __construct($app_key=null, $master_secret=null,$url=null) {
        if ($app_key) $this->app_key = $app_key;
        if ($master_secret) $this->master_secret = $master_secret;
        if ($url) $this->url = $url;
    }
    public function push($receiver='all',$content='',$m_type='',$m_txt='',$m_time='86400'){
        $base64=base64_encode("$this->app_key:$this->master_secret");
        $header=array("Authorization:Basic $base64","Content-Type:application/json");
        $data = array();
        $data['platform'] = 'all';       //目标用户终端手机的平台类型android,ios,winphone
        $data['audience'] = $receiver;    //目标用户

        $data['notification'] = array(
            //统一的模式--标准模式
            "alert"=>$content,
            //安卓自定义
            "android"=>array(
                "alert"=>$content,
                "title"=>"",
                "builder_id"=>1,
                "extras"=>array("type"=>$m_type, "txt"=>$m_txt)
            ),
            //ios的自定义
            "ios"=>array(
                "alert"=>$content,
                "badge"=>"1",
                "sound"=>"default",
                "extras"=>array("type"=>$m_type, "txt"=>$m_txt)
            ),
        );
        //苹果自定义---为了弹出值方便调测
        $data['message'] = array(
            "msg_content"=>$content,
            "extras"=>array("type"=>$m_type, "txt"=>$m_txt)
        );
        //附加选项
        $data['options'] = array(
            "sendno"=>time(),
            "time_to_live"=>$m_time,   //保存离线时间的秒数默认为一天
            "apns_production"=>0,     //指定 APNS 通知发送环境：0开发环境，1生产环境。
        );
        $param = json_encode($data);
        $res = $this->push_curl($param,$header);
        if($res){     //得到返回值--成功已否后面判断
            return $res;
        }else{       //未得到返回值--返回失败
            return false;
        }
    }
    public function push_curl($param="",$header="") {
        if (empty($param)) { return false; }
        $postUrl = $this->url;
        $curlPost = $param;
        $ch = curl_init();                            //初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);                //抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);               //设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);         //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);                //post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);        // 增加 HTTP Header（头）里的字段
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);      // 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);                           //运行curl
        curl_close($ch);
        return $data;
    }

}
