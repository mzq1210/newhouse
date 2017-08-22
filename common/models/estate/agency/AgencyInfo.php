<?php

/**
 * 外部经纪人信息
 * <liangshimao>
 */
namespace common\models\estate\agency;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class AgencyInfo extends ActiveRecord
{
    public static function tableName() {
        return 'External_AgencyInfo';
    }

    public static function tableDesc() {
        return '外部经纪人信息';
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
     * 获取经纪人基本信息,默认全部显示,如果想仅显示在职经纪人则把isActive设置为true.
     * @Author: <liangshimao>
     * @Date: 2017-03-29
     * @Return: Array
     */
    public static function getDetail($agencyID, $fields = '*',$isActive = false){
        if(!is_numeric($agencyID)) return false;
        $query = self::find()->select($fields)->where(['agencyID' => $agencyID]);
        if($isActive){
            $query->active();
        }
        return $query->asArray()->one();
    }
    /**
     * 获取表信息
     * @Author: <wangluohua>
     * @Date: 2017-04-14
     * @Return: Array
     */
    public static function selectAll($fields = '*', $condition = []){
        return self::find()->select($fields)->where($condition)->asArray()->all();
    }
    
    public static function selectOne($fields = '*', $condition = []){
        return self::find()->select($fields)->where($condition)->asArray()->one();
    }
}