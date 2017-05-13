<?php

/**
 *  银行卡管理
 * User: LF
 * Date: 2017/3/27
 */
class ThirdPartyController extends ApiController {

    private $model;

    public function init() {
        parent::init();
        $this->model = ThirdPartyModel::getInstance();
    }

    function checkCardNoAction() {
        $params = $this->getPost();
        $paramsInfo=array("cardNo"=>"","userName"=>"");
        $this->checkParams($params,$paramsInfo);       
        $rs = json_decode($this->model->checkCardNo($params),true);
        if($rs["code"]==1 && $this->user_id){
            UserModel::updataUser(array("bindidcard"=>1),$this->user_id);
            $file = array("IDnumber"=>$params['cardNo'],"realname"=>$params['userName']);
            UserModel::updataUserProfile($file,$this->user_id);
        }
        $data=array();
        $code="1001";
        if($rs["isok"]==0){
            $code="1005";//查询失败 原因
        }else{
            if($rs["code"]==1){
                $code="1000";
            }elseif($rs["code"]==2){
                $code="1006";//不一致
            }else{
                $code="1007";//无此身份证
            }
        }
        $this->apiReturn($data,$code);
    }

}
