<?php
use Db;
class IndexController extends Yaf_Controller_Abstract {
   public function indexAction() {//默认Action
       $conf=\Yaf_Application::app()->getConfig();
       var_dump($conf);exit;
       $m->test();
       $this->getView()->assign("content", "Hello World");
   }
}
?>