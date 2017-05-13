<?php

class SystemModel extends BaseModel{
    public static function getmessageCenter($start=0, $pageSize=10, $where,$isGetCount = false,$field="*"){
        $mod = M("yaf_system_jiguang_log");
        if ($isGetCount) {//计算总条数
            $count = $mod->where($where)->getRowCount();
            return $count;
        } else {
            $list = $mod->field($field)->where($where)->order('id desc')->limit($start,$pageSize)->getAll();
            return $list;
        }
    }

    public static function addMaidouLog($user_id,$data){
        $add_data = array(
            'user_id' => $user_id,
            'maidou' => isset($data['maidou']) ? floatval($data['maidou']) : "0.00",
            'maidou_type' => isset($data['maidou_type']) ? intval($data['maidou_type']) : 1,
            'record_type' => isset($data['record_type']) ? intval($data['record_type']) : 1,
            'ctime' => time(),
            'notes' => isset($data['notes']) ? trim($data['notes']) : '',
        );
        $log_id = M("yaf_user_maidoul_log")->insert($add_data);
        if ($log_id) {
            return $log_id;
        } else {
            return false;
        }
    }

    //后台操作日志
    public static function addAdminActionLog($user_id,$data){
        $add_data = array(
            'user_id' => $user_id,
            'ctime' => time(),
            "title"=>isset($data['title']) ? trim($data['title']) : '',
            'action_notes' => isset($data['action_notes']) ? trim($data['action_notes']) : '',
        );
        $log_id = M("yaf_admin_action_log")->insert($add_data);
        if ($log_id) {
            return $log_id;
        } else {
            return false;
        }
    }
}
