<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/27
 * Time: 13:51
 */
class ProductController extends ApiController
{
    //产品详情
    public function getProductAction()
    {
        $product_id = $this->get("id");
        if($product_id){
            $p_mod = new ProductModel();
            $product_field = array(
                "id", "name", "goods_sn", "catid", "content","description","sell_price", "cost_price", "promotional_price", "promotional_stock", "promotional_start", "promotional_start", "promotional_end","product_serve", "stock","CONCAT('".IMG_URL."',product_img) AS product_img", "list_img",
                "visit", "integral", "product_type", "is_hot", "is_overseas", "is_new", "is_high", "is_sales","is_owner_info");
            $product = $p_mod->getProductById($product_id, $product_field);
            $product['list_img'] = $product['list_img'];

            $attribute = $p_mod->getProductAttribute($product_id);  //获取产品属性
            $productProfile = $p_mod->getProductProfile($product_id);  //获取产品副表数据
            $parameters = json_decode($productProfile['parameters'], true); //规格参数
            $product['parameters'] = $parameters;
            $productAttribute = array();
            foreach ($attribute as $attribute_key => $attribute_value) {
                $attribute_value_new = array();
                $attribute_value_new['p_attr_v_id'] = $attribute_value['id'];
                $attribute_value_new['attr_value_id'] = $attribute_value['attr_value_id'];
                $attribute_value_new['attr_value_name'] = $attribute_value['attr_value_name'];
                $productAttribute[$attribute_value['attr_id']]['attr_id'] = $attribute_value['attr_id'];
                $productAttribute[$attribute_value['attr_id']]['attr_name'] = $attribute_value['attr_name'];
                $productAttribute[$attribute_value['attr_id']]['attr_value_list'][] = $attribute_value_new;
                if ($attribute_value['is_default'] == 1) {
                    $productAttribute[$attribute_value['attr_id']]['default'] = $attribute_value['attr_value_id'];
                }
            }
            if($product['product_serve']){
                $product['product_serve'] = explode(",",$product['product_serve']);
                $product['product_serve'] = $p_mod->getProductServe($product['product_serve']);
            }
            $product['roductattribute'] = $productAttribute;

            if($this->user_id){
                $where = array("user_id" => $this->user_id);
                $cart_num = ShopCartModel::cartList("", "", $where, true);
            }else{
                $cart_num = 0;
            }
            if ($product['id']) {
                $code = 1000;
            } else {
                $code = 1004;
            }
            $model= new CommentModel();
            $product_comment=$model->getComment(array("goods_id"=>$product_id));
            $this->apiReturn(array("product"=>$product,"product_comment"=>$product_comment,"cart_num"=>$cart_num), $code);
        }else{
            $this->apiReturn(array(),1001);
        }

    }

