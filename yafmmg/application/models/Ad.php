<?php
/**
 *
 * User:zhouyouth
 * Date: 2017/4/11
 * Time: 10:19
 */
class  AdModel extends BaseModel{
   //添加广告分类
   public  function insert($data){
       $model= M('yaf_ad_cat');
       $res=$model->insert($data);
       if($res==="0"){
            return false;
       }
       return true;
   }
 public  function  getAdlist($curr){
       $model=M('yaf_ad_cat');
       $count=$model->getRowCount();
       $perpage =100;//每页显示条数
       $pages=ceil($count/$perpage);
       $offset=($curr-1)*10;
       $data=$model->query("select * from yaf_ad_cat order by `order` desc  limit $offset,$perpage ");
       if(!$data){
            return false;
       }
     return $data;
   }
  public  function getOne($id){
      $model=M('yaf_ad_cat');
      $res=$model->where(array('id'=>$id))->getOne();
      if(!$res){
        return false;
      }
      return $res;
  }
 public function getOneAd($id){
     $model=M('yaf_ad');
     $res=$model->where(array('id'=>$id))->getOne();
     if(!$res){
         return false;
     }
     return $res;

 }
  public  function  update($data){
      $model = M('yaf_ad_cat');
      $res=$model->where(array('id'=>$data['id']))->update($data);
      if(!$data){
          return false;
      }
      return $data;
  }
 public function updateAd($data) {
     $model = M('yaf_ad');
     if(empty($data['img_url'])){
         unset($data['img_url']);
     }
     $res=$model->where(array('id'=>$data['id']))->update($data);

     if(!$data){
         return false;
     }
     return $data;

 }
 public function del($data){
     $model = M('yaf_ad_cat');
     $res=$model->where(array('id'=>$data['id']))->delete();
     if(!$data){
         return false;
     }
     return $data;
 }

    /**
     * @param $data
     * @return bool
     */
    public  function delAdSingle($data){
      $model = M('yaf_ad');
      $res=$model->where(array('id'=>$data['id']))->delete();
      if(!$res){
          return false;
      }
      return true;
  }
    public function delAd($data){
     $model = M('yaf_ad');
     $res=$model->where('id in( '.$data['id'].')')->delete();
     if(!$res){
         return false;
     }
     return true;
 }
 public function insertAd($data){
     $model= M('yaf_ad');
     $res=$model->insert($data);
     if($res==="0"){
         return false;
     }
     return true;
 }
 public function getAd($curr){
     $model= M('yaf_ad');
     $count=$model->getRowCount();
     $perpage =100;//每页显示条数
     $pages=ceil($count/$perpage);
     $offset=($curr-1)*10;
     $data=$model->query("select yaf_ad_cat.pos_name,yaf_ad.* from yaf_ad LEFT JOIN yaf_ad_cat on yaf_ad_cat.id=yaf_ad.ad_cat_id  order by yaf_ad.`order` desc  limit $offset,$perpage ");
     if(!$data){
         return false;
     }
     return $data;
 }
 public function count(){
     $model= M('yaf_ad_cat');
     $count=$model->query(' select sum(t.cad) as zong from (select ac.pos_name ,count(ad_name) cad  from yaf_ad ad LEFT JOIN yaf_ad_cat ac ON ac.id= ad.ad_cat_id GROUP BY ac.pos_name) t');
     if(!$count){
        return false;
     }
     return $count;
 }
public function countGroup(){
        $model= M('yaf_ad_cat');
        $zong=$model->query('select ac.pos_name, ac.id ,ac.pos_height,ac.pos_width,count(ad_name) cad  from yaf_ad ad LEFT JOIN yaf_ad_cat ac ON ac.id= ad.ad_cat_id GROUP BY ac.pos_name');
        if(!$zong){
            return false;
        }
        return $zong;
    }
public function displayAd($data){
    $model= M('yaf_ad');
    $zong=$model->where(array('id'=>$data['id']))->update($data);
    if(!$zong){
        return false;
    }
    return $zong;
  }
}