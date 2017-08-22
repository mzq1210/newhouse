<?php
/**
 * 首页主题类别推荐
 * @Author: leexb
 * @Date: 17-4-13
 */

namespace common\models\webMgmt;

use common\models\query\BaseQuery;
use Yii;
use yii\db\ActiveRecord;

class EstateCategory extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_HomeThemeEstateCategory';
    }

    public static function tableDesc(){
        return '首页主题类别推荐';
    }

    public static function find(){
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 通过站点编码、城市编码获取当前城市下的推荐主题
     * @Params: array $params
     *          int $params['companycode'] 城市公司编码
     *          int $params['clientType'] 客户端类型0-Website1-Mobilesite2-App *注意：暂时去掉按客户端类型分类查询
     * @Params: string $fields 需要查询的字段
     * @Return Array
     * @Auhtor: <lixiaobin>
     * @Date: 2017-04-13
     */
    public static function selectRecord($params, $fields='*'){
        return self::find()->select($fields)
            ->where(['companyCode' => $params['companyCode']])
            ->active()
            ->audit()
            ->orderBy('sortIndex ASC, themeEstateCategoryID ASC')
            ->asArray()
            ->all();
    }


    /**
     * 通过站点编码、城市编码获取当前城市下的推荐主题的列表页banner
     * @Params: array $params
     *          int $params['companycode'] 城市公司编码
     *          int $params['categoryID'] 分类ID
     *          int $params['clientType'] 客户端类型0-Website1-Mobilesite2-App *注意：暂时去掉按客户端类型分类查询
     * @Params: string $fields 需要查询的字段
     * @Return Array
     * @Auhtor: <lixiaobin>
     * @Date: 2017-05-19
     */
    public static function selectRecordThemeBanner($params, $fields='*'){
        return self::find()->select($fields)
            ->where(['companyCode' => $params['companyCode'], 'themeEstateCategoryID' => $params['categoryID']])
            ->active()
            ->audit()
            ->orderBy('sortIndex ASC')
            ->asArray()
            ->one();
    }
}