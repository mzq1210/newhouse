<?php
/**
 * 住宅合作银行
 * <liangshimao>
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class ResidentialBank extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_ResidentialBuilding_Bank';
    }

    public static function tableDesc() {
        return '住宅合作银行';
    }
}