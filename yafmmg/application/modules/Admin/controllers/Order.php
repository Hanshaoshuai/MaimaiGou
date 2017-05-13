<?php

class OrderController extends AdminController {

    public function indexAction() {
        
    }

    public function checkPaymentOrderAction() {
        $id = $this->getPost("id");
        $pay = new PaymentModel();
        $rs = $pay->checkPaymentOrder($id);
        echo json_encode($rs, JSON_FORCE_OBJECT);
        exit;
    }

    //订单管理
    public function OrderManageAction() {
        $params = $this->getPost();
        //获取订单列表
        $m = new OrderModel();
        $list = $m->getOrderList($params);
        $this->assign("orderList", $list);
        //获取产品分类列表
        $P = new ProductModel();
        $kuaid=$m->getKuaidi();
         $this->assign("kuaidi",$kuaid);		
        //$categoryList=$P->categoryInfo(array());
        $this->assign("categoryList", array());
        //获取产品分类 以及分类下的详细信息  订单数量
        $this->assign('search', $params);
    }

    //订单详情
    public function OrderDetailedAction() {
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
        $order_product_sn = $this->get("order_product_sn");
        $m = new OrderModel();
        $shopInfo = $m->getOrderShopInfo($order_product_sn);
        $this->assign("shopInfo", $shopInfo[0]);
        $orderInfo = $m->getOrderInfo($shopInfo[0]['order_id']);
        $this->assign("orderInfo", $orderInfo[0]);
        $address = $m->getAddress($shopInfo[0]['id']);
        $this->assign("address", $address[0]);
        //$count = $m->getOneOrderCount($order_product_sn);
        //$this->assign("count", $count);
    }

    //物流信息
    public function logisticsInsertAction() {
        $params = $this->getPost();
        $m = new OrderModel();
        $rs = $m->logisticsInsert($params);
        $data = array();
        if ($rs > 0) {
            $msg = "操作成功";
            $code = "1000";
        } else {
            $msg = "操作失败";
            $code = "1001";
        }
        echo json_encode(array("data" => $data, "msg" => $msg, "code" => $code));
        exit;
    }

    public function orderUpdateAction() {
        $order_product_sn = $this->get("order_product_sn");
        $m = new OrderModel();
        $orderInfo = $m->getOrderInfo($order_product_sn);
        // var_dump($orderInfo);
        $this->assign("orderInfo", $orderInfo[0]);
    }

    public function orderDelAction() {
        $id = $this->getPost("id");
        $m = new OrderModel();
        $rs = $m->orderDel($id);
        echo $rs;
        exit;
    }

    public function importExclAction() {
        ini_set('max_execution_time', '0');
        if (!isset($_FILES['filename']['tmp_name'])) {
            echo "请选择文件";
            exit;
        }
        $file = $_FILES['filename'];
        $tmp_file = $file['tmp_name'];
        $file_types = explode(".", $file ['name']);
        $file_type = $file_types [count($file_types) - 1];
        /* 判别是不是.xls文件，判别是不是excel文件 */
        if (strtolower($file_type) != "xls") {
            $this->error('不是Excel文件，重新上传');
        }
        /* 设置上传路径 */
        $savePath = APP_PATH . '/public/upload/';
        /* 以时间来命名上传的文件 */
        $str = date('Ymdhis');
        $file_name = $str . "." . $file_type;
        /* 是否上传成功 */
        if (!copy($tmp_file, $savePath . $file_name)) {
            $this->error('上传失败');
        }
        $res = ExcelToArray($savePath . $file_name);
        unlink($savePath . $file_name);
        krsort($res);
        $rs = $this->writDb($res);
        exit;
    }

