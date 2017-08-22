<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-20
 * Time: 下午1:32
 */

namespace common\models\estate\extend;
use yii\db\ActiveRecord;

class HouseTypeProComment extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_HouseTypeProComment';
    }

    public static function tableDesc() {
        return '户型专业点评';
    }

    public static function selectRecord($houseTypeID,$fields = '*')
    {
        if(!is_numeric($houseTypeID)){
            return false;
        }
        return self::find()->select($fields)->where(['houseTypeID'=>$houseTypeID])->asArray()->all();
    }
}