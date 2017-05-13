<?php
/**
 *
 * User: zhouyouth
 * Date: 2017/4/1
 * Time: 10:45
 */
class DrawalCrashController extends ApiController {
    private  $maidou=array();//我的上周积分和上上周积分
    private  $txmaidou=array();//我的可转出金额
    public  function init(){
        parent::init();

    }
  //方案二
   public function testAction(){
       $uid=$this->user_id;
       $model=new CrashModel();
       $iIndex= $model->checkWeekMaiodu($uid);//iIndex 为我的业绩指数
       $maxCrash= $model->imaxTixian($uid);//我的最大可转出麦豆
       //我的最大可转出金额
       $iCrash=$maxCrash['reflect_maidou'] *$iIndex*$this->drawalCrash();
       var_dump($iCrash);exit;
   }
    //公司的总业绩指数=(上周公司的利润总额+补贴资金)/本周三转出积分总额
    private function drawalCrash(){
        $lastWeekCpyProfit =360*3;        //上周公司内润总额,假设3060卖出去三单
        $allowance=100;//公司补贴金额
        $cpyProfitIndex=($lastWeekCpyProfit+$allowance)/$this->thisWedALlToCrashAction();
//         var_dump($cpyProfitIndex);exit;array();
        if($cpyProfitIndex){
            if(!round($cpyProfitIndex,2)){
                return "1";
            }
        }
        return  round($cpyProfitIndex,2);  //
    }

    //本周三转出总额
    private  function  thisWedALlToCrashAction(){
        //我的业绩参数=上周积分总额/上上周获取的积分总额
        $i_Profitparmas=round($this->getSession('lmaidou')/$this->getSession('lLmaidou'),2);
//         $i_Profitparmas=1500/1000;//假设我的可转出积分上周积分为1500,上上周为1000
        //每个人业绩参数总和
        $profitParamsAllAverage=1.23;//假设业绩平均值为1.23
        //业绩参数平均值=每个人的个人业绩参数的总和/人数
        //我的业绩指数=我的业绩参数/业绩参数平均值
        $i_Profitindex=$i_Profitparmas/$profitParamsAllAverage;//我的业绩指数
        // ,假设 a,b,c
        //a 的转出总额为
        //b的转出额为1500 ,c为1000
        $b=1500;
        $c=1000;
        //本周三转出总额=(上周我获取的可用于转出总积分*我的业绩指数的总和)指的是上周的所有会员
        //本周三转出总额=a+b+c;
        $thisWedAlltoCrash=($this->getSession('lmaidou')* $i_Profitindex)+$b+$c;//
//         var_dump( $thisWedAlltoCrash);exit;
        return   $thisWedAlltoCrash;
    }


