<?php

/**
 *
 * User: zhouyouth
 * Date: 2017/3/27
 * Time: 13:41
 */
class  UserModel extends BaseModel
{
    //操作数据库
    public $table = "yaf_user_user";
    public function checklogin($tel, $pwd)
    {
        $pw = md5($pwd . 'erew23423428899oppooo43534');
        $conf = Yaf_Application::app()->getConfig()->yaf_mmg;
        $obj = Db\Mysql::getInstance($conf, $this->table);
        $res = $obj->query("select id,status from yaf_user_user where phone='" . $tel . "'");
        if (empty($res) || $res[0]['status']==1) {
            return false;
        }
        $result = $obj->query("select * from yaf_user_user where (username='".$tel."' OR phone='" . $tel . "') and password=" . "'" . $pw . "'");
        if (empty($result)) {
            return false;
        }
        //登录成功
        setSession('userID', $result['0']['id']);
        setSession('usertel', $result['0']['phone']);
        return $res;
    }
    public  function  insertToken($data){
        $model= M('yaf_user_login_token');
        $result= $model->where(array('user_id'=>$data['user_id']))->getOne();
        if($result){
            $model->where(array('user_id'=>$data['user_id']))->update($data);
            $data= $model->where(array('user_id'=>$data['user_id']))->getOne();
            return $data;
        }
        $res=$model->insert($data);
        $data=$model->where(array('id'=>$res))->getOne($data);
        if($res==="0"){
            return false;
        }
        return $data;
    }

    public function register($data = array(),$profile=array())
    {
        $conf = Yaf_Application::app()->getConfig()->yaf_mmg;
        $obj = Db\Mysql::getInstance($conf, $this->table);
        $zt = $obj->query("select id from yaf_user_user where phone='" . $data['phone'] . "'");
        if($zt){
            $exist=array("code"=>'1001',"msg"=>"phone already has been register!");
            print_r(json_encode($exist));die;
        }

        $res = $obj->insert($data);
        if($res){
            $activities_conf = \Yaf_Application::app()->getConfig()->activities->register;
            $now = time();
            if($now>strtotime($activities_conf->start_time) && $now<strtotime($activities_conf->end_time)){
                $records_data = array("user_id"=>$res,"maidou"=>$activities_conf->maidou,"maidou_type"=>5,"record_type"=>9,"notes"=>"注册平台送超市麦豆:".$activities_conf->maidou);
                self::addMaidouLog($records_data);
                $profile['supermarket_maidou'] = $activities_conf->maidou;
            }
            $profile['uid']=$res;
            $obj = Db\Mysql::getInstance($conf, 'yaf_user_profile');
            $result=$obj->insert($profile);
        }
        return true;

    }
    public  function  restPwd($data){
        $conf = Yaf_Application::app()->getConfig()->yaf_mmg;
        $obj = Db\Mysql::getInstance($conf, $this->table);
        $res = $obj->query("select id,phone,password from yaf_user_user where phone='" . $data['phone'] . "'");
        if (empty($res)) {
            $exist=array("code"=>1001,"msg"=>"phone is not exist!");
            print_r(json_encode($exist));die;
        }
        $data['password']=MD5(($data['password'] . 'erew23423428899oppooo43534'));
        $res=$obj->where(array('phone'=>$data['phone']))->update(array('password'=>$data['password']));
        if(empty($res)&& $res!==0){
           return false;
        }
        return true;
    }
    /**
     * 添加/更新用户送货地址
     * @param int $user_id 用户ID号
     * @param array $post 表单
     */
    public static function updateUserAddress($user_id, $post)
    {
        if (intval($user_id) == 0) return false;

        foreach ($post as $key => &$value) {
            $value = htmlspecialchars($value);
        }
        $address_id = isset($post['address_id']) ? intval($post['address_id']) : 0;
        $data = array(
            'user_id' => $user_id,
            'delivery_name' => trim($post['delivery_name']),
            'delivery_phone' => isset($post['delivery_phone']) ? trim($post['delivery_phone']) : '',
            'delivery_province' => isset($post['delivery_province']) ? trim($post['delivery_province']) : '',
            'delivery_urban' => isset($post['delivery_urban']) ? trim($post['delivery_urban']) : '',
            'delivery_county' => isset($post['delivery_county']) ? trim($post['delivery_county']) : '',
            'delivery_address' => isset($post['delivery_address']) ? trim($post['delivery_address']) : '',
            'ctime' => time(),
        );
        if (isset($post['is_default']) && $post['is_default']) {
            $data['is_default'] = 1;
        }

        $address_db = M('yaf_user_address');
        if (!$address_id) {
            $add_data = $address_db->where("user_id='".$user_id."'")->getOne();
            if(empty($add_data)){  //如果第一次添加  设置为默认地址
                $data['is_default'] = 1;
            }
            $affected = $address_id = $address_db->insert($data);
        } else {
            $affected = $address_db->where('id=' . $address_id)->update($data);
        }

        //设置其他不为默认
        if (isset($data['is_default']) && $data['is_default']) {
            $address_db->where("user_id='".$user_id."' and id!='" . $address_id . "'")->update(array('is_default' => 0));
        }
        return $affected;
    }
    //修改默认地址
    public static function updataDefaultAddress($address_id,$user_id){
        //设置其他不为默认
        $address_db = M('yaf_user_address');
        $address_db->where("user_id='".$user_id."' and id!='" . $address_id . "'")->update(array('is_default' => 0));
        return $address_db->where("user_id='".$user_id."' and id='" . $address_id . "'")->update(array('is_default' => 1));
    }

