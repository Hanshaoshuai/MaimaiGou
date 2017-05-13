<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/31
 * Time: 16:25
 */
class ShopCartModel extends BaseModel
{

    /*
     * 添加购物车
     */
    public static function addCart($data, $user_id)
    {
        $data = array(
            'user_id' => $user_id,
            'pid' => intval($data['pid']),
            'qty' => isset($data['qty']) ? intval($data['qty']) : 1,
            'product_type' => isset($data['product_type']) ? intval($data['product_type']) : 1,
            'attr' => isset($data['attr']) ? trim($data['attr']) : '',
            'owner_info' => isset($data['owner_info']) ? trim($data['owner_info']) : '',
            'price' => isset($data['price']) ? trim($data['price']) : 0,
            'status' => 1,
            'update_time' => time(),
            'ctime' => time(),
        );
        $cart_id = M("yaf_shop_cart")->where(array("user_id" => $user_id))->insert($data);
        if ($cart_id) {
            return $cart_id;
        } else {
            return false;
        }
    }

    //删除购物车商品
    public static function delCart($id, $user_id)
    {
        $where = "user_id='" . $user_id . "'";
        if (is_array($id)) {
            $ids = implode(",", $id);
            $where .= " and id in (" . $ids . ") ";
        } else {
            $where .= " and id = '" . $id . "' ";
        }
        return M("yaf_shop_cart")->where($where)->delete();
    }

    //修改购物车商品
    public static function updataCart($id,$data, $user_id)
    {
        $where = "user_id='" . $user_id . "' AND id='".$id."'";
        return M("yaf_shop_cart")->where($where)->update($data);
    }

    //购物车列表
    public static function cartList($start=0, $pageSize=10, $where,$isGetCount = false,$field="*"){
        $mod = M("yaf_shop_cart");
        if ($isGetCount) {//计算总条数
            $count = $mod->where($where)->getRowCount();
            return $count;
        } else {
            $list = $mod->field($field)->where($where)->order('id desc')->limit($start,$pageSize)->getAll();
            return $list;
        }
    }
}