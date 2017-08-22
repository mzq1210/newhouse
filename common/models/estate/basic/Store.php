<?php
/**
 * 商铺表
 * <liangshimao>
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class Store extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_Store';
    }

    public static function tableDesc() {
        return '商铺表';
    }
}