    //删除收货地址
    public static function deleltAddress($address_id,$user_id){
        $where = "user_id='" . $user_id . "'";
        if (is_array($address_id)) {
            $ids = implode(",", $address_id);
            $where .= " and id in (" . $ids . ") ";
        } else {
            $where .= " and id = '" . $address_id . "' ";
        }
        $re_d = M("yaf_user_address")->where($where)->update(array('is_delete' => 1));
        $d_address = M("yaf_user_address")->where("user_id='" . $user_id . "' and is_default=1 and is_delete=0")->getOne();
        if($d_address){
            return $re_d;
        }else{
            $d_address_new = M("yaf_user_address")->where("user_id='" . $user_id . "'  and is_delete=0 ")->order(array("id"=>"desc"))->getOne();
            if($d_address_new){
                return M("yaf_user_address")->where("user_id='" . $user_id . "' and id='".$d_address_new['id']."'  and is_delete=0 ")->update(array('is_default' => 1));
            }else{
                return true;
            }
        }

    }

    //删除收货地址
    public static function deleltAddress_old($address_id,$user_id){
        $where = "user_id='" . $user_id . "'";
        if (is_array($address_id)) {
            $ids = implode(",", $address_id);
            $where .= " and id in (" . $ids . ") ";
        } else {
            $where .= " and id = '" . $address_id . "' ";
        }
        $re_d = M("yaf_user_address")->where($where)->delete();
        $d_address = M("yaf_user_address")->where("user_id='" . $user_id . "' and is_default=1")->getOne();
        if($d_address){
            return $re_d;
        }else{
            $d_address_new = M("yaf_user_address")->where("user_id='" . $user_id . "'")->order(array("id"=>"desc"))->getOne();
            if($d_address_new){
                return M("yaf_user_address")->where("user_id='" . $user_id . "' and id='".$d_address_new['id']."'")->update(array('is_default' => 1));
            }else{
                return true;
            }
        }

    }

    //根据id获取收货地址
    public static function getAddressById($id,$field="*"){
        return M("yaf_user_address")->field($field)->where(array("id"=>$id))->getOne();
    }
    //获取默认收货地址
    public static function getUserDefaultAddress($user_id){
        return M("yaf_user_address")->where(array("user_id"=>$user_id,"is_default"=>1))->getOne();
    }
    //收货地址列表
    public static function getAddressList($start=0, $pageSize=10, $where=array(), $isGetCount = false,$field="*"){
        $mod = M("yaf_user_address");
        $where['is_delete'] = 0;
        if ($isGetCount) {//计算总条数
            $count = $mod->where($where)->getRowCount();
            return $count;
        } else {
            $list = $mod->field($field)->where($where)->order(array("is_default"=>"desc"))->getAll();
            return $list;
        }
    }
    //获取用户副表信息
    public static function getUserProfile($user_id,$field="*"){
        return M("yaf_user_profile")->field($field)->where(array("uid"=>$user_id))->getOne();
    }
    //获取用户主表信息
    public static function getUserMainInfo($user_id,$field="*"){
        return M("yaf_user_user")->field($field)->where(array("id"=>$user_id))->getOne();
    }
    //今日麦豆数
    public static function getDayMaidou($user_id){
        $data =  M("yaf_user_maidoul_log")->field("Sum(maidou) as daymaidou")->where("user_id='".$user_id."' and day(FROM_UNIXTIME(ctime, '%Y-%m-%d %H:%i:%S'))=day(now())")->getOne();
        return $data;

    }
    //根据用户ID 查找用户信息
    public function getUserInfo($params){
        $uid=$params['uid'];
        $user['info']= M("yaf_user_user")->field("username,phone,ctime,provinces,urban,county,type,bindidcard")->where(array("id"=>$uid))->getAll();
        $user['logo']= M('yaf_user_profile')->field("logo,vip_start_time,vip_end_time")->where(array("uid"=>$uid))->getOne();
        $user['Payment_num']=M("yaf_shop_order_product")->where(array("user_id"=>$uid,"order_status"=>0))->getRowCount();
        $user['Shipping_num']=M("yaf_shop_order_product")->where(array("user_id"=>$uid,"order_status"=>2))->getRowCount();
        $user['shipment_num']=M("yaf_shop_order_product")->where(array("user_id"=>$uid,"order_status"=>1))->getRowCount();
        $user['commet_num']=M("yaf_shop_comment")->where(array("uid"=>$uid,"status"=>0))->getRowCount();
        return $user;
    }

