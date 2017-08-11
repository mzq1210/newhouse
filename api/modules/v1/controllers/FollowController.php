<?php
/**
 * 用户关注楼盘接口
 * User: <liangshimao>
 * Date: 17-5-17
 * Time: 下午2:16
 */
namespace api\modules\v1\controllers;

use app\components\ActiveController;
use common\logic\FollowLogic;
class FollowController extends ActiveController
{
    /**
     * 获取关注信息
     * <liangshimao>
     */
    public function actionIndex()
    {
        $estateID = $this->request->get('estateID');
        $userID = $this->request->get('userID',1);
        $propertyTypeID = $this->request->get('propertyTypeID',1);
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = FollowLogic::getStatus($userID,$estateID,$propertyTypeID);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        return ['成功', 200, ['results'=>$data]];
    }

    /**
     * 添加关注
     *<liangshimao>
     */
    public function actionAdd()
    {
        $estateID = $this->request->get('estateID');
        $userID = $this->request->get('userID',1);
        $propertyTypeID = $this->request->get('propertyTypeID',1);
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = FollowLogic::addWish($userID,$estateID,$propertyTypeID);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        return ['成功', 200, ['results'=>$data]];
    }

    /**
     * 取消关注
     * <liangshimao>
     */
    public function actionDel()
    {
        $estateID = $this->request->get('estateID');
        $userID = $this->request->get('userID',1);
        $propertyTypeID = $this->request->get('propertyTypeID',1);
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = FollowLogic::cancelWish($userID,$estateID,$propertyTypeID);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
        return ['成功', 200, ['results'=>$data]];
    }
}