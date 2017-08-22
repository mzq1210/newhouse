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

class AlbumImage extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_AlbumImage';
    }

    public static function tableDesc() {
        return '楼盘相册图片';
    }

    public static function find()
    {
        return \Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 查询相册信息
     * <liangshimao>
     */
    public static function selectRecord($albumID,$fields = '*')
    {
        return self::find()->select($fields)->where(['albumID'=>$albumID])->active()->orderBy(['sortIndex'=>SORT_ASC])->asArray()->all();
    }

    /**
     * 查询相册中的一条数据
     */
    public static function selectOneRecord($albumID,$fields = '*')
    {
        return self::find()->select($fields)->where(['albumID'=>$albumID])->active()->orderBy(['sortIndex'=>SORT_ASC])->asArray()->one();
    }

    /**
     * 获取相册详情
     */
    public static function getDetail($id,$fields = '*')
    {
        return self::find()->select($fields)->where(['id'=>$id])->asArray()->one();
    }
}