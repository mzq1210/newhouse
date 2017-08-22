<?php
/**
 * 商铺合作银行表
 * <liangshimao>
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class StoreBank extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_Store_Bank';
    }

    public static function tableDesc() {
        return '商铺合作银行表';
    }
}