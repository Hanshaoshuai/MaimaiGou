<?php

/**
 * Created by PhpStorm.
 * User: zhanggen
 * Date: 2017/3/27
 * Time: 14:47
 */
class ProductModel extends BaseModel {

    const TYPE_CATEGORY = 1; //产品分类

    /*
     * 根据产品ID获取产品信息
     */

    public static function getProductById($id, $field = "*") {
        return M("yaf_shop_product")->field($field)->where(array("id" => $id))->getOne();
    }

    /*
     * 根据产品ID获取产品属性值
     */

    public static function getProductAttribute($id, $field = "*") {
        return M("yaf_shop_product_attribute")->field($field)->where(array("product_id" => $id))->getAll();
    }

    /*
     * 根据产品属性ID获取产品属性值
     */
    public static function getProductAttributeById($id, $field = "*") {
        return M("yaf_shop_product_attribute")->field($field)->where(array("id" => $id))->getOne();
    }

    /*
     * 根据属性ID和产品id获取产品下的所有属性值
     */
    public static function getProductAttributeByAttrId($pid,$attr_id, $field = "*",$isGetCount = false) {
        $mod = M("yaf_shop_product_attribute");
        if ($isGetCount) {//计算总条数
            $count = $mod->where(array("product_id" => $pid,"attr_id"=>$attr_id))->getRowCount();
            return $count;
        } else {
            $list =  $mod->field("attr_value_name")->where(array("product_id" => $pid,"attr_id"=>$attr_id))->getAll();
            return $list;
        }
    }


    /*
     * 根据所有产品属性
     */

    public static function getAllAttribute($start = 0, $pageSize = 100, $where = array(), $isGetCount = false, $field = "*") {
        $mod = M("yaf_shop_attribute");
        if ($isGetCount) {//计算总条数
            $count = $mod->where($where)->getRowCount();
            return $count;
        } else {
            $list = $mod->field($field)->where($where)->limit($start, $pageSize)->getAll();
            return $list;
        }
    }

    /*
     * 根据产品ID获取产品副表信息
     */

    public static function getProductProfile($id, $field = "*") {
        return M("yaf_shop_product_profile")->field($field)->where(array("product_id" => $id))->getOne();
    }

    //我的收藏 根据商品id 查找商品
    public function myCollect($params) {
        $ids = $params['product_ids'];
        $limit1 = ($params['page'] - 1) * $params['pageSize'];
        $sql = "select a.id,a.name,b.name catid,a.product_img,a.sell_price from yaf_shop_product a left join yaf_shop_category b on
             a.catid=b.id where a.id in (" . $ids . ") limit " . $limit1 . "," . $params['pageSize'];
        return M()->query($sql, array());
    }

    /**
     * 获取商品分类列表
     */
    public function category($params) {
        $db = M("yaf_shop_category");
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 8;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        if (isset($params['parentid']) && (isset($params['parentid']) != "" || $params['parentid'] == "0")) {
            $where["parentid"] = $params['parentid'];
        } else {
            //$where["parentid"]=0;
            $where['is_nav'] = 1;
        }
        if (isset($params['status'])) {
            $where["status"] = $params['status'];
        }
        $where['is_delete'] = 0;
        $url = \Yaf_Application::app()->getConfig()->qiniu->imgUrl;
        $rs = $db->field('name,id,CONCAT("' . $url . '",img) as img')->where($where)->order("`order` desc")->limit($params['limit'])->getAll();
        $coun = $db->field('count(id) as coun')->where($where)->order("`order` desc")->getAll();
        return array("data" => $rs, "countPage" => ceil($coun[0]['coun'] / 100), "pageSize" => $params['limit'], "thisPage" => $params['page'], "count" => $coun[0]['coun']);
        //return $rs;
    }

    /**
     * 获取商品分类列表
     */
    public static function categoryList($params) {
        $db = M("yaf_shop_category");
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 100;
        $where = array();
        if (isset($params['parentid']) && isset($params['parentid']) != "") {
            $where["parentid"] = $params['parentId'];
        }
        $where['is_delete'] = 0;
        $rs = $db->field('name,id,img,parentid')->where($where)->order("`order` desc")->getAll();
        return $rs;
    }

