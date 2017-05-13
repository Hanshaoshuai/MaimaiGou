<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/30
 * Time: 14:06
 */
class OrderModel extends BaseModel {

    public static function getOrderInfoById($order_id, $user_id) {
        $where = array("id" => $order_id, "user_id" => $user_id);
        return M("yaf_shop_order")->where($where)->getOne();
    }

    //根据用户ID 查找用户所有订单详细信息
    public function getOrderAll($params, $getCount = FALSE) {
        $uid = $params['uid'];
        if ($getCount) {
            return M("yaf_shop_order_product")->where(array("user_id" => $uid))->getRowCount();
        } else {
            $limit1 = ($params['page'] - 1) * $params['pageSize'];
            $sql = "select * from yaf_shop_order_product  where user_id='" . $uid . "' order by update_time desc limit " . $limit1 . "," . $params['pageSize'];
            return M()->query($sql, array());
        }
    }

    //根据用户id及 付款状态 查找订单
    public function getOrderPayment($params, $getCount = FALSE) {
        $uid = $params['uid'];
        if ($getCount) {
            return M("yaf_shop_order_product")->where(array("user_id" => $uid, "order_status" => "0"))->getRowCount();
        } else {
            $limit1 = ($params['page'] - 1) * $params['pageSize'];
            $sql = "select * from yaf_shop_order_product where order_status=0 and user_id='" . $uid . "' order by update_time desc limit " . $limit1 . "," . $params['pageSize'];
            return M()->query($sql, array());
        }
    }

    //根据用户id及 配送状态 查找待发货/待收货订单
    public function getOrderShipping($params, $status, $getCount = FALSE) {
        $uid = $params['uid'];
        if ($getCount) {
            return M("yaf_shop_order_product")->where(array("user_id" => $uid, "order_status" => $status))->getRowCount();
        } else {
            $limit1 = ($params['page'] - 1) * $params['pageSize'];
            $sql = "select * from yaf_shop_order_product where order_status='" . $status . "' and user_id='" . $uid . "' order by update_time desc limit " . $limit1 . "," . $params['pageSize'];
            return M()->query($sql, array());
        }
    }

    //根据用户id及 配送状态 查找待发货/待收货订单
    public static function getProductOrderList($start = 0, $pageSize = 10, $where, $isGetCount = false, $field = "*") {
        if ($isGetCount) {
            return M("yaf_shop_order")->where($where)->getRowCount();
        } else {
            $sql = "select " . $field . " from yaf_shop_order_product where " . $where . " order by update_time desc  limit " . $start . "," . $pageSize;
            return M()->query($sql, array());
        }
    }

    //根据用户id 及评论状态查找未评论订单
    public function getOrderCommet($params, $getCount = FALSE) {
        $uid = $params['uid'];
        if ($getCount) {
            return M("yaf_shop_order_product")->where(array("user_id" => $uid, "order_status" => 4))->getRowCount();
        } else {
            $limit1 = ($params['page'] - 1) * $params['pageSize'];
            $sql = "select * from yaf_shop_order_product where order_status=3 and user_id='" . $uid . "' limit " . $limit1 . "," . $params['pageSize'];
            return M()->query($sql, array());
        }
    }

    //根据用户ID keyWord 查找用户订单详细信息
    public function getSearchOrder($params, $getCount = FALSE) {
        $uid = $params['uid'];
        if (!empty($params['keyWords'])) {
            if ($getCount) {
                $sql = "select count(*) count from yaf_shop_order_product  where user_id='" . $uid . "' and product_name like '%" . $params['keyWords'] . "%'";
                return M()->query($sql, array());
            } else {
                $limit1 = ($params['page'] - 1) * $params['pageSize'];
                $sql = "select * from yaf_shop_order_product where user_id='" . $uid . "' and product_name like '%" . $params['keyWords'] . "%' limit " . $limit1 . "," . $params['pageSize'];
                return M()->query($sql, array());
            }
        }
    }

    //根据订单id查找订单评论状态
    public function getCommetStatus($OrderId) {
        return M("yaf_shop_comment")->field("status")->where(array("orders_id" => $OrderId))->getOne();
    }

