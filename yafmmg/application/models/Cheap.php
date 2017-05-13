<?php
class CheapModel  extends  BaseModel{
    public  function getGoodsbyCat($data){
        $model= M('yaf_shop_product');
        $res= $model->field('name,id,img')->where(array('catid'=> $data['catid']))->getAll();
        return $res;
    }
   public  function  insertch($data){
       $model= M('yaf_user_coupon');
       $res=$model->insert($data);
      return $res;
   }
    public function cheapList(){
        $model= M('yaf_user_coupon');
        $res=$model->getAll();
        return $res;
    }
    public  function  openupdate($id){
        $model = M('yaf_user_coupon');
        $res=$model->where(array('id'=>$id))->getOne();
        if(!$res){
            return false;
        }
        return $res;
    }
    public function updateCheap($data) {
        $model = M('yaf_user_coupon');
        if(empty($data['img'])){
            unset($data['img']);
        }
        $res=$model->where(array('id'=>$data['id']))->update($data);

        if(!$data){
            return false;
        }
        return $data;

    }
    public  function delcheapSingle($dta){
       $res= M('yaf_user_coupon')->where(array('id'=>$dta['id']))->delete();
        if(!$res){
            return false;
        }
        return true;
    }
    public  function delCheap($data){
        $res= M('yaf_user_coupon')->where("id in (".$data['id'].")")->delete();
        if(!$res){
            return false;
        }
        return true;
    }
}
