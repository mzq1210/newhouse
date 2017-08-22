<?php
/**
 * 住宅主表model类
 * <liangshimao>
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class Residential extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_ResidentialBuilding';
    }

    public static function tableDesc() {
        return '住宅主表';
    }
}