    //查找用户已购买的产品
    public static function getBuyProduct($params) {
        $uid = $params['uid'];
        $sql = "select product_id,product_img,product_name,price from yaf_shop_order_product where order_status!=0 and user_id='" . $uid . "'order by end_time desc limit 3";
        return M()->query($sql, array());
    }

    //获取订单数
    public static function getOrderCount($where) {
        return M("yaf_shop_order_product")->where($where)->getRowCount();
    }

    //获取订单金额
    public static function getOrderAmount($where) {
        return M("yaf_shop_order_product")->field("sum(price) as amount")->where($where)->getOne();
    }

    //获取产品订单信息=
    public static function getProductOrderInfo($where,$field="*") {
        return M("yaf_shop_order_product")->field($field)->where($where)->getOne();
    }

    //获取物流公司
    public static function getExpress($where) {
        return M("yaf_system_express")->field("name,com ")->where($where)->getOne();
    }

    //订单列表
    public function getOrderList($params = array()) {
        $where = " where  1=1 ";
        $a = false;
        // $limit=4;
        if (isset($params['order_product_sn']) && !empty($params['order_product_sn'])) {
            $where .= " and a.order_product_sn LIKE '%" . $params['order_product_sn'] . "%'";
            $a = true;
        }
        if (isset($params['order_status']) && (!empty($params['order_status']) || $params['order_status'] === "0")) {
            $where .= " and a.order_status='" . $params['order_status'] . "'";
            $a = true;
        }
        if (isset($params['payment_status']) && (!empty($params['payment_status']) || $params['payment_status'] === "0")) {
            $where .= " and a.payment_status='" . $params['payment_status'] . "'";
            $a = true;
        }
        if (isset($params['payment_method']) && !empty($params['payment_method'])) {
            $where .= " and d.payment_method='" . $params['payment_method'] . "'";
            $a = true;
        }
        if (isset($params['endTime']) && $params['endTime'] != "") {
            $where .= " and a.ctime<'" . strtotime($params['endTime']) . "'";
            $a = true;
        }
        if (isset($params['beginTime']) && $params['beginTime'] != "") {
            $where .= " and a.ctime<'" . strtotime($params['beginTime']) . "'";
            $a = true;
        }
        if (isset($params['phone'])) {
            $where .= " and e.phone like'%" . $params['phone'] . "%'";
            $a=true;
        }
        if ($a == true) {
            $where .= " ";
            $limit = "";
        }else{
            $where .= " and a.is_delete=0 ";
            $limit = " limit 0,500";
        }

        $url = \Yaf_Application::app()->getConfig()->qiniu->imgUrl;
        $sql = "SELECT
            a.id,a.product_id,a.order_id,a.order_product_sn,a.product_name,a.product_num,CONCAT('" . $url . "',b.product_img) as product_img,
            a.price,a.order_status,
            a.payment_status,
            a.shipping_status,
            a.ctime,
            c.name,
            a.pay_order_num,
			a.pay_time
        FROM
	yaf_shop_order_product as a
        INNER JOIN  yaf_shop_product as b ON a.product_id=b.id
        INNER JOIN yaf_shop_category as c ON c.id=b.catid
        INNER JOIN yaf_user_user as e ON a.user_id=e.id
        " . $where . "
        order by a.id desc " . $limit;
        $O = M();
        $rs = $O->query($sql);
        // var_dump($this->getCategoryAllId());
        //exit;
        return $rs;
    }

    public function getOrderInfo($id) {
        $m = M("yaf_shop_order");
        $sql = "select * from yaf_shop_order  where id='" . $id . "'";
        $rs = $m->query($sql);
        return $rs;
    }

    public function getOrderShopInfo($order_product_sn) {
        $m = M();
        $url = \Yaf_Application::app()->getConfig()->qiniu->imgUrl;
        $sql = "select a.*,CONCAT('" . $url . "',b.product_img) as p_img from yaf_shop_order_product as a,yaf_shop_product as b   where a.product_id=b.id and a.order_product_sn='" . $order_product_sn . "'";
        $rs = $m->query($sql);
        if(!empty($rs)){
            $cartList_v['attr'] = json_decode($rs[0]['attr'], true);
            $cartList_v["attr_show"] = "";
            if (!empty($cartList_v['attr'])) {
                foreach ($cartList_v['attr'] as $attr_key => $attr_v) {
                    if (!empty($cartList_v["attr_show"])) {
                        $cartList_v["attr_show"] .= "/";
                    }
                    if ($attr_v["attr_value"]["attr_value_name"] && $attr_v['attr_name']) {
                        $cartList_v["attr_show"] .= $attr_v['attr_name'] . ":" . $attr_v["attr_value"]["attr_value_name"];
                    }
                }
            }
            $rs[0]['attr'] = $cartList_v["attr_show"];
        }
        return $rs;
    }

