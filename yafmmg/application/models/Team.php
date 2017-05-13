<?php

/**
 *
 * User: LF
 * Date: 2017/3/27
 */
class TeamModel {

    private static $_instance;
    private $db;

    private function __construct() {
        $this->init();
    }

    private function init() {
        //$this->db = M("yaf_user_bankcard", "yaf_mmg");
    }

    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    /**
     * 获取用户信息
     * @param type $uid
     * @return type
     */
    public function myReferrals($params = array()) {
        $db = M("yaf_user_user");
        $where['id'] = $params['uid'];        
        $rs = $db->field("parentId")->where($where)->getOne();
        $myReferrals="";
        if(!empty($rs['parentId'])){
            $where1['id']=$rs['parentId'];
            $rs = $db->field("phone")->where($where1)->getOne();
            $myReferrals=$rs['phone'];
        }
        return array("myReferrals"=>$myReferrals);
    }
    
    public function getTeamCount($params){
        $db = M("yaf_user_user");  
        $where['id']=$params['uid'];
        $userInfo=$db->where($where)->getOne();    
        if($userInfo['type']==2){
            $sql1="select count(id) as numOne from yaf_user_user where provinces='".$userInfo['provinces']."' and  urban='".$userInfo['urban']."' and county='".$userInfo['county']."' " ;  
        }else{
            $sql1="select count(id) as numOne from yaf_user_user where parentId=".$params['uid']." " ;
        }
        $oneTeamCount=$db->query($sql1);  
		/*if($userInfo['type']==2){			
			$totay=strtotime(date("Y-m-d",time()));
			$sql3="select count(id) as numO from yaf_user_user where area='".$userInfo['area']."'  and ctime> ".$totay."";
			$totayCount=$db->query($sql3);
			$oneTeamCount[0]['numOne']=$totayCount[0]['numO']."/".$oneTeamCount[0]['numOne'];
		}*/
        $sql2="select count(id) as numTwo from yaf_user_user  where parentId in(select id from yaf_user_user where parentId=".$params['uid'].")";        
        $twoTeamCount=$db->query($sql2);
        return array("oneTeamCount"=>$oneTeamCount[0]['numOne'],"twoTeamCount"=>$twoTeamCount[0]['numTwo']);
    }

    public function getTeamCountCopy($params){
        $db = M("yaf_user_user");
        $where['id']=$params['uid'];
        $userInfo=$db->where($where)->getOne();
        if($userInfo['type']==2){
            $sql1="select count(id) as numOne from yaf_user_user where provinces='".$userInfo['provinces']."' and  urban='".$userInfo['urban']."' and county='".$userInfo['county']."' order by ctime desc" ;
        }else{
            $sql1="select count(id) as numOne from yaf_user_user where parentId=".$params['uid'] ;
        }
        $oneTeamCount=$db->query($sql1);
        $sql2="select count(id) as numTwo from yaf_user_user  where parentId in(select id from yaf_user_user where parentId=".$params['uid'].")";
        $twoTeamCount=$db->query($sql2);
        return array("oneTeamCount"=>$oneTeamCount[0]['numOne'],"twoTeamCount"=>$twoTeamCount[0]['numTwo']);
    }
    /**
     * 获取一级团队
     * @param type $params
     * @return type
     */
    public function LevelOne($params = array()) {
        $db = M("yaf_user_user"); 
        $page= isset($params['page'])?$params['page']:1;
        $limit= isset($params['limit'])?$params['limit']:10;
        $beginLimit=($page-1)*$limit;
        $where['id']=$params['uid'];
        $userInfo=$db->where($where)->getOne();        
        if($userInfo['type']==2){
            $sql="select a.phone,a.type,c.phone as referee_phone,FROM_UNIXTIME(a.ctime, '%Y-%m-%d') as ctime,b.logo from yaf_user_user as a LEFT JOIN yaf_user_profile as b on b.uid=a.id LEFT JOIN yaf_user_user as c on a.parentId=c.id where a.provinces='".$userInfo['provinces']."' and  a.urban='".$userInfo['urban']."' and a.county='".$userInfo['county']."' order by  a.ctime desc  limit ".$beginLimit.",".$limit." ";
        }else{
            $sql="select a.phone,a.type,c.phone as referee_phone,FROM_UNIXTIME(a.ctime, '%Y-%m-%d') as ctime,b.logo from yaf_user_user as a LEFT JOIN yaf_user_profile as b on b.uid=a.id LEFT JOIN yaf_user_user as c on a.parentId=c.id where a.parentId=".$params['uid']."  order by  a.ctime desc limit ".$beginLimit.",".$limit."" ;              
        }  
        //$sql="select a.phone,a.type,b.phone as recommend,a.ctime,c.logo from yaf_user_user as a LEFT JOIN yaf_user_user as b on a.parentId=b.id  LEFT JOIN yaf_user_profile as c on c.uid=b.id where a.parentId=".$params['uid']." limit ".$beginLimit.",".$limit." ";
        //$sql="select a.phone,a.type, FROM_UNIXTIME(a.ctime, '%Y-%m-%d') as ctime,b.logo from yaf_user_user as a LEFT JOIN yaf_user_profile as b on b.uid=a.id where a.parentId=".$params['uid']." limit ".$beginLimit.",".$limit."" ;              
       
        $rs = $db->query($sql);
        return $rs;
    }    
    /**
     * 获取二级团队
     * @param type $params
     * @return type
     */
    public function LevelTwo($params = array()) {
        $db = M("yaf_user_user");
        $page= isset($params['page'])?$params['page']:1;
        $limit= isset($params['limit'])?$params['limit']:10;
        $beginLimit=($page-1)*$limit;        
        $sql="select a.phone,a.type,FROM_UNIXTIME(a.ctime, '%Y-%m-%d') as ctime,c.logo,b.phone as referee_phone from yaf_user_user as a  LEFT JOIN yaf_user_profile as c on c.uid=a.id  LEFT JOIN yaf_user_user as b on a.parentId=b.id where a.parentId in(select id from yaf_user_user where parentId=".$params['uid'].")  "." order by ctime desc limit ".$beginLimit.",".$limit." ";
        $rs = $db->query($sql);
        return $rs;
    }

