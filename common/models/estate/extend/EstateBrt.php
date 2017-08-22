<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-20
 * Time: 下午1:33
 */

namespace common\models\estate\extend;
use yii\db\ActiveRecord;

class EstateBrt extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_TrafficRelation';
    }

    public static function tableDesc() {
        return '楼盘和快速公交对应表';
    }
}