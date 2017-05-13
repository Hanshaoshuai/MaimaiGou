<?php

/**
 *  商品
 * User: LF
 * Date: 2017/3/27
 */
class BannerController extends ApiController {

    private $model;

    public function init() {        
        $this->model = BannerModel::getInstance();
    } 
    public  function bannerListAction(){
		        //广告图与首页统一  稍后调整完成 删除此代码
        $params["id"] = 46;
        $m = BannerModel::getInstance();
        $rs = $m->bannerList(array("id" => $params["id"]));
        $this->apiReturn($rs);
        //$params=$this->getParams();cal_days_in_month
        $rs=$this->model->bannerList($params);
        return $this->apiReturn($rs);
    }

}
