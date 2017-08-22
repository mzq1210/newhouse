<?php
/**
 * 楼盘扩展信息表
 */

namespace common\models\estate\basic;
use yii\db\ActiveRecord;

class Extend extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_ExtendInfo';
    }

    public static function tableDesc() {
        return '楼盘扩展信息';
    }

    /**
     * 获取楼盘扩展信息表中数据
     * @Author: <liangshimao>
     * @Date: 2017-03-24
     * @Return: Array
     */
    public static function selectRecord($estateID, $fields = '*'){
        if(!is_numeric($estateID)) return false;
        return self::find()->select($fields)->where(['estateID' => $estateID])->asArray()->one();
    }

}