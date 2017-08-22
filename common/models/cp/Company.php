<?php
/**
 * 查询城市公司code
 * @Author: <lixiaobin>
 *@ Date: 17-3-24
 */

namespace common\models\cp;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;
use yii\helpers\Json;

class Company extends ActiveRecord{

    public static function tableName(){
        return 'CP_CompanyInfo';
    }

    public static function tableDesc(){
        return '城市公司信息表';
    }

    public static function find(){
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 根据domain查询开通的城市公司信息
     * @Params: string $domain 三级域名中的城市简拼
     * @Params: string $fields 需要查询的字段
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-03-24
     */
    public static function selectRecord($domain, $fields = '*'){
        return self::find()->select($fields)->where(['secondDomainName' => $domain])->active()->asArray()->One();
    }

    /**
     * 根据城市编码，查询开通的城市公司是否开启商业导航
     * @Params: string $companyCode 公司编码
     * @Params: string $fields 需要查询的字段
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-22
     */
    public static function selectRecordNav($companyCode, $fields = '*'){
        return self::find()->select($fields)->where(['companyCode' => $companyCode])->active()->asArray()->One();
    }

    /**
     * 获取所有城市公司
     * @Params: string $fields 需要查询的字段
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-25
    */
    public static function selectRecordAll($fields){
        return self::find()->select($fields)->active()->asArray()->All();
    }
}