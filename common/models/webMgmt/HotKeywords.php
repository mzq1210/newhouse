<?php
/**
 * 热门搜索关键词 model 类
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */

namespace common\models\webMgmt;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class HotKeywords extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_HotSearchKeyword';
    }

    public static function tableDesc(){
        return '热门搜索词';
    }

    public static function find(){
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 根据不同城市、不同的网站类型和物业类型获取所配置的热门关键词
     * @Params: Array $params
     *          $params['clientType']  客户端类型0-Website1-Mobilesite2-App  *注意：暂时去掉按客户端类型分类查询
     *          $params['companyCode']  城市公司编码
     *          $params['propertyTypeID'] 物业类型 1:住宅 5:商业 8:写字楼
     * @Params: string $fields 需要查询的字段名称
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-11
    **/
    public static function selectRecord($params, $fields= '*'){
        return self::find()->select($fields)
            ->where(['companyCode' => $params['companyCode'], 'propertyTypeID' => $params['propertyTypeID']])
            ->active()
            ->audit()
            ->orderBy('sortIndex ASC, id ASC')
            ->asArray()
            ->All();
    }
}