    //商品搜索接口  （接口后期修改）
    public function searchProductAction(){
        $keyword = $this->get("keyword");
        $product_field = array(
            "id", "name", "sell_price", "cost_price", "stock", "visit", "integral","CONCAT('".IMG_URL."',product_img) AS product_img", "product_type", "is_hot", "is_overseas", "is_new", "is_high", "is_sales");
        if($keyword){
            $product_list = M("yaf_shop_product")->field($product_field)->where("yaf_shop_product.name like '%".$keyword."%' AND status=1 AND is_delete=0")->getAll();
            if ($product_list) {
                $code = 1000;
            } else {
                $code = 1004;
            }
            $this->apiReturn($product_list, $code);
        }else{
            $this->apiReturn(array(), 1001);
        }
    }
    //添加收藏
    public function addCollectAction(){
        // print_r($_COOKIE);die;
        $product_id=$this->get("product_id")?$this->get("product_id"):"";
        //print_r($product_id);die;
        if(empty($product_id)||!is_numeric($product_id)){
            $this->apiReturn(array("status"=>0),"1001");
        }
        if (!key_exists("shop_collect_info",$_COOKIE)){
            $collect_info[0]=$product_id;
            setcookie("shop_collect_info",serialize($collect_info),time()+3600*24*7);
            $this->apiReturn(array("status"=>1),"1000","收藏成功");
        }else{
            $collect_info = unserialize(stripslashes($this->getCookie("shop_collect_info")));
            // print_r($collect_info);die;
            foreach ($collect_info as $v){
                if($v==$product_id){
                    $this->apiReturn(array("status"=>0),"1004","该产品已收藏");
                }
            }
            if(array_push($collect_info,$product_id)){
                setcookie("shop_collect_info",serialize($collect_info),time()+3600*24*7);
                $this->apiReturn(array("status"=>1),"1000","收藏成功");
            }
        }
    }
    //我的收藏
    public function myCollectAction(){
        $ids=$this->get('ids')?$this->get('ids'):'';
        if (empty($ids)){
            $this->apiReturn(array(),"1004","收藏为空");
        }
        if(is_string($ids)){
            $ids=trim($ids,'\"');
        }
        if(is_array($ids)){
            $ids=implode(',',$ids);
        }

        if(!empty($ids)){
           // $params['product_ids']=implode(",", $collect_info );
            $params['product_ids']=$ids;
            $params['page']=$this->get('page')?$this->get('page'):1;
            $params['pageSize']=$this->get('pageSize')?$this->get('pageSize'):100;
            if(!is_numeric($params['page']) || !is_numeric($params['pageSize'])){
                $this->apiReturn(array(),"1001");
            }
    
            $O_mod = new ProductModel();
            $data=$O_mod->myCollect($params);
            foreach($data as $k=>$v){
                $data[$k]['img']=IMG_URL.$v['product_img'];
            }
            if(empty($data)){
                $this->apiReturn(array(),"1004","收藏为空");
            }else {
                $this->apiReturn($data,"1000");
            }
        }else{
            $this->apiReturn(array(),"1004","收藏为空");
        }

       /*
        * if (!key_exists("shop_collect_info", $_COOKIE) && empty($ids)){
            $this->apiReturn(array(),"1004","收藏为空");
        }
       // $collect_info = unserialize(stripslashes($this->getCookie("shop_collect_info")));
       if(!empty($collect_info )){
              $params['product_ids']=implode(",", $collect_info );
            //$params['product_ids']=$ids;
            $params['page']=$this->get('page')?$this->get('page'):1;
            $params['pageSize']=$this->get('pageSize')?$this->get('pageSize'):3;
            if(!is_numeric($params['page']) || !is_numeric($params['pageSize'])){
                $this->apiReturn(array(),"1001");
            }

            $O_mod = new ProductModel();
            $data=$O_mod->myCollect($params);
            if(empty($data)){
                $this->apiReturn(array(),"1004","收藏为空");
            }else {
                $this->apiReturn($data,"1000");
            }
        }else{
            $this->apiReturn(array(),"1004","收藏为空");
        }*/
    }
    //删除收藏
    public function delCollectAction(){
        $product_id=$this->get("product_id")?$this->get("product_id"):'';
        if(!empty($product_id)){
            if (!key_exists("shop_collect_info", $_COOKIE)){
                $this->apiReturn(array(),"1004","收藏为空");
            }
            $collect_info = unserialize(stripslashes($this->getCookie("shop_collect_info")));
            if(!in_array($product_id,$collect_info)){
                $this->apiReturn(array(),"1001","该产品已删除");
            }
            foreach ($collect_info as $k=>$v){
                if($v==$product_id){
                    unset($collect_info[$k]);
                }
            }
            if(!empty($collect_info)){
                setcookie("shop_collect_info",serialize($collect_info),time()+3600*24*7);
                $this->apiReturn(array(),"1000");
            }else{
                setcookie("shop_collect_info",serialize($collect_info),time()+3600*24*7);
                $this->apiReturn(array(),"1000");
            }
    
        }
        /*  批量删除
         *
         $arr=$this->getPost("ids");
         if(is_array($arr)){
         $collect_info = unserialize(stripslashes($this->getCookie("shop_collect_info")));
         foreach ($arr as $v){
         foreach ($collect_info as $k=>$v1){
         if($v==$v1){
         unset($collect_info[$k]);
         }
         }
         }
         setcookie("shop_collect_info",serialize($collect_info));
         } */
    
    }
}