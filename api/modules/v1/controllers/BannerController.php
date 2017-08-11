<?php
/**
 * 获取banner和广告接口
 * @Author: leexb
 * @Date: 17-4-23
 */

namespace api\modules\v1\controllers;

use Yii;
use app\components\ActiveController;
use common\helper\cache\BannerInfoCache;

class BannerController extends ActiveController
{

    /**
     * 根据客户端类型、公司编码、广告唯一码获取不同的banner广告
     * @Params: Int companyCode 公司编码
     * @Params: Int clientType 客户端类型 0-Website;1-Mobilesite;2-App
     * @Params: String categoryKey 广告唯一类型编码
     * @Return: Json
     * @Author: <lixiaobin>
     * @Date: 2017-04-27
     */
    public function actionIndex()
    {
        try {
            $params['companyCode'] = $this->request->get('companyCode', '');
            $params['clientType'] = $this->request->get('clientType', '');
            $params['categoryKey'] = $this->request->get('categoryKey', '');
            if (!is_numeric( $params['companyCode'])) {
                return ['公司编码格式错误', 201];
            }
            if (!is_numeric($params['clientType'])) {
                return ['客户端类型格式错误', 201];
            }
            if (empty($params['categoryKey'])) {
                return ['广告编码不能为空', 201];
            }
            $banner = BannerInfoCache::getBannerInfoCache($params);
            $results = [];
            if (!empty($banner)) {
                $results['results'] = $banner;
            }
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

}