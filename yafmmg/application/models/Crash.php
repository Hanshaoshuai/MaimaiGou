<?php
/**
 *
 * User: zhouyouth
 * Date: 2017/4/1
 * Time: 11:33
 */
class CrashModel extends BaseModel {
    public $tableName="yaf_user_reflect_log";
    public  function getAllProfit($userid){
        $allProfit = M("yaf_user_profile")->where(array("uid"=>$userid))->getOne();
        return $allProfit;

}
 public  function cpyIndex($uid){
     $res=M('yaf_user_reflect_log')->query("call p_present_amount($uid,0,0)");
return $res;
 }
    public function TaskNotice() {
        $nextMonday = $this->getLastWednesday();
        $lastMonday = $this->getLLastWednesday();
        $lastSunday = $this->getLastThursday();
        $lastSunday = $this->getLLastThursday();
    }
    /**
     * 取得上周三
     * @return string
     */
    private function getLastWednesday()
    {
        if(date('l',time())=='Wednesday')  return date(strtotime('last Wednesday'));
        return date(strtotime('-1 week last Wednesday'));
    }
    /**
     * 取得上上周三
     * @return string
     */
    private function getLLastWednesday()
    {

        return date(strtotime('-2 week last Wednesday'));
    }
    /**
     * 取得上周四
     * @return string
     */
    private function getLastThursday()
    {

        return date(strtotime('-1 week last Thursday'));
    }
    /**
     * 取得上上周四
     * @return string
     */
    private function getLLastThursday()
    {

        return date(strtotime('-2 week last Thursday'));
    }
    /**
     * 这周三
     * @return string
     */
    private function getThisWednesday()
    {
        return date(strtotime('-1 week this Wednesday'));
    }
}