    /**
     * @param $post_data
     * @return ProductList
     */
    public static function getProductList($start = 0, $pageSize = 15, $where_arr = array(), $isGetCount = false, $field = "*") {
        $key = isset($where_arr["keyword"]) ? $where_arr["keyword"] : "";
        $catid = isset($where_arr["cat_id"]) ? $where_arr["cat_id"] : "";
        $ctime = isset($where_arr["ctime"]) ? $where_arr["ctime"] : "";
        $where = "1=1";
        if ($key) {
            $where .= " and name like '%" . $key . "%'";
        }
        if ($catid) {
            $where .= " and catid ='" . $catid . "'";
        }
        /*
          if($ctime){
          $where .= " AND DATE_FORMAT(FROM_UNIXTIME(ctime),'%Y-%m-%d') = DATE_FORMAT(FROM_UNIXTIME(".$ctime."),'%Y-%m-%d')";
          }
         */
        $where .= " and is_delete = 0 ";
        if ($isGetCount) {//计算总条数
            $count = M("yaf_shop_product")->where($where)->getRowCount();
            return $count;
        } else {
            $results = M("yaf_shop_product")->field($field)->where($where)->order('sorts desc,ctime desc')->limit($start, $pageSize)->getAll();
            return $results;
        }
    }

    /*
     * 添加/修改产品状态
     */