    //我的可转出金额
    /**
     *
     */
    public function getMyDrawaltoCrashAction(){
        if($this->user_id){
            $data['user_id']=$this->user_id;
        }else{
            $data['user_id']=$this->getPost("uid");
            if(empty($data['user_id'])){
                $data['user_id']=$this->get("uid");
            }
            if(empty($data['user_id'])){
                $this->apiReturn((object)array(),"10000",'未登录！');
            }
        }

        $allProfit = UserModel::getUserProfile($data['user_id'],"reflect_maidou");
        $user_info = UserModel::getUserMainInfo($data['user_id'],"phone,type");

        $where = "user_id = '".$this->user_id."'"; //获取所有记录
        $count = UserProfileModel::getDayMaidou("", "", $where, true);
        $finalData[0]['reflect_record']=$count;

        $izong=$allProfit['reflect_maidou'];//我的总转出麦豆
        //公司指数
        $mode= new CrashModel();
        $res=$mode->cpyIndex( $data['user_id']);
        if($res){
            //公司业绩指数
            $cpyIndex=isset($res[0]['week_profit_index']) ? $res[0]['week_profit_index'] :'1';

            //我的业绩指数
            $iIndex=$res[0]['my_performance_index'];
        }else{
            $cpyIndex = 1;
            $iIndex = 1;
        }
        if($user_info['type']==2){
            $finalData[0]['record_index']=1;
        }else if($user_info['type']==1){
            /*
            $cout_data = M("")->table("yaf_user_reflect_log")->field("count(1) as num")->where("user_id='".$this->user_id."'")->getOne();
            $r_count = $cout_data['num'];
            if($r_count){

            }else{
                $finalData[0]['record_index']=1;
            }
            */
            $finalData[0]['record_index']=$iIndex;
        }else{
            $finalData[0]['record_index']=$iIndex;
        }

        //$finalData[0]['record_index']=0.75;
        $finalData[0]['all_record_index']=$cpyIndex;
        $finalData[0]['reflect_maidou']=$izong;
        $finalData[0]['phone']=$user_info['phone'];
        $finalData[0]['type']=$user_info['type'];;

        if(empty($finalData)){
            $this->apiReturn($finalData,1004,"fail");
        }
        $this->apiReturn($finalData,1000,"success!");
    }
    //转出记录
    public function  drawalCrashRecordAction(){
        $data['user_id']=$this->getPost("uid");
        $p=$this->getPost('p');
        $p=isset($p) ? $p:1;
        $model= new UserProfileModel();
        $res=$model->getgetDrawalRecord($data,$p);
        $final=array('code'=>1000,'zongtximaiodu'=>$res['0']['zongtxmaidou'],'msg'=>'success');
        unset($res[0]);
        $final['data']=array_values($res);
        if($res){
            print_r(json_encode($final)) ;exit;
        }
        $this->apiReturn(array(),1004,"fails!");
    }
    //我要转出
    public function  idrawlAction(){

$week =  date("w");
                if($week!=3){
                    $data = array('code' => '1006', "msg" => "未到转出时间");
                    print_r(json_encode($data));
                    die;
                }

        $this->verifyLogin();
        $data['user_id']=$this->getPost("uid");
        $drawalMaidou['umaidou']=$this->getPost('umaidou');
        $check_code = $this->getPost("check_code");
        $data['bank_card']=$this->getPost('bank_card');
        $userinfo = UserModel::getUserMainInfo($data['user_id']);
        /*
        if($data['user_id']!=74972) {
            $data = array('code' => '1006', "msg" => "禁止转出");
            print_r(json_encode($data));
            die;
        }
        */
        if(!$userinfo){
            $data = array('code' => '1006', "msg" => "用户不存在");
            print_r(json_encode($data));
            die;
        }

        if($userinfo['type']==0){
            $data = array('code' => '1006', "msg" => "普通会员不能转出");
            print_r(json_encode($data));
            die;
        }

            
         /*   if($userinfo['type']==1){
                $week =  date("w");
                if($week!=3){
                    $data = array('code' => '1006', "msg" => "未到转出时间");
                    print_r(json_encode($data));
                    die;
                }
            }

            if($userinfo['type']==1){
                $h =  date("h");
                if($h>=12){
                    $data = array('code' => '1006', "msg" => "未到转出时间");
                    print_r(json_encode($data));
                    die;
                }
            }*/
            
            if($userinfo['type']==2){
                $day =  date("d");
                if($day!=15){
                    $data = array('code' => '1006', "msg" => "未到转出时间");
                    print_r(json_encode($data));
                    die;
                }
            }

            /*  暂时屏蔽短信验证
            if (empty($check_code)) {
                $data = array('code' => '1001', "msg" => "验证码不能为空！");
                print_r(json_encode($data));
                die;
            }

            if (!isset($_SESSION['check_code'])) {
                $check_code_true = "";
            } else {
                $check_code_true = $_SESSION['check_code'];
            }
            if (strtolower($check_code) != strtolower($check_code_true)) {
                $data = array('code' => '1001', "msg" => "验证码错误");
                print_r(json_encode($data));
                die;
            }
            */




        if(empty( $data['bank_card'])){
            $this->apiReturn((object)array(),1004,"银行卡不能为空");
        }
        $user_file = UserModel::getUserProfile($data['user_id'],"reflect_maidou");
        if($drawalMaidou['umaidou']>$user_file['reflect_maidou']){
            $this->apiReturn( $drawalMaidou['umaidou'],1004,"超出可转出金额");
        }
        //获取所有记录
        if($userinfo['type']==2){
            //公司的总业绩指数
            $data['all_record_index']=1;

            //我的业绩指数
            $data['record_index']=1;

            //我的转出积分
            $data['reflect_maidou']= $drawalMaidou['umaidou'];

            //转出金额
            $data['reflect_price']= $drawalMaidou['umaidou'];
        }else if($userinfo['type']==1){
            $cout_data = M("yaf_user_reflect_log")->table("yaf_user_reflect_log")->field("count(1) as num")->where("user_id='".$data['user_id']."'")->getOne();
            $count_1 = $cout_data['num'];
            //如果是第一次转出   1:1转出
            if($count_1){
                $mode= new CrashModel();
                $res=$mode->cpyIndex($data['user_id']);
                //总业绩指数
                $cpyIndex=isset($res[0]['week_profit_index']) ? $res[0]['week_profit_index'] :'1';

                //我的业绩指数
                $my_performance_index = $res[0]['my_performance_index'];

                //转出金额  转出金额 = 转出积分* 总业绩指数 * 我的业绩指数
                $iCrash=round($drawalMaidou['umaidou']*$cpyIndex * $my_performance_index,2);

                //我的业绩指数
                $data['record_index']=$res[0]['my_performance_index'];

                //我的转出积分
                $data['reflect_maidou']= $drawalMaidou['umaidou'];

                //公司的总业绩指数
                $data['all_record_index']=$cpyIndex;
                //转出金额
                $data['reflect_price']= $iCrash;
            }else{
                //公司的总业绩指数
                $data['all_record_index']=1;

                //我的业绩指数
                $data['record_index']=1;

                //我的转出积分
                $data['reflect_maidou']= $drawalMaidou['umaidou'];

                //转出金额
                $data['reflect_price']= $drawalMaidou['umaidou'];
            }
        }
        $data['ctime']=time();
        //插入到转出记录表里面
        $model= new UserProfileModel();
		               
        $res=$model->insetDrawlLog($data, $drawalMaidou['umaidou']);
        if(@$res['code']==1003){
            $this->apiReturn((object)array(),1004,$res['msg']);
        if(!$res){
            $this->apiReturn((object)array(),1004,"插入记录表失败!");
        }

        }
        $this->apiReturn($data,1000," drawal success!");

    }