    /**
     * 获取一级团队
     * @param type $params
     * @return type
     */
    public function userLevelOne($params = array()) {
    /*    $db = M("yaf_user_user");
        $page= isset($params['page'])?$params['page']:1;
        $limit= isset($params['limit'])?$params['limit']:50;
        $beginLimit=($page-1)*$limit;
        //$sql="select a.phone,a.type,b.phone as recommend,a.ctime,c.logo from yaf_user_user as a LEFT JOIN yaf_user_user as b on a.parentId=b.id  LEFT JOIN yaf_user_profile as c on c.uid=b.id where a.parentId=".$params['uid']." limit ".$beginLimit.",".$limit." ";
        $sql="select a.id,a.phone,a.type, FROM_UNIXTIME(a.ctime, '%Y-%m-%d') as ctime,b.realname,b.inviteCode from yaf_user_user as a LEFT JOIN yaf_user_profile as b on b.uid=a.id where a.parentId=".$params['uid']." limit ".$beginLimit.",".$limit."" ;

        $rs = $db->query($sql);
        return $rs;*/
        $db = M("yaf_user_user");
        $page= isset($params['page'])?$params['page']:1;
        $limit= isset($params['limit'])?$params['limit']:50;
        $beginLimit=($page-1)*$limit;
        $where['id']=$params['uid'];
        $userInfo=$db->where($where)->getOne();
        if($userInfo['type']==2){
            $sql="select a.id,a.phone,a.type,c.phone as referee_phone,FROM_UNIXTIME(a.ctime, '%Y-%m-%d') as ctime,b.realname from yaf_user_user as a LEFT JOIN yaf_user_profile as b on b.uid=a.id LEFT JOIN yaf_user_user as c on a.parentId=c.id where a.provinces='".$userInfo['provinces']."' and  a.urban='".$userInfo['urban']."' and a.county='".$userInfo['county']."' order by  a.ctime desc  limit ".$beginLimit.",".$limit." ";
        }else{
            $sql="select a.id,a.phone,a.type,c.phone as referee_phone,FROM_UNIXTIME(a.ctime, '%Y-%m-%d') as ctime,b.realname from yaf_user_user as a LEFT JOIN yaf_user_profile as b on b.uid=a.id LEFT JOIN yaf_user_user as c on a.parentId=c.id where a.parentId=".$params['uid']."  order by  a.ctime desc limit ".$beginLimit.",".$limit."" ;
        }
        //$sql="select a.phone,a.type,b.phone as recommend,a.ctime,c.logo from yaf_user_user as a LEFT JOIN yaf_user_user as b on a.parentId=b.id  LEFT JOIN yaf_user_profile as c on c.uid=b.id where a.parentId=".$params['uid']." limit ".$beginLimit.",".$limit." ";
        //$sql="select a.phone,a.type, FROM_UNIXTIME(a.ctime, '%Y-%m-%d') as ctime,b.logo from yaf_user_user as a LEFT JOIN yaf_user_profile as b on b.uid=a.id where a.parentId=".$params['uid']." limit ".$beginLimit.",".$limit."" ;

        $rs = $db->query($sql);
        return $rs;
    }
    /**
     * 获取二级团队
     * @param type $params
     * @return type
     */
    public function userLevelTwo($params = array()) {
        $db = M("yaf_user_user");
        $page= isset($params['page'])?$params['page']:1;
        $limit= isset($params['limit'])?$params['limit']:50;
        $beginLimit=($page-1)*$limit;
        $sql="select a.id,a.phone,a.type,FROM_UNIXTIME(a.ctime, '%Y-%m-%d') as ctime,c.realname,c.inviteCode from yaf_user_user as a  LEFT JOIN yaf_user_profile as c on c.uid=a.id where a.parentId in(select id from yaf_user_user where parentId=".$params['uid'].")  "." limit ".$beginLimit.",".$limit." ";
        $rs = $db->query($sql);
        return $rs;
    }

}