    public static function updataProduct($post,$attr_arr=false) {
        $product_id = isset($post['product_id']) ? intval($post['product_id']) : 0;
        $default_array = array('name', 'goods_sn', 'catid', 'content', 'description', 'keywords', 'sell_price', 'cost_price', 'promotional_price', 'promotional_stock', 'promotional_start',
            'promotional_end', 'stock', 'sales', 'img', 'product_img', 'small_img', 'list_img', 'locality', 'brand', 'weight', 'unit', 'visit', 'is_delete', 'ctime',
            'update_time', 'integral', 'product_type', "product_serve", 'is_hot', 'is_new', 'is_overseas', 'is_high', 'is_sales', 'is_reputation','is_owner_info', 'sorts', 'status', 'is_comment', 'notes'
        );
        $data = array();
        foreach ($post as $post_key => $post_v) {
            if (in_array($post_key, $default_array)) {
                $data[$post_key] = $post_v;
            }
        }

        $product_db = M('yaf_shop_product');
        if (!$product_id) {
            $data = array(
                'name' => $post['name'],
                'catid' => intval($post['catid']),
                'content' => isset($post['content']) ? trim($post['content']) : "",
                'description' => isset($post['description']) ? trim($post['description']) : "",
                'keywords' => isset($post['keywords']) ? trim($post['keywords']) : "",
                'sell_price' => floatval($post['sell_price']),
                'cost_price' => floatval($post['cost_price']),
                'promotional_price' => isset($post['promotional_price']) ? floatval($post['promotional_price']) : "0.00",
                'promotional_stock' => isset($post['promotional_stock']) ? intval($post['promotional_stock']) : 0,
                'promotional_start' => isset($post['promotional_start']) ? strtotime($post['promotional_start']) : 0,
                'promotional_end' => isset($post['promotional_end']) ? strtotime($post['promotional_end']) : 0,
                'stock' => isset($post['stock']) ? intval($post['stock']) : 100,
                'img' => isset($post['img']) ? trim($post['img']) : "",
                'product_img' => isset($post['product_img']) ? trim($post['product_img']) : "",
                // 'small_img' => trim($post['small_img']),
                'list_img' => isset($post['list_img']) ? trim($post['list_img']) : "",
                'locality' => isset($post['locality']) ? trim($post['locality']) : "",
                'brand' => isset($post['brand']) ? trim($post['brand']) : "",
                'weight' => isset($post['weight']) ? floatval($post['weight']) : "",
                'unit' => isset($post['unit']) ? trim($post['unit']) : "",
                'is_delete' => 0,
                'ctime' => time(),
                'update_time' => time(),
                'integral' => isset($post['integral']) ? intval($post['integral']) : "0",
                'product_type' => isset($post['product_type']) ? intval($post['product_type']) : 0,
                'product_serve' => isset($post['product_serve']) ? intval($post['product_serve']) : "",
                'is_hot' => isset($post['is_hot']) ? intval($post['is_hot']) : 0,
                'is_new' => isset($post['is_new']) ? intval($post['is_new']) : 0,
                'is_overseas' => isset($post['is_overseas']) ? intval($post['is_overseas']) : 0,
                'is_high' => isset($post['is_high']) ? intval($post['is_high']) : 0,
                'is_sales' => isset($post['is_sales']) ? intval($post['is_sales']) : 0,
                'is_reputation' => isset($post['is_reputation']) ? intval($post['is_reputation']) : 0,
                'is_owner_info' => isset($post['is_owner_info']) ? intval($post['is_owner_info']) : 0,
                'is_comment' => isset($post['is_comment']) ? intval($post['is_comment']) : 0,
                'notes' => isset($post['notes']) ? trim($post['notes']) : "",
                 'sorts' => isset($post['sorts']) ? trim($post['sorts']) : 999
            );
            $affected = $product_id = $product_db->insert($data);
            if($product_id && $attr_arr){
                self::addAttributeValuesNew($product_id,$attr_arr);
            }
            //更新货号
            $sn = isset($post['goods_sn']) ? trim($post['goods_sn']) : '';
            if (empty($sn)) {
                $sn = self::getProductSn($product_id);
            } else {
                $count = $product_db->where("goods_sn='" . $sn . "' AND id!='" . $product_id . "'")->getRowCount();
                if ($count) {
                    $sn .= '-' . $product_id;
                }
            }
            $product_db->where(array('id' => $product_id))->update(array('goods_sn' => $sn));
        } else {
            if(!empty($attr_arr)){
                $re_p_v = false;
                foreach ($attr_arr as $key=>$item) {
                    if($key){
                        $attr_count = self::getProductAttributeByAttrId($product_id,$key, "",true);
                        if($attr_count){
                            $re_p_v = self::delAttributeValuesNew($product_id,$key);
                        }else{
                            $re_p_v = true;
                        }

                    }else{
                        $re_p_v = true;
                    }
                }
                if($re_p_v){
                    self::addAttributeValuesNew($product_id,$attr_arr);
                }
            }
            if ($data) {
                $data['update_time'] = time();
                if(!isset($data['is_hot'])){
                    $data['is_hot']=0;
                }
                if(!isset($data['is_new'])){
                    $data['is_new']=0;
                }       
                if(!isset($data['is_overseas'])){
                    $data['is_overseas']=0;
                }  
                if(!isset($data['is_high'])){
                    $data['is_high']=0;
                }       
                if(!isset($data['is_sales'])){
                    $data['is_sales']=0;
                }           
                if(!isset($data['is_reputation'])){
                    $data['is_reputation']=0;
                }                   
                $affected = $product_db->where('id=' . $product_id)->update($data);
            } else {
                return false;
            }
        }
        return $affected;
    }

    //删除商品(不建议使用)
    public static function delProduct($id) {
        if ($id) {
            $where = "";
            if (is_array($id)) {
                $ids = implode(",", $id);
                $where .= " and id in (" . $ids . ") ";
            } else {
                $where .= " and id = '" . $id . "' ";
            }
            return M("yaf_shop_product")->where($where)->delete();
        } else {
            return false;
        }
    }

    //删除商品(软删除)
    public static function softDeleltProduct($id) {
        if ($id) {
            if (is_array($id)) {
                $ids = implode(",", $id);
                $where = "id in (" . $ids . ") ";
            } else {
                $where = "id = '" . $id . "' ";
            }
            return M("yaf_shop_product")->where($where)->update(array("is_delete" => 1));
        } else {
            return false;
        }
    }

