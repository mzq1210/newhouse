<?php
/**
 * 首页服务模块配置逻辑
 * @Author: <lixiaobin>
 * @Date: 17-4-28
 */

namespace common\logic;

use common\models\webMgmt\SiteConfig;

class SiteConfigLogic{

    /**
     * 首页服务模块配置信息
     * @Params: Array $params
     *          Int $params['companyCode'] 公司编码
     *          Int $params['clientType'] 客户端类型 0-Website;1-Mobilesite;2-App
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-28
     */
    public static function selectConfigLogic($params){
        $siteConfig = SiteConfig::selectRecord($params);
        //整合数组
        if(!empty($siteConfig)){
            $siteConfigArr = [];
            foreach ($siteConfig as $val){
                $val['homeModuleImageName'] = IMG_DOMAIN . $val['homeModuleImageName'];
                if($val['position'] == 0){
                    $siteConfigArr['nav'][] = $val;
                }elseif ($val['position'] == 1){
                    $siteConfigArr['centerContent'][] = $val;
                }

            }
            return $siteConfigArr;
        }
        return false;
    }

}