    //获取收货地址信息
    public function getAddress($id) {
        $m = M();
        $sql = "select b.* from yaf_shop_order_product as a left join yaf_user_address as b on a.address_id=b.id where a.id='" . $id . "'";
        $rs = $m->query($sql);
        return $rs;
    }

    //获取订单商品总数量以及金额
    public function getOneOrderCount($order_id) {
        $m = M();
        $sql = "select sum(product_num) as product_num,sum(price) as price from yaf_shop_order_product where order_id=" . $order_id;
        $rs = $m->query($sql);
        return $rs;
    }

    //添加物流信息
    public function logisticsInsert($params) {
        $data = array();
        $data['logistics_order'] = $params['logistics_order'];
        $data['logistics_com'] = $params['logistics_com'];
        $data['shipping_status'] = 1;
        $data['order_status'] = 2;

        $where = array('id' => $params['id']);
        $M = M("yaf_shop_order_product");
        $rs = $M->where($where)->update($data);
        return $rs;
    }

    //生成订单
    public function createOrder($params) {
        try {
            $this->checkParams($params);
            $shop_coupon = $params['shop_coupon'];
            $order_amount = $params['order_amount'];
            //优惠券是否重复
            if (count(array_filter($shop_coupon)) != count(array_unique(array_filter($shop_coupon)))) {
                throw new PDOException("优惠券重复", "2101");
            }
            $couponList = $this->getCouponList(array_filter($shop_coupon));
            $cartList = $this->cartList(array_keys($shop_coupon));
            if (empty($cartList)) {
                throw new PDOException('购物车商品不存在', "2001");
            }
            $this->checkCoupon(array_filter($shop_coupon), $couponList, $cartList);
            //订单总金额
            $priceSum = 0;
            foreach ($cartList as $cartV) {
                $priceSum += $cartV['price'] * $cartV['qty'];
            }
            //订单优惠总金额
            $counponSum = 0;
            foreach ($couponList as $couponV) {
                $counponSum += $couponV['price'];
            }
            if ($order_amount != ($priceSum - $counponSum)) {
                throw new PDOException("订单金额不一致", '2201');
            }
            //创建订单
            $orderData['address_id'] = $params['address_id'];
            $orderData['order_amount'] = $priceSum;
            $orderData['pay_amount'] = $priceSum - $counponSum;
            $orderData['payment_sn'] = $this->paymentSn($params['user_id']);
            $orderData['user_id'] = $params['user_id'];
            $orderData['couponlist'] = $couponList;
            $orderData['cartlist'] = $cartList;
            $orderData['shop_coupon'] = $shop_coupon;
            $orderSt = $this->orderMake($orderData);          
            if (FALSE !== $orderSt) {
                $paymentData = array();
                $paymentData['order_id'] = $orderSt['data']['order_id'];
                $paymentData['maidou'] = $priceSum - $counponSum;
                $paymentData['user_id'] = $params['user_id'];
                //现金支付
              
                if (isset($params['pay_type']) && $params['pay_type'] == "cash") {
                    $paymentData['amount'] = $priceSum - $counponSum;
                    $paymentData['channel'] = $params['channel'];
                    if (!isset($params['channel']) && ($params['channel'] != "upacp" || $params['channel'] != "alipay")) {
                        echo json_encode(array("code" => "1001", "data" => array(), "msg" => "channel 参数错误"));
                        exit;
                    }
                    // $params['amount'] = 1;
                    $paymentData['orderno'] = date("YmdHis") . rand(1, 9999);
                    $returnCha = $this->cashPayment($paymentData);
                    if ($returnCha['code'] == "1000") {
                        $s = M("yaf_shop_order");
                        $s->where(array("id" => $paymentData['order_id']))->update(array("pay_order_num" => $returnCha['data']['id'],"pay_time"=>time(), "payment_method" => $params['channel']));
                        M("yaf_shop_order_product")->where(array("order_id" => $paymentData['order_id']))->update(array("pay_order_num"=>$returnCha['data']['id'],"payment_method" => $params['channel']));
                    }
                    return json_encode($returnCha);
                }
                //麦豆支付
                return $this->orderPayment($paymentData);
            }
            throw new PDOException("订单提交失败", "2202");
        } catch (PDOException $e) {
            return json_encode(array("data" => "", "code" => $e->getCode(), "msg" => $e->getMessage()));            
        }
    }

