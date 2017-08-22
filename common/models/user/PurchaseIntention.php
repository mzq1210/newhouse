<?php
/**
 * 用户购买意向表model类
 * User: <liangshimao>
 * Date: 17-4-5
 * Time: 下午6:01
 */

namespace common\models\user;


use yii\db\ActiveRecord;

class PurchaseIntention  extends ActiveRecord
{
    public static function tableName() {
        return 'CP_PurchaseIntention';
    }

    public static function tableDesc() {
        return '用户购买意向表';
    }

    public static function getName($purchaseIntentionID)
    {
        $info = self::find()->where(['purchaseIntentionID'=>$purchaseIntentionID])->one();
        if(empty($info)){
            return "暂无信息";
        }
        return $info->purchaseIntentionDescription;
    }

    public static function getSelectRecord($fields, $condition)
    {
        $info = self::find()->select($fields)->where($condition)->asArray()->all();
        if(empty($info)){
            return "暂无信息";
        }
        return $info;
    }
}