<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-20
 * Time: 下午1:31
 */

namespace common\models\estate\extend;
use yii\db\ActiveRecord;

class HouseTypeProtoRoom extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_HouseTypeProtoRoom';
    }

    public static function tableDesc() {
        return '户型样板间';
    }

    public static function selectRecord($houseTypeID,$fields = '*')
    {
        if(!is_numeric($houseTypeID)){
            return false;
        }
        return self::find()->select($fields)->where(['houseTypeID'=>$houseTypeID])->asArray()->all();
    }
}