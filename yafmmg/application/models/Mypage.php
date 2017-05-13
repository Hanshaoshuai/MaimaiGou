<?php
class MypageModel extends BaseModel{

    //根据用户id,及票劵状态查找票劵
    public function getCouponByStatus($uid,$status){
       return M("yaf_user_coupon")->where(array("user_id"=>$uid,"status"=>$status))->getAll();
    }
    //根据产品id查找产品
    public function getProductById($id){
       return M("yaf_shop_product")->where(array("id"=>$id))->getOne();
    }
    //根据用户id 查找用户密码    
    public function getPwd($uid){
        return M("yaf_user_user")->field("password")->where(array("id"=>$uid))->getOne();
    }
    //根据用户id 修改用户密码
    public function savePwd($uid,$pwd){
        return M("yaf_user_user")->where(array("id"=>$uid))->update(array("password"=>$pwd));
    }
    //我的收藏 根据商品id 查找商品
    public function myCollect($params) {
        $ids=$params['product_ids'];
        $limit1=($params['page']-1)*$params['pageSize'];
        $sql="select a.id,a.name,b.name catid,a.img,a.sell_price from yaf_shop_product a left join yaf_shop_category b on
             a.catid=b.id where a.id in (" . $ids . ") limit ".$limit1.",".$params['pageSize'];
        return M()->query($sql,array());
    }
    //根据用户ID 查找用户信息(移动到User)
    public function getUserInfo($params){
        $uid=$params['uid'];
        $user['info']= M("yaf_user_user")->field("username,ctime,provinces,urban,county,type")->where(array("id"=>$uid))->getAll();
        $user['logo']= M('yaf_user_profile')->field("logo")->where(array("uid"=>$uid))->getOne();
        $user['Payment_num']=M("yaf_shop_order")->where(array("user_id"=>$uid,"payment_status"=>0))->getRowCount();
        $user['Shipping_num']=M("yaf_shop_order")->where(array("user_id"=>$uid,"Shipping_status"=>0))->getRowCount();
        $user['commet_num']=M("yaf_shop_comment")->where(array("user_id"=>$uid,"status"=>0))->getRowCount();
        return $user;
    }
    //根据用户ID 查找用户所有订单详细信息(移动到Order)
    public function getOrderAll($params){
        $uid=$params['uid'];
        $limit1=($params['page']-1)*$params['pageSize'];
        $sql="select * from yaf_shop_order a left join  yaf_shop_order_product b
        on a.id=b.order_id where a.user_id='" . $uid . "' limit ".$limit1.",".$params['pageSize'];
        return M()->query($sql,array());
    }
    //根据用户id及 付款状态 查找订单(移动到Order)
    public function getOrderPayment($params){
        $uid=$params['uid'];
        $limit1=($params['page']-1)*$params['pageSize'];
        $sql="select * from yaf_shop_order a left join yaf_shop_order_product b
        on a.id=b.order_id where a.payment_status=0 and a.user_id='" . $uid . "' limit ".$limit1.",".$params['pageSize'];
        return M()->query($sql,array());
    }
    //根据用户id及 配送状态 查找待发货/待收货订单(移动到Order)
    public function getOrderShipping($params,$status){
        $uid=$params['uid'];
        $limit1=($params['page']-1)*$params['pageSize'];
        $sql="select * from yaf_shop_order a left join yaf_shop_order_product b
        on a.id=b.order_id where a.shipping_status='" . $status . "' and a.user_id='" . $uid . "' limit ".$limit1.",".$params['pageSize'];
        return M()->query($sql,array());
    }
    //根据用户id 及评论状态查找未评论订单(移动到Order)
    public function getOrderCommet($params){
         $uid=$params['uid'];
         $limit1=($params['page']-1)*$params['pageSize'];
         $sql="select *from yaf_shop_order a left join yaf_shop_comment b
         on a.id=b.orders_id
         left join yaf_shop_order_product c
         on a.id=c.order_id
         where b.status=0 and a.user_id='" . $uid . "' limit ".$limit1.",".$params['pageSize'];
         return M()->query($sql,array());
     } 
    //根据用户ID 查找用户所有订单详细信息(移动到Order)
    public function getSearchOrder($params){
         $uid=$params['uid'];
         $limit1=($params['page']-1)*$params['pageSize'];
         if(!empty($params['keyWords'])){
             $sql="select * from yaf_shop_order a left join  yaf_shop_order_product b
             on a.id=b.order_id where a.user_id='" . $uid . "' and b.name like '%".$params['keyWords']."%' limit ".$limit1.",".$params['pageSize'];
         }
         return M()->query($sql,array());
     }
     
}