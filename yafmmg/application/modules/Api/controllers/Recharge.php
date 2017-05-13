<?php

/**
 *  充值卡
 * User: LF
 * Date: 2017/3/27
 */
class RechargeController extends ApiController {

    public function init() {
        parent::init();
    }
    //去充值
    public function goRechargeAction()
    {
        $this->verifyLogin();
        $params = array();
        $params['page'] = $this->get("page"); //预留
        $params['pageSize'] = $this->get("pageSize"); //预留
        $page = !empty($params["page"]) ? $params["page"] : 1;
        $pageSize = !empty($params["pageSize"]) ? $params["pageSize"] : 15;

        $where = "status = 1 AND (start_time < UNIX_TIMESTAMP() AND (end_time=0 OR end_time>UNIX_TIMESTAMP())) OR (start_time=0 AND (end_time=0 OR end_time>UNIX_TIMESTAMP())) "; //获取所有记录

        $start = ($page - 1) * $pageSize;
        //$count = RechargeModel::getRechargeCard($start, $pageSize, "type", "desc",  $where,true);
        $field = "id,price,maidou,type";
        $recharge_card = RechargeModel::getRechargeCard($start, $pageSize, "type", "desc",  $where,false,$field);
        $recharge_record_where = "user_id='".$this->user_id."' AND status=1";
        $recharge_record = RechargeModel::getRechargeRecord("", "", "", "", $recharge_record_where,true);
        if($recharge_record){
            $is_first_recharge = 0;
        }else{
            $is_first_recharge = 1;
        }
        $user_field = "spend_maidou,reflect_maidou,performance_maidou,supermarket_maidou,agents_maidou";
        $user_profile = UserModel::getUserProfile($this->user_id,$user_field);
        $amount_remaining = $user_profile['spend_maidou']+$user_profile['reflect_maidou']+$user_profile['performance_maidou']+$user_profile['supermarket_maidou']+$user_profile['agents_maidou'];
        if ($recharge_card) {
            $code = 1000;
            $data = array(
                //"count" => $count,
                //"thisPage" => $page,
                //"pageSize" => $pageSize,
               // "countPage" => ceil($count / $pageSize),
                "is_first_recharge"=>$is_first_recharge,
                "amount_remaining" => $amount_remaining,
                "recharge_card" => $recharge_card,
            );
        } else {
            $code = 1004;
            $data = array();
        }
        $this->apiReturn($data, $code);

    }
}
