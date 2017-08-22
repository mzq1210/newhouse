<?php
/**
 * 地图搜索模型
 * @Author: <mzq>
 * @Date: 17-3-27
 */

namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\Exception;
use common\helper\pinyin\YPinYin;
use common\models\estate\config\Area;
use common\helper\cache\SearchCache;
use common\models\estate\extend\AreaPriceStatistics;

class MapSearchForm extends Model {
    
    /**
     * @desc
     * @Author: <mzq>
     * @Date: 2017-03-28
     * @param $params
     * @param $solrDb
     * @return array
     */
    public static function getArea($params, $solrDb) {
        try {
            $where = self::_getSolrSearchQ($params, $solrDb);
            $param['q'] = $where;
            $param['facet.field'] = ['estateAreaId'];
            $list = Yii::$app->solr->select($param,$solrDb);
            if($list['success'] == 1) {
                return $list['data'];
            }
        } catch (Exception $ex) {
            return array('数据条件错误');
        }
        return array();
    }

    /**
     * @desc  获取单个信息
     * @Author: <mzq>
     * @Date: 2017-03-28
     * @param $id
     * @param $solrDb
     * @return array
     */
    public static function getInfo($id, $solrDb) {
        $field = 'id,estateName,coverImageName,estateAddress,estateTrack';
        return Yii::$app->solr->findByPk($id, $solrDb, $field);
    }

    /**
     * @desc: 组合q公共查询
     * @Author: <mzq>
     * @Date: 2017-03-28
     * @param $searchField
     * @param $solrDb
     * @return string
     */
    private static function _getSolrSearchQ($searchField, $solrDb){
        $where = "companyCode:{$searchField['companyCode']} AND status:2 ";
        $property = self::getPropertyType($solrDb);

        //需要搜索的字段
        $fields = ['estateHouseType', 'tag', 'propertyType', 'other'];
        foreach($searchField as $key => $value){
            if(in_array($key,$fields) && ($searchField['other'] != 1 || $searchField['other'] != 2)){
                $value = intval($value);
                $value > 0 && $where .= " AND {$key}:{$value} ";
            }
        }
        //关键字搜索
        if($searchField['search']) {
            $search = strtolower(trim($searchField['search']));
            $search = strtr($search, array('·' => ' ','.' => ' ','-' => ' ', '－' => ' '));
            $searchArr = preg_split("/[\s| ]+/", $search);
            $sCount = count($searchArr);
            if($sCount == 1)
            {
                $searchAllPinYin = '';
                if(preg_match("/[\x7f-\xff]/",$search)){
                    $searchAllPinYin = YPinYin::encode($search,'all');
                }
                $searchAllPinYin = !empty($searchAllPinYin) ? ' OR search:'.$searchAllPinYin : '';
                $where .= " AND ( search:$search" . $searchAllPinYin.")";
            }else{
                $search = '';
                foreach($searchArr as $keyword){
                    if($keyword != ''){
                        if(preg_match("/[\x7f-\xff]/",$keyword)){
                            $searchAllPinYin = YPinYin::encode($keyword,'all');
                            $search .= $searchAllPinYin.' OR ';
                        }
                        $search .= $keyword.' OR ';
                    }
                }
                $search = rtrim($search, ' OR ');
                $where .= " AND search:({$search}) ";
            }
        }

        //价格区间
        if($searchField['lastAveragePrice'] > 0){//类型搜索
            $price = SearchCache::getOnePrice($searchField['companyCode'], $property, $searchField['lastAveragePrice']);
            if (preg_match('/^(\d+)-(\d+)$/', $price['regionName'])) {
                $where .= " AND lastAveragePrice:[{$price['minValue']} TO {$price['maxValue']}] ";
            } else if (strpos($price['regionName'], '以下')) {
                $regionName = (int)$price['minValue'] - 1;
                $where .= " AND lastAveragePrice:[* TO {$regionName}] ";
            } else if (strpos($price['regionName'], '以上')) {
                $regionName = (int)$price['minValue'] + 1;
                $where .= " AND lastAveragePrice:[{$regionName} TO *] ";
            }

        }else{
            if($searchField['customPriceLow'] > 0 && $searchField['customPriceTop'] > 0)
            {
                $where .= "　AND lastAveragePrice:[{$searchField['customPriceLow']} TO {$searchField['customPriceTop']}] ";
            }else if($searchField['customPriceLow'] > 0){
                $price = (float) $searchField['customPriceLow'];
                $where .= "　AND lastAveragePrice:[{$price} TO *] ";
            }else if($searchField['customPriceTop'] > 0){
                $price = (float) $searchField['customPriceTop'];
                $where .= "　AND lastAveragePrice:[* TO {$price}] ";
            }
        }
        switch ($searchField['other']) {
            case 1:
                $endDate = date('Y-m-d', time() - (30 * 24 * 3600));
                $where .= " AND openingDate:[" . $endDate . " TO " . date('Y-m-d') . "] ";
                break;
            case 2:
                $endDate = date('Y-m-d', time() - (90 * 24 * 3600));
                $where .= " AND openingDate:[" . $endDate . " TO " . date('Y-m-d') . "] ";
                break;
        }
        if ($searchField['swLng'] > 0) {//范围搜索
            $where .= " AND estateLongitude:[{$searchField['swLng']} TO {$searchField['neLng']}] ";
            $where .= " AND estateLatitude:[{$searchField['swLat']} TO {$searchField['neLat']}] ";
        }
        return $where;
    }

