<?php
/**
 * 从model类中获取首页推荐主题分类
 * @Author: <lixiaobin>
 * @Date: 17-4-13
 */

namespace common\logic;

use common\models\webMgmt\EstateCategory;

class EstateCategoryLogic{

    /**
     * 根据当前客户端类型、城市编码获取热门主题分类
     * @Params: array $params
     *          int $params['companycode'] 城市公司编码
     *          int $params['clientType'] 客户端类型0-Website1-Mobilesite2-App *注意：暂时去掉按客户端类型分类查询
     * @Params: string $fields 需要查询的字段
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-13
     */
    public static function selectCategoryLogic($params, $fields='*'){
        $category = EstateCategory::selectRecord($params, $fields);
        if(empty($category)) return false;
        $categorys = [];
        foreach ($category as $val){
            $val['homeImageName'] = IMG_DOMAIN . $val['homeImageName'];
            $categorys[] = $val;
        }
        return $categorys;
    }

    /**
     * 通过站点编码、城市编码获取当前城市下的推荐主题的列表页banner
     * @Params: array $params
     *          int $params['companycode'] 城市公司编码
     *          int $params['categoryID'] 分类ID
     * @Params: string $fields 需要查询的字段
     * @Return Array
     * @Auhtor: <lixiaobin>
     * @Date: 2017-05-19
     */
    public static function selectThemeBannerLogic($params, $fields='*'){
        $themeBanaer = EstateCategory::selectRecordThemeBanner($params, $fields);
        if(empty($themeBanaer)) return false;
        $themeBanaer['themeImageName'] = IMG_DOMAIN . $themeBanaer['themeImageName'];
        return $themeBanaer;
    }
    
}
