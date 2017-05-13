<?php

/**
 *  支付接口
 * User: LF
 * Date: 2017/3/27
 */
class PaymentController extends ApiController {

    private $model;

    public function init() {
        parent::init();
        $this->model = PaymentModel::getInstance();
    }

    public function chargeAction() {
        $params = $this->getPost();
        //$paramsInfo=array("channel"=>"","amount"=>"","orderno"=>"");
        //$this->checkParams($params,$paramsInfo);
        $rs = $this->model->create($params);
        $data = $rs['data'];
        $code = $rs['code'];
        $msg = $rs['msg'];
        $this->apiReturn($data, $code, $msg);
    }

    /**
     * 麦豆支付
     * order_id 订单编号
     * maidou 麦豆数量
     */
    public function PaymentAction() {
        $params = $this->getPost();
        if (empty($this->user_id)) {
            $this->apiReturn(array(), 1001, "请先登入");
        }
        $params['user_id'] = $this->user_id;
        //$params['order_id'] = 2;
        // $params['maidou'] = 10;
        if ($params['payment_method'] == "maidou") {
            $paramsName = array("user_id" => "", "order_id" => "", "maidou" => "");
            $this->checkParams($params, $paramsName);
            $rs = $this->model->maiDouPayment($params);
        }

        $rs = json_decode($rs, true);
        $this->apiReturn($rs['data'], $rs['code'], $rs['msg']);
        exit;
    }

    public function PaymentPingAction() {
        $params = $this->getPost();
        //$params['channel']="upacp";
                if (empty($this->user_id)) {
            $this->apiReturn(array(), 1001, "请先登入");
        }
        $params['user_id'] = $this->user_id;
        if (!isset($params['channel']) && ($params['channel'] != "upacp" || $params['channel'] != "alipay")) {
            echo json_encode(array("code" => "1001", "data" => array(), "msg" => "channel 参数错误"),JSON_FORCE_OBJECT);
            exit;
        }
        // $params['channel'] = "upacp";
       // $params['amount'] = 1;
        $params['orderno'] = date("YmdHis") . rand(1, 9999);

        $pay = new PaymentModel();
        $rs = $pay->PaymentPing($params);
        echo json_encode($rs,JSON_FORCE_OBJECT);
        exit;
    }



    public function checkPaymentAction() {
        $id=$this->getPost("id");        
        $pay = new PaymentModel();
        $rs = $pay->checkOrder($id);  
        echo json_encode($rs,JSON_FORCE_OBJECT);exit; 
    }
    public function checkPaymentOrderAction() {
        $id=$this->getPost("id");        
        $pay = new PaymentModel();
        $rs = $pay->checkPaymentOrder($id);  
        echo json_encode($rs,JSON_FORCE_OBJECT);exit; 
    }    

}
