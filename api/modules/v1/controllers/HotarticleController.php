<?php
/**
 * 获取热门推荐文章接口
 * @Author: <lixiaobin>
 * @Date: 17-4-27
 */

namespace api\modules\v1\controllers;

use common\helper\cache\SiteCache;
use app\components\ActiveController;

class HotarticleController extends ActiveController
{

    /**
     * 根据公司编码、客户端类型获取热门推荐的文章
     * @Params: int companyCode 公司编码
     * @Params: int clientType 客户端类型 0-Website;1-Mobilesite;2-App *注意：暂时去掉按客户端类型分类查询
     * @Return: Json
     * @Author: <lixiaobin>
     * @Date: 2017-04-27
     */
    public function actionIndex()
    {
        try {
            $params['companyCode'] = $this->request->get('companyCode', '');
            //$clientType = $this->request->get('clientType', '');
            if (!is_numeric($params['companyCode'])) {
                return ['公司编码格式不正确', 201];
            }
            /*if (!is_numeric($clientType)) {
                return ['客户端类型格式不正确', 201];
            }*/
            $estate = SiteCache::getRecommenedArticleCache($params);
            $results = [];
            if (!empty($estate)) {
                $results['results'] = $estate;
            }
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

}