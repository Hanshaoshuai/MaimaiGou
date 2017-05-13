<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/10
 * Time: 17:10
 */
class publicsController extends AdminController
{
    public function fileuploadAction()
    {
        if ($_FILES) {
            $upload = new Tool\Uploads();
            $upload->set("path", UPLOAD_PATH . '/');
//         var_dump(UPLOAD_PATH);exit;
            $upload->set("maxsize", 2000000);
            $upload->set("allowtype", array("gif", "png", "jpg", "jpeg"));
            $upload->set("israndname", true);//是否随机命名
            // 上传单个文件
            //多文件上传时，name 用 数组命名 如 photo[]
            $info = $upload->upload("file");
            //使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
            if ($info) {
                //获取上传后文件名子
                $picName = $upload->getFileName();
                //上传到七牛云
                $q = new \Qiniu\QiNiuOperate();
                if (is_array($picName)) {
                    foreach ($picName as $k => $v) {
                        $picLocalUrl[] = $upload->path . $v;
                        $rs = $q->upload($upload->path . $v, $v);
                        $key = $k + 1;
                        $data['img' . $key] = $v;
                    }

                } else {
                    $rs = $q->upload($upload->path . $picName, $picName);
                    $data['img1'] = $picName;
                }
                if ($rs !== NULL) {
                    if (is_array($picName)) {
                        foreach ($picName as $k => $v) {
                            @unlink($upload->path . $v);
                        }
                    } else {
                        @unlink($upload->path . $picName);
                        @unlink($upload->path . $picName);
                    }

                } else {
                    $data = $upload->getErrorMsg();
                }
            } else {
                //获取上传失败以后的错误提示
                $data = $upload->getErrorMsg();

            }
        }

    }
}