<?php
/**
 *
 * User: zhouyouth
 * Date: 2017/3/30
 * Time: 9:39
 */
class WorkFlowModel extends BaseModel{
       public  $tableName="yaf_workflow";

    public function getSaleinfo(){
         $conf=Yaf_Application::app()->getConfig()->yaf_mmg;
         $obj=Db\Mysql::getInstance($conf,"yaf_workflow_class");
         $res=$obj->field('id,typename')->getAll();
         if(!$res){
                return false;
         }
        return  $res;
 }
  public   function  useApply($data){
      $conf=Yaf_Application::app()->getConfig()->yaf_mmg;
      $obj=Db\Mysql::getInstance($conf,$this->tableName);
      $res=$obj->insert($data);
      if($res){
         return true;
      }else{
         return false;
      }
  }
    public static function workFlowList($params,$getCount = FALSE){
        $limit1 = ($params['page'] - 1) * $params['pageSize'];
        $where=isset($params['where'])?$params['where']:"";
        $field=isset($params['field'])?$params['field']:"*";
        $order=isset($params['order'])?$params['order']:" add_time desc ";
        if($getCount){
          return M("yaf_workflow")->field("id")->where($where)->getRowCount();
        }else{
          return M("yaf_workflow")->field($field)->where($where)->order($order)->limit($limit1,$params['pageSize'])->getAll();
        }
    }

    //修改状态
    public static function UpdateWorkSta($where,$field){
        if(!empty($where)&&!empty($field)){
            return M("yaf_workflow")->where($where)->update($field);
        }
        return false;
    }
    public static function delWorkflow($where){
        if(!empty($where)){
            return M("yaf_workflow")->where($where)->delete();
        }
        return false;
    }
}