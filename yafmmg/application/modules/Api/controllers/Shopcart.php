<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/31
 * Time: 16:26
 */
class ShopCartController extends ApiController
{

    //立即购买（未完成）
    public function nowBuyAction()
    {
        $product_p = $this->getParams();
        $product_id = $product_p['product_id'];
        if ($product_id) {
            $userInfo = $this->getSession("userInfo");
            $product_field = array(
                "id", "product_type");
            $product = ProductModel::getProductById($product_id, $product_field);
            if($userInfo['type']==1 && $product['product_type']==3){
                $data = (object)array();
                $code = 1007;
                $this->apiReturn($data, $code,"你已购买过3060套餐");
            }
            $data = array();
        } else {
            $code = 1001;
            $data = array();
        }
        $this->apiReturn($data, $code);
    }

    //添加购物车
    public function addCartAction()
    {
        $this->verifyLogin();
        $post = $this->getPost();
        $userInfo = UserModel::getUserMainInfo($this->user_id,"type");
        if (isset($post['pid']) && $post['pid']) {
            $product_data = ProductModel::getProductById($post['pid'], array("sell_price,product_type"));
            if($product_data){
                //3060只能购买一次  如果是vip 说明已经购买过
                if($userInfo['type']==1 && $product_data['product_type']==3){
                    $data = (object)array();
                    $code = 1007;
                    $this->apiReturn($data, $code,"你已购买过3060套餐");
                }
                if (isset($post["qty"]) && $post["qty"]) {
                    if ($post["qty"] > 10000) {
                        $data = (object)array();
                        $code = 1001;
                        $this->apiReturn($data, $code);
                    }
                } else {
                    $code = 1001;
                    $data = array();
                    $this->apiReturn($data, $code);
                }
                $p_attr = ProductModel::getProductAttribute($post['pid']);
                if($p_attr){
                    if (isset($post["attr"]) && $post["attr"]) {
                        unset($post["attr"]);
                    }
                    $post["attr"] = "";
                    if (isset($post["attr_list"]) && $post["attr_list"]) {
                        $post["attr_list"] = explode(",", $post["attr_list"]);
                        foreach ($post["attr_list"] as $attr_key => $attr_v) {
                            if($attr_v){
                                $attr_v = str_replace("id_data=","",$attr_v);
                                $attr_v_v_1 = explode(",", $attr_v);
                                $attr_v_v = explode(":", $attr_v_v_1[0]);
                                $p_v_id = $attr_v_v[1];
                                if($p_v_id){
                                    $p_v = ProductModel::getProductAttributeById($p_v_id,"attr_name,attr_id,attr_value_name");
                                    if($p_v){
                                        $post["attr"][$attr_key]["attr_id"] = $p_v['attr_id'];
                                        $post["attr"][$attr_key]["attr_name"] = $p_v['attr_name'];
                                        $post["attr"][$attr_key]["attr_value"] = array("attr_value_id" => $p_v_id, "attr_value_name" => $p_v['attr_value_name']);
                                    }
                                }
                            }

                        }
                        if(!empty($post["attr"])){
                            $post["attr"] = json_encode($post["attr"]);
                        }
                    }
                }


                $owner_arr = array();
                if(isset($post["owner_name"]) && $post["owner_name"]){
                    $owner_arr['owner_name'] = $post["owner_name"];
                }

                if(isset($post["owner_card_number"]) && $post["owner_card_number"]){
                    $owner_arr['owner_card_number'] = $post["owner_card_number"];
                }
                if($owner_arr){
                    $post['owner_info'] = json_encode($owner_arr);
                }
                $post['product_type'] = $product_data['product_type'];
                $post['price'] = $product_data['sell_price'];
                $cart_id = ShopCartModel::addCart($post, $this->user_id);
                if ($cart_id) {
                    $code = 1000;  //参数错误
                    $data = array("cart_id" => $cart_id);
                } else {
                    $code = 1004;  //写入失败
                    $data = array();
                }
            }else{
                $code = 1004;  //写入失败
                $data = array();
                $this->apiReturn(array(), $code,"商品不存在！");
            }


        } else {
            $code = 1001;  //参数错误
            $data = array();
        }
        $this->apiReturn($data, $code);

    }

