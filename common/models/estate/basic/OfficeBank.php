<?php
/**
 * 写字楼和银行对应表
 * <liangshimao>
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class OfficeBank extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_Office_Bank';
    }

    public static function tableDesc() {
        return '写字楼合作银行对应关系表';
    }
}