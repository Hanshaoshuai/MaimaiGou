<?php

/**
 *
 * User: zhanggen
 * Date: 2017/4/1
 * Time: 14:44
 */
class AdminController extends BaseController {
    public function init() {
        //设置Controller的模板位置为模块目录下的views文件夹
        $this->setViewpath(APP_PATH . '/application/modules/' . $this->getModuleName() . '/views');
        $views = $this->initView();
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
                    echo $fileArray['savepath'] . "/" . $fileArray['l_savename'];
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
