<?php
ini_set('memory_limit','512M');
if(isset($_COOKIE['PHPSESSID'])&&$_COOKIE['PHPSESSID']!=""){    
    session_id($_COOKIE['PHPSESSID']);
}elseif(isset ($_POST['PHPSESSID'])&&$_POST['PHPSESSID']!=""){
    session_id($_POST['PHPSESSID']);
}
Yaf_Session::getInstance()->__get("abc");
define("APP_PATH",  realpath(dirname(__FILE__) . '/')); /* 指向public的上一级 */
define("SITE_URL", 'http://'.$_SERVER['SERVER_NAME'].str_replace('\\','/',substr(__DIR__,strlen($_SERVER['DOCUMENT_ROOT']))));
header("Access-Control-Allow-Origin: *");
try{
	$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");
	$app->bootstrap()->run();
} catch (Exception $e){
    echo "[" . date("Y-m-d H:i:s", time()) . "]". $e->getCode() . '--' . $e->getMessage() . "\r\n";
    //file_put_contents(APP_PATH.'/log/yaf.log',"[" . date("Y-m-d H:i:s", time()) . "]". $e->getCode() . '--' . $e->getMessage() . "\r\n" , FILE_APPEND);

}