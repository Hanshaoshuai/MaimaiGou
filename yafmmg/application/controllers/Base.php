<?php
/*
 * 所有Controller类的基类
 */
class BaseController extends Yaf_Controller_Abstract {

    protected $homeUrl;
    /*初始化*/
    public function init(){
       // Yaf_Dispatcher::getInstance()->autoRender(false);
    }
    //获取get参数
    public function get($key, $filter = TRUE)
    {
        if ($filter) {
            return filterStr($this->getRequest()->get($key));
        } else {
            return $this->getRequest()->get($key);
        }
    }
    //获取所有参数
    public function getParams($key="", $filter = TRUE)
    {
        if ($filter) {
            if($key){
                return filterStr($this->getRequest()->getParams($key));
            }else{
                return filterStr($this->getRequest()->getParams());
            }
        } else {
            if($key){
                return $this->getRequest()->getParams($key);
            }else{
                return $this->getRequest()->getParams();
            }
        }
    }
    //获取post参数
    public function getPost($key="", $filter = TRUE)
    {
        if($key){
            $post = $this->getRequest()->getPost($key);
            if($filter){
                if(is_array($post)){
                    foreach($post as &$post_v){
                        $post_v = filterStr($post_v);
                    }
                    return $post;
                }else{
                    return filterStr($post);
                }
            }else{
                return $post;
            }
        }else{
            $post = $this->getRequest()->getPost();
            if($filter){
                if(is_array($post)){
                    foreach($post as &$post_v){
                        $post_v = filterStr($post_v);
                    }
                    return $post;
                }else{
                    return filterStr($post);
                }
            }else{
                return $post;
            }
        }
    }

    public function getQuery($key, $filter = TRUE)
    {
        if ($filter) {
            return filterStr($this->getRequest()->getQuery($key));
        } else {
            return $this->getRequest()->getQuery($key);
        }
    }
    //获取Session
    public function getSession($key)
    {
        return Yaf_Session::getInstance()->__get($key);
    }
    //设置Session
    public function setSession($key, $val)
    {
        return Yaf_Session::getInstance()->__set($key, $val);
    }
    //删除Session
    public function unsetSession($key)
    {
        return Yaf_Session::getInstance()->__unset($key);
    }

    // Clear cookie
    public function clearCookie($key)
    {
        $this->setCookie($key, '');
    }

    /**
     * Set COOKIE
     */
    public function setCookie($key, $value, $expire = 3600, $path = '/', $domain = '')
    {
        setCookie($key, $value, CUR_TIMESTAMP + $expire, $path, $domain);
    }

    /**
     * 获取cookie
     */
    public function getCookie($key)
    {
        return trim($_COOKIE[$key]);
    }

    // Go home
    public function goHome()
    {
        jsRedirect($this->homeUrl);
    }

    // Show error
    public function showError($error, $tpl, $die = TRUE)
    {
        $buffer['error'] = $error;
        $this->display($tpl, $buffer);

        if ($die) {
            die;
        }
    }

    // 将 time 和 sign 拼在 URL 后
    private function _buildURL($url)
    {
        $i['time'] = CUR_TIMESTAMP;
        $i['sign'] = Helper::generateSign($i);

        $url .= '?time=' . $i['time'] . '&sign=' . $i['sign'];

        return $url;
    }

    // Execute a YAR request
    // $p 必须是数组键值对
    protected function yarRequest($url, $function, $p = '')
    {
        $url = $this->_buildURL($url);
        $client = new yar_client($url);
        $client->SetOpt(YAR_OPT_PACKAGER, 'json');
        $client->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 3000);

        // 如果没有则令$p为空数组
        if (!$p) {
            $p = array();
        }

        $data = $client->call($function, $p);
        return json_decode($data, TRUE);
    }

    // Execute concurrent yar reqeust
    // $p 必须是数组键值对
    protected function yarConcurrentRequest($url, $function, $p, $callback = 'callback')
    {
        $url = $this->_buildURL($url);
        Yar_Concurrent_Client::call($url, $function, $p, $callback);
    }

    protected function yarLoop()
    {
        return Yar_Concurrent_Client::loop();
    }

    /**
     * Verify API sign
     */
    public function verifySign()
    {
        $sign = $this->getRequest()->getPost('sign');
        $i['time'] = $this->getRequest()->getPost('time');

        // Only valid in 30 seconds
        if (CUR_TIMESTAMP - $i['time'] > 30) {
            $rep['code'] = 1001;
            $rep['error'] = 'error sign';

            Helper::response($rep);
        }

        $newSign = Helper::generateSign($i);

        if (strtolower($newSign) != $sign) {
            $rep['code'] = 1001;
            $rep['error'] = 'error sign';

            Helper::response($rep);
        }
    }

    /**
     *  API Response
     */
    public function response($error)
    {
        switch ($error) {
            case 'ERR_MISSING':
                $rep['code'] = 1002;
                $rep['error'] = 'misssing parameters';
                break;

            case 'ERR_NO_DATA':
                $rep['code'] = 9998;
                $rep['error'] = 'no data';
                break;

            case 'ERR_UNKNOWN':
                $rep['code'] = 9999;
                $rep['error'] = 'unknown error';
                break;

            case 'ERR_DUPLICATED':
                $rep['code'] = 1110;
                $rep['error'] = 'operation duplicated';
                break;

            case 'ERR_FAIL_UPLOAD_TO_CLOUD':
                $rep['code'] = 1008;
                $rep['error'] = 'failed to upload to cloud';
                break;

            case 'ERR_FAIL_UPLOAD_TO_TMP':
                $rep['code'] = 1007;
                $rep['error'] = 'failed to upload to tmp';
                break;

            case 'ERR_NO_FILE_RECEIVED':
                $rep['code'] = 1009;
                $rep['error'] = 'empty file received';
                break;
        }

        Helper::response($rep);
    }

    public function assign($key,$value){
        return $this->getView()->assign($key, $value);
    }

}
