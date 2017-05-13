<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/3
 * Time: 15:12
 */
class CouponModel extends BaseModel
{
    /**
     * @param int $start 开始位置
     * @param int $pageSize   页大小
     * @param array $where  条件
     * @param bool $isGetCount   false：返回列表   true:返回条数
     * @param string $field  需要查询的记录
     * @return mixed
     */
    public static function getCoupon($start=0, $pageSize=10, $where=array(), $isGetCount = false,$field="*"){
        $mod = M("yaf_user_coupon");
        if ($isGetCount) {//计算总条数
            $count = $mod->where($where)->getRowCount();
            return $count;
        } else {
            $list = $mod->field($field)->where($where)->limit($start,$pageSize)->getAll();
            return $list;
        }
    }

    //根据用户id,及票劵状态查找票劵
    public function getCouponByStatus($uid,$status){

        return M("yaf_user_coupon")->where(array("user_id"=>$uid,"status"=>$status))->getAll();
    }


    //根据产品id 获取这个产品的优惠券
    public static function getOneProductCoupon($uid,$product_id,$field="*"){
        return M("yaf_user_coupon")->field($field)->where(array("user_id"=>$uid,"product_id"=>$product_id,"status"=>1))->order("price DESC")->getOne();
    }


    //根据产品id查找产品
    public function getProductById($id){
        return M("yaf_shop_product")->where(array("id"=>$id))->getOne();
    }
}