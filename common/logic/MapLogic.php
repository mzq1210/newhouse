<?php
/**
 * 地图搜索类
 * @Author: <mzq>
 * @Date: 17-3-27
 */

namespace common\logic;

use Yii;
use common\models\MapSearchForm;
use app\components\base\BaseController;

class MapLogic{
    
    /**
     * @desc　异步获取信息
     * @Author: <mzq>
     * @Date: 2017-03-28
     * @param $params
     * @param $solrDb
     * @return array
     */
    public static function getMapData($params, $solrDb){
        $list = MapSearchForm::getArea($params, $solrDb);
        if(!empty($list)){
            return self::_getExtendInfo($list, $params['companyCode']);
        }
        return array();
    }

    /**
     * @desc　获取扩展信息
     * @Author: <mzq>
     * @Date: 2017-03-28
     * @param $list
     * @param $companyCode
     * @return array
     */
    private static function _getExtendInfo($list, $companyCode){
        $info = [];
        $areaIds = MapSearchForm::partition($list['facet']['estateAreaId']);
        $areaData = MapSearchForm::getAreaInfo($companyCode);
        $areaPriceData = MapSearchForm::getAreaPriceInfo();

        foreach ($areaIds as $key => $value){
            $areaInfo = isset($areaData[$key])? $areaData[$key] : [];
            if($areaInfo == []) continue;
            $info[$key]['estateAreaId'] = $key;
            $info[$key]['estateCount'] = $value;
            $info[$key]['estateAreaName'] = $areaInfo['estateAreaName'];
            $info[$key]['longitude'] = $areaInfo['longitude'];
            $info[$key]['latitude'] = $areaInfo['latitude'];
            $priceInfo = isset($areaPriceData[$key])? $areaPriceData[$key] : [];
            $info[$key]['averagePrice'] = !empty($priceInfo)? $priceInfo['averagePrice'] : 0;
        }
        return $info;
    }

}