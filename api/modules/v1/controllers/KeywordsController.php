<?php
/**
 * 热门关键词接口
 * @Author: <lixiaobin>
 * @Date: 17-4-26
 */

namespace api\modules\v1\controllers;

use app\components\ActiveController;
use common\helper\cache\HotKeywordsCache;
use common\models\webMgmt\HotKeywords;

class KeywordsController extends ActiveController
{

    /**
     * 根据客户端类型、公司编码、物业业态获取热门关键词
     * @Params: int $clientType 客户端类型 0-Website;1-Mobilesite;2-App *注意：暂时去掉按客户端类型分类查询
     * @Params: int $companyCode 公司编码
     * @Params: int $propertyTypeID '物业形态 1:普通住宅 5：商铺 8：写字楼
     * @Return: Json
     * @Author: <lixiaobin>
     * @Date: 2017-04-27
     */
    public function actionIndex()
    {
        try {
            $params['companyCode'] = $this->request->get('companyCode', '');
            //$clientType = $this->request->get('clientType', '');
            $params['propertyTypeID'] = $this->request->get('propertyTypeID', '');
            if (!is_numeric($params['companyCode'])) {
                return ['公司编码格式不正确', 201];
            }
            /*if (!is_numeric($clientType)) {
                return ['客户端类型格式不正确', 201];
            }*/
            if (!is_numeric($params['propertyTypeID'])) {
                return ['物业类型格式不正确', 201];
            }
            $keywords = HotKeywordsCache::getHotKeywordsCache($params);
            $results = [];
            if (!empty($keywords)) {
                $results = ['results' => $keywords];
            }
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

}