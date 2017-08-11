<?php
/**
 * 首页服务模块配置接口
 * @Author: <lixiaobin>
 * @Date: 17-4-28
 */

namespace api\modules\v1\controllers;

use app\components\ActiveController;
use common\helper\cache\SiteCache;
use common\models\webMgmt\SiteConfig;

class SiteconfigController extends ActiveController{

    /**
     * 首页服务模块配置信息
     * @Params: Int companyCode 公司编码
     * @Params: Int clientType 客户端类型 0-Website;1-Mobilesite;2-App
     * @Return: Json
     * @Author: <lixiaobin>
     * @Date: 2017-04-28
    */
    public function actionIndex(){
        try {
            $params['companyCode'] = $this->request->get('companyCode', '');
            $params['clientType'] = $this->request->get('clientType', '');
            if (!is_numeric($params['companyCode'])) {
                return ['公司编码格式不正确', 201];
            }
            if (!is_numeric($params['clientType'])) {
                return ['客户端类型格式不正确', 201];
            }

            $siteConfig = SiteCache::getConfigCache($params);
            $results = [];
            if (!empty($siteConfig)) {
                $results['results'] = $siteConfig;
            }
            return ['成功', 200, $results];
        }catch (\Exception $e){
            return [$e->getMessage(), 500];
        }
    }

}