    /**
     * 获得商品货号
     * @param int $pid 产品ID号
     * @return String
     */
    public static function getProductSn($pid) {
        $total_len = 12;
        $sn_pre = 'MMY';
        $sn_len = strlen($sn_pre);

        //判断加0的个数
        if (($total_len - $sn_len - strlen($pid)) > 0) {
            $j = $total_len - $sn_len - strlen($pid);
            for ($i = 0; $i < $j; $i++) {
                $sn_pre = $sn_pre . "0";
            }
        }
        //组合货号
        $sn_pre = $sn_pre . $pid;
        return $sn_pre;
    }

    //添加修改类目
    public static function updataCategory($post) {

        $category_id = isset($post['cat_id']) ? intval($post['cat_id']) : 0;
        $default_array = array('parentid', 'name', 'img', 'description', 'order', 'status', 'is_nav');
        $data = array();
        foreach ($post as $post_key => $post_v) {
            if (in_array($post_key, $default_array)) {
                $data[$post_key] = $post_v;
            }
        }

        $category_db = M('yaf_shop_category');
        if (!$category_id) {
            $data = array(
                'parentid' => isset($post['parentid']) ? $post['parentid'] : 0,
                'name' => $post['name'],
                'img' => isset($post['img']) ? $post['img'] : "",
                'description' => trim($post['description']),
                'order' => intval($post['order']),
                'ctime' => time(),
                'status' => isset($post['status']) ? intval($post['status']) : 1,
                'is_nav' => isset($post['is_nav']) ? intval($post['is_nav']) : 1,
            );
            $affected = $product_id = $category_db->insert($data);
        } else {
            if ($data) {
                $affected = $category_db->where('id=' . $category_id)->update($data);
            } else {
                return false;
            }
        }
        return $affected;
    }

    //添加修改品牌
    public static function updataBrand($post) {
        $brand_id = isset($post['brand_id']) ? intval($post['brand_id']) : 0;
        $default_array = array('name', 'img', 'description', 'location', 'is_domestic', 'ctime', 'is_delete', 'sorts', 'status');
        $data = array();
        foreach ($post as $post_key => $post_v) {
            if (in_array($post_key, $default_array)) {
                $data[$post_key] = $post_v;
            }
        }

        $brand_db = M('yaf_shop_brand');
        if (!$brand_id) {
            $data = array(
                'parentid' => isset($post['parentid']) ? $post['parentid'] : 0,
                'name' => $post['name'],
                'img' => isset($post['img']) ? $post['img'] : "",
                'description' => trim($post['description']),
                'location' => trim($post['location']),
                'is_domestic' => intval($post['is_domestic']),
                'ctime' => time(),
                'sorts' => isset($post['sorts']) ? intval($post['sorts']) : 0,
                'status' => intval($post['status']),
            );
            $affected = $brand_id = $brand_db->insert($data);
        } else {
            if ($data) {
                $affected = $brand_db->where('id=' . $brand_id)->update($data);
            } else {
                return false;
            }
        }
        return $affected;
    }

    /**
     * @param $post_data
     * @return geBrandList
     */
    public static function getBrandList($start = 0, $pageSize = 15, $where_arr = array(), $isGetCount = false, $field = "*") {
        $key = isset($where_arr["keyword"]) ? $where_arr["keyword"] : "";
        $ctime = isset($where_arr["ctime"]) ? $where_arr["ctime"] : "";
        $is_domestic = isset($where_arr["is_domestic"]) ? $where_arr["is_domestic"] : "";
        $where = "1=1";
        if ($key) {
            $where .= " and name like '%" . $key . "%'";
        }

        if ($is_domestic) {
            $where .= " and is_domestic = '" . $is_domestic . "'";
        }

        if ($ctime) {
            $where .= " AND DATE_FORMAT(FROM_UNIXTIME(ctime),'%Y-%m-%d') = DATE_FORMAT(FROM_UNIXTIME(" . $ctime . "),'%Y-%m-%d')";
        }
        $where .= " and is_delete = 0 ";
        if ($isGetCount) {//计算总条数
            $count = M("yaf_shop_brand")->where($where)->getRowCount();
            return $count;
        } else {
            $results = M("yaf_shop_brand")->field($field)->where($where)->order('sorts desc,ctime desc')->limit($start, $pageSize)->getAll();
            return $results;
        }
    }

