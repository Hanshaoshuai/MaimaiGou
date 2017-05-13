<?php
/**
 *
 * User: zhouyouth
 * Date: 2017/4/3
 * Time: 16:46
 */
class CommentModel extends BaseModel{

     public function comment($data){
        $model=M('yaf_shop_comment');
        $res=$model->insert($data);
        if(!$res){
            return false;
        }
         return $res;
     }
    public function  getComment($data){
        $model=M('yaf_shop_comment');
        $count=$model->getRowCount();
        $perpage =5;//每页显示条数
        $pages=ceil($count/$perpage);
        $data['curr'] =isset( $data['curr'])? $data['curr']:"1";
        $offset=($data['curr']-1)*$perpage;
        $res=$model->query('select userc.*,op.product_name,op.attr,op.ctime otime,usp.logo usrlogo from yaf_shop_comment as userc LEFT JOIN yaf_shop_order_product as op on userc.orders_id=op.order_id LEFT JOIN  yaf_user_profile as usp on usp.uid=userc.uid WHERE userc.goods_id='.$data['goods_id'].
            ' limit '.$offset.",".$perpage );
        if(!$res){
            return false;
        }
        return $res;
    }
}