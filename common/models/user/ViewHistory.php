<?php
/**
 * 用户浏览历史model类
 * User: <liangshimao>
 * Date: 17-4-13
 * Time: 上午10:40
 */

namespace common\models\user;


use yii\db\ActiveRecord;

class ViewHistory extends ActiveRecord
{
    public static function tableName() {
        return 'User_ViewHistory';
    }

    public static function tableDesc() {
        return '注册用户浏览历史';
    }

    public static function insertRecord($userID,$estateID,$propertyTypeID)
    {
        $model = new self();
        $model->setAttributes([
            'userID' => $userID,
            'estateID' => $estateID,
            'propertyTypeID' => $propertyTypeID,
            'isActive' => 1,
            'inDate' => date('Y-m-d H:i:s'),
            'lastEditDate' => date('Y-m-d H:i:s'),
        ],false);
        if($model->save()){
            return true;
        }
        return false;
    }
}