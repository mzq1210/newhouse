<?php
/**
 * 首页市场行情介绍(前台网站只获取前3个) model 类
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */

namespace common\models\webMgmt;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class MarketSummary extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_HomeMarketSummary';
    }

    public static function tableDesc(){
        return '首页市场行情介绍(前台网站只获取前3个)';
    }

    public static function find(){
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 根据公司编码获取市场行情
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公司编码
     * @Params: String $fields 需要查询的字段
     * @Retrun: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-28
     */
    public static function selectRecord($params, $fields = '*'){
        return self::find()->select($fields)
            ->where(['companyCode' => $params['companyCode']])
            ->active()
            ->audit()
            ->orderBy('sortIndex ASC, id ASC')
            ->limit(3)
            ->asArray()
            ->all();
    }
}