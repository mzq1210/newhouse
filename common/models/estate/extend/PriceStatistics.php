<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-20
 * Time: 下午1:29
 */

namespace common\models\estate\extend;
use common\models\query\BaseQuery;
use yii\db\ActiveRecord;

class PriceStatistics extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_PriceStatistics';
    }

    public static function tableDesc() {
        return '楼盘价格统计表';
    }

    /**
     * @inheritdoc
     * @return BaseQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return \Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }
    
    public static function findPrice($field='*', $condition=[]){
        $pageSize = $condition['pageSize'];
        unset($condition['pageSize']);
        return self::find()->select($field)->where($condition)->orderBy(['statisticsTime'=>SORT_DESC])->limit($pageSize)->asArray()->all();
    }
}