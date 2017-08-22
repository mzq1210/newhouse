<?php
/**
 * Banner信息 model类
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */
namespace common\models\webMgmt;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class BannerInfo extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_BannerInfo';
    }

    public static function tableDesc(){
        return 'Banner信息';
    }

    public static function find(){
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 根据站点类型、categoryID、城市编码 获取banner信息
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公式编码
     *          Int $params['categoryID'] 广告分类ID
     * @Params: String $fields 需要查询的字段
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-12
    */
    public static function selectRecord($params, $fields){
        $dateTime = date('Y-m-d H:i:s');
        $bannerInfo = self::find()->select($fields)
            ->where(['companyCode' => $params['companyCode'], 'categoryID' => $params['categoryID']])
            ->active()
            ->audit()
            ->andWhere(['<','beginDate',$dateTime])
            ->andWhere(['>','endDate',$dateTime])
            ->orderBy('sortIndex ASC')
            ->asArray()
            ->all();
        return $bannerInfo;
        
    }
}