    //订单号
    public function orderSn($uid) {
        $_pre = "MMG";
        return $_pre . date("YmdHis") . $uid . rand(1, 9999);
    }

    //支付单号
    public function paymentSn($uid) {
        $_pre = "MMG";
        return $_pre . date("YmdHis") . $uid . rand(1, 9999);
    }

    //获取优惠券列表
    public function getCouponList($params) {
        if (empty($params)) {
            return array();
        }
        $M = M();
        $couponInfoSql = "select * from yaf_user_coupon where id in(" . implode(',', $params) . ")";
        $rs = $M->query($couponInfoSql);
        $rsArr = array();
        foreach ($rs as $v) {
            $rsArr[$v['id']] = $v;
        }
        return $rsArr;
    }

    //检查订单参数
    public function checkParams($params) {
        if (!isset($params['shop_coupon']) || !is_array($params['shop_coupon']) || empty($params['shop_coupon'])) {
            throw new PDOException("shop_coupon  not array or cart not null", "1001");
        }
        if (!isset($params['user_id']) || empty($params['user_id'])) {
            throw new PDOException("user_id  not null", "1001");
        }
        if (!isset($params['address_id']) || empty($params['address_id'])) {
            throw new PDOException("address_id  not null", "1001");
        }
        if (!isset($params['order_amount']) || empty($params['order_amount'])) {
            throw new PDOException("order_amount  not  null", "1001");
        }
    }

    //检查优惠券是否可用
    public function checkCoupon($params, $couponList, $cartList) {
        foreach ($params as $key => $couponV) {
            if (!isset($couponList[$couponV])) {
                throw new PDOException("优惠券不存在", "2102");
            }
            if ($couponList[$couponV]['status'] == 2) {
                throw new PDOException("优惠券过期", "2105");
            }
            if ($couponList[$couponV]['status'] == 3) {
                throw new PDOException("优惠券已被使用", "2104");
            }
            if ($couponList[$couponV]['status'] != 1) {
                throw new PDOException("优惠券异常", "2103");
            }
            if ($couponList[$couponV]['amount_limit'] > ($cartList[$key]['qty'] * $cartList[$key]['price'])) {
                throw new PDOException("未满足使用优惠券条件", "2106");
            }
        }
    }

    //获取购物车商品
    public function cartList($params) {
        $M = M("yaf_shop_cart");
        $sql = "select a.*,b.name,b.img from yaf_shop_cart as a inner join yaf_shop_product as b on a.pid=b.id where a.id in(" . implode(",", $params) . ")";
        $rs = $M->query($sql);
        $rsArr = array();
        foreach ($rs as $v) {
            $rsArr[$v['id']] = $v;
        }
        return $rsArr;
    }