    //根据用户id 查找用户密码
    public function getPwd($uid){
        return M("yaf_user_user")->field("password")->where(array("id"=>$uid))->getOne();
    }
    //根据用户id 修改用户密码
    public function savePwd($uid,$pwd){
        return M("yaf_user_user")->where(array("id"=>$uid))->update(array("password"=>$pwd));
    }

    public static function getRegisterUser($start=0, $pageSize=10, $where=array(), $isGetCount = false,$field="*"){
        $mod = M("yaf_user_user");
        if ($isGetCount) {//计算总条数
            $count = $mod->where($where)->getRowCount();
            return $count;
        } else {
            $list = $mod->field($field)->where($where)->limit($start,$pageSize)->getAll();
            return $list;
        }
    }

    public static function addUserData($user){
        $field=array(
            'username'=>$user['username'],
            'password'=>md5($user['password'] . 'erew23423428899oppooo43534'),
            'phone'=>$user['phone'],
            'parentId'=>$user['parentId'],
            'provinces'=>$user['provinces']['name'],
            'urban'=>$user['urban']['name'],
            'county'=>$user['county']['name'],
            'type'=>$user['type'],
            'ctime'=>time(),
        );
        $conf = Yaf_Application::app()->getConfig()->yaf_mmg;
        $obj = Db\Mysql::getInstance($conf);
        $obj->begintransaction();
        try{
            $res = M("yaf_user_user")->insert($field);
            if($res) {
                if (!empty($user['inviteCode'])) {
                    $tuiPhone['uid'] = $res;
                    $tuiPhone['inviteCode'] = $user['inviteCode'];
                    M("yaf_user_profile")->insert($tuiPhone);
                    $result=M("yaf_user_profile")->where("uid=".$res)->getOne();
                    if(!$result){
                       throw new PDOException("会员信息添加失败","1004");
                    }
                }else{
                    $tuiPhone['uid'] = $res;
                    M("yaf_user_profile")->insert($tuiPhone);
                    $result=M("yaf_user_profile")->where("uid=".$res)->getOne();
                    if(!$result){
                        throw new PDOException("会员信息添加失败","1004");
                    }
                }
                if(!empty($user['address'])){
                    $super['address']=$user['address'];
                    $super['shop_xpoint']=!empty($user['shop_xpoint'])?$user['shop_xpoint']:0;
                    $super['shop_ypoint']=!empty($user['shop_ypoint'])?$user['shop_ypoint']:0;
                    $super['shop_img']=$user['photo'];
                    $super['user_id_shop']=$res;
                    //print_r($super);
                    $result=SuperShopModel::addSuperShop($super);
                    if(!$result){
                        $result = false;
                        throw new PDOException("超市添加失败","1004");
                    }
                }
            }else{
                throw new PDOException("添加失败","1004");
            }
            $obj->commit();
        }catch (PDOException $ex) {
            $obj->rollBack();
            //return json_encode(array("data" => "","msg" => $ex->getMessage()));
            return array("code" => $ex->getCode(),"msg" => $ex->getMessage());
        }
        return array("code"=>'1000',"msg" => "添加成功","user_id"=>$tuiPhone['uid']);
    }
    //用户列表
    public function userList($params,$filed="*",$getCount=FALSE){
        if($getCount){
            return M("yaf_user_user")->getRowCount();
        }else {
            $limit1 = ($params['page'] - 1) * $params['pageSize'];
            return M("yaf_user_user")->field($filed)->limit($limit1, $params['pageSize'])->getAll();
        }
    }
    //搜索会员
    public function searchUser($params,$where,$filed="*",$getCount=FALSE){
        if($getCount){
             $data=M("yaf_user_user")->field("count(id) as count")->where($where)->getOne();
             return $data['count'];
        }else {
            $limit1 = ($params['page'] - 1) * $params['pageSize'];
            return M("yaf_user_user")->field($filed)->where($where)->order("id desc")->limit($limit1, $params['pageSize'])->getAll();
        }
    }

