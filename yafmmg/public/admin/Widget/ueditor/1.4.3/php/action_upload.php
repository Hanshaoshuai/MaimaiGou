<?php
/**
 * 上传附件和上传视频
 * User: Jinqn
 * Date: 14-04-09
 * Time: 上午10:17
 */
include "Uploader.class.php";

/* 上传配置 */
$base64 = "upload";
switch (htmlspecialchars($_GET['action'])) {
    case 'uploadimage':
        $config = array(
            "pathFormat" => $CONFIG['imagePathFormat'],
            "maxSize" => $CONFIG['imageMaxSize'],
            "allowFiles" => $CONFIG['imageAllowFiles']
        );
        $fieldName = $CONFIG['imageFieldName'];
        break;
    case 'uploadscrawl':
        $config = array(
            "pathFormat" => $CONFIG['scrawlPathFormat'],
            "maxSize" => $CONFIG['scrawlMaxSize'],
            "allowFiles" => $CONFIG['scrawlAllowFiles'],
            "oriName" => "scrawl.png"
        );
        $fieldName = $CONFIG['scrawlFieldName'];
        $base64 = "base64";
        break;
    case 'uploadvideo':
        $config = array(
            "pathFormat" => $CONFIG['videoPathFormat'],
            "maxSize" => $CONFIG['videoMaxSize'],
            "allowFiles" => $CONFIG['videoAllowFiles']
        );
        $fieldName = $CONFIG['videoFieldName'];
        break;
    case 'uploadfile':
    default:
        $config = array(
            "pathFormat" => $CONFIG['filePathFormat'],
            "maxSize" => $CONFIG['fileMaxSize'],
            "allowFiles" => $CONFIG['fileAllowFiles']
        );
        $fieldName = $CONFIG['fileFieldName'];
        break;
}

/* 生成上传实例对象并完成上传 */
$up = new Uploader($fieldName, $config, $base64);

/**
 * 得到上传文件所对应的各个参数,数组结构
 * array(
 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
 *     "url" => "",            //返回的地址
 *     "title" => "",          //新文件名
 *     "original" => "",       //原始文件名
 *     "type" => ""            //文件类型
 *     "size" => "",           //文件大小
 * )
 */

/* 返回数据 */
$FileInfo = $up->getFileInfo();
if($FileInfo){
    $app_path =  realpath(dirname(__FILE__) . '/');

    $app_path = str_replace("public\admin\Widget\ueditor\\","",$app_path);
    $app_path = str_replace("1.4.3\php","",$app_path);
    $app_path = str_replace("\\","/",$app_path);
    //$Config_class_path = $app_path."application/library/Qiniu/Config.php";
    $qiNiuOperate_class_path = $app_path."application/library/Qiniu/QiNiuOperate.php";
  //  $Auth_class_path = $app_path."application/library/Qiniu/Auth.php";
   // $Zone_class_path = $app_path."application/library/Qiniu/Zone.php";
   // $function_class_path = $app_path."application/library/Qiniu/functions.php";
    $autoloade_class_path = $app_path."application/library/Qiniu/autoload.php";
    if(file_exists($autoloade_class_path)) {
       // include_once($Config_class_path);
       // include_once($qiNiuOperate_class_path);
      //  include_once($Auth_class_path);
       // include_once($Zone_class_path);
        include_once($autoloade_class_path);
        $file_path = $app_path.$FileInfo['url'];
        $q = new Qiniu\QiNiuOperate();
        $file_path = str_replace("//","/",$file_path);
        $qiniu_file_name = $FileInfo['url'];
        $rs1 = $q->upload($file_path, "ueditor_".$FileInfo['title']);
        if ($rs1 !== NULL) {
            @unlink($file_path);
        }
        $FileInfo['url'] = "http://onlzmxmxz.bkt.clouddn.com/".$rs1['key'];
        $FileInfo['title'] = $rs1['key'];
    }
}
return json_encode($FileInfo);
