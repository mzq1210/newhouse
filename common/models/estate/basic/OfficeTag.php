<?php
/**
 * 写字楼和楼盘特色对应表
 * <liangshimao>
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class OfficeTag extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_Office_BuildingTag';
    }

    public static function tableDesc() {
        return '写字楼和楼盘特色对应表';
    }
}