    private function writDb($res) {
        $a = 0;
        $M = M("");
        unset($res[0]);
        $arr = array();
        foreach ($res as $v) {
            $userInfo = $this->getUserInfo($v[0], $M);
            if (empty($userInfo)) {
                $arr[] = $v;
                continue;
            }
            $t = time();
            $address_id = $this->getAddress($userInfo['id'], $v, $M);
            if (!($address_id > 0)) {
                $arr[] = $v;
                continue;
            }
            $shopInfo = $this->getShopInfo($v[1], $M);
            if (empty($shopInfo)) {
                $arr[] = $v;
                continue;
            }
            $attr = $this->getAttr($v[2], $M);
            $shopOrder = array();
            $shopOrder['user_id'] = $userInfo['id'];
            $shopOrder['order_sn'] = $this->orderSn($userInfo['id']);
            $shopOrder['product_amount'] = $shopInfo['sell_price'];
            $shopOrder['order_amount'] = $shopInfo['sell_price'];
            $shopOrder['pay_amount'] = $shopInfo['sell_price'];
            $shopOrder['start_time'] = $t;
            $shopOrder['pay_time'] = $t;
            $shopOrder['confirm_time'] = $t;
            $shopOrder['pay_order_num'] = 1234556;
            $shopOrder['address_id'] = $address_id;
            $order_id = $this->insertShopOrder($shopOrder, $M);
            if ($order_id > 0) {
                
            } else {
                $arr[] = $v;
                continue;
            }
            if (empty($attr)) {
                $attr = "";
            } else {
                $attr = json_encode(array(array("attr_id" => $attr['attr_id'], "attr_name" => $attr['attr_name'], "attr_value" => array("attr_value_id" => $attr['id'], "attr_value_name" => $attr['attr_value_name']))));
            }
            $shopOrderProduct = array();
            $shopOrderProduct['user_id'] = $userInfo['id'];
            $shopOrderProduct['product_id'] = $v[1];
            $shopOrderProduct['product_type'] = $shopInfo['product_type'];
            $shopOrderProduct['order_id'] = $order_id;
            $shopOrderProduct['order_product_sn'] = $this->orderSn($userInfo['id']);
            $shopOrderProduct['product_name'] = $shopInfo['name'];
            $shopOrderProduct['total_amount'] = $shopInfo['sell_price'];
            $shopOrderProduct['product_num'] = 1;
            $shopOrderProduct['total_amount'] = $shopInfo['sell_price'];
            $shopOrderProduct['product_img'] = $shopInfo['product_img'];
            $shopOrderProduct['price'] = $shopInfo['sell_price'];
            $shopOrderProduct['payment_status'] = 1;
            $shopOrderProduct['order_status'] = 1;
            $shopOrderProduct['shipping_status'] = 0;
            $shopOrderProduct['confirm_time'] = $t;
            $shopOrderProduct['attr'] = $attr;
            $shopOrderProduct['owner_info'] = json_encode(array("owner_name" => $v[8], "owner_card_number" => $v[9])); //油卡信息
            $shopOrderProduct['remark'] = $t[10];
            $shopOrderProduct['pay_time'] = $t;
            $shopOrderProduct['ctime'] = $t;
            $shopOrderProduct['update_time'] = $t;
            $shopOrderProduct['address_id'] = $address_id;
            $shopOrderProduct['pay_order_num'] = 1234556;
            $r = $this->insertShopOrderProduct($shopOrderProduct, $M);
            if ($r > 0) {
                
            } else {
                $arr[] = $v;
                continue;
            }
            $a++;
        }
        echo "导完啦" . "<br>";
        echo "导入" . $a . "条<br>";
        file_put_contents("log/" . date("YmdHis") . "log.txt", json_encode($arr), FILE_APPEND);
    }

    private function getUserInfo($phone, $db) {
        $where['phone'] = $phone;
        return $db->table('yaf_user_user')->where($where)->getOne();
    }

    private function getAddress($uid, $data, $db) {
        $where['user_id'] = $uid;
        $area = str_replace("，", ",", $data[5]);
        $area = explode(",", $data[5]);
        $where['delivery_name'] = $data[4];
        $where['delivery_phone'] = $data[7];
        $where['delivery_province'] = $area[0];
        $where['delivery_urban'] = $area[1];
        $where['delivery_county'] = $area[2];
        $where['delivery_address'] = $data[6];
        $rs = $db->table('yaf_user_address')->where($where)->getOne();
        if (empty($rs)) {
            $data1['user_id'] = $uid;
            $data1['delivery_name'] = $data[4];
            $data1['delivery_phone'] = $data[7];
            $data1['delivery_province'] = $area[0];
            $data1['delivery_urban'] = $area[1];
            $data1['delivery_county'] = $area[2];
            $data1['delivery_address'] = $data[6];
            $data1['is_default'] = 1;
            $data1['ctime'] = time();
            return $this->addAddress($data1, $db);
        }
        return $rs['id'];
    }

    private function addAddress($data, $db) {
        return $db->table('yaf_user_address')->insert($data);
    }

    private function getShopInfo($pid, $db) {
        $where['id'] = $pid;
        return $db->table('yaf_shop_product')->where($where)->getOne();
    }

    private function getAttr($aid, $db) {
        $where['id'] = $aid;
        return $db->table('yaf_shop_product_attribute')->where($where)->getOne();
    }

    private function insertShopOrder($data, $db) {
        return $db->table('yaf_shop_order')->insert($data);
    }

