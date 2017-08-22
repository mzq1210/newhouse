<?php
/**
 * 获取均价嘞
 * @Author: <lixiaobin>
 * @Date: 2017-3-22
 */

namespace common\logic;

use common\models\webMgmt\EstatePriceRegion;

class EstatePriceLogic{

    /**
     * 根据城市和业态获取均价
     * @Params: Int $companyCode 城市编码
     * @Params: Int  $propertyTypeId 业态id
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-03
    */
    public static function selectPriceLogic($companyCode, $propertyTypeId, $fields = '*'){
        $price = EstatePriceRegion::selectRecord($companyCode, $propertyTypeId, $fields);
        if(!empty($price)) return $price;
        return false;
    }

}