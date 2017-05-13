<?php

/**
 * 充值模块
 * User: zhanggen
 * Date: 2017/4/15
 */
class RechargeModel extends BaseModel {
    //获取充值卡
    public static function getRechargeCard($start = 0, $pageSize = 10, $by="id",$order="desc", $where = array(), $isGetCount = false, $field = "*"){
        $mod = M("yaf_recharge_card");
        if ($isGetCount) {//计算总条数
            $count = $mod->where($where)->getRowCount();
            return $count;
        } else {
            $list = $mod->field($field)->where($where)->order($by." ".$order)->limit($start,$pageSize)->getAll();
            return $list;
        }
    }
    //获取充值记录
    public static function getRechargeRecord($start = 0, $pageSize = 10, $by="id",$order="desc", $where = array(), $isGetCount = false, $field = "*"){
        $mod = M("yaf_recharge_record");
        if ($isGetCount) {//计算总条数
            $count = $mod->where($where)->getRowCount();
            return $count;
        } else {
            $list = $mod->field($field)->where($where)->order($by." ".$order)->limit($start,$pageSize)->getAll();
            return $list;
        }
    }

    /*
  * 添加充值卡套餐
  */
    public static function addRechargeCard($post)
    {
        $data = array(
            'card_name' => trim($post['card_name']),
            'price' => isset($post['price'])?intval($post['price']):"0",
            'maidou' => isset($post['price'])?intval($post['maidou']):"0",
            'type' => isset($post['type'])?intval($post['type']):"0",
            'status' =>isset($post['status'])?intval($post['status']):"0",
            'start_time' =>isset($post['start_time'])?intval($post['start_time']):"0",
            'end_time' =>isset($post['end_time'])?intval($post['end_time']):"0",
            'ctime' => time(),
            'node'=> isset($post['node'])?trim($post['node']):"",
        );
        $serve_id = M("yaf_recharge_card")->insert($data);
        if ($serve_id) {
            return $serve_id;
        } else {
            return false;
        }
    }

    //删除充值卡套餐
    public static function delRechargeCard($id)
    {
        if (is_array($id)) {
            $ids = implode(",", $id);
            $where = "id in (" . $ids . ") ";
        } else {
            $where = "id = '" . $id . "' ";
        }
        return M("yaf_recharge_card")->where($where)->delete();
    }

    //修改充值卡套餐
    public static function  updateRechargeCard($id,$post)
    {
        $default_array = array( 'card_name', 'price', 'maidou','type','status','start_time','end_time','node');
        $data = array();
        foreach($post as $post_key=>$post_v){
            if(in_array($post_key,$default_array)){
                $data[$post_key] = $post_v;
            }
        }
        $data['update_time'] = time();
        $where = " id='".$id."'";
        return M("yaf_recharge_card")->where($where)->update($data);
    }
    //获取充值卡套餐
    public static function getRechargeCardById($brand_id,$field="*"){
        return M("yaf_recharge_card")->field($field)->where(array("id"=>$brand_id))->getOne();
    }


}
