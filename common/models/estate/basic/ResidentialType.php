<?php
/**
 * 住宅和建筑类型对应关系表
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class ResidentialType extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_ResidentialBuilding_BuildingType';
    }

    public static function tableDesc() {
        return '住宅和建筑类型对应关系表';
    }
}