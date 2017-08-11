<?php
/**
 * 楼盘详情api接口控制器
 * User: <liangshimao>
 * Date: 17-4-24
 * Time: 下午3:29
 */

namespace api\modules\v1\controllers;

use app\components\ActiveController;
use common\logic\EstateDetailLogic;

class DetailController extends ActiveController
{
    /**
     * 获取楼盘基本信息
     * @param int $estateID 楼盘id（必填）
     * @param int $companyCode 城市公司id（必填）
     * @author liangshimao
     */
    public function actionBasic()
    {
        $estateID = $this->request->get('estateID');
        $propertyTypeID = $this->request->get('propertyTypeID',ESTATE_PROPERTY_TYPE);
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = EstateDetailLogic::getBasic($estateID,$propertyTypeID);
            $data['commentNum'] = 0;
            if($data['collaborationType'] == 1){
                $data['commentNum'] = $data['totalSeeHouseReviewCount'] + $data['totalReviewCount'];
            }else{
                $data['commentNum'] = $data['totalReviewCount'];
            }
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }

    /**
     * 获取楼盘简要信息
     * @param int $estateID 楼盘id（必填）
     * @author liangshimao
     */
    public function actionSimple()
    {
        $estateID = $this->request->get('estateID');
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = EstateDetailLogic::getSimple($estateID,ESTATE_PROPERTY_TYPE);
            if(empty($data)){
                $data = EstateDetailLogic::getSimple($estateID,BUSINESS_PROERTY_TYPE);
            }
            if(empty($data)){
                $data = EstateDetailLogic::getSimple($estateID,OFFICE_PROERTY_TYPE);
            }
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }

    /**
     *获取经纪人带看评价（分页实现）
     * @param int $estateID 楼盘id（必填）
     * @param int $page 页码（选填）
     * @param int $pageSize 每页显示多少（选填）
     * @author <liangshimao>
     */
    public function actionTask()
    {
        $estateID = $this->request->get('estateID');
        $page = $this->request->get('page',1);
        $pageSize = $this->request->get('pageSize',PAGESIZE);
        if (empty($estateID) || !is_numeric($estateID)){
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = EstateDetailLogic::getAgencyTask($estateID, $pageSize, $page);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, $data];
    }
    

    /**
     * 获取用户评价信息(分页实现)
     * @param int $estateID 楼盘id（必填）
     * @param int $page 页码（选填）
     * @param int $pageSize 每页显示多少（选填）
     * @author <liangshimao>
     */
    public function actionReview()
    {
        $estateID = $this->request->get('estateID');
        $page = $this->request->get('page',1);
        $pageSize = $this->request->get('pageSize',PAGESIZE);
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = EstateDetailLogic::getUserReview($estateID, $pageSize, $page);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, $data];
    }

    /**
     * 用户评价
     * @param  array $params 插入信息
     * @author <wangluohua>
     */
    
    public function actionInreview()
    {
        $data = $this->request->post();
        if (empty($data['estateID'])) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $inResult = EstateDetailLogic::inUserReview($data);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if($inResult == true){
            $result['results'] = '插入成功';
            return ['插入成功', 200 ,$result];
        }
        $result['results'] = '插入失败';
        return ['插入失败', 200, $result];
    }

    /**
     * 获取同城楼盘推荐
     * @param int $estateID 楼盘id（必填）
     * @param int $companyCode 城市公司id（必填）
     * @author <liangshimao>
     */
    public function actionRecommend()
    {
        $estateID = $this->request->get('estateID');
        $propertyTypeID = $this->request->get('propertyTypeID',ESTATE_PROPERTY_TYPE);
        $pageSize = $this->request->get('pageSize',4);
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }

        try {
            $area = EstateDetailLogic::getDataFromSolr($estateID, $propertyTypeID, 'estateAreaId,companyCode');
            $estateAreaId = empty($area['estateAreaId'])?0:$area['estateAreaId'];
            $companyCode = empty($area['companyCode'])?0:$area['companyCode'];
            $data = EstateDetailLogic::getRecommend($estateID, $estateAreaId, $companyCode,$propertyTypeID,$pageSize);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }


        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }

    /**
     * 获取用户购买意向
     * @author <wangluohua>
     */
    public function actionPurchaseintention(){
        try{
           $result =  EstateDetailLogic::getPurchaseIntention();
        }catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
        if(empty($result)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$result]];
    }

    /**
     * 记录用户点击次数
     */
    public function actionViewlog()
    {
        $estateID = $this->request->get('estateID');
        $propertyTypeID = $this->request->get('propertyTypeID',ESTATE_PROPERTY_TYPE);
        $userID = $this->request->get('userID',0);
        $clientType = $this->request->get('clientType',0);
        $companyCode = $this->request->get('companyCode',0);
        try{
            $result =  EstateDetailLogic::setViewLog($estateID,$propertyTypeID,$userID,$clientType,$companyCode);
        }catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
        if(empty($result)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$result]];
        
    }
}