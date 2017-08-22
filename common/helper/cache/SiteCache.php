<?php
/**
 * 缓存首页需要的推荐主题、热门楼盘、热门文章
 * @Author: <lixiaobin>
 * @Date: 17-4-13
 */

namespace common\helper\cache;

use common\helper\BaseCache;
use common\logic\MarketLogic;
use common\logic\RecommendArticleLogic;
use common\logic\RecommendEstateLogic;
use common\logic\EstateCategoryLogic;
use common\logic\SiteConfigLogic;
use common\models\webMgmt\RecommendArticle;
use Faker\Provider\Base;

class SiteCache{
    //定义主题 key
    const REDIS_HTMO_THEME_ESTATE_CATEGORY = 'home_theme_estate_category';
    //定义热门楼盘 key
    const REDIS_HOME_RECOMMENDED_ESTATE = 'home_recommended_estate';
    //定义热门文章推荐key
    const REDIS_HOME_RECOMMENDED_ARTICLE = 'home_recommended_article';
    //定义市场行情key
    const REDIS_HOME_MARKET = 'home_market';
    //定义首页服务模块配置key
    const REDIS_HOME_CONFIG = 'home_module_config';
    //定义主题楼盘列表也banner
    const REDIS_THEME_IMG = 'theme_img';

    /**
     * 根据城市公司编码和客户端类型获取楼盘主题分类
     * @Params: array $params
     *          int $params['companycode'] 城市公司编码
     *          int $params['clientType'] 客户端类型0-Website1-Mobilesite2-App *注意：暂时去掉按客户端类型分类查询
     * @Retrun: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-27
    */
    public static function getHomeCategoryCache($params){
        //生成redis KEY
        $key = self::REDIS_HTMO_THEME_ESTATE_CATEGORY .':'.$params['companyCode'];
        $categoryArr = BaseCache::get($key);
        if($categoryArr === false){
            $categoryArr = EstateCategoryLogic::selectCategoryLogic($params, 'themeEstateCategoryID,title,homeImageName');
            if(empty($categoryArr)) return false;
            BaseCache::set($key, $categoryArr);
        }
        return $categoryArr;
    }

    /**
     * 根据城市公司编码和分类ID获取主题楼盘列表banner
     * @Params: array $params
     *          int $params['companycode'] 城市公司编码
     *          int $categoryID 主题楼盘分类ID
     * @Retrun: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-19
     */
    public static function getThemeBannerCache($params){
        //生成redis KEY
        $key = self::REDIS_THEME_IMG .':'.$params['companyCode'] .':'. $params['categoryID'];
        $themeImg = BaseCache::get($key);
        if($themeImg === false){
            $themeImg = EstateCategoryLogic::selectThemeBannerLogic($params, 'title,themeImageName');
            if(empty($themeImg)) return false;
            BaseCache::set($key, $themeImg);
        }
        return $themeImg;
    }

    /**
     * 根据公司编码和客户端类型获取热门推荐的楼盘
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公司编码
     *          Int $params['clientType'] 客户端类型 0:pc站 1:wap站 2：App *注意：暂时去掉按客户端类型分类查询
     * @Retrun: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-27
    */
    public static function getRecommendeEstateCache($params){
        //生成redis KEY
        $key = self::REDIS_HOME_RECOMMENDED_ESTATE . ':' .$params['companyCode'];
        $hotEstate = BaseCache::get($key);
        if($hotEstate === false){
            $hotEstate = RecommendEstateLogic::selectEstateLogic($params, 'estateID,propertyTypeID');
            if(empty($hotEstate)) return false;
            BaseCache::set($key, $hotEstate);
        }
        return $hotEstate;
    }

    /**
     * 根据公司编码和客户端类型获取热门文章
     * @Params: Array $params
     *          int $params['companyCode'] 城市公司编码
     *          int $params['clientType'] 客户端类型 0:pc站 1:wap站 2：App *注意：暂时去掉按客户端类型分类查询
     * @Retrun: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-27
     */
    public static function getRecommenedArticleCache($params){
        //生成redis key
        $key = self::REDIS_HOME_RECOMMENDED_ARTICLE .':'.$params['companyCode'];
        $hotArticle = BaseCache::get($key);
        if($hotArticle === false){
            $hotArticle = RecommendArticleLogic::selectArticLogic($params,'articleID,title,targetURL,summary,homeHotArticleImageName');
            if(empty($hotArticle)) return false;
            BaseCache::set($key, $hotArticle);
        }
        return $hotArticle;
    }

    /**
     * 根据公司编码获取市场行情
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公司编码
     * @Retrun: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-28
     */
    public static function getMarketCache($params){
        $key = self::REDIS_HOME_MARKET . ':' . $params['companyCode'];
        $market = BaseCache::get($key);
        if($market === false){
            $market = MarketLogic::selectMarketLogic($params, 'title,description,unit');
            if(empty($market)) return false;
            BaseCache::set($key, $market);
        }
        return $market;
    }

    /**
     * 首页服务模块配置信息
     * @Params: Array $params
     *          Int $params['companyCode'] 公司编码
     *          Int $params['clientType'] 客户端类型 0-Website;1-Mobilesite;2-App
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-28
     */
    public static function getConfigCache($params){
        $key = self::REDIS_HOME_CONFIG . ':' . $params['companyCode'] . ':' . $params['clientType'];
        $config = BaseCache::get($key);
        if($config === false){
            $config = SiteConfigLogic::selectConfigLogic($params);
            if(empty($config)) return false;
            BaseCache::set($key, $config);
        }
        return $config;
    }
}