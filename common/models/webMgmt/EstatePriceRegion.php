<?php
/**
 * 楼盘搜索均价区间 model 类
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */

namespace common\models\webMgmt;

use Yii;
use common\models\query\BaseQuery;
use yii\db\ActiveRecord;

class EstatePriceRegion extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_EstatePriceRegion';
    }

    public static function tableDesc(){
        return '楼盘搜索均价区间';
    }

    public static function find(){
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 根据当前城市编码、物业类型ID 获取价格标签
     * @Author: <lixiaobin>
     * @Date: 2017-03-27
     * @Params: int $companyCode 公司编码
     * @Params: int $propertyTypeId 物业类型ID
     * @Params: String $fields 所需字段
     * @Return: Array
    */
    public static function selectRecord($companyCode, $propertyTypeId, $fields = '*'){
        return self::find()->select($fields)->where(['companyCode' => $companyCode, 'propertyTypeID'=>$propertyTypeId])->active()->orderBy('minValue ASC')->asArray()->all();
    }

}