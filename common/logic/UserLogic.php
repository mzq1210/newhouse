<?php
/**
 * Created by PhpStorm.
 * User: luohua
 * Date: 17-5-27
 * Time: 下午4:52
 */
namespace common\logic;

use common\models\user\UserInfo;

class UserLogic{
    /**
     * 获取用户详情信息
     * @param int $userID
     * @return array|null|\yii\db\ActiveRecord
     * <wangluohua>
     */
    public static function getUserInfo($userID){
        return UserInfo::selectOneRecord($userID,"nickName,avatarImageName,realName,cellphone,email,registrationDate,userID,gender");
    }
}