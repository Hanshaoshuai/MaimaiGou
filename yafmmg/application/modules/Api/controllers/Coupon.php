<?php

/**
 * Created by PhpStorm.
 * User: zhanggen
 * Date: 2017/4/3
 * Time: 15:18
 */
class CouponController extends ApiController
{
    //我的票劵
    public function getCouponAction(){
        //setSession('userID', 70);
        $this->verifyLogin();
        $status=$this->get("status")?$this->get("status"):$this->getPost("status");
        if($status==1 || $status==2){
            $mod=new CouponModel();
            $page = $this->get("page");
            $pageSize = $this->get("pageSize");
            $page = !empty($page) ?$page : 1;
            $pageSize = !empty($pageSize) ? $pageSize : 10;
            $where = array("user_id" => $this->user_id,"status"=>$status);
            $start = ($page - 1) * $pageSize;
            $count = $mod->getCoupon($start, $pageSize, $where, true);
            $coupon_list = $mod->getCoupon($start, $pageSize,  $where);

            $Coupon = array();
            if($coupon_list){
                foreach ($coupon_list as $k => $v){
                    if(is_string($v['end_time'])){
                        $v['end_time'] = strtotime($v['end_time']);
                    }
                    $v['end_time'] =
                    $Coupon_v = array();
                    $p=ProductModel::getProductById($v['product_id'],"name");
                    $Coupon_v['coupon_id']=$v['id'];
                    $Coupon_v['product_id']=$v['product_id'];
                    $Coupon_v['product_name']=$p['name'];
                    $Coupon_v['price']=$v['price'];
                    $Coupon_v['end_time']=date("Y.m.d",$v['end_time']);
                    $Coupon_v['coupon_type']=$v['coupon_type'];
                    $Coupon[] = $Coupon_v;
                }
            }

            if($Coupon){
                $data = array(
                    "count" => $count,
                    "thisPage" => $page,
                    "pageSize" => $pageSize,
                    "countPage" => ceil($count / $pageSize),
                    "coupon_list" => $Coupon,
                );
                $this->apiReturn($data,"1000");
            }else{
                $data = array(
                    "count" => $count,
                    "thisPage" => $page,
                    "pageSize" => $pageSize,
                    "countPage" => ceil($count / $pageSize),
                    "coupon_list" => array(),
                );
                $this->apiReturn($data,"1004","无数据！");
            }
        }else{
            $this->apiReturn((object)array(),"1001");
        }
    }
}