    //根据属性差用户信息
    public static function userInfo($where,$field="*"){
        if(!is_array($where)){
            $where=array("id"=>$where);
        }else{
            $where=$where;
        }
        return M("yaf_user_user")->field($field)->where($where)->getOne();
    }
    public static function userInfo1($where,$field="*"){
        if(!is_array($where)){
            $where=array("uid"=>$where);
        }else{
            $where=$where;
        }
        return M("yaf_user_profile")->field($field)->where($where)->getOne();
    }
    //地址查询
    public static function getArea($pid=0){
        return M("yaf_system_city_area_street")->field("code,parentId,name,level")->where(array("parentId"=>$pid))->getAll();
    }
    public static function getAreaName($code){
        return M("yaf_system_city_area_street")->field("name")->where(array("code"=>$code))->getOne();
    }
    //会员添加
    public static function addUser($field){
        return M("yaf_user_user")->insert($field);
    }
    public static function addUserProfile($field){
        return M("yaf_user_profile")->insert($field);
    }
    //会员修改/(软删除)
    public static function updataUser($field,$uid){
        if($uid) {
            if (is_array($uid)) {
                $ids = implode(",", $uid);
                $where = "id in (" . $uid . ") ";
            } else {
                $where = "id = '" . $uid . "' ";
            }
            return M("yaf_user_user")->where($where)->update($field);
        }else{
            return false;
        }
    }

    //会员删除
    public static function deleteUser($uid){
        if($uid) {
            if (is_array($uid)) {
                return false;
                /*$ids = implode(",", $uid);
                $where = "id in (" . $uid . ") ";*/
            } else {
                $where = "id = '" . $uid . "' ";
            }
            $supershop=M("yaf_shop_supershop")->where("user_id_shop=".$uid)->getOne();
            $conf = Yaf_Application::app()->getConfig()->yaf_mmg;
            $obj = Db\Mysql::getInstance($conf);
            $obj->begintransaction();
            try{
                $res= M("yaf_user_user")->where($where)->delete();
                if($res){
                    if($supershop){
                        $res=M("yaf_shop_supershop")->where("user_id_shop=".$uid)->delete();
                        if(!$res){
                            throw new PDOException("删除超市信息失败");
                        }
                    }
                    $res=M("yaf_user_profile")->where("uid=".$uid)->delete();
                    if(!$res){
                        throw new PDOException("删除会员信息失败");
                    }
                }else{
                    throw new PDOException("删除会员失败");
                }
                $obj->commit();
            }catch (PDOException $ex) {
                $obj->rollBack();
                return array("code" => "0","msg" => $ex->getMessage());
            }
            return array("data" => "1","msg" =>"会员成功");
        }else{
            return false;
        }
    }

    //更新用户附表信息
    public static function updataUserProfile($field,$uid){
        return M("yaf_user_profile")->where(array('uid'=>$uid))->update($field);
    }

    //根据用户id 获取一张银行卡
    public static function cardByUserId($uid){
        return M('yaf_user_bankcard')->where(array("uid"=>$uid))->getOne();
    }


    //用户的麦豆记录
    public static function addMaidouLog($data){
        $data = array(
            'user_id' => isset($data['user_id']) ? intval($data['user_id']) : 0,
            'maidou_type' => isset($data['maidou_type']) ? intval($data['maidou_type']) : 0,  //麦豆类型 1：可消费麦豆 2：可转出麦豆 3：1:1转出麦豆 4：消费麦豆+转出麦豆
            'maidou' => isset($data['maidou']) ? intval($data['maidou']) : 0,                    //麦豆数
            'record_type' => isset($data['record_type']) ? intval($data['record_type']) : 1, //记录类型 1：代理返 2：vip推广 3：超市营业额 4：充值 5：商品返利 6：退货 7:消费 8:转出
            'ctime' => time(),
            'notes' => isset($data['notes']) ? trim($data['notes']) : ''   //备注
        );
        $re_id = M("yaf_user_maidoul_log")->insert($data);
        if ($re_id) {
            return $re_id;
        } else {
            return false;
        }
    }
 //添加提现记录
    public static function addUserReflect($field ){
        return M("yaf_user_reflect_log")->insert($field);
    }

}