<?php
/**
 * 缓存banner信息
 * @Author: <lixiaobin>
 * @Date: 17-4-13
 */

namespace common\helper\cache;

use common\helper\BaseCache;
use common\logic\BannerInfoLogic;

class BannerInfoCache{
    
    const REDIS_BANNER_INFO = 'banner_info';

    /**
     * 根据公司编码、客户端类型、广告分类唯一码获取banner广告信息
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公式编码
     *          Int $params['clientType'] 客户端类型0:Website 1:Mobilesite 2:App
     *          String $params['categoryKey'] 广告分类唯一码
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-27
    */
    public static function getBannerInfoCache($params){
        //生成redis KEY
        $key = self::REDIS_BANNER_INFO . ':' . $params['companyCode'] . ':' . $params['clientType'] . ':' . $params['categoryKey'];
        $bannerArr = BaseCache::get($key);
        if($bannerArr === false){
            $bannerArr = BannerInfoLogic::selectBannerLogic($params, 'bannerImageName,title,summary,targetURL' );
            if(empty($bannerArr)) return false;
            BaseCache::set($key, $bannerArr);
        }
        return $bannerArr;
    }
}