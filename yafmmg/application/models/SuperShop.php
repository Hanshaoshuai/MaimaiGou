<?php
/**
 *
 * User: zhouyouth
 * Date: 2017/4/2
 * Time: 14:26
 */
class SuperShopModel extends BaseModel{
     public $tableName="yaf_shop_supershop";
     public function fetchAllShop($data){
      $cs=M('yaf_shop_supershop');
         $count=$cs->getRowCount();
         $perpage =6;//每页显示条数
         $pages=ceil($count/$perpage);
         $data['p'] =isset( $data['p'])? $data['p']:"1";
         $offset=($data['p']-1)*$perpage;
         $url = \Yaf_Application::app()->getConfig()->qiniu->imgUrl;
      $res=$cs->field('CONCAT("' . $url . '",shop_img) as shop_img,shop_name,address,shop_xpoint,shop_ypoint,user_id_shop')->limit($offset,$perpage)->getAll();
      if($res==="0"){
         return false;
      }
         foreach($res as $k=>$v){
             $distnce=$this->getdistance($v['shop_xpoint'],$v['shop_ypoint'],$data['usrX'],$data['usrY']);
             $res[$k]["distance"]=number_format($distnce,2);
         }
    //排序
         $sort = array(
                      'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
        'field'     => 'distance',       //排序字段
      );
         $arrSort = array();
          foreach($res AS $uniqid => $row){
              foreach($row AS $key=>$value){
                  if($key=="distance"){
                      $value = str_replace(',', '', $value);
                      $arrSort[$key][$uniqid] = round($value, 2);
                  }else{
                      $arrSort[$key][$uniqid] = $value;
                  }
              }
          }
         if($sort['direction']){
                  @array_multisort($arrSort[$sort['field']], constant($sort['direction']), $res);
         }

         return $res;
  }
    public function fetchLocalAll($distance,$data){
        $cs=M('yaf_shop_supershop');
        $count=$cs->getRowCount();
        $perpage =6;//每页显示条数
        $pages=ceil($count/$perpage);
        $data['p'] =isset( $data['p'])? $data['p']:"1";
        $offset=($data['p']-1)*$perpage;
        $url = \Yaf_Application::app()->getConfig()->qiniu->imgUrl;
        $res=$cs->query('select shop_name,CONCAT("' . $url . '",shop_img) as shop_img,shop_xpoint,shop_ypoint,address ,user_id_shop from yaf_shop_supershop where shop_xpoint <'.$distance['maxLat'] .' and shop_xpoint >'.$distance['minLat']. ' and shop_ypoint <'.$distance['maxLat'].' and shop_ypoint >'.$distance['minLng'].
         'limit '.$offset." , " .$perpage);
//        var_dump($distance);exit;
        if($res==="0"){
            return false;
        }
        foreach($res as $k=>$v){
            $distnce=$this->getdistance($v['shop_xpoint'],$v['shop_ypoint'],$data['usrX'],$data['usrY']);
            $res[$k]["distance"]=number_format($distnce,2);
        }
        //排序
        $sort = array(
            'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'distance',       //排序字段
        );
        $arrSort = array();
        foreach($res AS $uniqid => $row){
            foreach($row AS $key=>$value){
                if($key=="distance"){
                    $value = str_replace(',', '', $value);
                    $arrSort[$key][$uniqid] = round($value, 2);
                }else{
                    $arrSort[$key][$uniqid] = $value;
                }
            }
        }
        if($sort['direction']){
            @array_multisort($arrSort[$sort['field']], constant($sort['direction']), $res);
        }
        return $res;

    }
    public function searchShop($data,$user){
        $cs=M('yaf_shop_supershop');
        $count=$cs->getRowCount();
        $perpage =6;//每页显示条数
        $pages=ceil($count/$perpage);
        $data['p'] =isset( $data['p'])? $data['p']:"1";
        $offset=($data['p']-1)*$perpage;
        $url = \Yaf_Application::app()->getConfig()->qiniu->imgUrl;
        $res=$cs->query('select shop_name,shop_xpoint,shop_ypoint,CONCAT("' . $url . '",shop_img) as shop_img,province,city,county,address,user_id_shop from yaf_shop_supershop where shop_name LIKE "%'.$data['name'].'%" OR province LIKE "%'.$data['name'].'%" or city LIKE "%'.$data['name'].'%" OR county LIKE "%'.$data['name'].
             '%"  limit '.$offset. ' , '.$perpage);
        if($res==="0"){
            return false;
        }
        foreach($res as $k=>$v){
            $distnce=$this->getdistance($v['shop_xpoint'],$v['shop_ypoint'],$user['usrX'],$user['usrY']);
            $res[$k]["distance"]=number_format($distnce,2);
        }
        //排序
        $sort = array(
            'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field'     => 'distance',       //排序字段
        );
        $arrSort = array();
        foreach($res AS $uniqid => $row){
            foreach($row AS $key=>$value){
                if($key=="distance"){
                    $value = str_replace(',', '', $value);
                    $arrSort[$key][$uniqid] = round($value, 2);
                }else{
                    $arrSort[$key][$uniqid] = $value;
                }
            }
        }
        if($sort['direction']){
            @array_multisort($arrSort[$sort['field']], constant($sort['direction']), $res);
        }
        return $res;
    }
    /**
     * 求两个已知经纬度之间的距离,单位为千米
     *
     * @param lng1 $ ,lng2 经度
     * @param lat1 $ ,lat2 纬度
     * @return float 距离，单位千米
     *
     */
 private   function getdistance($lng1, $lat1, $lng2, $lat2) {
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;//千米
//        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137*1000;//米
        return $s;
    }

