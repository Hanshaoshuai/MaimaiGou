<?php

/**
 *
 * User: zhouyouth
 * Date: 2017/3/27
 * Time: 14:44
 */
class ApiController extends BaseController {

    var $user_id; //当前登陆的用户ID
    private $code = array(
        "1000" => "success",
        "1001" => "参数错误",
        "1002" => "服务器无响应",
        "1003" => "非法请求",
        "1004"=>"操作失败",
        "1005"=>"查询失败",
        "1006"=>"身份证用户名不一至",
        "1007"=>"无此身份证"
    );

    /* 初始化 */

    public function init() {
        //验证登陆状态
        $token = $this->getPost("token");
        $user_id =  $this->getPost("user_id");
        if(empty($token)){
            $token = $this->get("token");
        }
        if(empty($user_id)){
            $user_id = $this->get("user_id");
        }
        if(empty($user_id)){
            $user_id = $this->get("uid");
        }
        if(empty($user_id)){
            $user_id = $this->get("uid");
        }
        //判断是否登录
        if($token &&  $user_id){
            //直接在数据库方式进行验证
            $infoTaken = M("yaf_user_login_token")->where(array("user_id"=>$user_id,"token"=>$token))->getOne();
            if($infoTaken){
                $this->user_id = $user_id;
                $userInfo = array();
                $userInfo['user_id'] = $user_id;
                $this->setSession("user_id",$user_id);
                $address = M("yaf_user_address")->field("id")->where(array("user_id"=>$user_id,"is_default"=>1))->getOne();
                if($address){
                    $userInfo['default_address'] = 1;
                }else{
                    $userInfo['default_address'] = 0;
                }
                $this->setSession("userInfo",$userInfo);//TODO:以后修改
            }
        }

        if($this->user_id){
            $this->user_id = $this->user_id; //暂时用于测试
        }else{
            $this->user_id = $user_id;;
        }

        Yaf_Dispatcher::getInstance()->autoRender(false);
    }

    protected function generateSign($parameters) {
        $signPars = API_KEY;

        foreach ($parameters as $k => $v) {
            if (isset($v) && 'sign' != $k) {
                $signPars .= $k . '=' . $v . '&';
            }
        }
        $signPars .= 'key=' . M_KEY;
        return strtolower(md5($signPars));
    }

    public function checkSign() {
        $sign = $this->getRequest()->getQuery('sign');
        $mobineTime["time"] = $this->getRequest()->getQuery('time'); //客户端时间
        //检验时间,超过20秒,校验失败
        if (time() - $mobineTime["time"] > 20) {
            $rep['code'] = 1001;
            $rep['error'] = 'error:time is out ';
            print_r(json_encode($rep));
            die;
        }
        $newSign = $this->generateSign($mobineTime);
        if (strtolower($newSign) != $sign) {
            $rep['code'] = 1002;
            $rep['error'] = 'sign  is  incorrect';
            print_r(json_encode($rep));
            die;
        }
    }

    /**
     * 参数检查
     * @param type $params  获取的数据  
     * @param type $paramsName  必填的参数
     * @example 
     *          $params=array("id"=>1,"title"=>2)
     *          $paramsName=array("id"=>array("备用:预留空间后期做数据类型以及其他多重判断"))
     *          checkParams($params,$paramsName);
     *          输出结果 error:title error
     * @throws Exception  缺少参数  
     */
    public function checkParams($params, $paramsName = array()) {
        // $defaultErrorMsg = " error";
        foreach ($paramsName as $key => $v) {
            if (!isset($params[$key])) {
                $code = "1001";
                $this->apiReturn(array(), $code, "缺少" . $key);
                //throw new Exception($key . $defaultErrorMsg);
            }
        }
    }

    /**
     * 统一返回格式
     * @param type $data    数据   
     * @param type $code    状态码
     * @param type $msg     提示语
     */
    public function apiReturn($data = array(), $code = "1000", $msg ="") {
        header("Content-type: text/html; charset=utf-8");
        if(empty($data)){
            $data=array();
        }
        if($msg==""){
            $msg=$this->code[$code];
        }
        echo json_encode(array("code" => $code, "data" => $data, "msg" =>$msg ));
        exit();
    }

    //认证用户
    public function verifyLogin(){
        if($this->user_id<=0){
            $this->apiReturn((object)array(),"10000",'未登录！');
        }
    }

    /**
     * 单个附件上传
     * @param string $subDir 子目录名称当$subType为custom时有效
     * @param string $subType 子目录创建方式 可以使用hash date custom
     * @param string $savePath 设置附件上传目录
     * @param array $allowExts 设置附件上传类型
     */
    final public static function upload($subDir = '', $subType = 'custom', $allowExts = array(),$img_type="")
    {
        $upload = new Tool\UploadFile();
        $upload->allowExts  = $allowExts ? $allowExts : array('jpg', 'gif', 'png', 'jpeg');
        $upload->savePath =  UPLOAD_PATH;// 设置附件上传目录
        $upload->autoSub = true;
        $upload->subType = $subType ? $subType : 'custom';
        $upload->subDir = '/';
        if(!is_dir(UPLOAD_PATH . $subDir)){
            mkdir(UPLOAD_PATH . $subDir);
        }
        if($img_type){
            $img_type = $img_type."_";
        }
        if(!$upload->upload()) {// 上传错误提示错误信息
            return $upload->getErrorMsg();
        }else{// 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            $fileArray = array();
            $q = new \Qiniu\QiNiuOperate();
            foreach ($info as $sinfo){
                $sinfo['savename'] = str_replace("/","",$sinfo['savename']);
                $qiniu_savename = $img_type.$sinfo['savename'];
                $sinfo['l_savename'] = $sinfo['savename'];
                $rs = $q->upload($sinfo['savepath']."/" . $sinfo['savename'], $qiniu_savename);
                if($rs){
                    $sinfo['savename'] = $qiniu_savename;
                }
                $fileArray[$sinfo['key']] = $sinfo;
            }
            if ($rs !== NULL) {
                if (is_array($fileArray)) {
                    foreach ($fileArray as $k => $v) {
                        @unlink($v['savepath'] . "/" . $v['l_savename']);
                    }
                } else {
                   // echo $fileArray['savepath'] . "/" . $fileArray['l_savename'];
                    @unlink($fileArray['savepath'] . "/" . $fileArray['l_savename']);
                }
                return $fileArray;
            }else{
                return false;
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see Action::error()
     * @param $dialog dialog弹窗
     */
    protected function error($message, $jumpUrl = '', $dialog = '', $ajax = false)
    {
        $this->assign('dialog', $dialog);
        parent::error($message, $jumpUrl, $ajax);
    }
}
