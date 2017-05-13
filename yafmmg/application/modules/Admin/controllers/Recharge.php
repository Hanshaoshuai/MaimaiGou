<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/10
 * Time: 17:10
 */
class RechargeController extends AdminController
{
    //获取充值卡套餐列表
    public function rechargeCardListAction(){
        $params = array();
        $params['page'] = $this->get("page");
        $params['pageSize'] = $this->get("pageSize");
        $page = !empty($params["page"]) ? $params["page"] : 1;
        $pageSize = !empty($params["pageSize"]) ? $params["pageSize"] : 10;
        $where = array("1" => 1);

        $start = ($page - 1) * $pageSize;
        $count = RechargeModel::getRechargeCard($start, $pageSize, "type", "desc",  $where, true);
        $recharge_card_list = RechargeModel::getRechargeCard($start, $pageSize, "type", "desc",  $where,false);
        $this->assign("count",$count);
        $this->assign("recharge_card_list",$recharge_card_list);
    }

    //添加充值卡套餐
    public function addRechargeCardAction(){
        $product_serve_post = $this->getPost();
        if(isset($product_serve_post['dosubmit'])){
            if(isset($product_serve_post['start_time'])){
                if($product_serve_post['start_time']){
                    $product_serve_post['start_time'] = strtotime($product_serve_post['start_time']);
                }else{
                    $product_serve_post['start_time'] = 0;
                }

            }
            if(isset($product_serve_post['end_time'])){
                if($product_serve_post['end_time']){
                    $product_serve_post['end_time'] = strtotime($product_serve_post['end_time']);
                }else{
                    $product_serve_post['end_time'] = 0;
                }

            }
            RechargeModel::addRechargeCard($product_serve_post);
            $url = "/index.php/admin/Recharge/rechargeCardList";
            jsRedirect($url);
        }
    }

    //删除充值卡套餐
    public function delRechargeCardAction()
    {
        $id = $this->getPost("id");
        $result = RechargeModel::delRechargeCard($id);
        if($result){
            echo json_encode(array("status"=>"1000","id"=>$result));
        }else{
            echo json_encode(array("status"=>"1004","id"=>0));
        }
        exit;
    }
    //修改充值卡套餐
    public function updateRechargeCardAction(){
        $recharge_card_id = $this->get("recharge_card_id");
        $data = $this->getPost();
        if(isset($data['dosubmit']) && $data['dosubmit']){
            if(isset($data['start_time'])){
                if($data['start_time']){
                    $data['start_time'] = strtotime($data['start_time']);
                }else{
                    $data['start_time'] = 0;
                }

            }
            if(isset($data['end_time'])){
                if($data['end_time']){
                    $data['end_time'] = strtotime($data['end_time']);
                }else{
                    $data['end_time'] = 0;
                }

            }
            $result = RechargeModel::updateRechargeCard($data['id'],$data);
            if(isset($data['is_ajax']) && $data['is_ajax']){
                if($result){
                    echo json_encode(array("status"=>"1000","id"=>$result));
                }else{
                    echo json_encode(array("status"=>"1004","id"=>""));
                }
                exit;
            }else{
                $url = "/index.php/admin/Recharge/rechargeCardList";
                jsRedirect($url);
            }
        }else{
            $recharge_card = RechargeModel::getRechargeCardById($recharge_card_id);
            $this->assign("recharge_card",$recharge_card);
        }

    }
}