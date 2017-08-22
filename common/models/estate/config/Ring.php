<?php

/**
 * 环线信息model类
 * <liangshimao>
 */
namespace common\models\estate\config;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class Ring extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_RingRoadInfo';
    }

    public static function tableDesc() {
        return '环线信息';
    }
    /**
     * @inheritdoc
     * @return BaseQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 根据城市编码获取地铁线路信息
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     * @Params: int $companyCode 城市编码
     * @Params: string $fields 字段
     * @Return: Array
    */
    public static function selectRecord($companyCode, $fields = '*'){
        if(is_numeric($companyCode)){
            return self::find()->select($fields)->where(['companyCode' => $companyCode])->active()->orderBy('sortIndex ASC, ringRoadID ASC')->asArray()->all();
        }
        return false;
    } 
}