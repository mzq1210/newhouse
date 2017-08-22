<?php
/**
 * 楼盘建筑类别信息model类
 * <liangshimao>
 */

namespace common\models\estate\config;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class Type extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_BuildingTypeInfo';
    }

    public static function tableDesc() {
        return '楼盘建筑类别';
    }
    /**
     * @inheritdoc
     * @return BaseQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }
}