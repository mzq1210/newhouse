<?php
/**
 * 获取市场行情接口
 * @Author: <lixiaobin>
 * @Date: 17-4-27
 */

namespace api\modules\v1\controllers;

use app\components\ActiveController;
use common\helper\cache\SiteCache;

class MarketController extends ActiveController{

    /**
     * 根据公司编码获取市场行情
     * @Params: Int companyCode 城市公司编码
     * @Retrun: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-28
     */
    public function actionIndex(){
        try {
            $params['companyCode'] = $this->request->get('companyCode', '');
            if (!is_numeric($params['companyCode'])) {
                return ['公司编码格式不正确', 201];
            }
            $market = SiteCache::getMarketCache($params);
            $results = [];
            if (!empty($market)) {
                $results = ['results' => $market];
            }
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }
    
}