<?php
/**
 * 从model类中获取首页推荐的热门楼盘
 * @Author: <lixiaobin>
 * @Date: 17-4-13
 */

namespace common\logic;

use common\models\SearchForm;
use common\models\webMgmt\RecommendedEstate;

class RecommendEstateLogic{

    /**
     * 根据当前客户端类型、城市编码推荐的热门楼盘
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公司编码
     *          Int $params['clientType'] 客户端类型 0:pc站 1:wap站 2：App *注意：暂时去掉按客户端类型分类查询
     * @Params: string $fields 需要查询的字段
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-13
     */
    public static function selectEstateLogic($params, $fields = '*'){
        $hotEstate = RecommendedEstate::selectRecord($params, $fields);
        $hotEstateArr  = [];
        if(!empty($hotEstate)){
            foreach ($hotEstate as $val){
                switch ($val['propertyTypeID']){
                    case ESTATE_PROPERTY_TYPE:
                        $solrDb = SOLR_ZHUZHAI;
                        break;
                    case BUSINESS_PROERTY_TYPE:
                        $solrDb = SOLR_STORE;
                        break;
                    case OFFICE_PROERTY_TYPE;
                        $solrDb = SOLR_OFFICE;
                        break;
                }
                $solrArr = SearchForm::selectSolrEstate($params['companyCode'], $val['estateID'], $solrDb);
                if(!empty($solrArr)){
                    $solrArr[0]['commentNum'] = 0;
                    $solrArr[0]['coverImageName'] = IMG_DOMAIN . $solrArr[0]['coverImageName'];
                    if($solrArr[0]['collaborationType'] == 1){
                        $solrArr[0]['commentNum'] = $solrArr[0]['totalSeeHouseReviewCount'] + $solrArr[0]['totalReviewCount'];
                    }else{
                        $solrArr[0]['commentNum'] = $solrArr[0]['totalReviewCount'];
                    }
                    $hotEstateArr[] = array_merge($val, $solrArr[0]);
                }
            }
            return $hotEstateArr;
        }else{
            $solrArr = SearchForm::getFourEstateInfo(['companyCode' => $params['companyCode'], 'collaborationType' => 1]);
            if(!empty($solrArr)){
                foreach ($solrArr as $val){
                    $val['estateID'] = $val['id'];
                    $val['propertyTypeID'] = ESTATE_PROPERTY_TYPE;
                    $val['commentNum'] = 0;
                    $val['commentNum'] = $val['totalSeeHouseReviewCount'] + $val['totalReviewCount'];
                    $val['coverImageName'] = IMG_DOMAIN . $solrArr[0]['coverImageName'];
                    $hotEstateArr[] = $val;
                }
                return $hotEstateArr;
            }
            return false;
        }
        return false;
    }

}