    /**
     * 树型下拉选择分类
     * @param int $selectid 选择中的ID号
     * @param int $vid 类型ID
     */
    public static function categorySelectList($selectid = 0, $vid = self::TYPE_CATEGORY) {
        $category = self::categoryList("");
        $tree = new \Tool\Tree();
        $array = array();
        foreach ($category as $r) {
            $r['selected'] = $r['id'] == $selectid ? 'selected' : '';
            $array[] = $r;
        }
        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $tree->init($array);
        $select_categorys = $tree->get_tree(0, $str);
        return $select_categorys;
    }

    //获取产品服
    public static function getProductServe($serveids) {
        if (is_array($serveids)) {
            $ids = implode(",", $serveids);
            $productserve = M("yaf_shop_product_serve")->where("id in (" . $ids . ")")->getAll();
        } else {
            $productserve = M("yaf_shop_product_serve")->where("id ='" . $serveids . "'")->getOne();
        }
        return $productserve;
    }

    //获取产品服
    public static function getAllProductServe() {
        $productserve = M("yaf_shop_product_serve")->getAll();
        return $productserve;
    }

    //获取分类信息
    public static function getCategoryById($cat_id, $field = "*") {
        return M("yaf_shop_category")->field($field)->where(array("id" => $cat_id))->getOne();
    }

    //获取品牌信息
    public static function getBrandById($brand_id, $field = "*") {
        return M("yaf_shop_brand")->field($field)->where(array("id" => $brand_id))->getOne();
    }

    /**
     * 获取商品分类列表
     */
    public static function brandList() {
        $db = M("yaf_shop_brand");
        $where = array();
        $where['is_delete'] = 0;
        $where['status'] = 1;
        $rs = $db->field('name,id')->where($where)->order("sorts desc")->getAll();
        return $rs;
    }

    //删除品牌(软删除)
    public static function softDeleltBrand($id) {
        if ($id) {
            if (is_array($id)) {
                $ids = implode(",", $id);
                $where = "id in (" . $ids . ") ";
            } else {
                $where = "id = '" . $id . "' ";
            }
            return M("yaf_shop_brand")->where($where)->update(array("is_delete" => 1));
        } else {
            return false;
        }
    }

    //删除品牌(软删除)
    public static function softDeleltCategory($id) {
        if ($id) {
            if (is_array($id)) {
                $ids = implode(",", $id);
                $where = "id in (" . $ids . ") ";
            } else {
                $where = "id = '" . $id . "' ";
            }
            return M("yaf_shop_category")->where($where)->update(array("is_delete" => 1));
        } else {
            return false;
        }
    }

    /*
     * 添加产品服务
     */

    public static function addProductServe($data) {
        $data = array(
            'name' => trim($data['name']),
            'content' => isset($data['content']) ? trim($data['content']) : "",
            'status' => intval($data['status']),
            'ctime' => time(),
        );
        $serve_id = M("yaf_shop_product_serve")->insert($data);
        if ($serve_id) {
            return $serve_id;
        } else {
            return false;
        }
    }

    //获取产品服务
    public static function productServeList($start = 0, $pageSize = 10, $where, $isGetCount = false, $field = "*") {
        $mod = M("yaf_shop_product_serve");
        if ($isGetCount) {//计算总条数
            $count = $mod->where($where)->getRowCount();
            return $count;
        } else {
            $list = $mod->field($field)->where($where)->limit($start, $pageSize)->getAll();
            return $list;
        }
    }

    //删除取产品服务
    public static function delProductServe($id) {
        if (is_array($id)) {
            $ids = implode(",", $id);
            $where = "id in (" . $ids . ") ";
        } else {
            $where = "id = '" . $id . "' ";
        }
        return M("yaf_shop_product_serve")->where($where)->delete();
    }

