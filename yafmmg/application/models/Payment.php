<?php

/**
 *
 * User: LF
 * Date: 2017/3/27
 */
class PaymentModel {

    private static $_instance;

    public function __construct() {
        $this->init();
    }

    private function init() {
        //$this->db = M("yaf_user_bankcard", "yaf_mmg");
    }

    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function create($params = array()) {
        $P = new Ping();
        $ch = $P->create($params);
        return $ch;
    }

    //麦豆支付
    public function maiDouPayment($params) {
        //参数判断
        if (!isset($params['order_id']) || empty($params['order_id'])) {
            return json_encode(array("data" => "", "code" => "1001", "msg" => "order_id is not null"));
        }
        if (!isset($params['maidou']) || empty($params['maidou'])) {
            return json_encode(array("data" => "", "code" => "1001", "msg" => "maidou is not null"));
        }
        if (!isset($params['user_id']) || empty($params['user_id'])) {
            return json_encode(array("data" => "", "code" => "1001", "msg" => "user_id is not null"));
        }
        //获取用户麦豆数量
        $maiDouCount = $this->getUserMaiDouCount($params['user_id']);
        if (empty($maiDouCount)) {
            return json_encode(array("data" => "", "code" => "1008", "msg" => "用户数据有误请联系管理人员"));
        }
        if ($params['maidou'] > ($maiDouCount['spend_maidou'] + $maiDouCount['reflect_maidou'])) {
            return json_encode(array("data" => array(), "code" => "3001", "msg" => "麦豆不足，请充值"));
        }

        //麦豆更新语句       
        if ($maiDouCount['spend_maidou'] >= $params['maidou']) {
            $spend_maidou = $maiDouCount['spend_maidou'] - $params['maidou'];
            $reflect_maidou = $maiDouCount['reflect_maidou'];
            $maidou_type = 1;
        } else {
            $spend_maidou = 0;
            $reflect_maidou = $maiDouCount['reflect_maidou'] - ($params['maidou'] - $maiDouCount['spend_maidou']);
            $maidou_type = 4;
        }
        if ($maiDouCount['spend_maidou'] == 0) {
            $maidou_type = 2;
        }
        $updateMaidouSql = "update yaf_user_profile set spend_maidou='" . $spend_maidou . "',reflect_maidou='" . $reflect_maidou . "' where uid=" . $params['user_id'];
        //更新订单商品表
        $updateOrderStatusSql = "update yaf_shop_order_product set payment_status=1,order_status=1,pay_time='" . time() . "',payment_method='maidou' where order_id='" . $params['order_id'] . "'";
        //更新订单主表
        $updateOrderSql = "update yaf_shop_order set payment_method='maidou',pay_time='" . time() . "',maidou_money='" . $params['maidou'] . "' where id='" . $params['order_id'] . "' and user_id='" . $params['user_id'] . "'";
        //添加麦豆消费记录
        $updateUserMaidoulLog = "insert into yaf_user_maidoul_log(user_id,maidou,maidou_type,record_type,ctime,notes) values(" . $params['user_id'] . "," . $params['maidou'] . "," . $maidou_type . ",7," . time() . ",'线上消费')";
        $conn = M()->getDbh();
        try {
            $conn->beginTransaction();
            $rs = $conn->exec($updateMaidouSql);
            if ($rs <= 0) {
                throw new PDOException("更新麦豆执行失败");
            }
            $rs2 = $conn->exec($updateOrderSql);
            if ($rs2 <= 0) {
                throw new PDOException("更新订单失败");
            }
            $rs1 = $conn->exec($updateOrderStatusSql);
            if ($rs1 <= 0) {
                throw new PDOException("更新订单失败1");
            }
            $rs3 = $conn->exec($updateUserMaidoulLog);
            if ($rs3 <= 0) {
                throw new PDOException("消费记录添加失败");
            }
            $conn->commit();
            return json_encode(array("data" => "", "code" => "1000", "msg" => 'success'));
        } catch (PDOException $e) {
            $conn->rollBack();
            return json_encode(array("data" => "", "code" => "1005", "msg" => $e->getMessage()));
        }
    }

