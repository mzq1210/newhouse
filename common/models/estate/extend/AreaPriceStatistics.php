<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-20
 * Time: 下午1:16
 */

namespace common\models\estate\extend;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class AreaPriceStatistics extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_AreaPriceStatistics';
    }

    public static function tableDesc() {
        return '区域楼盘价格统计表';
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
     * 获取区域均价信息
     * @Author <mzq>
     * @Date 2017-03-30
     * @Params arr | string $field 城市编码
     * @Return array
     */
    public static function selectRecord($field = '*'){
        return self::find()->select($field)->active()->asArray()->all();
    }

    public static function findPrice($field='*', $condition=[]){
        $pageSize = $condition['pageSize'];
        unset($condition['pageSize']);
        return self::find()->select($field)->where($condition)->orderBy(['statisticsTime'=>SORT_DESC])->limit($pageSize)->asArray()->all();
    }
}