    //修改产品服务
    public static function updataProductServe($id, $post) {
        $default_array = array('name', 'content', 'status');
        $data = array();
        foreach ($post as $post_key => $post_v) {
            if (in_array($post_key, $default_array)) {
                $data[$post_key] = $post_v;
            }
        }
        $data['update_time'] = time();
        $where = " id='" . $id . "'";
        return M("yaf_shop_product_serve")->where($where)->update($data);
    }

    //获取产品服务
    public static function getProductServeById($brand_id, $field = "*") {
        return M("yaf_shop_product_serve")->field($field)->where(array("id" => $brand_id))->getOne();
    }

    public static function addAttributeValues($data){
        $data = array(
            'product_id' => trim($data['product_id']),
            'attr_id' => isset($data['attr_id']) ? intval($data['attr_id']) : 0,
            'attr_name' => isset($data['attr_name']) ? trim($data['attr_name']) : "",
            'attr_value_id' => 0,
            'attr_value_name' => isset($data['attr_id']) ? trim($data['attr_value_name']) : "",
            'ctime' => time(),
        );
        $serve_id = M("yaf_shop_product_attribute")->insert($data);
        if ($serve_id) {
            return $serve_id;
        } else {
            return false;
        }
    }
    //添加产品属性
    public static function addAttributeValuesNew($product_id,$data){
        $sql = "INSERT INTO yaf_shop_product_attribute (product_id, attr_id, attr_name, attr_value_id, attr_value_name, is_default, ctime) VALUES";
        foreach ($data as $key=>$item) {
            if(isset($item['attr_v_list']) && $item['attr_v_list']){
                $item['attr_v_list'] = array_flip(array_flip($item['attr_v_list']));
                foreach($item['attr_v_list'] as $k=>$v){
                    $sql .= "( '".$product_id."', '".$key."', '".$item['attr_name']."', '0', '".$v."', '0', UNIX_TIMESTAMP()),";
                }
            }else{
                return false;
            }
        }
        $new_sql = substr($sql,0,strlen($sql)-1);
        $serve_id = M("yaf_shop_product_attribute")->query($new_sql,array(),false);
        if ($serve_id) {
            return $serve_id;
        } else {
            return false;
        }
    }
    //删除产品属性
    public static function delAttributeValuesNew($product_id,$attr_id) {
        return M("yaf_shop_product_attribute")->where("product_id='".$product_id."' AND attr_id='".$attr_id."'")->delete();
    }


    /*
     * 添加规格
     */
    public static function addAttribute($data) {
        $data = array(
            'name' => trim($data['name']),
            'content' => isset($data['content']) ? trim($data['content']) : "",
            'type' => isset($data['status']) ? intval($data['status']) : "1",
            'status' => intval($data['status']),
            'ctime' => time(),
        );
        $serve_id = M("yaf_shop_attribute")->insert($data);
        if ($serve_id) {
            return $serve_id;
        } else {
            return false;
        }
    }

    //获取规格列表
    public static function attributeList($start = 0, $pageSize = 10, $where, $isGetCount = false, $field = "*") {
        $mod = M("yaf_shop_attribute");
        if ($isGetCount) {//计算总条数
            $count = $mod->where($where)->getRowCount();
            return $count;
        } else {
            $list = $mod->field($field)->where($where)->limit($start, $pageSize)->getAll();
            return $list;
        }
    }

    //删除规格
    public static function delAttribute($id) {
        if (is_array($id)) {
            $ids = implode(",", $id);
            $where = "id in (" . $ids . ") ";
        } else {
            $where = "id = '" . $id . "' ";
        }
        return M("yaf_shop_attribute")->where($where)->delete();
    }

    //修改规格
    public static function updateAttribute($id, $post) {
        $default_array = array('name', 'content', 'status',"type");
        $data = array();
        foreach ($post as $post_key => $post_v) {
            if (in_array($post_key, $default_array)) {
                $data[$post_key] = $post_v;
            }
        }
        $data['update_time'] = time();
        $where = " id='" . $id . "'";
        return M("yaf_shop_attribute")->where($where)->update($data);
    }

    //获取规格
    public static function getAttributeById($brand_id, $field = "*") {
        return M("yaf_shop_attribute")->field($field)->where(array("id" => $brand_id))->getOne();
    }



}
