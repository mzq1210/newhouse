<?php
/**
 * 经纪人接口
 * User: liangshimao
 * Date: 17-4-28
 * Time: 下午4:25
 */

namespace api\modules\v1\controllers;

use common\helper\cache\EstateDetailCache;
use app\components\ActiveController;
use common\logic\AgencyInfoLogic;

class AgencyController extends ActiveController
{
    /**
     * 获取推荐经纪人
     * @param int $estateID 楼盘id（必填）
     * @author <liangshimao>
     */
    public function actionIndex()
    {
        $estateID = $this->request->get('estateID');
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = EstateDetailCache::getAgency($estateID);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
        
        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }

    /**
     * 获取明星经纪人
     * @param int $estateID 楼盘id（必填）
     * @author <liangshimao>
     */
    public function actionSuper()
    {
        $estateID = $this->request->get('estateID');
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = EstateDetailCache::getSuperAgency($estateID);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
        
        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }

    /**
     * 获取经纪人详情信息
     * @return array
     */
    public function actionDetail()
    {
        $agencyID = $this->request->get('agencyID');
        if (empty($agencyID) || !is_numeric($agencyID)) {
            return ['经纪人id为空或不合法', 201];
        }
        
        try {
            $data = AgencyInfoLogic::getAgencyInfo($agencyID);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }

    /**
     * 获取经纪人详情信息
     * @return array
     */
    public function actionStatus()
    {
        $user = $this->request->get('user');
        if (empty($user)) {
            return ['经纪人环信id为空', 201];
        }

        try {
            $data = AgencyInfoLogic::getAgencyStatus($user);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }
}