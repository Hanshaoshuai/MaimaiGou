<?php

/*
 * 全局函数
 */

function I($key, $dft = '') {
    $a = explode('/', trim($key));
    $key = $a[0];
    $type = isset($a[1]) ? $a[1] : '';
    $a = explode('.', $key);
    $n = count($a);
    if ($n == 1) {
        if (isset($_POST[$a[0]])) {
            $dft = $_POST[$a[0]];
        } else {
            isset($_GET[$a[0]]) && $dft = $_GET[$a[0]];
        }
    } elseif ($n == 2) {
        if ($a[0] == 'get') {
            isset($_GET[$a[1]]) && $dft = $_GET[$a[1]];
        } elseif ($a[0] == 'post') {
            isset($_POST[$a[1]]) && $dft = $_POST[$a[1]];
        }
    }
    switch (strtolower($type)) {
        case 'i':
            return intval($dft);
        case 'f':
            return floatval($dft);
        case 'b':
            return !(empty($dft) || $dft == 'no' || $dft == 'false' || $dft == 'fail' || $dft == 'null' || $dft == 'NULL');
        case 'a':
            if (is_string($dft)) {
                return preg_split('#[| ,]+#', $dft);
            }
            if (count($dft) == 1 && $dft === '') {
                $dft = array();
            }
        default:
            return $dft;
    }
    return $dft;
}

function M($tabName = "", $db = "") {
    if (is_array($db)) {
        $conf = $db;
    } else {
        if ($db == "") {
            $db = \Yaf_Application::app()->getConfig()->mysql->defaultDb;
        }
        $conf = \Yaf_Application::app()->getConfig()->{$db};
    }
    return Db\Mysql::getInstance($conf, $tabName);
}

function getSession($key) {
    return Yaf_Session::getInstance()->__get($key);
}

function setSession($key, $val) {
    return Yaf_Session::getInstance()->__set($key, $val);
}

/*
 * 生成完整URL
 * 示例 U('act');  U('');
 * 示例 U('ctl/act',['p1'=>'v1']);
 * 示例 U('ctl/act','p1/v1');
 * 示例 U('/mod/ctl/act/p1/v1');
 */

function U($act, $par = null,$pattern=true) {    
    $act = trim($act);
    $url = array(\SITE_URL."/index.php");
    if (strlen($act) > 0 && substr($act, 0, 1) == '/') {
        $url[] = substr($act, 1);
    } else {
        $act = empty($act) ? array() : explode('/', $act);
        $num = count($act);
        $rq = Yaf_Dispatcher::getInstance()->getRequest();
        if ($num < 3)
            $url[] = $rq->getModuleName();
        if ($num < 2)
            $url[] = $rq->getControllerName();
        if ($num < 1) {
            $url[] = $rq->getActionName();
        } else {
            $url[] = implode('/', $act);
        }
    }
    if (is_array($par)) {
        foreach ($par as $k => $v) {
            $url[] = $k;
            $url[] = $v;
        }
    } elseif (!empty($par)) {
        $url[] = $par;
    }
    echo implode('/', $url);return;
    if(!$pattern){
        echo implode('/', $url);return;
    }else{
        return implode('/', $url);
    }
    
}
