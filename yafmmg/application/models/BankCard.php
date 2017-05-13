<?php

/**
 *
 * User: LF
 * Date: 2017/3/27
 */
class BankCardModel {

    private static $_instance;
    private $db;

    private function __construct() {
        $this->init();
    }

    private function init() {
        $this->db = M("yaf_user_bankcard", "yaf_mmg");
    }

    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function cardList($params = array()) {
        $where['uid'] = $params['uid'];
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 10;
        $page= isset($params['page'])?$params['page']:1;
        $rs = $this->db->where($where)->order("id desc")->limit(($page-1)*$params['limit'],$params['limit'])->getAll();
        return $rs;
    }

    public function cardInfo($params = array()) {
        $where['id'] = $params['id'];
        $where['uid'] = $params['uid'];
        $rs = $this->db->field("id,card_no,bank_name,branch_info,bank_provinces,bank_urban")->where($where)->getOne();
        return $rs;
    }

    public function cardInsert($params) {
        $data['uid'] = $params['uid'];
        $data["bank_name"] = $params['bank_name'];
        if($data["bank_name"]==""){
            return array('data'=>array(),"code"=>"1001","msg"=>"银行卡名字不能为空");
        }
        $data["bank_provinces"] = $params['bank_provinces'];
        if($data["bank_provinces"]==""){
            return array('data'=>array(),"code"=>"1001","msg"=>"开户行省份不能为空");
        }        
        $data["bank_urban"] = $params['bank_urban'];
        if($data["bank_urban"]==""){
            return array('data'=>array(),"code"=>"1001","msg"=>"开户行市不能为空");
        }        
        $data["branch_info"] = $params['branch_info'];
        if($data["branch_info"]==""){
            return array('data'=>array(),"code"=>"1001","msg"=>"支行信息不能为空");
        }        
        $data["card_no"] = $params['card_no'];
        if($data["card_no"]==""){
            return array('data'=>array(),"code"=>"1001","msg"=>"银行卡号不能为空");
        }        
        $data["ctime"] = time();
        $sql="select * from yaf_user_bankcard where card_no='".$data["card_no"]."'";
        $rk=$this->db->query($sql);
        if(!empty($rk)){
            return array('data'=>array(),"code"=>"1001","msg"=>"银行卡号已存在");
        }
        $rs = $this->db->insert($data);
       $code="1001";
       $msg="添加失败";
        if($rs>0){
            $code="1000";
            $msg="success";            
        }
        return array('data'=>array(),"code"=>$code,"msg"=>$msg);        
    }

    public function cardUpdate($params) {
        $where['id'] = $params['id'];
        $where['uid'] = $params['uid'];
        $data["bank_name"] = $params['bank_name'];
        $data["bank_provinces"] = $params['bank_provinces'];
        $data["bank_urban"] = $params['bank_urban'];
        $data["branch_info"] = $params['branch_info'];
        $data["card_no"] = $params['card_no'];
        $data["update_time"] = time();
        $rs = $this->db->where($where)->update($data);
        return $rs;
    }

    public function cardDel($params) {
        $where['id'] = $params['id'];
        $where['uid'] = $params['uid'];
        $rs = $this->db->where($where)->delete();
        return $rs;
    }
    public function selectBrandCard($data){
         $res= $this->db->field('uid,card_no,bank_name')->where(array('uid'=>$data['userid']))->getAll();
        if(!$res){
            return false;
        }
        return $res;
    }
   public static function addUserBrandCard($data){
      return M("yaf_user_bankcard", "yaf_mmg")->insert($data);
   }
    public function updateCardstatus($where,$data){
        return  M("yaf_user_bankcard", "yaf_mmg")->where($where)->update($data);
    }

    public static function cardInfoBycard($where,$field="*"){
        return  M("yaf_user_bankcard", "yaf_mmg")->field($field)->where($where)->getOne();
    }

}
