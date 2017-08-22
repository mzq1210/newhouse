<?php
/**
 * 首页服务模块配置
 * @Author: <lixiaobin>
 * @Date: 17-4-28
 */

namespace common\models\webMgmt;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class SiteConfig extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_HomeModuleConfiguration';
    }

    public static function tableDesc(){
        return '首页服务模块配置';
    }

    public static function find(){
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 首页服务模块配置信息
     * @Params: Array $params
     *          Int $params['companyCode'] 公司编码
     *          Int $params['clientType'] 客户端类型 0-Website;1-Mobilesite;2-App
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-28
     */
    public static function selectRecord($params){
        return self::find()->select('title,position,summary,homeModuleImageName,targetURL,type')
            ->where(['companyCode' => $params['companyCode'], 'clientType'=> $params['clientType']])
            ->active()
            ->audit()
            ->orderBy('sortIndex ASC')
            ->asArray()
            ->all();
    }

}