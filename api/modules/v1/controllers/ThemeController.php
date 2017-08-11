<?php
/**
 * 主题楼盘接口
 * @Author: <lixiaobin>
 * @Date: 17-4-27
 */

namespace api\modules\v1\controllers;

use common\helper\cache\SiteCache;
use app\components\ActiveController;

class ThemeController extends ActiveController{

    /**
     * 根据公司编码 和 客户端类型获取楼盘主题分类
     * @Params: int $companyCode 公司编码
     * @Params: int $clientType 客户端类型0-Website1-Mobilesite2-App *注意：暂时去掉按客户端类型分类查询
     * @Return: Json
     * @Author: <lixiaobin>
     * @Date: 2017-04-27
    */
    public function actionIndex(){
        try{
            $params['companyCode'] = $this->request->get('companyCode', '');
            //$clientType = $this->request->get('clientType', '');
            if (!is_numeric($params['companyCode'])) {
                return ['公司编码格式不正确', 201];
            }
           /* if (!is_numeric($clientType)) {
                return ['客户端类型格式不正确', 201];
            }*/
            $category = SiteCache::getHomeCategoryCache($params);
            $results = [];
            if(!empty($category)){
                $results['results'] = $category;
            }
            return ['成功', 200, $results];
        }catch(\Exception $e){
            return [$e->getMessage(), 500];
        }
    }

    /**
     * 获取主题楼盘列表页banner图片
     * @Params: int $companyCode 公司编码
     * @Params: int $categoryID 主题楼盘分类ID
     * @Return: Json
     * @Author: <lixiaobin>
     * @Date: 2017-05-19
    */
    public function actionBanner(){
        try{
            $params['companyCode'] = $this->request->get('companyCode');
            if (!is_numeric($params['companyCode'])) {
                return ['公司编码格式不正确', 201];
            }
            $params['categoryID'] = $this->request->get('categoryID');
            if (!is_numeric($params['categoryID'])) {
                return ['分类ID格式不正确', 201];
            }
            $themeImg = SiteCache::getThemeBannerCache($params);
            $results = [];
            if(!empty($themeImg)){
                $results['results'] = $themeImg;
            }

            return ['成功', 200, $results];
        }catch (\Exception $e){
            return [$e->getMessage(), 500];
        }
    }


}