<?php

class Bootstrap extends Yaf_Bootstrap_Abstract {

    protected $config;

    public function _initConfig() {

        $this->config = Yaf_Application::app()->getConfig();
       // Yaf_Registry::set("config", $config);
        //var_dump($config);exit;
        // 获取配置使用 Yaf_Application::app()->getConfig()
    }

    public function _initCore() {
        define('TB_PREFIX', 'yaf_');
        define('_PUBLIC_', '/public');
        define('LIB_PATH', APP_PATH . '/application/library');
        define('MODEL_PATH', APP_PATH . '/application/model');
        define('FUNC_PATH', APP_PATH . '/application/function');
        define('ADMIN_PATH', APP_PATH . '/application/modules/Admin');
        define('__APP__', "");
        define("HOST_NAME", "http://" . $_SERVER['HTTP_HOST']);
        define('_ADMIN_THEME_', '/application/modules/Admin');
        // Admin CSS, JS PATH
        define('_ADMIN_CSS_PATH_', '/admin/css');
        define('_ADMIN_JS_PATH_', '/admin/js');
        define('IMAGECODE_PATH',  APP_PATH . '/public/ImageCode/');
        define('IMAGECODE_URL',  "http://" . $_SERVER['HTTP_HOST'].'/public/ImageCode/');

    }

    public function _initDefaultName(Yaf_Dispatcher $dispatcher) {
        //$dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("index");
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        //$router = Yaf_Dispatcher::getInstance()->getRouter(); 
        //创建一个简单路由协议实例
        // $route = new Yaf_Route_Simple("m", "c", "a");
        //$route = new \Yaf\Route\Simple("m", "c", "a");
        //使用路由器装载路由协议
        //$router->addRoute("name", $route);
        /* $router = Yaf_Dispatcher::getInstance()->getRouter();
          $arr = array(
          "module" => "admin",
          "controller" => "admin",
          "action" => "index"
          );
          $route = new Yaf_Route_Regex('/^\/admin$/', $arr);
          $route1 = new Yaf_Route_Regex('/^\/$/', $arr);
          $router->addRoute("admin", $route);
          $router->addRoute("admin1", $route1);
         */

        // $router = Yaf_Dispatcher::getInstance()->getRouter();        
        /**
         * 添加配置中的路由
         */
        //$router->addConfig(Yaf_Registry::get("config")->routes);
        //$route = new Yaf_Route_Simple("m", "c", "a");
        // $router->addRoute("name", $route);
        /* 	
          // 创建路由器
          //$router = \Yaf\Dispatcher::getInstance()->getRouter();

          //创建一个简单路由协议实例
          $route = new Yaf_Route_Simple("m", "c", "a");
          //$route = new \Yaf\Route\Simple("m", "c", "a");
          //使用路由器装载路由协议
          $route->addRoute("name", $route);

          // 创建复杂的路由协议

          /*$route = new \Yaf\Route\Rewrite(
          'exp/:ident',
          array(
          'controller' => 'index',
          'action' => 'index'
          )
          );

          $router->addRoute('exp', $route); */
    }

    public function _initConstant() {
        define("API_KEY", "maitai"); //Apikey
        define("M_KEY", "mmg_51"); //麦太key
        define('UPLOAD_PATH', APP_PATH . '/public/upload/workflow');
        define("IMG_URL","http://onlzmxmxz.bkt.clouddn.com/");
    }

    public function _initFunctions(Yaf_Dispatcher $dispatcher) {
        Yaf_Loader::import('Common/functions.php');
        Yaf_Loader::import('Common/Basic_Functions.php');
        Yaf_Loader::import('Qiniu/autoload.php');
    }

    public function _initSmarty(Yaf_Dispatcher $dispatcher) {
      //  var_dump($this->config->smarty);exit;
        //命令行下基本不需要使用smarty
       // $smarty = new Smarty_Adapter(null, $this->config->smarty);
        //$smarty->registerFunction('function', 'truncate', array('Tools', 'truncate'));
       // $dispatcher->setView($smarty);
    }

}
