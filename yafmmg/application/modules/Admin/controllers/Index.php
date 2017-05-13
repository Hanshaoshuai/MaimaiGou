<?php

class IndexController extends AdminController {

    public function indexAction() {        
        if (isset($_SESSION['uid']) && ($_SESSION['uid'] == 1 || $_SESSION['uid'] == 2)) {
            
        } else {
            $this->login1();
        }
    }

    public function login1() {
        $this->display('login');
        exit;
    }

    public function unLoginAction() {
        session_unset();
        session_destroy();
        header("location:" . SITE_URL . "/index.php/Admin/Index/index");
        exit;
    }

    public function loginAction() {
        $params = $this->getPost();
        if ($params['username'] == "admin" && $params['pass'] == "114177") {
            $this->setSession("uid", 1);
            echo "1000";
        } elseif ($params['username'] == "kefu" && $params['pass'] == "kefu123123") {
            $this->setSession("uid", 1);
            echo "1000";			
        } elseif ($params['username'] == "yunying" && $params['pass'] == "maimaigo123") {
            $this->setSession("uid", 2);
            echo "1000";
        } else {
            echo "1001";
        }
        exit;
    }

    public function homeAction() {
        $register_count = UserModel::getRegisterUser('', '', '', true);
        $this->assign("register_count", $register_count);
        $param_arr = "order_status!=0";
        $order_count = OrderModel::getOrderCount($param_arr);
        $this->assign("order_count", $order_count);
        $order_amount = OrderModel::getOrderAmount($param_arr);
        $this->assign("order_amount", $order_amount['amount']);
    }

}
