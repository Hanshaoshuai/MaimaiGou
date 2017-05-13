<?php

/**
 *  商品
 * User: LF
 * Date: 2017/3/27
 */
class ShopController extends ApiController {

    private $model;

    public function init() {        
        $this->model = ShopModel::getInstance();
    } 
    public  function shopListAction(){
        $params=$this->getPost();
        if(!$params){
            $params['limit'] = $this->get("limit");
            $params['page'] = $this->get("page");
            $params['product_type'] = $this->get("product_type");
            $params['is_hot'] = $this->get("is_hot");
            $params['catid'] = $this->get("catid");
            $params['order'] = $this->get("order");
            $params['sort'] = $this->get("sort");
            $params['keywords'] = $this->get("keywords");
            $params['is_new'] = $this->get("is_new");
            $params['is_overseas'] = $this->get("is_overseas");
            $params['is_high'] = $this->get("is_high");
            $params['is_sales'] = $this->get("is_sales");
        }
        $rs=$this->model->shopList($params);
        echo  json_encode(array("data"=>$rs['list'],"code"=>"1000","msg"=>"","total"=>$rs['total']));  exit;      
    }

}