    //订单生成
    public function orderMake($params) {
        $conn = M()->getDbh();
    
        try {
            $conn->beginTransaction();
            //yaf_shop_order表添加订单信息
            $insertOrderSql = "insert into yaf_shop_order(product_amount,user_id,order_sn,order_amount,start_time,pay_order_num,address_id)"
                    . "VALUES('" . $params['pay_amount'] . "','" . $params['user_id'] . "','" . $params['payment_sn'] . "','" . $params['order_amount'] . "','" . time() . "','" . $params['order_amount'] . "','" . $params['address_id'] . "')";
            $shop_order = $conn->exec($insertOrderSql);
            if ($shop_order <= 0) {
                throw new PDOException("订单生成失败", "2202");
            }
            $order_id = $conn->lastInsertId();
            foreach ($params['shop_coupon'] as $key => $v) {
                $orderSn = $this->orderSn($params['user_id']);
                //yaf_shop_order_product表添加信息coupon_price
                if (isset($params['couponlist'][$v]['id'])) {
                    $coupon_id = $params['couponlist'][$v]['id'];
                    $coupon_price = $params['couponlist'][$v]['price'];
                    //更新优惠券信息
                    $updateCouponSql = "update yaf_user_coupon set status=3 where id='" . $coupon_id . "'";
                    $u = $conn->exec($updateCouponSql);
                    if ($u <= 0) {
                        throw new PDOException("优惠券更新失败", "2104");
                    }
                } else {
                    $coupon_id = "";
                    $coupon_price = "0.00";
                }
                $attr = json_encode(json_decode($params['cartlist'][$key]['attr'], true));
                $order_data = array(
                    "coupon_price"=>$coupon_price,
                    "coupon_id"=>$coupon_id,
                    "user_id"=>$params['cartlist'][$key]['user_id'],
                    "product_id"=> $params['cartlist'][$key]['pid'],
                    "product_type"=>$params['cartlist'][$key]['product_type'],
                    "order_id"=>$order_id,
                    "order_product_sn"=>$orderSn,
                    "product_name"=>$params['cartlist'][$key]['name'],
                    "product_num"=>$params['cartlist'][$key]['qty'],
                    "product_img"=>$params['cartlist'][$key]['img'],
                    "price"=>$params['cartlist'][$key]['price'],
                    "attr"=>$attr,
                    "owner_info"=>$params['cartlist'][$key]['owner_info'],
                    "ctime"=>time(),
                    "address_id"=>$params['address_id']
                );
                $rr = M("yaf_shop_order_product")->insert($order_data);
                if ($rr <= 0) {
                    throw new PDOException("订单生成失败", "2202");
                }
                //删除购物车记录
                $del = "delete from yaf_shop_cart where id='" . $params['cartlist'][$key]['id'] . "'";
                $d = $conn->exec($del);
                if ($d <= 0) {
                    throw new PDOException("购物车删除失败", "2203");
                }

                //写入优惠券使用记录(临时在创建订单时写记录，有成功回调的时候写入记录)
                if($coupon_id){
                    $coupon_data = array("coupon_id"=>$coupon_id,"status"=>1);
                    CouponModel::couponInsertUserRecords($params['cartlist'][$key]['user_id'],$coupon_data);
                }

            }       
            $conn->commit();            
            return array("data" => array('order_id' => $order_id), "code" => "1000", "msg" => 'success');
        } catch (PDOException $e) {            
            $conn->rollBack();
            return FALSE;            
        }
    }

    //麦豆支付
    public function orderPayment($paymentData) {
        $pay = new PaymentModel();
        $rs = $pay->maiDouPayment($paymentData);
        return $rs;
    }

    //单个产品麦豆重新支付
    public function orderRestartPayment($paymentData) {
        $pay = new PaymentModel();
        $rs = $pay->maiDouRestartPayment($paymentData);
        return $rs;
    }

    //现金支付
    public function cashPayment($paymentData) {
        $pay = new PaymentModel();
        $rs = $pay->PaymentPing($paymentData);
        return $rs;
    }

