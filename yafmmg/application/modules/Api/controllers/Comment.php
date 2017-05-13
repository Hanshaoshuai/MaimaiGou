<?php
/**
 *
 * User: zhou
 * Date: 2017/4/3
 * Time: 16:42
 */
class CommentController extends ApiController{
    public  function init(){


    }
    public  function commentAction(){
        if($_FILES){
            $upload=new Tool\Uploads();
            $upload ->set("path", UPLOAD_PATH.'/');
//         var_dump(UPLOAD_PATH);exit;
            $upload ->set("maxsize", 2000000);
            $upload ->set("allowtype", array("gif", "png", "jpg","jpeg"));
            $upload ->set("israndname", true);//是否随机命名
            // 上传单个文件
            //多文件上传时，name 用 数组命名 如 photo[]
            $info   =   $upload->upload("photo");
            //使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
            if($info) {
                //获取上传后文件名子
                $picName=$upload->getFileName();
                //上传到七牛云
                $q= new \Qiniu\QiNiuOperate();
                if(is_array($picName)){
                    foreach($picName as $k=>$v)
                    {
                        $picLocalUrl[]= $upload->path.$v;
                        $rs = $q->upload($upload->path.$v,$v);
                        $key=$k+1;
//                        $data['img'.$key]=$v;
//                        $data['img'.$key]=$v;
                        $data['img'.$key]=Yaf_Application::app()->getConfig()->qiniu->imgUrl.$v;
                    }

                }else{
                    $rs = $q->upload($upload->path.$picName,$picName);
//                    $data['img1']=$picName;
                    $data['img1']=Yaf_Application::app()->getConfig()->qiniu->imgUrl.$picName;
                }
                if($rs!== NULL){
                    if(is_array($picName)){
                        foreach($picName as $k=>$v){
                            @unlink($upload->path.$v);
                        }
                    }else{
                        @unlink($upload->path.$picName);
                        @unlink($upload->path.$picName);
                    }

                }else{
                    $this->apiReturn($data='',1004," upload to qiniu img fail");
                }
            }else {
                //获取上传失败以后的错误提示
                $data =$upload->getErrorMsg();
                $this->apiReturn($data,1004," upload to local img fail");
            }
        }

       $data['uid']=$this->getPost('uid');
       $data['phone']=$this->getPost('phone');
       $data['phone']=str_replace(substr($data['phone'],3,4),'✲✲✲✲', $data['phone']);
       $data['goods_id']=$this->getPost('goods_id');
        $data['goods_img']=$this->getPost('goods_img');
       $data['orders_id']=$this->getPost('orders_id');
       $data['comment']=$this->getPost('comment');//好评
       $data['centent']=$this->getPost('centent');//内容
       $data['logistics_score']=$this->getPost('logistics_score');//物流
       $data['service_score']=$this->getPost('service_score');//服务
       $data['ctime']=time();
       $model= new CommentModel();
       $res=$model->comment($data);
        $final[]=$data;
        if(!$res){
            $this->apiReturn($final,1004,"fail");
        }
        $this->apiReturn($final,1000,"success!");
    }
    public  function getCommentAction(){
        $data['goods_id']=$this->getPost('goods_id');
        $data['curr']=$this->getPost('p');
        $data['curr']=isset( $data['curr'])? $data['curr']:"1";
        $model= new CommentModel($data);
        $res=$data=$model->getComment($data);
        if(empty($res)){
            $this->apiReturn($res,1004,"无数据");
        }
        foreach($res as &$v){
            $v["attr"] = json_decode($v["attr"],true);
        }
        if(!$res){
            $this->apiReturn($res,1004,"fail");
        }
        $this->apiReturn($res,1000,"success!");
    }
}