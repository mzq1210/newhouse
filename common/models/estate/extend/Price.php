<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-20
 * Time: 下午1:29
 */

namespace common\models\estate\extend;
use yii\db\ActiveRecord;

class Price extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_Price';
    }

    public static function tableDesc() {
        return '楼盘价格信息表';
    }
}