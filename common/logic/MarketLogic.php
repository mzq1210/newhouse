<?php
/**
 * 获取市场行情数据
 * @Author: <lixiaobin>
 * @Date: 17-4-28
 */

namespace common\logic;

use common\models\webMgmt\MarketSummary;

class MarketLogic{

    /**
     * 根据公司编码获取市场行情
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公司编码
     * @Params: String $fields 需要查询的字段
     * @Retrun: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-28
     */
    public static function selectMarketLogic($params,$fields = '*'){
        return MarketSummary::selectRecord($params, $fields);
    }
}