    //删除购物车
    public function delCartAction()
    {
        $id = $this->getPost("id");
        $result = ShopCartModel::delCart($id, $this->user_id);
        if ($result) {
            $code = 1000;
            $data = (object)array();
        } else {
            $code = 1004;
            $data = (object)array();
        }
        $this->apiReturn($data, $code);
    }

    //修改购物车
    public function updataCartAction()
    {
        $id = $this->getPost("id");
        $p_qty = $this->getPost("qty");
        if($p_qty<1){
            $code = 1006;
            $data = (object)array();
            $this->apiReturn($data, $code,"数量不能小于1！");
        }
        $data = array('qty'=>$p_qty);

        $result = ShopCartModel::updataCart($id,$data, $this->user_id);
        if ($result) {
            $code = 1000;
            $data = (object)array();
        } else {
            $code = 1004;
            $data = (object)array();
        }
        $this->apiReturn($data, $code);
    }


    //购物车列表
    public function cartListAction(){
        $this->verifyLogin();
        $params = array();
        $params['page'] = $this->get("page");
        $params['pageSize'] = $this->get("pageSize");
        if($params['page']){
            $params['page'] = $this->getPost("page");
        }
        if($params['pageSize']){
            $params['pageSize'] = $this->getPost("pageSize");
        }
        $page = !empty($params["page"]) ? $params["page"] : 1;
        $pageSize = !empty($params["pageSize"]) ? $params["pageSize"] : 10;
        $where = array("user_id" => $this->user_id);

        $start = ($page - 1) * $pageSize;
        $count = ShopCartModel::cartList($start, $pageSize, $where, true);
        $cartList = ShopCartModel::cartList($start, $pageSize,  $where);
        $receive_address = UserModel::getUserDefaultAddress($this->user_id);
        if ($receive_address) {
            $receive_address_id = $receive_address['id'];
        }else{
            $receive_address_id = 0;
        }
        foreach($cartList as &$cartList_v){
            $cart_pid = $cartList_v['pid'];
            $url = \Yaf_Application::app()->getConfig()->qiniu->imgUrl;
            $product_field = "name,CONCAT('" . $url . "',product_img) as product_img";
            $cart_product = ProductModel::getProductById($cart_pid,$product_field);
            $cartList_v['name'] = $cart_product['name'];
            $cartList_v['product_img'] = $cart_product['product_img'];
            if($cartList_v['attr']){
                $cartList_v['attr'] = json_decode($cartList_v['attr'],true);
                if ( $cartList_v['attr']) {
                    $cartList_v["attr_show"] = "";
                    foreach ($cartList_v['attr'] as $attr_key => $attr_v) {
                        if (!empty($cartList_v["attr_show"])) {
                            $cartList_v["attr_show"] .= "/";
                        }
                        if($attr_v["attr_value"]["attr_value_name"] && $attr_v['attr_name']){
                            $cartList_v["attr_show"] .= $attr_v['attr_name'] . ":" . $attr_v["attr_value"]["attr_value_name"];
                        }
                    }
                }
            }
            unset($cartList_v['attr']);//暂时不返回
        }
        if ($cartList) {
            $code = 1000;
            $data = array(
                "count" => $count,
                "thisPage" => $page,
                "pageSize" => $pageSize,
                "countPage" => ceil($count / $pageSize),
                "receive_address_id"=>$receive_address_id,
                "cartList" => $cartList,
            );
            $this->apiReturn($data, $code);
        } else {
            $code = 1004;
            $data = array(
                "count" => $count,
                "thisPage" => $page,
                "pageSize" => $pageSize,
                "countPage" => ceil($count / $pageSize),
                "receive_address_id"=>$receive_address_id,
                "cartList" => array(),
            );
            $mag = "购物车为空";
            $this->apiReturn($data, $code,$mag);
        }

    }
}