    //麦豆重新付款支付
    public function maiDouRestartPayment($params) {

        //参数判断
        if (!isset($params['product_order_id']) || empty($params['product_order_id'])) {
            return json_encode(array("data" => "", "code" => "1001", "msg" => "order_id is not null"));
        }
        if (!isset($params['user_id']) || empty($params['user_id'])) {
            return json_encode(array("data" => "", "code" => "1001", "msg" => "user_id is not null"));
        }
        //获取用户麦豆数量
        $maiDouCount = $this->getUserMaiDouCount($params['user_id']);
        if (empty($maiDouCount)) {
            return json_encode(array("data" => "", "code" => "1008", "msg" => "用户数据有误请联系管理人员"));
        }
        if ($params['maidou'] > ($maiDouCount['spend_maidou'] + $maiDouCount['reflect_maidou'])) {
            return json_encode(array("data" => "", "code" => "3001", "msg" => "麦豆不足，请充值"));
        }
        //麦豆更新语句
        if ($maiDouCount['spend_maidou'] >= $params['maidou']) {
            $spend_maidou = $maiDouCount['spend_maidou'] - $params['maidou'];
            $reflect_maidou = $maiDouCount['reflect_maidou'];
        } else {
            $spend_maidou = 0;
            $reflect_maidou = $maiDouCount['reflect_maidou'] - ($params['maidou'] - $maiDouCount['spend_maidou']);
        }
        $updateMaidouSql = "update yaf_user_profile set spend_maidou='" . $spend_maidou . "',reflect_maidou='" . $reflect_maidou . "' where uid=" . $params['user_id'];
        //更新订单商品表
        $updateOrderStatusSql = "update yaf_shop_order_product set payment_status=1,order_status=1,payment_method='maidou' where id='" . $params['product_order_id'] . "'";
        //更新订单主表
        /* 重新支付  主表不做更新
          $updateOrderSql = "update yaf_shop_order set payment_method='maidou',pay_time='" . time() . "',maidou_money='" . $params['maidou'] . "' where id='" . $params['order_id'] . "' and user_id='" . $params['user_id'] . "'";
         */
        $conn = M()->getDbh();
        try {
            $conn->beginTransaction();
            $rs = $conn->exec($updateMaidouSql);
            if ($rs <= 0) {
                throw new PDOException("更新麦豆执行失败");
            }
            $rs1 = $conn->exec($updateOrderStatusSql);
            if ($rs1 <= 0) {
                throw new PDOException("更新订单失败1");
            }
            $conn->commit();
            return json_encode(array("data" => "", "code" => "1000", "msg" => 'success'));
        } catch (PDOException $e) {
            $conn->rollBack();
            return json_encode(array("data" => "", "code" => "1005", "msg" => $e->getMessage()));
        }
    }

    //获取用户麦豆信息
    public function getUserMaiDouCount($user_id) {
        $m = M("yaf_user_profile");
        $rs = $m->where("uid=?", array($user_id))->getOne();
        return $rs;
    }

    public function rechargeMaiDou() {
        
    }

    public function PaymentPing($input_data) {
        $p = new Ping();
        $cah = $p->create($input_data);
        if ($cah['code'] == "1000") {
            $C = M("yaf_recharge_card");
            if (isset($input_data['card_id']) && $input_data['card_id'] != "") {
                $cardInfo = $C->where(" id='" . $input_data['card_id'] . "' ")->getOne();
                if (!empty($cardInfo)) {
                    $data['maidou'] = $cardInfo['maidou'];
                    $data['type'] = 2;  //充值标记
                } else {
                    return array("code" => "1007", "msg" => "充值卡不存在", "data" => array());
                }
            } else {
                $data['maidou'] = $input_data['amount'];
                $data['type'] = 1;  //现金支付标记
            }
            $data['user_id'] = $input_data['user_id'];
            $data['order_sn'] = $this->orderSn($data['user_id']);
            $data['payment_method'] = $input_data['channel'];
            $data['price'] = $input_data['amount'];
            $return = json_decode($cah['data'], true);
            $data['pay_order_num'] = $return['id'];
            $m = M("yaf_recharge_record");
            $rs = $m->insert($data);
            if ($rs > 0) {
                return $cah;
            }
        }
        return $cah;
    }

    //订单号
    public function orderSn($uid) {
        $_pre = "MMG";
        return $_pre . date("YmdHis") . $uid . rand(1, 9999);
    }