    //获取麦豆记录
    public function daymaidouAction(){
        $show_type = array(
            '1'=>array("title"=>"代理返利","icon"=>"+"),
            '2'=>array("title"=>"平台赠送","icon"=>"+"),
            '3'=>array("title"=>"超市营业额","icon"=>"+"),
            '4'=>array("title"=>"充值","icon"=>"+"),
            '5'=>array("title"=>"商品返利","icon"=>"+"),
            '6'=>array("title"=>"退货","icon"=>"+"),
            '7'=>array("title"=>"消费","icon"=>"-"),
            '8'=>array("title"=>"转出","icon"=>"-"),
            '0'=>array("title"=>"其它","icon"=>""),
            '9'=>array("title"=>"注册赠送","icon"=>"+"),
        );
        $params = array();
        $params['page'] = $this->get("page");
        $params['pagesize'] = $this->get("pagesize");
        $page = !empty($params["page"]) ? $params["page"] : 1;
        $pageSize = !empty($params["pagesize"]) ? $params["pagesize"] : 10;

       // $where = "user_id = '".$this->user_id."' AND DATE_FORMAT(FROM_UNIXTIME(ctime),'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d')"; //获取当天记录
        $where = "user_id = '".$this->user_id."'"; //获取所有记录
        $start = ($page - 1) * $pageSize;
        $count = UserProfileModel::getDayMaidou($start, $pageSize, $where, true);
        $field = "maidou,maidou_type,record_type,ctime";
        $daymaidouList = UserProfileModel::getDayMaidou($start, $pageSize,  $where,false,$field);
        if($daymaidouList){
            foreach($daymaidouList as $key=>$v){
                $daymaidouList[$key]['show_type'] = $show_type[$v['record_type']]['title'];
                $daymaidouList[$key]['show_icon'] = $show_type[$v['record_type']]['icon'];
            }
        }
        if ($daymaidouList) {
            $code = 1000;
            $data = array(
                "count" => $count,
                "thisPage" => $page,
                "pageSize" => $pageSize,
                "countPage" => ceil($count / $pageSize),
                "daimaidouList" => $daymaidouList,
            );
        } else {
            $code = 1004;
            $data = array(
                "count" => $count,
                "thisPage" => $page,
                "pageSize" => $pageSize,
                "countPage" => ceil($count / $pageSize),
                "daimaidouList" => array(),
            );
        }
        $this->apiReturn($data, $code);
    }
    public function selectBankCardAction(){
        $data['userid']=$this->getPost('userid');
        $bank= BankCardModel::getInstance()->selectBrandCard($data);
        if(!$bank){ 
            $this->apiReturn('', 1004,'fails!');
        }
        $this->apiReturn($bank, 1000,'success!');
    }
}
