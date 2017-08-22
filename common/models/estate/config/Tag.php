<?php
/**
 * 楼盘特色标签model类
 * <liangshimao>
 */

namespace common\models\estate\config;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;
use yii\helpers\ArrayHelper;

class Tag extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_BuildingTagInfo';
    }

    public static function tableDesc() {
        return '楼盘特色标签';
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
     * 根据当前城市获取特色标签
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     * @Params: int $companyCord 城市编号
     * @Params: string $fields 需要查询的字段
     * @Return Array
    */
    public static function selectRecord($companyCode, $propertyTypeID, $fields = '*')
    {
        //判断是否为数字或者数字字符串
        if (is_numeric($companyCode)) {
            return self::find()->select($fields)->where(['companyCode' => $companyCode,'propertyTypeID' => $propertyTypeID])->active()->orderBy('sortIndex ASC, tagID ASC')->asArray()->all();
        }
        return false;
    }

    /**
     * 根据id获取标签名称
     */
    public static function getTagName($tagId)
    {
        if(is_array($tagId)){
            $list = self::find()->select('tagName')->where(['in','tagID',$tagId])->active()->asArray()->all();
            return ArrayHelper::getColumn($list,'tagName',false);
        }else{
            $info = self::find()->select('tagName')->where(['tagID'=>$tagId])->active()->one();
            return $info->tagName;
        }

    }
}