<?php
/**
 * 首页楼盘推荐 model 类
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */

namespace common\models\webMgmt;

use common\models\query\BaseQuery;
use Yii;
use yii\db\ActiveRecord;

class RecommendedEstate extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_HomeRecommendedEstate';
    }

    public static function tableDesc(){
        return '首页楼盘推荐';
    }

    public static function find(){
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }
    
    /**
     * 通过站点编码、城市编码获取当前城市下的热门楼盘
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公司编码
     *          Int $params['clientType'] 客户端类型 0:pc站 1:wap站 2：App *注意：暂时去掉按客户端类型分类查询
     * @Params: string $fields 需要查询的字段
     * @Return Array
     * @Auhtor: <lixiaobin>
     * @Date: 2017-04-13
    */
    public static function selectRecord($params, $fields = '*'){
        return self::find()->select($fields)->where(['companyCode' => $params['companyCode']])
            ->active()
            ->audit()
            ->orderBy('sortIndex ASC, id ASC')
            ->asArray()
            ->all();
    }

}