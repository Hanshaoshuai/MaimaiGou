<?php

/**
 *  银行卡管理
 * User: LF
 * Date: 2017/3/27
 */
class BankCardController extends ApiController {

    private $model;
    private $uid;

    public function init() {
        parent::init();
        $this->model = BankCardModel::getInstance();
        $this->verifyLogin();
        $this->uid = $this->user_id;
    }

    public function cardListAction() {
        
        $params=$this->getPost();
        $params['uid'] = $this->uid;
        $rs = $this->model->cardList($params);
        $this->apiReturn($rs);
    }

    //获取实名认证信息
    public function goAddCardAction(){
        $this->verifyLogin();
        $user_profile = UserModel::getUserProfile($this->user_id,"realname,IDnumber");
        if($user_profile){
            $this->apiReturn($user_profile,"1000");
        }else{
            $this->apiReturn((object)array(),"1004","获取失败！");
        }
    }

    public function cardInsertAction() {
        $params = $this->getPost();
        $params['uid'] = $this->uid;
        $paramsInfo = array(
            "bank_name" => "",
            "bank_provinces" => "",
            "bank_urban" => "",
            "branch_info" => "",
            "card_no" => ""
        );
        $this->checkParams($params, $paramsInfo);
        $rs = $this->model->cardInsert($params);
        $this->apiReturn($rs['data'], $rs['code'],$rs['msg']);
    }

    public function cardInfoAction() {
        $params = $this->getPost();
        $params['uid'] = $this->uid;
        $paramsInfo = array("id" => "");
        $this->checkParams($params, $paramsInfo);
        $rs = $this->model->cardInfo($params);
        $this->apiReturn($rs);
    }

    public function cardUpdateAction() {
        $params = $this->getPost();
        $params['uid'] = $this->uid;
        $paramsInfo = array(
            "id" => "",
            "bank_name" => "",
            "bank_provinces" => "",
            "bank_urban" => "",
            "branch_info" => "",
            "card_no" => ""
        );
        $this->checkParams($params, $paramsInfo);
        $rs = $this->model->cardUpdate($params);
        if (!$rs > 0) {
            $code = "1004";
        } else {
            $code = "1000";
        }
        $this->apiReturn(array(), $code);
    }

    public function cardDelAction() {
        $params = $this->getPost();
        $paramsInfo = array("id" => "");
        $params['uid'] = $this->uid;
        $this->checkParams($params, $paramsInfo);
        $rs = $this->model->cardDel($params);
        if (!$rs > 0) {
            $code = "1004";
        } else {
            $code = "1000";
        }
        $this->apiReturn(array(), $code);
    }

}
