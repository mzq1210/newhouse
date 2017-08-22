<?php
/**
 * 获取后台配置的热门关键词
 * @Author: <lixiaobin>
 * @Date: 17-4-11
 */

namespace common\logic;

use common\models\webMgmt\HotKeywords;

class HotKeywordsLogic{
    
    /**
     * 根据当前客户端类型、城市编码、物业类型从模型中获取后台设置的热门关键词
     *@Params: Array $params
     *          $params['clientType']  客户端类型0-Website1-Mobilesite2-App  *注意：暂时去掉按客户端类型分类查询
     *          $params['companyCode']  城市公司编码
     *          $params['propertyTypeID'] 物业类型 1:住宅 5:商业 8:写字楼
     * @Params: string $fields 需要查询的字段
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-11
    */
    public static function selectHotKeywordsLogic($params, $fields= '*'){
        return HotKeywords::selectRecord($params, $fields);
    } 
    
}