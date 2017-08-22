<?php
/**
 * 物业类型model类
 * <liangshimao>
 */

namespace common\models\estate\config;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class Property extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_PropertyType';
    }

    public static function tableDesc() {
        return '物业类型表';
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
     * 根据父类ID查询类型
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     * @Params: int $parentID 父类id来判定属于住宅还是商业
     * @Params: string $fields 需要查询的字段
     * @Return: Array
    */
    public static function selectRecord($parentID = 1, $fields = '*'){
        return self::find()->select($fields)->where(['parentID' => $parentID])->active()->asArray()->all();
    }

    /**
     * 根据id获取物业类型名称
     * @Author: <lixiaobin>
     * @Date: 2017-05-23
     * @Params: int $propertyTypeID 业态ID
     * @Return: String
     *
     */
    public static function getPropertyTypeName($propertyTypeID)
    {
        $info = self::findOne(['propertyTypeID'=>$propertyTypeID]);
        if(!empty($info)){
            return $info->propertyTypeName;
        }
        return "未知业态";
    }

    /**
     * 根据业态id数组得到业态名称数组
     * @param array $array
     * @return array|bool
     * <liangshimao>
     */
    public static function makepropertyArray($array = [])
    {
        if(empty($array) || !is_array($array)){
            return false;
        }
        $res = [];
        foreach ($array as $propertyTypeID){
            $res[$propertyTypeID] = self::getPropertyTypeName($propertyTypeID);
        }
        return $res;
    }
}