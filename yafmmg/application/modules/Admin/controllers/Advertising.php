<?php
class AdvertisingController extends AdminController {
    public function init(){
        date_default_timezone_set('Asia/Shanghai');
    }
   public function testlistAction(){
       $model= new AdModel();
       $data=$model->getAdlist($curr=1);
       print_r(json_encode($data));exit;
   }
    public function indexAction(){
    }
    //广告管理
    public function advertisingAction(){
        $model= new AdModel();
        $data=$model->getAdlist($curr=1);
        $this->assign("data", $data);
        //广告列表
        $adData=$model->getAd($curr=1);
        if(empty($adData)){

        }
        $this->assign("adData", $adData);
        //广告图分类
        $adCateCount= new AdModel();
        $adCount=$adCateCount->count();
        $adGroup=$adCateCount->countGroup();
        $arr=array();
        $json='';
        foreach($adGroup as  $k => $v){

                $json .= json_encode($v) . ',';

        }
//         var_dump($arr);exit;
        $this->assign("json", $json);
        $this->assign("adCount", $adCount);
        $this->assign("adGroup", $adGroup);
    }
    public function insertAdAction(){
        $data['ad_name']=$this->getPost('ad_name');
        $data['ad_cat_id']=$this->getPost('ad_cat_id');
        $data['img_url']=$this->getPost('img_url');
        $data['ad_link']=$this->getPost('ad_link');
        $data['ad_width']=$this->getPost('ad_width');
        $data['ad_height']=$this->getPost('ad_height');
        $data['order']=$this->getPost('order');
        $data['ctime']=date('Y-m-d,H:i:s',time());
        $model= new AdModel();
        $data=$model->insertAd($data);
        if(!$data){
            $array=array('code'=>'0','msg'=>'success');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','msg'=>'success');
        print_r ($res=json_encode($array));
        return false;
    }
    public function openupdateAdAction(){
        $id=$this->getPost("id");
        $model= new AdModel();
        $data=$model->getOneAd($id);
        print_r( json_encode($data));exit;
    }
    public function updateAdAction(){
        $data['id']=$this->getPost('ad_id');
        $data['ad_cat_id']=$this->getPost('ad_cat_id');
        $data['ad_name']=$this->getPost('ad_name');
        $data['ad_width']=$this->getPost('ad_width');
        $data['ad_height']=$this->getPost('ad_height');
        $data['ad_link']=$this->getPost('ad_link');
        $data['img_url']=$this->getPost('img_url');
        $data['order']=$this->getPost('order');
        $model= new AdModel();
        $data=$model->updateAd($data);
        if(!$data){
            $array=array('code'=>'0','msg'=>'success');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','msg'=>'success');
        print_r ($res=json_encode($array));
        return false;

    }
    public function uploadAction(){
      if($_FILES['Filedata']){
          $upload=new Tool\Uploads();
          $upload ->set("path", UPLOAD_PATH.'/');
          $upload ->set("maxsize", 2000000);
          $upload ->set("allowtype", array("gif", "png", "jpg","jpeg"));
          $upload ->set("israndname", true);//是否随机命名
          // 上传单个文件
          $info   =   $upload->upload('Filedata');
          //多文件上传时，name 用 数组命名 如 photo[]
          if($info) {
              //获取上传后文件名子
              $picName=$upload->getFileName();
              //上传到七牛云
              $q= new \Qiniu\QiNiuOperate();
              $rs = $q->upload($upload->path.$picName,$picName);
              $data['img_url']=Yaf_Application::app()->getConfig()->qiniu->imgUrl.$picName;
              if($rs!== NULL){

                      @unlink($upload->path.$picName);
                      @unlink($upload->path.$picName);
                  $up_width=300;
                  $up_height=200;
                  echo "{path:\"".$data['img_url']."\", width:".$up_width.", height:".$up_height."}";die;
              }else{
                  $array=array('code'=>'1','msg'=>'success');
                  print_r ($res=json_encode($array));return false;
              }
          }else {
              //获取上传失败以后的错误提示
              $data =$upload->getErrorMsg();
              $array=array('code'=>'0','msg'=>$data);
              print_r ($res=json_encode($array));
              return false;
          }
      }

    }
    public function adCatAction()
    {
        $model= new AdModel();
        $data=$model->getAdlist($curr=1);
        $this->assign("data", $data);
        //添加广告分类
        //取出商品分类
        //商品分类
        $model= new ProductModel();
        $pcat=$model->categorySelectList("");
        $this->assign('pcat',$pcat);

    }
    public function insertAction(){
        $data['pos_name']=$this->getPost('pos_name');
        $data['pos_width']=$this->getPost('pos_width');
        $data['category_id']=$this->getPost('category_id');
        $data['pos_height']=$this->getPost('pos_height');
        $data['order']=$this->getPost('order');
        $data['order'] =isset(  $data['order'])?:0;
        $data['ctime']=date('Y-m-d,H:i:s',time());
        $model= new AdModel();
        $data=$model->insert($data);
        if(!$data){
            $array=array('code'=>'0','msg'=>'success');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','msg'=>'success');
        print_r ($res=json_encode($array));
        return false;
    }
    public  function adListAction($curr=1){

    }


    public  function openUpdateAction(){
        $id=$this->getPost("id");
        $model= new AdModel();
        $data=$model->getOne($id);
        print_r( json_encode($data));exit;
    }
    public function editAction(){
        $data['id']=$this->getPost('id');
        $data['pos_name']=$this->getPost('pos_name');
        $data['pos_width']=$this->getPost('pos_width');
        $data['pos_height']=$this->getPost('pos_height');
        $data['category_id']=$this->getPost('category_id');
        $data['order']=$this->getPost('order');
        $model= new AdModel();
        $data=$model->update($data);
        if(!$data){
            $array=array('code'=>'0','msg'=>'success');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','msg'=>'success');
        print_r ($res=json_encode($array));
        return false;
    }
    public function delAction(){
        $data['id']=$this->getPost('id');
        $model= new AdModel();
        $data=$model->del($data);
        if(!$data){
            $array=array('code'=>'0','msg'=>'删除失败');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','msg'=>'删除成功');
        print_r ($res=json_encode($array));
        return false;
    }
    public function delAdSingleAction(){
        $data['id']=$this->getPost('id');
        $model= new AdModel();
        $data=$model->delAdSingle($data);
        if(!$data){
            $array=array('code'=>'0','msg'=>'删除失败');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','msg'=>'删除成功');
        print_r ($res=json_encode($array));
        return false;
    }
    public function delAdAction(){
        $data['id']=$this->getPost('idcon');
        $model= new AdModel();
        $data=$model->delAd($data);
        if(!$data){
            $array=array('code'=>'0','msg'=>'删除失败');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','msg'=>'删除成功');
        print_r ($res=json_encode($array));
        return false;
    }
    public function displayAction()
    {
        $data['status']=$this->getPost('status');
        $data['id']=$this->getPost('id');
        $model= new AdModel();
        $data=$model->displayAd($data);
        if(!$data){
            $array=array('code'=>'0','id'=>  $data['id'],'msg'=>'gunbi');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','id'=>  $data['id'],'msg'=>'kaiqi');
        print_r ($res=json_encode($array));
        return false;
    }
    public  function bakAction(){
        //商品分类
        $model= new ProductModel();
        $pcat=$model->categorySelectList("");
        $this->assign('pcat',$pcat);
        $model = new CheapModel();
        $res=$model->cheapList();
        $this->assign('res',$res);
//        $res=$model->getCount();

//        $data['catid']=$this->getPost('cat_id');;
//        $model= new CheapModel();
//        $data=$model->getGoodsbyCat($data);
////        var_dump($data);exit;
//        $json='';
//        foreach ($data as $k => $v) {
//            $json .= json_encode($v) . ',';
//        }
//        $this->assign("json", $json);
    }
    //优惠券
    public  function cheapListAction(){
     //商品分类
        $model= new ProductModel();
        $pcat=$model->categorySelectList("");
        $this->assign('pcat',$pcat);
        $model = new CheapModel();
        $res=$model->cheapList();
        $this->assign('res',$res);
//        $res=$model->getCount();

//        $data['catid']=$this->getPost('cat_id');;
//        $model= new CheapModel();
//        $data=$model->getGoodsbyCat($data);
////        var_dump($data);exit;
//        $json='';
//        foreach ($data as $k => $v) {
//            $json .= json_encode($v) . ',';
//        }
//        $this->assign("json", $json);
    }
    public function getGoodsAction(){
        $data['catid']=$this->getPost('cat_id');;
        $model= new CheapModel();
        $data=$model->getGoodsbyCat($data);
        print_r(json_encode($data)) ;
        return false;
    }
    public function insertCheapAction(){
      $data['product_id']=rtrim($this->getPost('idcon'),',');
      $data['goods_cat_id']=$this->getPost('goods_cat_id');
      $data['name']=$this->getPost('name');
      $data['count']=$this->getPost('count');
      $data['coupon_type']=$this->getPost('coupon_type');
      $data['start_time']=strtotime($this->getPost('start_time'));
      $data['end_time']=strtotime($this->getPost('end_time'));
      $data['expire_start']=strtotime($this->getPost('expire_start'));
      $data['expire_end']=strtotime($this->getPost('expire_end'));
      $data['img']=$this->getPost('img');
      $data['price']=$this->getPost('price');
      $model = new CheapModel();
      $res=$model->insertch($data);
        if(!$res){
            $array=array('code'=>'0','msg'=>'success');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','msg'=>'success');
        print_r ($res=json_encode($array));
        return false;

    }
    public  function openupdateCheapAction(){
        $id=$this->getPost("id");
        $model= new CheapModel();
        $data=$model->openupdate($id);
        print_r( json_encode($data));exit;
    }
    public function updateCheapAction(){
        $data['product_id']=rtrim($this->getPost('idcon'),',');
        $data['id']=$this->getPost('ch_id');
        $data['goods_cat_id']=$this->getPost('goods_cat_id');
        $data['name']=$this->getPost('name');
        $data['count']=$this->getPost('count');
        $data['coupon_type']=$this->getPost('coupon_type');
        $data['start_time']=strtotime($this->getPost('start_time'));
        $data['end_time']=strtotime($this->getPost('end_time'));
        $data['expire_start']=strtotime($this->getPost('expire_start'));
        $data['expire_end']=strtotime($this->getPost('expire_end'));
        $data['img']=$this->getPost('img');
        $data['price']=$this->getPost('price');
//       var_dump($data);exit;
        $model= new AdModel();
        $model = new CheapModel();
        $res=$model->updateCheap($data);
        if(!$res){
            $array=array('code'=>'0','msg'=>'success');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','msg'=>'success');
        print_r ($res=json_encode($array));
        return false;
    }
    public function singleDelAction(){
        $data['id']=$this->getPost('id');
        $model = new CheapModel();
        $data=$model->delcheapSingle($data);
        if(!$data){
            $array=array('code'=>'0','msg'=>'删除失败');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','msg'=>'删除成功');
        print_r ($res=json_encode($array));
        return false;
    }
    public function delCheapAction(){
        $data['id']=$this->getPost('idcon');
        $model= new CheapModel();
        $data=$model->delCheap($data);
        if(!$data){
            $array=array('code'=>'0','msg'=>'删除失败');
            print_r ($res=json_encode($array)) ;die;
        }
        $array=array('code'=>'1','msg'=>'删除成功');
        print_r ($res=json_encode($array));
        return false;
    }

}