    public function checkOrder($id) {
        $p = new Ping();
        $cah = $p->checkOrder($id);
        $return = json_decode($cah, true);
        if ($return['paid'] == true) {
            $m = M("yaf_recharge_record");
            $data['status'] = 1;
            $where['pay_order_num'] = $id;
            $rs = $m->where($where)->update($data);
            if ($rs > 0) {
                $m = M("yaf_recharge_record");
                $userInfo = $m->where($where)->getOne();
                $U = M("yaf_user_profile")->getDbh();
                $sql = "update yaf_user_profile set spend_maidou=spend_maidou+" . $userInfo['maidou'] . " where uid='" . $userInfo['user_id'] . "'";
                $rk = $U->exec($sql);
                if ($rk > 0) {
                    return array("code" => "1000", "msg" => "充值成功", "data" => array());
                }
            }
        }
        return array("code" => "1001", "msg" => "查询失败", "data" => array());
    }

    public function checkPaymentOrder($id) {
        $p = new Ping();
        $cah = $p->checkOrder($id);
        $return = json_decode($cah, true);
        if ($return['paid'] == true) {
            $m = M("yaf_recharge_record");
            $data['status'] = 1;
            $where['pay_order_num'] = $id;
            $rs = $m->where($where)->update($data);
            if ($rs > 0) {
                //查询商品订单信息
                $S = M("yaf_shop_order");
                $orderInfo = $S->where($where)->getOne();
                if (!empty($orderInfo)) {
                    $orderShop = M("yaf_shop_order_product");
                    $orderShopInfo = $orderShop->where(array('order_id' => $orderInfo['id']))->getAll();
                    if (!empty($orderShopInfo)) {
                       
                        foreach ($orderShopInfo as $v) {                             
                            if ($v['product_type'] == 5) {                                
                                $sql = "update yaf_user_profile set supermarket_maidou=supermarket_maidou+" . (3060 * $v['product_num']) . " where uid='" . $v['user_id'] . "' ";
                                $S->getDbh()->exec($sql);
                                $sql = "select * from yaf_user_user where id='" . $v['user_id'] . "'";
                                $U = M("yaf_user_user");
                                $userInfo = $U->where(array("id" => $v['user_id']))->getOne();
                                if ($userInfo['type'] == 0) {
                                    $sql = "update yaf_user_user set type=1 where id='" . $v['user_id'] . "'";
                                    $S->getDbh()->exec($sql);
                                }
                                $p_order_sql = "update yaf_shop_order_product set shipping_status=2,payment_status=1,order_status=5,update_time='" . time() . "' where id='" . $v['id'] . "'";
                            }else{
                                $p_order_sql = "update yaf_shop_order_product set payment_status=1,order_status=1,update_time='" . time() . "' where id='" . $v['id'] . "'";
                            }                            
                            $S->getDbh()->exec($p_order_sql);
                        }
                    }
                } else {
                    //拆单后的回调
                    $orderShop = M("yaf_shop_order_product");
                    $orderShopInfo = $orderShop->where($where)->getAll();
                    if (!empty($orderShopInfo)) {
                        foreach ($orderShopInfo as $v) {                                                        
                            if ($v['product_type'] == 5) {
                                $U = M("yaf_user_user");
                                $userInfo = $U->where(array("id" => $v['user_id']))->getOne();
                                if ($userInfo['type'] == 0) {
                                    $sql = "update yaf_user_user set type=1 where id='" . $v['user_id'] . "'";
                                    $S->getDbh()->exec($sql);

                                    //如果不是会员  充值后更新为会员  会员开始时间  结束时间  麦豆数
                                    $sql = "update yaf_user_profile set supermarket_maidou=supermarket_maidou+" . (3060 * $v['product_num']) . ",vip_start_time='" . time() . "',vip_end_time='" . strtotime("+1 year") . "' where uid='" . $v['user_id'] . "' ";
                                    $S->getDbh()->exec($sql);
                                }
                                $p_order_sql = "update yaf_shop_order_product set shipping_status=2,payment_status=1,order_status=5,update_time='" . time() . "' where id='" . $v['id'] . "'";
                            } else {
                                $p_order_sql = "update yaf_shop_order_product set payment_status=1,order_status=1,update_time='" . time() . "' where id='" . $v['id'] . "'";
                                //如果是会员  麦豆数
                                $sql = "update yaf_user_profile set supermarket_maidou=supermarket_maidou+" . (3060 * $v['product_num']) . " where uid='" . $v['user_id'] . "' ";
                                $S->getDbh()->exec($sql);
                            }
                            $S->getDbh()->exec($p_order_sql);
                        }
                    }
                }
                return array("code" => "1000", "msg" => "付款成功", "data" => array());
            }
        }
        return array("code" => "1001", "msg" => "付款失败", "data" => array());
    }

}
