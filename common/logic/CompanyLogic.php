<?php
/**
 * 获取城市公司编码
 * @Author: <lixiaobin>
 * @Date: 17-3-24
 */
namespace common\logic;

use common\helper\pinyin\YPinYin;
use yii\helpers\Json;
use common\models\cp\Company;

class CompanyLogic{


    /**
     * 根据二级域名中的domain查询城市公司code
     * @Params: string $domain 三级域名中的城市简拼
     * @Params: string $fields 需要查询的字段
     * @Return Array
     * @Author: <lixiaobin>
     * @Date: 2017-03-24
     */
    public static function selectCompanyCodeLogic($domain, $fields = '*'){
        $companyInfo = Company::selectRecord($domain,$fields);
        if(!empty($companyInfo)){
            $switchConfig = Json::decode($companyInfo['switchConfig']);
            if(!empty($switchConfig['isEnableFrontend'])){
                //unset($companyInfo['switchConfig']);
                return $companyInfo;
            }
        }
        return false;
    }

    /**
     * 根据domain查询城市公司信息
     * @Params: string $domain 三级域名中的城市简拼
     * @Params: string $fields 需要查询的字段
     * @Return Array
     * @Author: <mzq>
     * @Date: 2017-03-24
     */
    public static function selectCompanyDetailLogic($domain, $fields = '*'){
        $companyInfo = Company::selectRecord($domain,$fields);
        if(!empty($companyInfo)){
            return $companyInfo;
        }
        return false;
    }
    
    /**
     * 根据二级域名中的domain查询城市公司code
     * @Params: string $domain 三级域名中的城市简拼
     * @Params: string $fields 需要查询的字段
     * @Return Array
     * @Author: <lixiaobin>
     * @Date: 2017-03-24
     */
    public static function selectCompanyLogic($domain, $fields = '*'){
        $companyInfo = Company::selectRecord($domain,$fields);
        if(!empty($companyInfo)){
            $switchConfig = Json::decode($companyInfo['switchConfig']);
            $companyInfo['isEnableFrontend'] = $switchConfig['isEnableFrontend'];
            $companyInfo['isEnableCommercial'] = $switchConfig['isEnableCommercial'];
            return $companyInfo;
        }
        return false;
    }

    /**
     * 获取所有城市，并且按照城市首字母重组数组
     * @Params: string $fields 需要查询的字段
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-25
    */
    public static function selectCompanyAllLogic($fields = '*'){
        //获取所有城市公司
        $companyAll = Company::selectRecordAll($fields);
        $companyArr = [];
        if(!empty($companyAll)){
            foreach ($companyAll as $val){
                //根据switchConfig字段中的isEnableFrontend判断城市是否开通
                $switchConfig = Json::decode($val['switchConfig']);
                //如果城市没有开启，unset掉
                if(empty($switchConfig['isEnableFrontend'])){
                    unset($val);
                }else{
                    //根据城市名称转换拼音
                    $vKey = YPinYin::getFirstCharter($val['companyName']);
                    $vKey = strtoupper($vKey);
                    unset($val['switchConfig']);
                    $companyArr[$vKey][] = $val;
                }
            }
            ksort($companyArr,SORT_ASC);
            return $companyArr;
        }
        return false;
    }
}