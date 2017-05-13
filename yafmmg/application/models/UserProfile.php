<?php
/**
 *
 * User: zhouyouth
 * Date: 2017/4/1
 * Time: 11:46
 */
 class UserProfileModel extends BaseModel{
     public $tableName="yaf_user_profile";

     public function getMaidou(){
        $profile= M($this->tableName);


     }
    public function insetDrawlLog($data,$umaiodu){

//       扣除麦豆
       // $model= M('yaf_user_profile');
        $m = M("");
        $conn = $m->getDbh();
        try{
            $conn->beginTransaction();//开启事务处理
            //扣除可转出麦豆
            $sql="update   yaf_user_profile set reflect_maidou =reflect_maidou-".$umaiodu. ' where uid='. $data['user_id'];
            $affected_rows=$conn->exec($sql);
            if(!$affected_rows)
            {
                throw new PDOException();
            }

            //写入转出记录
            //$mdoel=M("yaf_user_reflect_log");
            $res=M("yaf_user_reflect_log")->table("yaf_user_reflect_log")->insert($data);
            if(!$res){
                throw new PDOException();
            }

            //写入麦豆记录
            $add_data = array(
                'user_id' => $data['user_id'],
                'maidou' => $umaiodu,
                'maidou_type' => 2,
                'record_type' => 8,
                'ctime' => time(),
                'notes' => "转出：可转出麦豆：".$umaiodu,
            );


            $log_res = M("yaf_user_maidoul_log")->table("yaf_user_maidoul_log")->insert($add_data);
            if(!$log_res){
                throw new PDOException();
            }

            $conn->commit();//交易成功就提交
        }catch(PDOException $e){
            $array['msg']='扣除麦豆失败';
            $array['code']=1003;
            $conn->rollback();
            return $array;
        }

        if($res==="0"){
            return false;
        }
        return true;

    }

    //我的转出记录
     public function getgetDrawalRecord($data,$p){
         $mdoel=M("yaf_user_reflect_log");
         $count=$mdoel->getRowCount();
         $perpage =1000;//每页显示条数
         $pages=ceil($count/$perpage);
         $offset=($p-1)*$perpage;
         $res=$mdoel->field('user_id,bank_card,reflect_maidou,all_record_index,record_index,reflect_price,ctime,status')->where($data)->limit($offset,$perpage)->getAll();
         if($res==="0"){
             return false;
         }
         $zongtxmaidou=$mdoel->field('sum(reflect_maidou) as zongtxmaidou')->where($data)->order("id desc")->getAll();
         $data =array_merge($zongtxmaidou,$res);
        return $data;
    }

    //今日麦豆
    public static function getDayMaidou($start = 0, $pageSize = 10, $where = array(), $isGetCount = false, $field = "*")
    {
        $mod = M("yaf_user_maidoul_log");
        if ($isGetCount) {//计算总条数
            $count = $mod->where($where)->getRowCount();
            return $count;
        } else {
            $list = $mod->field($field)->where($where)->order("id desc")->limit($start,$pageSize)->getAll();
            return $list;
        }
    }
     //转出列表
     public static function DrawalRecordList($params,$where = array(),$getCount=FALSE,$field = "*"){
         $mdoel=M("yaf_user_reflect_log");
        if($getCount){
            $count = $mdoel->where($where)->getRowCount();
            return $count;
        }else{
            $limit1=($params['page']-1)*$params['pageSize'];
            $res=$mdoel->field($field)->where($where)->order('ctime desc')->limit($limit1,$params['pageSize'])->getAll();
            return $res;
        }
     }
     //修改转出状态
     public static function UpdateDrawalStatus($where,$field){
         if(!empty($where)&&!empty($field)){
             return M("yaf_user_reflect_log")->where($where)->update($field);
         }
         return false;
     }
}