    private function insertShopOrderProduct($data, $db) {
        return $db->table('yaf_shop_order_product')->insert($data);
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

    public function exportOrderAction() {
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
        @set_time_limit(1000);
        $params=$this->getPost();
        
        $m = M();
        $sql = "SELECT 
	a.order_product_sn,
	CONCAT('`',b.phone),	
	CONCAT('`',a.pay_order_num),
	a.payment_method ,
	if(a.payment_status=1,'已支付','未支付') ,
	' ' ,
	c.delivery_name ,
	CONCAT('`',c.delivery_phone ),		
	a.owner_info,
	a.owner_info as owner_info_i,
	CONCAT(c.delivery_province,c.delivery_urban,c.delivery_county,c.delivery_address) ,
	a.remark ,
	'无需发票' ,
	' 无',
	a.price*a.product_num,
	'发货处理',
	FROM_UNIXTIME(a.ctime, '%Y-%m-%d %H:%i:%S') as ctime,
	a.product_name,
	a.attr,
	a.product_num 	
from 
	yaf_shop_order_product as a   INNER JOIN	
	yaf_user_user as b on a.user_id=b.id INNER JOIN	
	yaf_user_profile as d on a.user_id=d.uid 	
	LEFT JOIN yaf_user_address as c on c.id=a.address_id	
where 
	a.payment_status=1
	and a.ctime>=" . strtotime($params['beginTime']) . "
	and a.ctime<" . strtotime($params['endTime']) . "
	";
        $rs = $m->query($sql);
        foreach ($rs as $key => $v) {
            $cartList_v['attr'] = json_decode($rs[$key]['attr'], true);
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
            $rs[$key]['attr'] = $cartList_v["attr_show"];
            $owner_info = json_decode($rs[$key]['owner_info'], true);
            $owner_info_i = json_decode($rs[$key]['owner_info_i'], true);
            if (!empty($owner_info)) {
                $rs[$key]['owner_info'] = "`" . $owner_info['owner_card_number'];
            }
            if (!empty($owner_info)) {
                $rs[$key]['owner_info_i'] = $owner_info_i['owner_name'];
            }
        }
        //$rs=array(array("1",2),array("1",2));
        $letter = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");
        $tableheader = array(
            "订单号", "用户名", "核账单号", "支付方式", "支付状态", "发货时间", "收件人", "收件人号码", "油卡账号", "持卡人",
            "收件地址", "备注", "是否需要发票", "发票抬头", "总金额", "是否需要发票1", "下单时间", "产品名",
            "产品属性", "产品数量"
        );
        excel($rs, $letter, $tableheader);
        //var_dump($rs);exit;
    }

    public function updateOrderAllAction() {

        ini_set('max_execution_time', '0');
        if (!isset($_FILES['filename']['tmp_name'])) {
            echo "请选择文件";
            exit;
        }
        $file = $_FILES['filename'];
        $tmp_file = $file['tmp_name'];
        $file_types = explode(".", $file ['name']);
        $file_type = $file_types [count($file_types) - 1];
        /* 判别是不是.xls文件，判别是不是excel文件 */
        if (strtolower($file_type) != "xls") {
            $this->error('不是Excel文件，重新上传');
        }
        /* 设置上传路径 */
        $savePath = APP_PATH . '/public/upload/';
        /* 以时间来命名上传的文件 */
        $str = date('Ymdhis');
        $file_name = $str . "." . $file_type;
        /* 是否上传成功 */
        if (!copy($tmp_file, $savePath . $file_name)) {
            $this->error('上传失败');
        }
        $res = ExcelToArray($savePath . $file_name);
        unlink($savePath . $file_name);
        unset($res[0]);        
        krsort($res);
        $rs = $this->uploadOrder($res);

        exit;
    }

    public function uploadOrder($res) {
        $a = 0;
        $M = M("");
        $arr = array();        
        foreach ($res as $v) {
            if (isset($v[0]) && $v[0] != "") {
                $where['order_product_sn'] = $v[0];
                $r = $M->table('yaf_shop_order_product')->where($where)->getOne();
              
                if (!empty($r)) {  
                    $rr = $M->table('yaf_shop_order_product')->where($where)->update(array('order_status' => 2, "shipping_status" => 1));                      
                    if ($rr > 0) {
                        $a++;
                    } else {
                        $arr[] = $v;
                    }
                } else {
                    $arr[] = $v;
                }
            }
        }
        echo "更新完成</br>";
        echo "更新数量" . $a;
        file_put_contents("log/update/" . date("YmdHis") . "log.txt", json_encode($arr), FILE_APPEND);
        exit;
    }

}