    //添加超市
    public static function addSuperShop($field){
        return M("yaf_shop_supershop")->insert($field);
    }
    public static function searchSuperShop($where,$field='*'){
        return M("yaf_shop_supershop")->field($field)->where($where)->getOne();
    }
    public static function updateSuperShop($where,$field){
        return M("yaf_shop_supershop")->where($where)->update($field);
    }

    public  function payMaidou($data,$user_profile,$shuju){
        $model= M('yaf_user_maidoul_log');

        try{
            $model->beginTransaction();//开启事务处理
            if($user_profile['supermarket_maidou']>=$data['maidou']){
                $sql='update  yaf_user_profile'. ' set supermarket_maidou =supermarket_maidou- '.$data['maidou'].' where uid='.$data['user_id'];
            }else{
                $supermarket_maidou = $data['maidou']-$user_profile['supermarket_maidou']; //剩余需要支付的麦豆
                if($user_profile['spend_maidou']>=$supermarket_maidou){
                    $sql="update  yaf_user_profile set supermarket_maidou='0',spend_maidou =spend_maidou- ".$supermarket_maidou." where uid='".$data['user_id']."'";
                }else{
                    $supermarket_maidou = $data['maidou']-$user_profile['supermarket_maidou']-$user_profile['spend_maidou']; //剩余需要支付的麦豆
                    if($user_profile['reflect_maidou']>=$supermarket_maidou){
                        $sql="update  yaf_user_profile set supermarket_maidou='0',spend_maidou='0',reflect_maidou =reflect_maidou- ".$supermarket_maidou." where uid='".$data['user_id']."'";
                    }else{
                        $msg="个人麦豆转出失败";
                        $array=array('code'=>1006,'msg' => $msg);
                        throw new PDOException(json_encode(($array)));
                    }
                }
            }
            $affected_rows=$model->sqlexec($sql);
            $msg="个人麦豆转出失败";
            $array=array('code'=>1006,'msg' => $msg);
            if(!$affected_rows)
                 throw new PDOException(json_encode(($array)));
            $sql="update   yaf_user_profile set performance_maidou=performance_maidou+".$data['maidou']. ' where uid='.$shuju['user_id_shop'];
            $affected_rows=$model->sqlexec($sql);
            if(!$affected_rows)
            {  $array['msg']='向店家转入失败';
                $array['code']=1003;
                 throw new PDOException(json_encode(($array)));
            }
            $model->insert($data);  //写入用户消费记录
            $shop_data = array(
                'user_id' => $shuju['user_id_shop'],
                'maidou' => $data['maidou'],
                'maidou_type' => 5,
                'record_type' => 3,
                'ctime' => time(),
                'notes' => "超市营业额"
            );
            $model->insert($shop_data);  //写入超市营业额记录
            $model->commit();//交易成功就提交
        }catch(PDOException $e){
            $model->rollback();
            return $array;
        }
        return true;
    }
}