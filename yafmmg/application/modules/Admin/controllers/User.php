<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/10
 * Time: 15:43
 */
class UserController extends AdminController
{

    public function indexAction()
    {
    }
    //会员列表
    public function userListAction(){
        $user=null;
        $user_obj=new UserModel();
        $params['page'] = $this->get('page') > 0 ? $this->get('page') : 1;
        $params['pageSize'] = $this->get('pageSize') > 0 ? $this->get('pageSize') : 10;
        $params['keyword']=$this->get('keyword')?$this->get('keyword'):'';
        $params['stime']=$this->get('stime')?$this->get('stime'):'';
        $params['type']=$this->get('type');
        $params['sarea']=$this->get('sarea')?$this->get('sarea'):'';
        if(!empty($params['keyword']) || !empty($params['stime']) || !empty($params['type']) || $params['type']=='0' || !empty($params['sarea']) ){

            if(!empty($params['type'])||$params['type']=='0'){
                $where="type='". $params['type']."'";
                $str="type=".$params['type']."&pageSize=". $params['pageSize'];
                if(!empty($params['stime'])){
                    $where.=" and ctime > ".strtotime($params['stime']);
                    $str.="&stime=".$params['stime'];
                }
                if(!empty($params['keyword'])){
                    if(preg_match("/^1[34578]\d{9}$/", $params['keyword'])){
                        $where=" phone = ".$params['keyword'];
                    }elseif(preg_match("/^[a-zA-Z]+$/", $params['keyword'])){
                        $where=" phone = '".$params['keyword']."'";
                    }else{
                        $where1=array('realname'=>$params['keyword']);
                        $uid=UserModel::userInfo1($where1,'uid');
                        if($uid){
                            $where=" id =".$uid['uid'];
                        }else{
                            $where="";
                        }
                    }
                    $str.="&keyword=".$params['keyword'];
                }
            }elseif(!empty($params['stime'])){
                $where=" ctime > ".strtotime($params['stime']);
                $str="&stime=".$params['stime']."&pageSize=". $params['pageSize'];
                if(!empty($params['keyword'])){
                    if(preg_match("/^1[34578]\d{9}$/", $params['keyword'])){
                        $where=" phone = ".$params['keyword'];
                    }else{
                        $where1=array('realname'=>$params['keyword']);
                        $uid=UserModel::userInfo1($where1,'uid');
                        if($uid){
                            $where=" id =".$uid['uid'];
                        }else{
                            $where="";
                        }
                    }
                    $str.="&keyword=".$params['keyword'];
                }
            }elseif(!empty($params['keyword'])){
                if(preg_match("/^1[34578]\d{9}$/", $params['keyword'])){
                    $where=" phone = ".$params['keyword'];
                }elseif(preg_match("/^[a-zA-Z]+$/", $params['keyword'])){
                    $where=" phone = '".$params['keyword']."'";
                }else{
                    $where1=array('realname'=>$params['keyword']);
                    $uid=UserModel::userInfo1($where1,'uid');
                    if($uid){
                        $where=" id =".$uid['uid'];
                    }else{
                        $where="";
                    }
                }
                $str="keyword=".$params['keyword'];

            }elseif(!empty($params['sarea'])){
                $str="sarea=".$params['sarea']."&pageSize=". $params['pageSize'];
                $len=mb_strlen ($params['sarea'],"utf8");
                $p1=mb_strpos($params['sarea'],"省",0,"utf8");
                $p2=mb_strpos($params['sarea'],"市",0,"utf8");
                if($p1){
                    $p=mb_substr($params['sarea'],0,$p1+1,'utf8');
                    $where="provinces ='".$p."'";
                    if($p2){
                        $u=mb_substr($params['sarea'],$p1+1,$p2-$p1,'utf8');
                        $where.=" and urban='".$u."'";
                        $c=mb_substr($params['sarea'],$p2+1,$len-$p2,'utf8');
                        if($c){
                            $where.=" and county='".$c."'";
                        }
                    }
                }elseif($p2){

                        $u=mb_substr($params['sarea'],0,$p2+1,'utf8');
                        $where="provinces ='".$u."'";
                        $c=mb_substr($params['sarea'],$p2+1,$len-$p2,'utf8');
                        if($c){
                            $where.=" and county='".$c."'";
                        }

                }else{
                    $where="";
                }

            }else{
                $where="";
                $str="pageSize=". $params['pageSize'];
            }
        }else{
            $where="";
            $str="pageSize=". $params['pageSize'];
        }
        //是否导出
        $excel=$this->get('excel');
        $user=$user_obj->searchUser($params,$where);
        foreach($user as $k=> $v){
            $user_profile = UserModel::getUserProfile($v['id']);
            $reflect_maidou = $user_profile['reflect_maidou'];   //可转出麦豆
            $spend_maidou = $user_profile['spend_maidou'] + $user_profile['frozen_maidou'];      //可消费麦豆=消费麦豆+冻结麦豆数
            $assets = $reflect_maidou + $spend_maidou; //总资产
            $daymaidou_arr = UserModel::getDayMaidou($v['id']);  //当日获得麦豆数
            $user[$k]['assets'] = $assets ? $assets : 0;
            $user[$k]['reflect_maidou'] = $reflect_maidou ? $reflect_maidou : 0;
            $user[$k]['daymaidou'] = $daymaidou_arr['daymaidou'] ? $daymaidou_arr['daymaidou'] : 0;
            $team['uid']=$v['id'];
            $model=TeamModel::getInstance();
            $num=$model->getTeamCount($team);
            $user[$k]['teamCount']=$num['oneTeamCount']+$num['twoTeamCount'];
            $user[$k]['realname']=$user_profile['realname'];
            $user[$k]['IDnumber']=$user_profile['IDnumber'];
            $user[$k]['inviteCode']=$user_profile['inviteCode'];
            $user[$k]['last_login_ctime']=$user_profile['last_login_ctime'];
            $user[$k]['last_login_ip']=$user_profile['last_login_ip'];
            $user[$k]['vip_end_time']=$user_profile['vip_end_time'];

        }
        $sum =$user_obj->searchUser($params,$where,'*',1);
        $count=ceil($sum/$params['pageSize']);
       // $url="userList?keyword=" .$params['keyword']."&stime=". $params['stime']."&type=".$params['type'];
        if($str){
            $url="userList?".$str;
        }else{
            $url="userList";
        }

        if($excel){
            foreach($user as $k=>$value){
                $where=array('uid'=>$value['id']);
                $model=BankCardModel::getInstance();
                $cardList=$model->cardList($where);
                /*print_r($cardList);die;*/
                $data[$k]['id']=$value['id'];
                $data[$k]['phone']=$value['phone'];
                $data[$k]['realname']=$value['realname'];
                $data[$k]['IDnumber']="'".$value['IDnumber'];
                $data[$k]['inviteCode']=$value['inviteCode'];
                if($value['type']==0){
                    $data[$k]['type']="普通用户";
                }elseif($value['type']==1){
                    $data[$k]['type']="VIP用户";
                }else{
                    $data[$k]['type']="代理商";
                }
                $data[$k]['area']=$value['provinces'].$value['urban'].$value['county'];
                /*$data[$k]['teamCount']=$value['teamCount'];*/
                $data[$k]['daymaidou']=$value['daymaidou'];
                $data[$k]['reflect_maidou']=$value['reflect_maidou'];
                $data[$k]['assets']=$value['assets'];
                if($cardList){
                    $data[$k]['card_no']="'".$cardList[0]['card_no'];
                    $data[$k]['bank_name']=$cardList[0]['bank_name'];
                    $data[$k]['branch_info']=$cardList[0]['branch_info'];
                }else{
                    $data[$k]['card_no']="###";
                    $data[$k]['bank_name']="###";
                    $data[$k]['branch_info']="###";
                }

                if($value['status']==0){
                    $data[$k]['status']="正常";
                }else{
                    $data[$k]['status']="冻结";
                }
                if($value['ctime']){
                    $data[$k]['ctime']=date("Y-m-d",$value['ctime']);
                }
                if($value['vip_end_time']){
                    $data[$k]['vip_end_time']=date("Y-m-d",$value['vip_end_time']);
                }else{
                    $data[$k]['vip_end_time']="###";
                }
                if($value['last_login_ctime']){
                    $data[$k]['last_login_ctime']=date("Y-m-d H:s",$value['last_login_ctime']);
                }else{
                    $data[$k]['last_login_ctime']="###";
                }
                $data[$k]['last_login_ip']=$value['last_login_ip'];

            }
            $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S');
            $tableheader = array('ID','用户名','姓名','身份证号','推荐人','用户类型','所属区域','今日积分','可用积分','用户积分','银行卡号','银行名称','支行信息','状态','注册时间','vip到期时间','最近登录时间','最近登录IP');
            excel($data,$letter,$tableheader);
        }
        $page=generatePageLink2($params['page'],$count,$url,$count);
        $area=UserModel::getArea();
        $this->getView()->assign("params", $params);
        $this->getView()->assign("area", $area);
        $this->getView()->assign("sum", $sum);
        $this->getView()->assign("page", $page);
        $this->getView()->assign("user", $user);
    }
    //会员添加
    public function userAddAction(){
        $user=$this->getPost();

       // print_r($user);die;
        if(!empty($user['phone'])){
            $conf = Yaf_Application::app()->getConfig()->yaf_mmg;
            $obj = Db\Mysql::getInstance($conf, 'yaf_user_user');
            $result = $obj->where(array('phone'=>$user['phone']))->getOne();
            if($result){
                $url="/index.php/admin/user/userList";
                gotoURL("该手机号已注册！",$url);exit;

            }
            if($user['type']!=2){
                if(!preg_match("/^1[34578]\d{9}$/", $user['phone'])){
                    $url="/index.php/admin/user/userList";
                    gotoURL("手机号格式错误！",$url);exit;
                }
            }
        }
        if($user['password']){
            //验证六位数以上数字，字母
            if(!preg_match("/^[0-9a-zA-Z_#]{6,16}$/", $user['password'])){
                $url="/index.php/admin/user/userList";
                gotoURL("密码格式错误！",$url);exit;
            }
        }
        if(!empty($user['inviteCode'])){
            $where1=array('phone'=>$user['inviteCode']);
            $filed="id";
            $user_parentId=UserModel::userInfo($where1,$filed);
            if(!$user_parentId){
                $url="/index.php/admin/user/userList";
                gotoURL("没有找到推荐人信息！",$url);exit;
            }
            $user['parentId']= $user_parentId['id'];
        }else{
            $user['parentId']='0';
        }

        if($user){
            $user['provinces']=UserModel::getAreaName($user['provinces']);
            $user['urban']=UserModel::getAreaName($user['urban']);
            $user['county']=UserModel::getAreaName($user['county']);
            $info = $this->upload("","custom",array(),"photo");
            if(is_array($info)){
                //上传背景图片
                if($info){
                    if (isset($_FILES['shop_img']) && $_FILES['shop_img']['name']){
                        $user['photo'] = $info['shop_img']['savename'];
                    }
                }else{
                    $user['photo'] = "";
                    $this->error($info);
                }
            }else{
                $user['photo'] = "";
            }
        }
        $result = UserModel::addUserData($user);
        if($result['code']=='1000'){
            if (isset($_SESSION['uid']) && ($_SESSION['uid'] == 1 || $_SESSION['uid'] == 2)) {
                $user_id =$_SESSION['uid'];
                if($result['user_id']){
                    SystemModel::addAdminActionLog($user_id,array("title"=>"添加账号","action_notes"=>json_encode($user)));
                }
            }
            $url="/index.php/admin/user/userList";
            gotoURL($result['msg'],$url);exit;
        }else{
            $url="/index.php/admin/user/userList";
            gotoURL($result['msg'],$url);exit;
        }
        return false;
    }
    //会员信息
    public function userEditAction(){
        $code=$this->get('code') ? $this->get('code') :0;
        $type=$this->get('type') ? $this->get('type') :'';
        if($code !=0){
            if($type=="urban"){
                $urban=UserModel::getArea($code);
                $str= "<option value=''>选择市</option>";
                foreach($urban as $u){
                    $str.="<option value=".$u['code'].">".$u['name']."</option>";
                }
                echo $str;
            }elseif($type=="county"){
                $county=UserModel::getArea($code);
                $str= "<option value=''>选择区</option>";
                foreach($county as $c){
                    $str.="<option value=".$c['code'].">".$c['name']."</option>";
                }
                echo $str;
            }
            die;
        }
        $uid=$this->get('id');
        $area1=UserModel::getArea();
        $user=UserModel::userInfo($uid);
        $user1=UserModel::userInfo1($uid);
        $where=array('user_id_shop'=>$uid);
        $field="id,shop_name,shop_img,shop_xpoint,shop_ypoint,address,user_id_shop";
        $supershop=SuperShopModel::searchSuperShop($where,$field);
        $user['realname']=$user1['realname'];//真实姓名
        $user['inviteCode']=$user1['inviteCode'];//推荐人
        if($user1['last_login_ctime']){
            $user['last_login_ctime']=date('Y-m-d',$user1['last_login_ctime']);//最后一次登录时间
        }else{
            $user['last_login_ctime']="";
        }
        $user['last_login_ip']=$user1['last_login_ip'];//最近登录Ip
        $user['area']=$user['provinces'].$user['urban'].$user['county'];
        if($user1['vip_end_time']){
            $user['etime'] =date("Y-m-d",$user1['vip_end_time']);//会员到期时间
        }else{
            $user['etime']="";
        }
        if($user1['vip_start_time']){
            $user['stime']=date("Y-m-d",$user1['vip_start_time']);//会员开始时间
        }else{
            $user['stime']="";
        }
        if($user['ctime']){
            $user['ctime']=date("Y-m-d",$user['ctime']);//会员注册时间
        }else{
            $user['ctime']="";
        }
        $user_profile = UserModel::getUserProfile($uid);
        $reflect_maidou = $user_profile['reflect_maidou'];   //可转出麦豆
        $spend_maidou = $user_profile['spend_maidou'] + $user_profile['frozen_maidou']; //可消费麦豆=消费麦豆+冻结麦豆数
        $daymaidou_arr = UserModel::getDayMaidou($uid);  //当日获得麦豆数
        $user['reflect_maidou']=$reflect_maidou;//可转出麦豆
        $user['spend_maidou']=$spend_maidou;//可消费麦豆
        $user['daymaidou'] = $daymaidou_arr['daymaidou'] ? $daymaidou_arr['daymaidou'] : 0;//当日麦豆数
        $mode= new CrashModel();
        $res=$mode->cpyIndex( $uid);
        if($res){
            $user['iIndex']=$res[0]['my_performance_index'];//我的业绩指数
        }else{
            $user['iIndex']="0";
        }
        $this->getView()->assign("area", $area1);
        $this->getView()->assign("supershop", $supershop);
        $this->getView()->assign("user", $user);
    }
    //会员修改
    public function userUpdateAction(){
        $user=$this->getPost();
        $uid=$user['uid'];
        if(!empty($user['inviteCode'])){
            $where1=array('phone'=>$user['inviteCode']);
            $fileds="id,area,provinces,urban,county";
            $user_parentId=UserModel::userInfo($where1,$fileds);
            $filed['parentId']= $user_parentId['id']?$user_parentId['id']:'0';
            $filed['area']=$user_parentId['area']?$user_parentId['area']:"西湖区";
            $filed['provinces']=$user_parentId['provinces']?$user_parentId['provinces']:"浙江省";
            $filed['urban']=$user_parentId['urban']?$user_parentId['urban']:"杭州市";
            $filed['county']=$user_parentId['county']?$user_parentId['county']:"西湖区";
        }
        if(!empty($user['password'])){
            $fileds="password";
            $user_password=UserModel::userInfo($uid,$fileds);
            $newpassword=md5($user['password'] . 'erew23423428899oppooo43534');
            if( $newpassword != $user_password['password']){
                $filed['password']=md5($user['password'] . 'erew23423428899oppooo43534');
            }else{
                $url="/index.php/admin/user/userEdit/id/".$uid;
                gotoURL("密码与原密码相同",$url);exit;
            }

        }
        if(!empty($user['provinces']) && !empty($user['urban']) && !empty($user['county']) ){
            $user['provinces']=UserModel::getAreaName($user['provinces']);
            $user['urban']=UserModel::getAreaName($user['urban']);
            $user['county']=UserModel::getAreaName($user['county']);
            $filed['provinces']= $user['provinces']['name'];
            $filed['urban']=$user['urban']['name'];
            $filed['county']=$user['county']['name'];
        }
        //print_r($user);die;
        //表 yaf_user_user 字段修改
        $filed['type']=$user['type'];
        $filed['username']=$user['username'];
        $filed['nickname']=$user['nickname'];
        $filed['phone']=$user['phone'];
        $filed['status']=$user['status'];
        //表 yaf_user_profile 字段修改
        $filed1['realname']=$user['realname'];
        $filed1['inviteCode']=$user['inviteCode'];
        //表 yaf_shop_supershop 字段修改
        $supershop['address']=$user['address'];
        $supershop['shop_xpoint']=$user['shop_xpoint']?$user['shop_xpoint']:0;
        $supershop['shop_ypoint']=$user['shop_ypoint']?$user['shop_xpoint']:0;
        $info = $this->upload("","custom",array(),"photo");
        if(is_array($info)){
            //上传背景图片
            if($info){
                if (isset($_FILES['shop_img']) && $_FILES['shop_img']['name']){
                    $shop_img = $info['shop_img']['savename'];
                }
            }else{
                $shop_img = "";
                $this->error($info);
            }
        }else{
            $shop_img = "";
        }
        if(!empty($shop_img)){
            $supershop['shop_img']=$shop_img;
        }
        $res=UserModel::updataUser($filed,$uid);
        if (isset($_SESSION['uid']) && ($_SESSION['uid'] == 1 || $_SESSION['uid'] == 2)) {
            $user_id =$_SESSION['uid'];
            if($res){
                $filed['uid'] = $uid;
                SystemModel::addAdminActionLog($user_id,array("title"=>"修改用户信息","action_notes"=>json_encode($filed)));
            }
        }
        $res1=UserModel::updataUserProfile($filed1,$uid);
        $res2="";
        if(!empty($user['address'])){
            $where1=array('user_id_shop'=>$uid);
            $supershop_id=SuperShopModel::searchSuperShop($where1,"id");
            if($supershop_id){
                $where2=array('id'=>$user['super_shop_id']);
                $res2=SuperShopModel::updateSuperShop($where2,$supershop);
            }else{
                $supershop['user_id_shop']=$uid;
                $res2=SuperShopModel::addSuperShop($supershop);
            }
        }
        if($res || $res1 || $res2 ){
            $url="/index.php/admin/user/userEdit/id/".$uid;
            gotoURL("修改成功！",$url);exit;
        }else{
            $url="/index.php/admin/user/userEdit/id/".$uid;
            gotoURL("基本信息修改失败",$url);exit;
        }

        return false;
    }
    //会员删除
    public function userDelAction(){
        $uid=$this->get('id');
       // $filed=array("status"=>"4");//暂用（status 状态标记）
       // $res=UserModel::updataUser($filed,$uid);
       $res=UserModel::deleteUser($uid);
       /* if($res){
            $data=array("code"=>'1',"msg"=>'删除成功');
        }else{
            $data=array("code"=>'0',"msg"=>'删除失败');
        }*/
        //print_r($data);die;
        print_r(json_encode($res));die;
        return false;
    }
    //用户转出列表
    public function drawaltoCrashAction(){
        $excel=$this->get('excel');
        //print_r($_GET);
        $model= new UserProfileModel();
        $params['page'] = $this->get('page') > 0 ? $this->get('page') : 1;
        $params['pageSize'] = $this->get('pageSize') > 0 ? $this->get('pageSize') : 10;
        $params['status']=$this->get('status');
        $params['stime']=$this->get('stime')?$this->get('stime'):'';
        $params['phone']=$this->get('phone');
        if(!empty($params['status']) || !empty($params['stime']) || !empty($params['phone']) || $params['status']=='0'){
            $params['status']=$this->get('status');
            $params['stime']=$this->get('stime');
            $params['phone']=$this->get('phone');
            if(!empty($params['phone'])){
                $id='';
                if(preg_match("/^[0-9]+$/", $params['phone'])){
                    if(preg_match("/^1[34578]\d{9}$/", $params['phone'])){
                        $result= UserModel::userInfo(array("phone"=>$params['phone']),"id");
                        if($result){
                            $id=$result['id'];
                        }
                    }else{
                        $where=array('card_no'=>$params['phone']);
                        $result= BankCardModel::cardInfoBycard($where,'uid');
                        if($result){
                            $id=$result['uid'];
                        }

                    }
                }else{
                    $where1=array('realname'=>$params['phone']);
                    $uid=M("yaf_user_profile")->field("uid")->where($where1)->getAll();
                    if($uid){
                        //$ids = array_column($uid, 'uid');
                        foreach($uid as $vo){
                            $ids[]= $vo['uid'];
                        }
                       // print_r($ids);
                    }
                }
            }
            if(!empty($params['status'])|| $params['status']==='0'){
                $where="status='". $params['status']."'";
                $str='status='.$params['status']."&pageSize=". $params['pageSize'];
                if(!empty($id)){
                    $where.=" and user_id='".$id."'";
                    $str.="&phone=".$params['phone'];
                }
                if(!empty($params['stime'])){
                    $where.=" and ctime > ".strtotime($params['stime']);
                    $str.="&stime=".$params['stime'];
                }

            }elseif(!empty($id)){
                $where=" user_id='".$id."'";
                $str="phone=".$params['phone']."&pageSize=". $params['pageSize'];
                if(!empty($params['stime'])){
                    $where.=" and ctime > ".strtotime($params['stime']);
                    $str.="&stime=".$params['stime'];
                }
            }elseif(!empty($params['stime'])){
                $where=" ctime > ".strtotime($params['stime']);
                $str="&stime=".$params['stime']."&pageSize=". $params['pageSize'];
            }else{
                $where="";
                $str="pageSize=". $params['pageSize'];
            }
        }else{
            $where="";
            $str="pageSize=". $params['pageSize'];
        }
        if(!isset($ids)){
            $sum=$model->DrawalRecordList($params,$where,true);
            $params['page']=$params['page']>ceil($sum/$params['pageSize'])?ceil($sum/$params['pageSize']):$params['page'];
            $params['page'] = $params['page'] > 0 ?$params['page']:1;
            $res=$model->DrawalRecordList($params,$where);
            $count=ceil($sum/$params['pageSize']);
        }else{
            //真实姓名查找
            $count=1;
            $ids=implode(',',$ids);
            $p=$params['status'];
            if($p==="0"|| !empty($p)){
                $where2="user_id IN( ".$ids." ) and status=".$params['status'];
            }else{
                $where2="user_id IN( ".$ids." )";
            }
            $sql1="select count('user_id') num from yaf_user_reflect_log where ".$where2;
            $rs=M()->query($sql1);
            if($rs){
                $sum=$rs[0]['num'];
            }else{
                $sum=0;
            }
            $sql="select * from yaf_user_reflect_log where ".$where2." order by user_id asc,ctime desc ";
            $res=M()->query($sql);
        }

        foreach($res as $k=> $v){
            //print_r($v);
            $phone=UserModel::userInfo($v['user_id'],"username,phone ");
            $realname=UserModel::userInfo1($v['user_id']," realname ");
            $res[$k]['phone']=$phone['phone'];
            $res[$k]['realname']=$realname['realname'];
            if($v['ctime']){
                $res[$k]['ctime']=date('Y-m-d',$v['ctime']);
            }else{
                $res[$k]['ctime']="";
            }
            if($v['update_time']){
                $res[$k]['update_time']=date('Y-m-d',$v['update_time']);
            }else{
                $res[$k]['update_time']="";
            }
            $field=" bank_name,branch_info ";
            $cardInfo= BankCardModel::cardInfoBycard("card_no='".$v['bank_card']."'", $field);
            if($cardInfo){
                $res[$k]['bank_name']=$cardInfo['bank_name'];
                $res[$k]['branch_info']=$cardInfo['branch_info'];
            }else{
                $res[$k]['bank_name']="###";
                $res[$k]['branch_info']="###";
            }

        }
        if($excel){
            foreach ($res as $k=>$v) {
                $data[$k]['id']=$v['id'];
                $data[$k]['user_id']=$v['user_id'];
                $data[$k]['phone']=$v['phone'];
                $data[$k]['realname']=$v['realname'];
                $data[$k]['reflect_maidou']=$v['reflect_maidou'];
                $data[$k]['reflect_price']=$v['reflect_price'];
                $data[$k]['record_index']=$v['record_index'];
                $data[$k]['bank_card']="'".$v['bank_card'];
                $data[$k]['bank_name']=$v['bank_name'].$v['branch_info'];
                $is = mb_strstr($v['bank_name'],"建设");
                if($is){
                    $data[$k]['is_bank'] =1;
                }else{
                    $data[$k]['is_bank']= 2;
                }
                if($v['status']==0){
                    $data[$k]['status']="申请中";
                }elseif($v['status']==1){
                    $data[$k]['status']="打款成功";
                }elseif($v['status']==2){
                    $data[$k]['status']="不通过";
                }else{
                    $data[$k]['status']="不通过";
                }
                $data[$k]['ctime']=$v['ctime'];
                $data[$k]['update_time']=$v['update_time'];
            }
            $letter = array('A','B','C','D','E','F','G','H','i','j','k','l','M');
            $tableheader = array('序号','用户ID','用户名','姓名','转出积分','实际金额','业绩指数','转出账号','银行信息','是否本行','转出状态','申请时间','处理时间');
            excel($data,$letter,$tableheader);
            exit;
        }

        if($str){
            $url="drawaltoCrash?".$str;
        }else{
            $url="drawaltoCrash";
        }
        $page=generatePageLink2($params['page'],$count,$url,$count);
        $this->getView()->assign("params", $params);
        $this->getView()->assign("page", $page);
        $this->getView()->assign("sum", $sum);
        $this->getView()->assign("res", $res);
    }
    //转出状态修改
    public function updateDrawalStatusAction(){
        $id=$this->getPost('id');
        $status=$this->getPost('status');
        if(!empty($id) && !empty($status)){
            //$where=array('id'=>$id);
            if (is_array($id)) {
                $ids = implode(",", $id);
                $where = "id in (" . $ids . ") ";
            } else {
                $where = "id = '" . $id . "' ";
            }
            $field=array('status'=>$status,'update_time'=>time());
            $res=UserProfileModel::UpdateDrawalStatus($where,$field);
        }
        if($res){
            if (isset($_SESSION['uid']) && ($_SESSION['uid'] == 1 || $_SESSION['uid'] == 2)) {
                $user_id =$_SESSION['uid'];
                if($res){
                    SystemModel::addAdminActionLog($user_id,array("title"=>"修改转出状态","action_notes"=>json_encode($field)));
                }
            }
            $data=array('code'=>'1000');
        }else{
            $data=array('code'=>'1004');
        }
        print_r(json_encode($data));die;
        return false;
    }
    //我的团队信息
    public function userTeamAction(){
        $params['uid']=$this->get('uid');
        $params['page'] = $this->get('page') > 0 ? $this->get('page') : 1;
        $params['pageSize'] = $this->get('pageSize') > 0 ? $this->get('pageSize') : 50;
        $ajaxteam=$this->get('team');
        $model=TeamModel::getInstance();
        $Oneteam=$model->userLevelOne($params);
        foreach($Oneteam as $k=>$v){
            $user_profile = UserModel::getUserProfile($v['id']);
            $reflect_maidou = $user_profile['reflect_maidou'];   //可转出麦豆
            $spend_maidou = $user_profile['spend_maidou'] + $user_profile['frozen_maidou'];      //可消费麦豆=消费麦豆+冻结麦豆数
            $assets = $reflect_maidou + $spend_maidou; //总资产
            $Oneteam[$k]['assets'] = $assets ? $assets : 0;
        }
        $Twoteam=$model->userLevelTwo($params);
        foreach($Twoteam as $k=>$v){
            $user_profile = UserModel::getUserProfile($v['id']);
            $reflect_maidou = $user_profile['reflect_maidou'];   //可转出麦豆
            $spend_maidou = $user_profile['spend_maidou'] + $user_profile['frozen_maidou'];      //可消费麦豆=消费麦豆+冻结麦豆数
            $assets = $reflect_maidou + $spend_maidou; //总资产
            $Twoteam[$k]['assets'] = $assets ? $assets : 0;
        }
        $Team=array_merge($Oneteam,$Twoteam);
        $sum=$model->getTeamCount($params);
        if($sum['oneTeamCount']>$sum['twoTeamCount']){
            $count=ceil($sum['oneTeamCount']/$params['pageSize']);
        }else{
            $count=ceil($sum['twoTeamCount']/$params['pageSize']);
        }
    /*    $count=1000;//ceil($sum/$params['pageSize']);
        $url="userTeam";
        $page=generatePageLink2($params['page'],$count,$url,$count);
       var_dump($page);die;*/
        if($ajaxteam){
            $str="";
           foreach($Team as $u ){
               $str.="<tr>";
               $str.="<td>".$u['phone']."</td>";
               $str.="<td>".$u['realname']."</td>";
               if($u['type']==0){
                   $str.="<td>普通用户</td>";
               }elseif($u['type']==1){
                   $str.="<td>vip用户</td>";
               }else{
                   $str.="<td>代理商</td>";
               }
               $str.="<td>".$u['assets']."</td>";
               $str.="<td>".$u['ctime']."</td>";
               $str.="</tr>";
           }
           echo $str;die;
        }
        $this->getView()->assign("uid", $params['uid']);
        $this->getView()->assign("sum", $sum);
        $this->getView()->assign("count", $count);
        $this->getView()->assign("team", $Team);
        /*$this->getView()->assign("page", $page);*/
    }
    //省市区
    public function cityAction(){
        $code=$this->get('code') ? $this->get('code') :0;
        $type=$this->get('type') ? $this->get('type') :'';
        if($code !=0){
            if($type=="urban"){
                $urban=UserModel::getArea($code);
                $str= "<option value=''>选择市</option>";
                foreach($urban as $u){
                    $str.="<option value=".$u['code'].">".$u['name']."</option>";
                }
                echo $str;
            }elseif($type=="county"){
                $county=UserModel::getArea($code);
                $str= "<option value=''>选择区</option>";
                foreach($county as $c){
                    $str.="<option value=".$c['code'].">".$c['name']."</option>";
                }
                echo $str;
            }
        }
        return false;
    }
    //实名认证
    public function userRealAction(){
        $data=$this->getPost();
        $model = ThirdPartyModel::getInstance();
        $rs = json_decode($model->checkCardNo($data),true);
        if($rs["isok"]==0){
            $msg=array("msg"=>'查询失败 原因');
            $code="1005";//查询失败 原因
        }else{
            if($rs["code"]==1){
                $conf = Yaf_Application::app()->getConfig()->yaf_mmg;
                $obj = Db\Mysql::getInstance($conf);
                $obj->begintransaction();
                try{
                    $filed=array('bindidcard'=>'1');
                    if($data['num']=='0'){
                        $res=UserModel::updataUser($filed,$data['id']);
                    }else{
                        $res=1;
                    }
                    if($res){
                        $realname=$data['userName'];
                        $IDnumber=$data['cardNo'];
                        $filed2=array('realname'=>$realname,'IDnumber'=>$IDnumber);
                        $res2=UserModel::updataUserProfile($filed2,$data['id']);
                        if (isset($_SESSION['uid']) && ($_SESSION['uid'] == 1 || $_SESSION['uid'] == 2)) {
                            $user_id =$_SESSION['uid'];
                            if($res2){
                                $filed2['uid'] = $data['id'];
                                SystemModel::addAdminActionLog($user_id,array("title"=>"实名认证","action_notes"=>json_encode($filed2)));
                            }
                        }
                        if(!$res2){
                            throw new PDOException("认证信息修改失败");
                        }
                    }else{
                            throw new PDOException("认证状态修改失败");
                    }
                    $obj->commit();
                }catch (PDOException $ex){
                    $obj->rollBack();
                    $msg= array("msg" => $ex->getMessage());
                    print_r(json_encode($msg));die;
                }
                $msg=array("msg"=>'认证成功');
            }elseif($rs["code"]==2){
                $msg=array("msg"=>'不一致');
                $code="1006";//不一致
            }else{
                $msg=array("msg"=>'无此身份证');
                $code="1007";//无此身份证
            }
        }
            print_r(json_encode($msg));die;
    }
    //添加银行卡
    public function addBankcardAction(){
        $data=$this->getPost();
        $data['ctime']=time();
        $res=BankCardModel::addUserBrandCard($data);
        if (isset($_SESSION['uid']) && ($_SESSION['uid'] == 1 || $_SESSION['uid'] == 2)) {
            $user_id =$_SESSION['uid'];
            if($res){
                SystemModel::addAdminActionLog($user_id,array("title"=>"添加银行卡","action_notes"=>json_encode($data)));
            }
        }
        if($res){
            $data=array("msg"=>'添加成功');
            print_r(json_encode($data));die;
        }else{
            $data=array("msg"=>'添加失败');
            print_r(json_encode($data));die;
        }
    }
    //银行卡管理
    public function cardListAction(){
        $data=$this->getPost();
        //修改银行卡
        if(isset($data['card_no'])){
            $model=BankCardModel::getInstance();
            $data['update_time']=time();
            //print_r($data);die;
            $where=array('id'=>$data['id']);
            $res=$model->updateCardStatus($where,$data);
            if (isset($_SESSION['uid']) && ($_SESSION['uid'] == 1 || $_SESSION['uid'] == 2)) {
                $user_id =$_SESSION['uid'];
                if($res){
                    SystemModel::addAdminActionLog($user_id,array("title"=>"修改银行卡","action_notes"=>json_encode($data)));
                }
            }
            if($res){
                $data=array("msg"=>'修改成功');
            }else{
                $data=array("msg"=>'修改失败');
            }
           echo json_encode($data);die;
        }
        //删除银行卡
        if(isset($data['del_id'])){
            $id['id']=$data['del_id'];
            $id['uid']=$data['uid'];
            $model=BankCardModel::getInstance();
            $res=$model->cardDel($id);
            if (isset($_SESSION['uid']) && ($_SESSION['uid'] == 1 || $_SESSION['uid'] == 2)) {
                $user_id =$_SESSION['uid'];
                if($res){
                    SystemModel::addAdminActionLog($user_id,array("title"=>"删除银行卡","action_notes"=>json_encode($id)));
                }
            }
            if($res){
                $data=array("msg"=>'删除成功');
            }else{
                $data=array("msg"=>'删除失败');
            }
            //print_r($data);die;
            echo json_encode($data);die;
        }
        //查看银行卡
        $uid=$this->get('id');
        $where['uid']=$uid;
        $model=BankCardModel::getInstance();
        $cardList=$model->cardList($where);
        $this->getView()->assign("cardList", $cardList);
    }
    //工单列表
    public function workOrderAction(){
        $params['page'] = $this->get('page')? $this->get('page') : 1;
        $params['pageSize'] = $this->get('pageSize')>0?$this->get('pageSize'):10;
        $params['type']=$this->get('type');
        $params['add_time']=$this->get('add_time')?$this->get('add_time'):'';
        $params['keyword']=$this->get('keyword')?$this->get('keyword'):'';
        $params['sta']=$this->get('sta');
        $str="";
        if(!empty($params['type']) ||!empty($params['add_time']) || !empty($params['keyword']) || !empty($params['sta']) || $params['sta']=='0'){
            if(!empty($params['type'])){
                $params['where']="typeid = '".$params['type']."'";
                $str="type=".$params['type']."&pageSize=". $params['pageSize'];
                if(!empty($params['add_time'])){
                    $params['add_time1']=strtotime($params['add_time']);
                    $params['where'].=" and add_time > '".$params['add_time1']." '";//2017-04-22
                    $str.="&add_time=".$params['add_time'];
                }
                if(!empty($params['sta'])||$params['sta']=='0' ){
                    $params['where'].=" and sta = '".$params['sta']." '";
                    $str.="&sta=".$params['sta'];
                }
                if(!empty($params['keyword'])){
                    $where=array("phone"=>$params['keyword']);
                    $user_id=UserModel::userInfo($where,"id");
                    if($user_id){
                        $params['where'].=" and userid = '".$user_id['id']." '";
                        $str.="&keyword=".$params['keyword'];
                    }
                }

            }elseif(!empty($params['sta'])||$params['sta']=='0'){
                $params['where']=" sta = '".$params['sta']." '";
                $str="sta=".$params['sta']."&pageSize=". $params['pageSize'];
                if(!empty($params['keyword'])){
                    $where=array("phone"=>$params['keyword']);
                    $user_id=UserModel::userInfo($where,"id");
                    if($user_id){
                        $params['where'].=" and userid = '".$user_id['id']." '";
                        $str.="&keyword=".$params['keyword'];
                    }
                }
                if(!empty($params['add_time'])){
                    $params['add_time1']=strtotime($params['add_time']);
                    $params['where'].=" and add_time > '".$params['add_time1']." '";//2017-04-22
                    $str.="&add_time=".$params['add_time'];
                }
            }elseif(!empty($params['add_time'])){
                $params['add_time1']=strtotime($params['add_time']);
                $params['where']=" add_time > '".$params['add_time1']."'";//2017-04-22
                $str="add_time=".$params['add_time']."&pageSize=". $params['pageSize'];
                if(!empty($params['keyword'])){
                    $where=array("phone"=>$params['keyword']);
                    $user_id=UserModel::userInfo($where,"id");
                    $params['where'].=" and userid = '".$user_id['id']." '";
                    $str.="&keyword=".$params['keyword'];
                }
            }elseif(!empty($params['keyword'])){
                $where=array("phone"=>$params['keyword']);
                $user_id=UserModel::userInfo($where,"id");
                if($user_id){
                    $params['where']=" userid = '".$user_id['id']." '";
                    $str="keyword=".$params['keyword']."&pageSize=". $params['pageSize'];
                }

            }else{
                $params['where']="";
                $str="pageSize=". $params['pageSize'];
            }
        }else{
            $params['where']="";
            $str="pageSize=". $params['pageSize'];
        }
        if($str){
            $url="workOrder?".$str;
        }else{
            $url="workOrder";
        }
        $model=new WorkFlowModel();
        $workType=$model->getSaleinfo();
        $res=WorkFlowModel::workFlowList($params);
        foreach($res as $k=>$v){
            $user_type=UserModel::userInfo($v['userid'],'phone,type');
            $res[$k]['phone']=$user_type?$user_type['phone']:"";
            $res[$k]['user_type']=$user_type?$user_type['type']:0;
            if($v['add_time']){
                $res[$k]['add_time']=date("Y-m-d H:i:s",$v['add_time']);
            }else{
                $res[$k]['add_time']="";
            }

            if($v['update_time']){
               $res[$k]['update_time']=date("Y-m-d H:i:s",$v['update_time']);
            }else{
               $res[$k]['update_time'] ="";
            }
            if($v['img1'] || $v['img2'] || $v['img3'] ){
               $res[$k]['img'] ="【查看图片】";
            }else{
               $res[$k]['img'] =" ";
            }

        }

        $sum=WorkFlowModel::workFlowList($params,1);
        $count=ceil($sum/$params['pageSize']);
        $page=generatePageLink2($params['page'],$count,$url,$count);
        $this->getView()->assign("params", $params);
        $this->getView()->assign('sum',$sum);
        $this->getView()->assign('page',$page);
        $this->getView()->assign("workType", $workType);
        $this->getView()->assign('res',$res);
    }
    //工单状态修改
    public function updateWorkStaAction(){
        $id=$this->getPost('id');
        $sta=$this->getPost('sta');
        if(!empty($id) && !empty($sta) || $sta==0){
            //$where=array('id'=>$id);
            if (is_array($id)) {
                $ids = implode(",", $id);
                $where = "id in (" . $ids . ") ";
            } else {
                $where = "id = '" . $id . "' ";
            }
            $time=time();
            $field=array('sta'=>$sta,'update_time'=>$time);
            $res=WorkFlowModel::UpdateWorkSta($where,$field);
            if (isset($_SESSION['uid']) && ($_SESSION['uid'] == 1 || $_SESSION['uid'] == 2)) {
                $user_id =$_SESSION['uid'];
                if($res){
                    SystemModel::addAdminActionLog($user_id,array("title"=>"工单状态修改","action_notes"=>json_encode($field)));
                }
            }
        }
        if($res){
            $data=array('code'=>'1000');
        }else{
            $data=array('code'=>'1004');
        }
        print_r(json_encode($data));die;
        return false;
    }
   //工单删除
    public function delWorkflowAction(){
        $id=$this->getPost('id');
        if(!empty($id)){
            //$where=array('id'=>$id);
            if (is_array($id)) {
                $ids = implode(",", $id);
                $where = "id in (" . $ids . ") ";
            } else {
                $where = "id = '" . $id . "' ";
            }
            $res=WorkFlowModel::delWorkflow($where);
            if (isset($_SESSION['uid']) && ($_SESSION['uid'] == 1 || $_SESSION['uid'] == 2)) {
                $user_id =$_SESSION['uid'];
                if($res){
                    SystemModel::addAdminActionLog($user_id,array("title"=>"工单删除","action_notes"=>json_encode($id)));
                }
            }
        }
        if($res){
            $data=array('code'=>'1000');
        }else{
            $data=array('code'=>'1004');
        }
        print_r(json_encode($data));die;
        return false;
    }
   //导入转出Excel表
    public function excelToDrawalAction(){
        if (! empty ( $_FILES ['file_stu'] ['name'] ))
        {
            $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
            $file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
            $file_type = $file_types [count ( $file_types ) - 1];
            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower ( $file_type ) != "xls")
            {
                $this->error ( '不是Excel文件，重新上传' );
            }
            /*设置上传路径*/
            $savePath = APP_PATH . '/public/upload/';
            /*以时间来命名上传的文件*/
            $str = date ( 'Ymdhis' );
            $file_name = $str . "." . $file_type;
            /*是否上传成功*/
            if (! copy ( $tmp_file, $savePath . $file_name ))
            {
                $this->error ( '上传失败' );
            }
              $res =ExcelToArray ( $savePath . $file_name );
              unlink ($savePath . $file_name);
           /* echo "<pre>";
            print_r($res);die;*/

      /*         [1] => Array
        (
            [0] => user_id
            [1] => phone
            [2] => bank_card
            [3] => reflect_maidou
            [4] => ctime
            [5] => record_index
            [6] => reflect_price
        )

           */
          /*  echo "<pre>";
            print_r($res);die;*/
            $conf = Yaf_Application::app()->getConfig()->yaf_mmg;
            $obj = Db\Mysql::getInstance($conf);
            $obj->begintransaction();
            try{
           foreach ( $res as $k => $v )
                {
                    if ($k !=0 && $k !=1)
                    {
                       /* $data['user_id']=$v [0];
                        $data ['bank_card'] = mb_substr($v [2],4);
                        $data ['reflect_maidou'] = $v [3];
                        $data ['ctime'] = time();
                        $data ['record_index'] = $v [5];
                        $data ['reflect_price'] = $v [6];*/
                        //$data['id']=$v[0];
                        //$data['status']=$v[1];
                        //$result = UserModel::addUserReflect($data);

                        if(!empty($v[0]) && !empty($v[1])){
                            //$where=array('id'=>$id);
                            $where = "id = '" . $v[0] . "' ";
                            $field=array('status'=>$v[1],'update_time'=>time());
                            $result=UserProfileModel::UpdateDrawalStatus($where,$field);
                        }
                        if (!$result)
                        {
                            throw new PDOException("修改失败");
                        }
                    }
                }
                $obj->commit();
            }catch (PDOException $ex){
                $obj->rollBack();
                print_r($ex->getMessage());die;
            }
            echo "成功";
        }
        $url="/index.php/admin/user/userList";
        gotoURL("添加成功！",$url);exit;


