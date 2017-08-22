<?php

/**
 *用户关注楼盘列表
 * <liangshimao>
 */

namespace common\models\user;

use Yii;
use yii\db\ActiveRecord;

class WishList extends ActiveRecord
{
    public static function tableName() {
        return 'User_Wishlist';
    }

    public static function tableDesc() {
        return '注册用户关注楼盘';
    }

    /**
     * 增加楼盘关注
     * @param $userID
     * @param $estateID
     * @param $propertyTypeID
     * @return bool
     * <liangshimao>
     */
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

    /**
     * 取消关注
     * <liangshimao>
     */
    public static function editRecord($userID,$estateID,$propertyTypeID,$params)
    {
        $model = self::find()->where(['estateID'=>$estateID,'propertyTypeID'=>$propertyTypeID,'userID'=>$userID])->one();
        if(empty($model)){
            return false;
        }
        $model->setAttributes($params,false);
        if($model->save()){
            return true;
        }
        return false;
    }

    /**
     * 查看用户关注状态
     *<liangshimao>
     */
    public static function getWishStatus($userID,$estateID,$propertyTypeID)
    {
        $model = self::find()->where(['estateID'=>$estateID,'propertyTypeID'=>$propertyTypeID,'userID'=>$userID])->one();
        if(empty($model)){
            return false;
        }
        if($model->isActive == 1){
            return true;
        }
        return false;
    }

    /**
     * 用户关注楼盘
     * @param $userID
     * @param $estateID
     * @param $propertyTypeID
     * @return array|null|ActiveRecord
     */
    public static function addWish($userID,$estateID,$propertyTypeID)
    {
        $model = self::find()->where(['estateID'=>$estateID,'propertyTypeID'=>$propertyTypeID,'userID'=>$userID])->one();
        if(empty($model)){
            $info = self::insertRecord($userID,$estateID,$propertyTypeID);
        }else{
            $info = self::editRecord($userID, $estateID, $propertyTypeID, ['isActive'=>1]);
        }
        return $info;
    }
}