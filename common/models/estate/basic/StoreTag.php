<?php
/**
 * 商铺和楼盘特色对应表
 * <liangshimao>
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class StoreTag extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_Store_BuildingTag';
    }

    public static function tableDesc() {
        return '商铺和楼盘特色对应表';
    }
}