<?php

/**
 *
 * User: LF
 * Date: 2017/3/27
 */
class IndexModel {

    private static $_instance;
    private $db;

    private function __construct() {
        $this->init();
    }

    private function init() {
       // $this->db = M("yaf_user_bankcard", "yaf_mmg");
    }

    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function category() {  
        $db= M("yaf_shop_category", "yaf_mmg");
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 100;
        if(isset($params['parentid'])&&isset($params['parentid'])!=""){
            $where["parentid"]=$params['parentId'];
        }else{
            $where["parentid"]=0;
        }
        $rs = $db->field('name,id,img')->where($where)->order("`order` desc")->limit($params['limit'])->getAll();
        return $rs;
    }
    public function wordOfMouth() {  
        $db= M("yaf_shop_product", "yaf_mmg");
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 40;
                $url= \Yaf_Application::app()->getConfig()->qiniu->imgUrl;  
        $rs = $db->field("id,name,CONCAT('".$url."',product_img) as img,sell_price")->where(array("is_delete"=>0,'status'=>1))->order("is_reputation desc,sorts asc")->limit($params['limit'])->getAll();
        return $rs;
    }    

    public function youLike() {    
        $db= M("yaf_shop_product", "yaf_mmg");
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 40;
                $url= \Yaf_Application::app()->getConfig()->qiniu->imgUrl;  
        $rs = $db->field("id,name,CONCAT('".$url."',product_img) as img,sell_price")->where(array("is_delete"=>0,'status'=>1))->order("is_hot desc,sorts asc")->limit($params['limit'])->getAll();
        return $rs;
    }
}