    public function hcreateOrder($params) {
        try {
            if (!isset($params['user_id']) || empty($params['user_id'])) {
                return json_encode(array("code" => "1001", "data" => array(), "msg" => "user_id not null"));
            }
            if (!isset($params['address_id']) || empty($params['address_id'])) {
                return json_encode(array("code" => "1001", "data" => array(), "msg" => "address_id not null"));
            }
            if (!isset($params['num']) || empty($params['num'])) {
                return json_encode(array("code" => "1001", "data" => array(), "msg" => "num not null"));
            }
            if (!isset($params['attr']) || empty($params['attr'])) {
                $params['attr'] = "";
               // return json_encode(array("code" => "1001", "data" => array(), "msg" => "attr not null"));
            }
            if (!isset($params['order_amount']) || empty($params['order_amount'])) {
                return json_encode(array("code" => "1001", "data" => array(), "msg" => "order_amount not null"));
            }
            if (!isset($params['owner_info']) || empty($params['owner_info'])) {
                $params['owner_info'] = "";
            }
            $m = M();
            $paramsName = array("user_id" => "", "pid" => "", "attr" => "", "order_amount" => "", "num" => "", "owner_info" => "");
            $shopInfo = "select * from yaf_shop_product where id='" . $params['pid'] . "'";
            $rs = $m->query($shopInfo);
            $rs = $rs[0];
            $order_amount = $rs['sell_price'] * $params['num'];
            $paymentSn = $this->paymentSn($params['user_id']);
            $orderSn = $this->orderSn($params['user_id']);
            //优惠券操作
            $coupon_id = "";
            $coupon_price = "0.00";
            if (isset($params['coupon_id']) && !empty($params['coupon_id'])) {
                $coupon_id = $params['coupon_id'];
                $sql = "select * from yaf_user_coupon where id='" . $coupon_id . "'";
                $coupon = $m->query($sql);
                if (empty($coupon)) {
                    return json_encode(array("code" => "2102", "data" => array(), "msg" => "优惠券不存在"));
                }

                if ($coupon[0]['status'] == 2) {
                    throw new PDOException("优惠券过期", "2105");
                }
                if ($coupon[0]['status'] == 3) {
                    throw new PDOException("优惠券已被使用", "2104");
                }
                if ($coupon[0]['status'] != 1) {
                    throw new PDOException("优惠券异常", "2103");
                }
                if ($order_amount < $coupon[0]['amount_limit']) {
                    return json_encode(array("code" => "2106", "data" => array(), "msg" => "未满足使用优惠券条件"));
                }
                $coupon_price = $coupon[0]['price'];
            }

            $product_amount = $order_amount - $coupon_price;
            $conn = $m->getDbh();
            //订单数据写入
            try {
                $conn->beginTransaction();
                //yaf_shop_order表添加订单信息
                $insertOrderSql = "insert into yaf_shop_order(product_amount,user_id,order_sn,order_amount,start_time,pay_order_num,address_id)"
                        . "VALUES('" . $product_amount . "','" . $params['user_id'] . "','" . $paymentSn . "','" . $order_amount . "','" . time() . "','" . $order_amount . "','" . $params['address_id'] . "')";
                $r = $conn->exec($insertOrderSql);
                if ($r <= 0) {
                    throw new PDOException("支付订单生成失败");
                }
                $order_id = $conn->lastInsertId();
                //yaf_shop_order_product表添加信息
                $insertOrderSql1 = "insert into yaf_shop_order_product(coupon_price,coupon_id,user_id,product_id,product_type,order_id,order_product_sn,product_name,product_num,product_img,price,attr,owner_info,ctime,address_id)"
                        . "VALUES('" . $coupon_price . "','" . $coupon_id . "','" . $params['user_id'] . "','" . $rs['id'] . "','" . $rs['product_type'] . "','" . $order_id . "','" . $orderSn . "','" . $rs['name'] . "','" . $params['num'] . "','" . $rs['img'] . "','" . $rs['sell_price'] . "','" . $params['attr'] . "','" . $params['owner_info'] . "','" . time() . "','" . $params['address_id'] . "')";
                $rr = $conn->exec($insertOrderSql1);
                if ($rr <= 0) {
                    throw new PDOException("商品订单生成失败");
                }
                if ($coupon_id != "") {
                    $updateCouponSql = "update yaf_user_coupon set status=3 where id='" . $coupon_id . "'";
                    $u = $conn->exec($updateCouponSql);
                    if ($u <= 0) {
                        throw new PDOException("优惠券更新失败", "2104");
                    }
                }
                $conn->commit();
                $paymentData = array();
                $paymentData['order_id'] = $order_id;
                $paymentData['maidou'] = $order_amount;
                $paymentData['user_id'] = $params['user_id'];
                return $this->orderPayment($paymentData);
                //return array("data" => array('order_id' => $order_id), "code" => "1000", "msg" => 'success');
            } catch (PDOException $e) {
                $conn->rollBack();
                //return FALSE;
                return json_encode(array("data" => "", "code" => "2001", "msg" => $e->getMessage()));
            }
        } catch (PDOException $e) {
            return json_encode(array("data" => "", "code" => "2001", "msg" => $e->getMessage()));
        }
    }

