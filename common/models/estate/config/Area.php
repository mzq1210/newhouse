<?php
/**
 * 楼盘区域信息model类
 * <liangshimao>
 */

namespace common\models\estate\config;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class Area extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_AreaInfo';
    }

    public static function tableDesc() {
        return '楼盘区域';
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
     * 获取当前城市的区域信息
     * @Author <lixiaobin>
     * @Date 2017-03-21
     * @Params int $companyCode 城市编码
     * @Return array
    */
    public static function selectRecord($companyCode, $fields = '*'){
        if(is_numeric($companyCode)){
           return  self::find()->select($fields)->where(['companyCode' => $companyCode])->active()->orderBy('sortIndex ASC, estateAreaID ASC')->asArray()->all();
        }
        return false;
    }

    /**
     * @desc获取当前城市的顶级区域信息
     * @Author <mzq>
     * @Date 2017-03-30
     * @Params int $companyCode 城市编码
     * @Return array
     */
    public static function selectParentRecord($companyCode, $fields = '*'){
        if(is_numeric($companyCode)){
            return self::find()->select($fields)->where(['companyCode' => $companyCode])->active()->parent()->orderBy('sortIndex ASC, estateAreaID ASC')->asArray()->all();
        }
        return false;
    }


    public static function selectEstateNameRecord($companyCode, $estateAreaID, $fields = '*'){
        if(is_numeric($companyCode)){
            return  self::find()->select($fields)->where(['companyCode' => $companyCode,'estateAreaID' => $estateAreaID])->active()->orderBy('sortIndex ASC')->asArray()->one();
        }
        return false;
    }

    /**
     * @desc获取当前区域信息
     * @Author <wangluohua>
     * @Params int $estateAreaID 区域id
     */

    public static function selectAreaInfo($estateAreaID){
        if (empty($estateAreaID))
            return false;
        return self::findOne($estateAreaID);
    }
}