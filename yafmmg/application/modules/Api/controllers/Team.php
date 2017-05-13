<?php

/**
 *  我的团队
 * User: LF
 * Date: 2017/3/27
 */
class TeamController extends ApiController {

    private $model;

    public function init() {
        parent::init();
        $this->verifyLogin();
        $this->model = TeamModel::getInstance();
    }
    //我的推荐人
    public function myReferralsAction() {
        $params = $this->getRequest()->getParams();
$params['uid']=$this->user_id;
        $paramsInfo=array("uid"=>"");
        $this->checkParams($params,$paramsInfo);
        $rs = $this->model->myReferrals($params);
        $teamNum=$this->model->getTeamCount($params);
        $this->apiReturn(array_merge($rs,$teamNum));
    }
    //我的推荐人
    public function LevelOneAction() {
        $params = $this->getRequest()->getParams();
        $params['uid']=$this->user_id;
        $paramsInfo=array("uid"=>"");

        $this->checkParams($params,$paramsInfo);
        $rs = $this->model->LevelOne($params);
        $this->apiReturn($rs);
    }
    //我的推荐人
    public function LevelTwoAction() {
        $params = $this->getRequest()->getParams();
        $params['uid']=$this->user_id;
        $paramsInfo=array("uid"=>"");
        $this->checkParams($params,$paramsInfo);
        $rs = $this->model->LevelTwo($params);
        $this->apiReturn($rs);
    }

}
