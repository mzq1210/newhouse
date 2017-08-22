<?php
/**
 * 写字楼
 * <liangshimao>
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class Office extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_Office';
    }

    public static function tableDesc() {
        return '写字楼';
    }
}