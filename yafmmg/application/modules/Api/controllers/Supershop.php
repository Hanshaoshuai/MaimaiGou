<?php

/**
 *
 * User: zhouyouth
 * Date: 2017/4/2
 * Time: 13:44
 */
class SuperShopController extends ApiController {

    public function init() {
        parent::init();
    }

    public function getAllShopAction() {
        $data['p'] = $this->getPost("p");
        $data['p'] = isset($data['p']) ? $data['p'] : $this->getPost("page");
        $data['usrX'] = $this->getPost("usrX");
        $data['usrY'] = $this->getPost("usrY");
        if (!isset($data['usrX']) || $data['usrX'] == 0) {
            $data['usrX'] =  "120.210471";
            $data['usrY'] =  "30.20561";
        }
        
        $shop = new SuperShopModel();
        $res = $shop->fetchAllShop($data);
        if (!$res && !is_array($res)) {
            $this->apiReturn($res, 1004, "fails!");
        }
        $this->apiReturn($res, 1000, "success!");
    }

    public function getLocalShopAction() {
        $data['p'] = $this->getPost("p");
        $data['p'] = isset($data['p']) ? $data['p'] : $this->getPost("page");
        $data['usrX'] = $this->getPost("usrX"); //经度
        $data['usrY'] = $this->getPost("usrY"); //纬度
        if (!isset($data['usrX']) || $data['usrX'] == 0) {
            $data['usrX'] =  "120.210471";
            $data['usrY'] =  "30.20561";
        }
        $distance = $this->calcScope($data['usrX'], $data['usrY'], 50000); //查找50公里范围内的商家
        $shop = new SuperShopModel();
        $res = $shop->fetchLocalAll($distance, $data);
        if (!$res && !is_array($res)) {
            $this->apiReturn($res, 1004, "fails!");
        }
        $this->apiReturn($res, 1000, "success!");
    }

    /**
     * 根据经纬度和半径计算出范围
     * @param string $lat 经度
     * @param String $lng 纬度
     * @param float $radius 半径
     * @return Array 范围数组
     */
    public function calcScope($lat, $lng, $radius) {
        $degree = (24901 * 1609) / 360.0;
        $dpmLat = 1 / $degree;

        $radiusLat = $dpmLat * $radius;
        $minLat = $lat - $radiusLat;       // 最小经度
        $maxLat = $lat + $radiusLat;       // 最大经度

        $mpdLng = $degree * cos($lat * (pi() / 180));
        $dpmLng = 1 / $mpdLng;
        $radiusLng = $dpmLng * $radius;
        $minLng = $lng - $radiusLng;      // 最小纬度
        $maxLng = $lng + $radiusLng;      // 最大纬度
        if ($minLat > $maxLat) {
            $tmp = $minLat;
            $minLng = $maxLat;
            $maxLat = $tmp;
        }

        if ($minLng > $maxLng) {
            $tmpp = $minLng;
            $minLng = $maxLng;
            $maxLng = $tmpp;
        }
        /** 返回范围数组 */
        $scope = array(
            'minLat' => $minLat,
            'maxLat' => $maxLat,
            'minLng' => $minLng,
            'maxLng' => $maxLng
        );
        return $scope;
    }

    public function searchShopAction() {
        $data['p'] = $this->getPost("p");
        $data['p'] = isset($data['p']) ? $data['p'] : $this->getPost("page");
        $user['usrX'] = $this->getPost("usrX"); //经度
        $user['usrY'] = $this->getPost("usrY"); //纬度
        if (!isset($data['usrX']) || $data['usrX'] == 0) {
            $data['usrX'] =  "120.210471";
            $data['usrY'] =  "30.20561";
        }
        $data['name'] = $this->getPost('name');
        $shop = new SuperShopModel();
        $res = $shop->searchShop($data, $user);
        if (!$res && !is_array($res)) {
            $this->apiReturn($res, 1004, "fails!");
        }
        $this->apiReturn($res, 1000, "success!");
    }

    public function localBuyAction() {
        $this->verifyLogin();
        $data['user_id'] = $this->user_id;
        //$data['user_id']=$this->getPost('userid');
        $shuju['user_id_shop'] = $this->getPost('user_id_shop'); //店主id
        $data['maidou'] = $this->getPost('maidou');
        $user_profile = UserModel::getUserProfile($data['user_id']);
        $spend_maidou = $user_profile['spend_maidou'] + $user_profile['reflect_maidou'] + $user_profile['supermarket_maidou'];
        $data['ctime'] = time();
        $data['notes'] = "超市消费";
        if ($data['maidou'] > $spend_maidou) {
            $this->apiReturn($spend_maidou, 1005, "麦豆不足!");
        }
        $model = new SuperShopModel();
        $spend_maidou = $model->payMaidou($data, $user_profile, $shuju);
        if (!$spend_maidou) {
            $this->apiReturn($spend_maidou, 1005, "支付失败!");
        } else {
            if (@$spend_maidou['code'] == 1003) {
                $this->apiReturn('', 1005, $spend_maidou['msg']);
            } elseif (@$spend_maidou['code'] == 1006) {
                $this->apiReturn('', $spend_maidou['code'], $spend_maidou['msg']);
            }
            $uid = $shuju['user_id_shop'];
            $pushText = "交易成功,您麦豆到账" . $data['maidou'];
            $res = $this->JpushApi($uid, $pushText);
            if (!$res) {
                $this->apiReturn('', 1004, "交易成功,推送失败!");
            }
            //插入推送记录表
            $jiguangModel = new JiGuangLogModel();
            $jguang['title'] = $pushText;
            $jguang['content'] = $pushText;
            $jguang['msg_type'] = 3;
            $jguang['sender'] = 0;
            $jguang['reciver'] = $shuju['user_id_shop'];
            $jguang['push_type'] = '3';
            $jguang['msg_type'] = 3;
            $jguang['create_time'] = time();
            $res = $jiguangModel->insert($jguang);
            if (!$res) {
                $this->apiReturn($data['maidou'], 1000, "success but write log fails!");
            }
            $this->apiReturn($data['maidou'], 1000, "success!");
        }
    }

    public function zongmaidouAction() {
        $data['user_id'] = $this->getPost('uid');
        $user_profile = UserModel::getUserProfile($data['user_id']);
        $spend_maidou[]['zongmaidou'] = $user_profile['spend_maidou'] + $user_profile['reflect_maidou'] + $user_profile['supermarket_maidou'];     //可消费麦豆=消费麦豆+可转出麦豆数+超市消费麦豆
        if (!$spend_maidou) {
            $this->apiReturn($spend_maidou, 1004, "fails!");
        } else {
            $this->apiReturn($spend_maidou, 1000, "success!");
        }
    }

    public function JpushApi($uid, $pushText) {
        $pushObj = new JpushModel();
        //组装需要的参数
        //$receive = 'all'; //全部
        //$receive = array('tag'=>array('id','id','9527'));      //标签
        $receive = array('alias' => array('mmg_' . $uid)); //别名
        $content = $pushText;
        $m_type = 'http';
        $m_txt = '';
        $m_time = '600';        //离线保留时间
        $result = $pushObj->push($receive, $content, $m_type, $m_txt, $m_time, $model = 0);
        if ($result) {
            $res_arr = json_decode($result, true);
            if (isset($res_arr['error'])) {
                return false;
            } else {
                //处理成功的推送......
                //echo '推送成功.....';
                return true;
            }
        } else {      //接口调用失败或无响应
            //echo '接口调用失败或无响应';
            return false;
        }
    }

}
