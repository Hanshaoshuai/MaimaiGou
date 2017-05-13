<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/27
 * Time: 13:51
 */
class ProductController extends BaseController
{

    public function getProductAction() {

        $product_id = $this->get("id");
        $p_mod = new ProductModel();
        $product  = $p_mod->getProductById($product_id);
        $product['list_img'] = json_decode($product['list_img'],true);
        $attribute = $p_mod->getProductAttribute($product_id);  //获取产品属性
        $productProfile = $p_mod->getProductProfile($product_id);  //获取产品副表数据
        $parameters = json_decode($productProfile['parameters'],true); //规格参数
        $product['parameters'] = $parameters;
        $productAttribute = array();
        foreach($attribute as $attribute_key=>$attribute_value){
            $attribute_value_new = array();
            $attribute_value_new['attr_value_id'] = $attribute_value['attr_value_id'];
            $attribute_value_new['attr_value_name'] = $attribute_value['attr_value_name'];
            $productAttribute[$attribute_value['attr_id']]['attr_id'] = $attribute_value['attr_id'];
            $productAttribute[$attribute_value['attr_id']]['attr_name'] = $attribute_value['attr_name'];
            $productAttribute[$attribute_value['attr_id']]['attr_value_list'][] = $attribute_value_new;
            if($attribute_value['is_default']==1){
                $productAttribute[$attribute_value['attr_id']]['default'] =  $attribute_value['attr_value_id'];
            }
        }
        $product['roductattribute'] = $productAttribute;
        print_r($product);
        exit;
    }
}