    public function rePayment($params) {
        $id = $params['order_id'];
        $where['id'] = $id;
        $m = M("yaf_shop_order");
        $r = $m->where($where)->getOne();
        if (empty($r)) {
            return FALSE;
        }
        $paymentData = array();
        $paymentData['order_id'] = $r['id'];
        $paymentData['maidou'] = $r['order_amount'];
        $paymentData['user_id'] = $r['user_id'];
        if ($params['payment_method'] == "maidou") {
            return $this->orderPayment($paymentData);
        } elseif ($params['payment_method'] == "alipay" || $params['payment_method'] == "upacp") {
            $pay_order_num = $r['pay_order_num'];
            $p = new Ping();
            $cah = $p->checkOrder($pay_order_num);
            return json_encode(array("data" => $cah, "code" => array(), "msg" => "success"));            
        } else {
            return FALSE;
        }
    }

    //拆分后产品单个支付
    public function restartPayment($params) {
        ;
        $id = $params['product_order_id'];
        $where['id'] = $id;
        $m = M("yaf_shop_order_product");
        $r = $m->where($where)->getOne();        
        if (empty($r)) {
            return FALSE;
        }
        if ($r['order_status'] != 0) {
            return json_encode(array("data" => "", "code" => "2001", "msg" => "不能重复支付！"));
        }
        $paymentData = array();
        $paymentData['product_order_id'] = $r['id'];

        //没有产品订单总金额  临时计算
        if ($r['total_amount'] == 0) {
            $paymentData['maidou'] = $r['price'] * $r['product_num'];
        } else {
            $paymentData['maidou'] = $r['total_amount'];
        }
        $paymentData['user_id'] = $r['user_id'];
        if ($params['payment_method'] == "maidou") {

            return $this->orderRestartPayment($paymentData);
        } elseif ($params['payment_method'] == "alipay" || $params['payment_method'] == "upacp") {
            $paymentData['orderno'] = date("YmdHis") . rand(1, 9999);
            $paymentData['channel'] = $params['payment_method'];
            $paymentData['amount'] = $paymentData['maidou'];
            $returnCha = $this->cashPayment($paymentData);
            if ($returnCha['code'] == "1000") {
                $s = M("yaf_shop_order_product");
                $s->where(array("id" => $paymentData['product_order_id']))->update(array("pay_order_num" => $returnCha['data']['id'], "payment_method" => $params['payment_method']));
            }
            return json_encode($returnCha);
            //return $returnCha;
           // return json_encode(array("data" => $returnCha, "code" => "1000", "msg" => "success"));
            //$return = json_decode($cah, true);
        } else {
            return FALSE;
        }
    }


    public static function updateProductOrder($product_order_id,$post){
        $product_order_db = M("yaf_shop_order_product");
        $default_array = array(  'price', 'payment_method', 'payment_status', 'order_status', 'shipping_status',
            'shipping_id', 'shipping_method', 'logistics_com', 'logistics_order', 'confirm_time', 'shipping_time', 'end_time', 'attr',
            'owner_info', 'remark', 'pay_time', 'pay_order_num', 'address_id','update_time'
        );
        $data = array();
        foreach ($post as $post_key => $post_v) {
            if (in_array($post_key, $default_array)) {
                $data[$post_key] = $post_v;
            }
        }
        $data['update_time'] = time();
        $affected = $product_order_db->where('id=' . $product_order_id)->update($data);
        if($affected){
            return true;
        }else{
            return false;
        }
    }
    public function orderDel($id){
        $O=M("yaf_shop_order_product");
        $where['id']=$id;
        $rs=$O->where($where)->update(array("is_delete"=>1));
        $data = array();
        if ($rs > 0) {
            $msg = "操作成功";
            $code = "1000";
        } else {
            $msg = "操作失败";
            $code = "1001";
        }
        return json_encode(array("data" => $data, "msg" => $msg, "code" => $code));
    }

    //根据用户id及 配送状态 查找待发货/待收货订单[临时确认收货]
    public static function getProductOrderList_new($start = 0, $pageSize = 10, $where, $isGetCount = false, $field = "*") {
        if ($isGetCount) {
            return M("yaf_shop_order")->where($where)->getRowCount();
        } else {
            $list = M("yaf_shop_order_product")->field($field)->where(" order_status=2 AND pay_time<1493913600")->order('id desc')->limit($start,$pageSize)->getAll();
            return$list;
        }
    }
    //获取物流列表
    public  function getKuaidi(){
        $sql="select * from yaf_system_express where status=1";
        $M=M();
        $r=$M->query($sql);
        return $r;
    }	


}
