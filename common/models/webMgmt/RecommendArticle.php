<?php
/**
 * 首页推荐(热门)文章 model 类
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */

namespace common\models\webMgmt;

use common\models\query\BaseQuery;
use Yii;
use yii\db\ActiveRecord;

class RecommendArticle extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_HomeRecommendedArticle';
    }

    public static function tableDesc(){
        return '首页推荐(热门)文章';
    }

    public static function find(){
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 获取首页热门推荐的文章
     * @Params: Array $params
     *          int $params['companyCode'] 城市公司编码
     *          int $params['clientType'] 客户端类型 0:pc站 1:wap站 2：App *注意：暂时去掉按客户端类型分类查询
     * @Return Array
     * @Author: <lixiaobin>
     * @Date: 2014-04-13
    */
    public static function selectRecord($params,$fields = '*'){
        return self::find()->select($fields)
            ->where(['companyCode' => $params['companyCode']])
            ->active()
            ->audit()
            ->orderBy('sortIndex ASC, id ASC')
            ->limit(2)
            ->asArray()
            ->all();
    }

}