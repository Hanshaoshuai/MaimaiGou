<?php

namespace Qiniu;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

/**
 * 七牛操作类
 *
 * @author zxcvdavid@gmail.com
 *
 */
class QiNiuOperate {

    private static $_instances;
    private $accessKey = 'Qu3MVCsB-yUnvCcpiTUcMZ4QM7b2MuVLBLtXp52v';
    private $secretKey = 'O27b0re-ynLMmEVwva4wyfguf_ZIKWTQNKl_HdoA';
    private $bucket = 'maimaigo'; // 要上传的空间
    private $imgUrl = "http://onlzmxmxz.bkt.clouddn.com/";

    public function __construct($conf = "") {
        
    }

    public function init() {
        
    }

    public static function getInstance($conf, $tableName = "") {
        /* $idx = md5($conf['db_host'] . $conf['db_name']);
          if (!isset(self::$_instances[$idx])) {
          self::$_instances[$idx] = new self($conf);
          }
          self::$_instances[$idx]->tabName = $tableName;
          return self::$_instances[$idx]; */
    }

    public function fileList($bucket = "", $limit = 100, $prefix = "", $marker = "") {
        if ($bucket == "") {
            $bucket = $this->bucket;
        }
        $auth = new Auth($this->accessKey, $this->secretKey);

        $bucketMgr = new BucketManager($auth);
        list($iterms, $marker, $err) = $bucketMgr->listFiles($bucket, $prefix, $marker, $limit);
        
        if ($err !== null) {
            $iterms=array();
        }
        return $iterms;
    }

    /**
     * @param type string $filePath 要上传文件的本地路径
     * @param type string $fileName 上传到七牛后保存的文件名
     * @return hash
     * @return key
     */
    public function upload($filePath = "", $fileName = "", $bucket = "") {
        // 构建鉴权对象
        $auth = new Auth($this->accessKey, $this->secretKey);
        // 生成上传 Token
        if ($bucket == "") {
            $bucket = $this->bucket;
        }
        $token = $auth->uploadToken($bucket);
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $fileName, $filePath);
        if ($err !== null) {
            $res = $err;
        } else {
            $res = $ret;
        }
        return $res;
    }

    public function getImgUrl($fileName) {
        return $this->imgUrl . $fileName;
    }

    public function downloadUrl() {
        $auth = new Auth($this->accessKey, $this->secretKey);
        // 私有空间中的外链 http://<domain>/<file_key>
        $baseUrl = 'http://sslayer.qiniudn.com/1.jpg?imageView2/1/h/500';
        // 对链接进行签名
        $signedUrl = $auth->privateDownloadUrl($baseUrl);
        return $signedUrl;
    }

    public function delFile($fileName) {
        $auth = new Auth($this->accessKey, $this->secretKey);
        $bucketMgr = new BucketManager($auth);
        $err = $bucketMgr->delete($this->bucket, $fileName);
        $rs = "Success!";
        if ($err !== null) {
            $rs = $err;
        }
        return $rs;
    }

}
