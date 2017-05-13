<?php

/**
 *
 * User: zhouyouth
 * Date: 2017/3/30
 * Time: 9:39
 */
class WorkFlowController extends ApiController
{
    public function init()
    {


    }

    public function saleServiceAction()
    {
        $model = new WorkFlowModel();
        $res = $model->getSaleinfo();
        if (!$res) {
            $this->apiReturn($data = "", 1004, "fail");
        }
        $this->apiReturn($res, 1000, "success!");
    }

    public function userProblemApplyAction()
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
            $info = $upload->upload("photo");
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
                    $this->apiReturn($data = '', 1004, " upload to qiniu img fail");
                }
            } else {
                //获取上传失败以后的错误提示
                $data []= $upload->getErrorMsg();

                $this->apiReturn($data, 1004, " upload to local img fail");
            }
        }
        $data['typeid'] = $this->getPost("typeid");//订单类型id
        $data['userid'] = $this->getRequest()->getPost("userid");
        $data['typename'] = $typeName = $this->getRequest()->getPost('typename');
        $data['order_id'] = $typeName = $this->getRequest()->getPost('order_id');
        $data['content'] = $this->getRequest()->getPost('content');
        $data['add_time']=time();
        $model = new WorkFlowModel($data);
        $res = $model->useApply($data);
        $arr[]=$data;
        if (!$res) {
            $this->apiReturn($data, 1004, " save user apply info fails");
        }
        $this->apiReturn($arr, 1000, "success");
    }
}