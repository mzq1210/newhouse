<?php
/**
 * 用户信息接口.
 * User: luohua
 * Date: 17-5-27
 * Time: 下午4:48
 */

namespace api\modules\v1\controllers;


use app\components\ActiveController;
use common\logic\UserLogic;

class UserController extends ActiveController
{
    /**
     * 用户基本信息查询
     * @param int $userID 用户id（必填）
     * @author <wangluohua>
     */
    public function actionIndex()
    {
        $userID = $this->request->get('userID');
        if (empty($userID) || !is_numeric($userID)) {
            return ['用户id为空或不合法', 201];
        }
        try {
            $data = UserLogic::getUserInfo($userID);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }
}