<?php

use Qiniu\QiNiuOperate;

/**
 *  首页
 * User: LF
 * Date: 2017/3/27
 */
class IndexController extends ApiController {

    private $model;

    public function init() {
        $this->model = IndexModel::getInstance();
    }

    public function IndexAction() {
        $m = BannerModel::getInstance();
        //获取2F广告信息
        $banner1 = $m->bannerList(array("id" => 48));
        $title1 = $m->getBannerTitle(48);
        //获取3F广告信息
        $banner2 = $m->bannerList(array("id" => 50));
        $title2 = $m->getBannerTitle(50);
        //获取4F数据
        $wordOfMouth = $this->model->wordOfMouth();
        $title3 = "口碑最佳";
        //获取5F数据
        $youLike = $this->model->youLike();
        $title4 = "猜你喜欢";
        $this->apiReturn(array(
            "index_2" => array("title" => $title1, "list" => $banner1),
            "index_3" => array("title" => $title2, "list" => $banner2),
            "index_4" => array("title" => $title3, "list" => $wordOfMouth),
            "index_5" => array("title" => $title4, "list" => $youLike),
        ));
    }

    /**
     * 获取广告图片
     * 
     */
    public function bannerAction() {
        $params = $_GET;
        $params["id"] = 46;
        $m = BannerModel::getInstance();
        $rs = $m->bannerList(array("id" => $params["id"]));
        $this->apiReturn($rs);
    }

    public function categoryAction() {
		if($this->getParams()){
			$params = $this->getParams();     
		}else{
			$params = $_GET;     
		}
        $params['status']=1;
        $m = new ProductModel();
        $rs = $m->category($params);
        $rs['code']="1000";
        $rs['msg']="success";
        echo json_encode($rs);exit;
        $this->apiReturn($rs);
    }

}
