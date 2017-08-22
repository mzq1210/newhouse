<?php
/**
 * Banner类型 model
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */
namespace common\models\webMgmt;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class BannerCategory extends ActiveRecord{
    public static function tableName(){
        return 'WebMgmt_BannerCategory';
    }

    public static function tableDesc(){
        return 'Banner分类型表';
    }

    public static function find(){
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 根据客户端类型、分类ID获取信息
     * @Author: <lixiaobin>
     * @date: 2017-04-27
     * @Params: int $clientType 客户端类型0-Website1-Mobilesite2-App
     * @Params: int $categoryID 广告唯一类型编码
     * @Return: Array
    */
    public static function selectRecord($clientType, $categoryKey, $fields='*'){
        return self::find()->select($fields)
            ->where(['categoryKey' => $categoryKey,'clientType' => $clientType])
            ->active()
            ->asArray()
            ->one();
    }
}