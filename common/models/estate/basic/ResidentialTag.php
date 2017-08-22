<?php
/**
 *住宅和楼盘特色关系表
 * <liangshimao>
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class ResidentialTag extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_ResidentialBuilding_BuildingTag';
    }

    public static function tableDesc() {
        return '住宅和楼盘特色关系表';
    }
}