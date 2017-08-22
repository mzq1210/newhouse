<?php
/**
 * 缓存城市热门关键词
 * @Author: <lixiaobin>
 * @Date: 17-4-11
 */

namespace common\helper\cache;

use common\helper\BaseCache;
use common\logic\HotKeywordsLogic;

class HotKeywordsCache{

    //定义城市关键词redis key
    const REDIS_COMPANY_HOTSEARCH = 'hot_search_keyword';

    /**
     * 根据当前客户端类型、城市编码、物业类型获取后台设置的热门关键词
     * @Params: Array $params
     *          $params['clientType']  客户端类型0-Website1-Mobilesite2-App  *注意：暂时去掉按客户端类型分类查询
     *          $params['companyCode']  城市公司编码
     *          $params['propertyTypeID'] 物业类型 1:住宅 5:商业 8:写字楼
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-11
    */
    public static function getHotKeywordsCache($params){
        //拼接 redis KEY
        $key = self::REDIS_COMPANY_HOTSEARCH.':'.$params['companyCode'].'-'.$params['propertyTypeID'];
        $hotSearchKeyword = BaseCache::get($key);
        if($hotSearchKeyword === false){
            $hotSearchKeyword = HotKeywordsLogic::selectHotKeywordsLogic($params, 'keyword,targetURL');
            if(empty($hotSearchKeyword)) return false;
            BaseCache::set($key, $hotSearchKeyword);
        }
        return $hotSearchKeyword;
    }

}