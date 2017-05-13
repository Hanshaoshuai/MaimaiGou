<?php

/**
 *  商品
 * User: LF
 * Date: 2017/3/27
 */
class ShopModel {

    private static $_instance;
    private $db;

    private function __construct() {
        $this->init();
    }

    private function init() {
        ;
    }

    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function shopList_new($params = array()) {
        $db = M("yaf_shop_product");
        $where = " status =1 and is_delete=0";

        if (isset($params["product_type"])) {
            $where .= " and product_type = '" . $params["product_type"] . "' ";
        }
        if (isset($params["is_hot"])) {
           // $where .= " and is_hot = '" . $params["is_hot"] . "' ";
        }
        if (isset($params["is_new"])) {
            $where .= " and is_new = '" . $params["is_new"] . "' ";
        }
        if (isset($params["is_overseas"])) {
            $where .= " and is_overseas = '" . $params["is_overseas"] . "' ";
        }
        if (isset($params["is_high"])) {
            $where .= " and is_high = '" . $params["is_high"] . "' ";
        }
        if (isset($params["is_sales"])) {
            $where .= " and is_sales = '" . $params["is_sales"] . "' ";
        }
        //口碑最佳
        if (isset($params["is_reputation"])) {
            $where .= " and is_reputation = '" . $params["is_reputation"] . "' ";
        }
        if (isset($params["catid"])) {
            $where .= " and catid = '" . $params["catid"] . "' ";
        }
        if (isset($params["keywords"])) {
            $where .= " and keywords like'% " . $params["catid"] . "%' ";
        }
        $page = 1;
        if (isset($params['page'])) {
            $page = $params['page'];
        }
        $limit = 20;
        if (isset($params['limit'])) {
            $limit = $params['limit'];
        }
        if (isset($params['order'])) {
            $sort = "desc";
            if (isset($params['sort']) && $params['sort'] == "asc") {
                $sort = "asc";
            }
            switch ($params['order']) {
                case 1:
                    $orderFeild = "sales";
                    break;
                case 2:
                    $orderFeild = "sell_price";
                    break;
                default :
                    $orderFeild = "id";
                    break;
            }
            $order = array($orderFeild => $sort);
        } else {
            $order = array("id" => "desc");
        }
		//$order['sorts']=>"asc";
        $url = \Yaf_Application::app()->getConfig()->qiniu->imgUrl;
        $rs = $db->field("sales,id,name,catid,sell_price,cost_price,promotional_price,promotional_stock,promotional_start,promotional_end,promotional_end,stock,CONCAT('" . $url . "',product_img) as img,visit,integral,status")->where($where)->order($order)->limit(($page - 1) * $limit, $limit)->getAll();
        $count = $db->field("count(id) as total")->where($where)->order($order)->getAll();
        return array("list" => $rs, "total" => $count[0]['total']);
    }

    public function shopList($params = array()) {
        $db = M("yaf_shop_product");
        $where = array();
        $where['status'] = 1;
        $where['is_delete'] = 0;
        if (isset($params["product_type"])) {
            $where["product_type"] = $params["product_type"];
        }
        if (isset($params["is_hot"])) {
            //$where["is_hot"] = $params["is_hot"];
        }
        if (isset($params["is_new"])) {
            $where["is_new"] = $params["is_new"];
        }
        if (isset($params["is_overseas"])) {
            $where["is_overseas"] = $params["is_overseas"];
        }
        if (isset($params["is_high"])) {
            $where["is_high"] = $params["is_high"];
        }
        if (isset($params["is_sales"])) {
            $where["is_sales"] = $params["is_sales"];
        }
        //口碑最佳
        if (isset($params["is_reputation"])) {
            $where["is_reputation"] = $params["is_reputation"];
        }
        $where2=" 1=1"; 
        if (isset($params["catid"]) && $params["catid"] != "") {
            //$where["catid"] = $params["catid"];
            $catid=$this->getCatid($params["catid"]);
            $catid=rtrim($catid, ",");
            $where2=" catid in(".$catid.")";
        }        
        $where1 = " 1=1";
        if (isset($params["keywords"])) {
            $where1 = " keywords like'%" . $params["keywords"] . "%'";
        }
        $page = 1;

        if (isset($params['page'])) {
            $page = $params['page'];
        }
        
        if (isset($params['limit'])) {
            $limit = $params['limit'];
        }
		$limit = 60;
        if (isset($params['order'])&&($params['order']!=0||$params['order']!=null)) {
            $sort = "desc";
            if (isset($params['sort']) && $params['sort'] == "asc") {
                $sort = "asc";
            }
            switch ($params['order']) {
                case 1:
                    $orderFeild = "sales";
                    break;
                case 2:
                    $orderFeild = "sell_price";
                    break;
                default :
                    //$orderFeild = "sorts";
					//$sort="asc";
                    break;
            }
            $order[$orderFeild] =  $sort;
        } 
		
		$order['sorts']=" asc";
        $url = \Yaf_Application::app()->getConfig()->qiniu->imgUrl;
        $rs = $db->field("sales,id,name,catid,sell_price,cost_price,promotional_price,promotional_stock,promotional_start,promotional_end,promotional_end,stock,CONCAT('" . $url . "',product_img) as img,visit,integral,status")->where($where)->where($where1)->where($where2)->order($order)->limit(($page - 1) * $limit, $limit)->getAll();
        //echo $db->field("sales,id,name,catid,sell_price,cost_price,promotional_price,promotional_stock,promotional_start,promotional_end,promotional_end,stock,CONCAT('" . $url . "',product_img) as img,visit,integral,status")->where($where)->where($where1)->where($where2)->order($order)->limit(($page - 1) * $limit, $limit)->getSql();
        $count = $db->field("count(id) as total")->where($where)->where($where1)->where($where2)->order($order)->getAll();
        return array("list" => $rs, "total" => $count[0]['total']);
    }

    public function getCatid($id, $arr = array()) {
        $category_ids = $id . ",";
        $m = M("yaf_shop_product");
        $sql = "select id from yaf_shop_category where parentid='" . $id . "'";
        $rs = $m->query($sql);

        foreach ($rs as $key => $val)
            $category_ids .= $this->getCatid($val["id"]);
        return $category_ids;
        //return $arr;
    }

}
