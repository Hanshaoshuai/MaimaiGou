<?php

/**
 *  广告位
 * User: LF
 * Date: 2017/3/27
 */
class BannerModel {

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

    public function bannerList($params = array()) {
        $db = M("yaf_ad_cat");
        $where = " 1=1 ";
        $params1 = array();
        if (isset($params["category_id"])) {
            $where .= " and b.category_id=? ";
            $params1[] = $params["category_id"];
        }
        if (isset($params["id"])) {
            $where .= " and b.id=? ";
            $params1[] = $params["id"];
        }
        $where .= " and a.status=1 ";
        /*if (isset($params["location"]) && !empty($params["location"])) {
            $where .= " and a.location=? ";
            $params1[] = $params["location"];
        }*/

        $params['limit'] = isset($params['limit']) ? $params['limit'] : 7;
        $page = 1;
        if (isset($params['page']) && is_int($params['page'])) {
            $page = $params['page'];
        }
        $limit = 3;
        if (isset($params['limit']) && is_int($params['limit'])) {
            $limit = $params['limit'];
        }
        $sql = "select a.id,a.ad_name as name,a.img_url as image,a.ad_link as url,1 as location  from yaf_ad as a INNER JOIN yaf_ad_cat as b  on a.ad_cat_id=b.id where " . $where . " order by a.order asc limit " . ($page - 1) * $limit . "," . $limit . "";        
        $rs = $db->query($sql, $params1);
        return $rs;
    }

    public function getBannerTitle($id) {           
        $db = M("yaf_ad_cat");
        $where["id"] = $id;
        $rs = $db->field("pos_name as name")->where($where)->getOne();
        return $rs['name'];
    }

}
