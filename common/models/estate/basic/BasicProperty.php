<?php
/**
 * 楼盘物业对应表model类
 * <liangshimao>
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class BasicProperty extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_BasicInfoPropertyType';
    }

    public static function tableDesc() {
        return '楼盘-物业对应表';
    }
    
    
}