        return false;
    }
   //导入会员Excel表
    public function excelToUserAction(){
        if (! empty ( $_FILES ['file_stu'] ['name'] ))
        {
            $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
            $file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
            $file_type = $file_types [count ( $file_types ) - 1];
            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower ( $file_type ) != "xls")
            {
                $this->error ( '不是Excel文件，重新上传' );
            }
            /*设置上传路径*/
            $savePath = APP_PATH . '/public/upload/';
            /*以时间来命名上传的文件*/
            $str = date ( 'Ymdhis' );
            $file_name = $str . "." . $file_type;
            /*是否上传成功*/
            if (! copy ( $tmp_file, $savePath . $file_name ))
            {
                $this->error ( '上传失败' );
            }
            $res =ExcelToArray ( $savePath . $file_name );
            unlink ($savePath . $file_name);

            $conf = Yaf_Application::app()->getConfig()->yaf_mmg;
            $obj = Db\Mysql::getInstance($conf);
            $obj->begintransaction();
            $textarr = array();
            try{
            foreach ( $res as $k => $v )
            {
                if ($k != 1 && $k !=0)
                {
                    $data ['username']=$v [0];
                    $where1=array('phone'=>$v[0]);
                    $userid=UserModel::userInfo($where1,"id");
                    if($userid){
                        $textarr[]=$v;
                        continue;
                    }
                    $data ['password'] = md5($v[1].'erew23423428899oppooo43534');
                    $where=array('phone'=>$v[2]);
                    $parentid=UserModel::userInfo($where,"id");
                    if($parentid){
                        $data ['parentId'] = $parentid['id'];
                    }else{
                        $data ['parentId']=0;
                    }
                    $data ['phone'] = $v [0];
                    $arr=explode(',',$v[3]);
                    $data ['provinces']=$arr[0];
                    $data ['urban']=$arr[1];
                    $data ['county']=$arr[2];
                    $data['ctime']=time();
                        $result = UserModel::addUser($data);
                        if ($result)
                        {
                            $data1['uid']=$result;
                            $data1['inviteCode']=$v [2];
                            $res=UserModel::getUserProfile($result);
                            if(!$res){
                                $res=UserModel::addUserProfile($data1);
                            }
                        }else{
                            throw new PDOException($data ['username']."添加失败");
                        }
                }
            }
                $obj->commit();
            }catch (PDOException $ex){
                $obj->rollBack();
                print_r($ex->getMessage());die;
            }
            echo "<pre>";
            var_dump($textarr);
            file_put_contents("log/".date("YmdHis")."log.txt", $textarr,FILE_APPEND );
        }
      echo "导完了";
        return false;
    }

}