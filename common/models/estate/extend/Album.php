<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-20
 * Time: 下午1:32
 */

namespace common\models\estate\extend;
use common\models\query\BaseQuery;
use yii\db\ActiveRecord;

class Album extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_Album';
    }

    public static function tableDesc() {
        return '楼盘相册';
    }

    public static function find()
    {
        return \Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 查询相册信息
     * <liangshimao>
     */
    public static function selectRecord($estateID,$propertyTypeID,$fields = '*')
    {
        return self::find()->select($fields)->where(['estateID'=>$estateID])->property($propertyTypeID)->audit()->active()->orderBy(['sortIndex'=>SORT_ASC])->asArray()->all();
    }
}