    /**
     * @desc 获取区域数据
     * @Author: <mzq>
     * @Date: 2017-03-28
     * @param $companyCode
     * @return array
     */
    public static function getAreaInfo($companyCode){
        $areaData = [];
        $field = ['estateAreaID','estateAreaName','longitude','latitude'];
        $info = Area::selectParentRecord($companyCode, $field);
        foreach ($info as $key => $value){
            $areaData[$value['estateAreaID']] = $value;
        }
        return $areaData;
    }

    /**
     * @desc 获取区域均价数据
     * @Author: <mzq>
     * @Date: 2017-03-28
     * @return array
     */
    public static function getAreaPriceInfo(){
        $areaPriceData = [];
        $field = ['estateAreaID','averagePrice'];
        $info = AreaPriceStatistics::selectRecord($field);
        foreach ($info as $key => $value){
            $areaPriceData[$value['estateAreaID']] = $value;
        }
        return $areaPriceData;
    }

    /**
     * @desc 将一位数分成一个二位数组
     * @Author: <mzq>
     * @Date: 2017-03-28
     * @param $arr
     * @return array
     */
    public static function partition($arr){
        if(empty($arr)) return array();
        $key = $value = array();
        for($i=0;$i<count($arr);$i++){
            if($i%2 == 0){
                $key[] = $arr[$i];
            } else {
                $value[] = $arr[$i];
            }
        }
        return array_filter(array_combine($key,$value));
    }

    /**
     * @desc　判断调用哪个solr
     * @Author: <mzq>
     * @Date: 2017-03-31
     * @return int
     */
    public static function getSolrName($propertyId){
        switch ($propertyId){
            case ESTATE_PROPERTY_TYPE:
                $solr = SOLR_ZHUZHAI;
                break;
            case BUSINESS_PROERTY_TYPE:
                $solr = SOLR_STORE;
                break;
            case OFFICE_PROERTY_TYPE:
                $solr = SOLR_OFFICE;
                break;
            default:
                $solr = SOLR_ZHUZHAI;
                break;
        }
        return $solr;
    }

    /**
     * @desc　判断属于哪个物业类型的价格
     * @Author: <mzq>
     * @Date: 2017-03-31
     * @param $solrDb
     * @return int
     */
    private static function getPropertyType($solrDb){
        return $propertyType = $solrDb == SOLR_ZHUZHAI ? ESTATE_PROPERTY_TYPE : ($solrDb == SOLR_STORE ? BUSINESS_PROERTY_TYPE : ($solrDb == SOLR_OFFICE ? OFFICE_PROERTY_TYPE : ESTATE_PROPERTY_TYPE));
    }
}
