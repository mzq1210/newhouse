<?php
/**
 * 用户基本信息表
 * User: <liangshimao>
 * Date: 17-4-5
 * Time: 下午5:27
 */

namespace common\models\user;
use yii\db\ActiveRecord;

class UserInfo extends ActiveRecord
{
    public static function tableName() {
        return 'User_BasicInfo';
    }

    public static function tableDesc() {
        return '注册用户扩展信息表';
    }

    /**
     * 获取用户基本信息
     * @Author: <liangshimao>
     * @Date: 2017-03-29
     * @Return: Array
     */
    public static function selectOneRecord($userID, $fields = '*'){
        if(!is_numeric($userID)) return false;
        return self::find()->select($fields)->where(['userID' => $userID])->asArray()->one();
    }
}