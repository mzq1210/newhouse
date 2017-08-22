<?php
/**
 * 处理主题推荐楼盘的逻辑
 * @Author: <lixiaobin>
 * @Date: 17-4-17
 */

namespace common\logic;

use common\models\estate\extend\HouseType;
use common\models\SearchForm;
use common\models\webMgmt\ThemeEstate;

class ThemeEstateLogic{

    /**
     * 将获取到的主题楼盘列表，根据不同的业态获取楼盘信息
     * @Params: Int $companyCode 公司编码
     * @Params: Array $params
     *          Int $params['categoryID'] 推荐主题分类ID 必填
     *          Int $params['page'] 当前页 必填
     *          Int $params['pageSize'] 每页显示的条数 必填
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
    */
    public static function selectEstateLogic($companyCode, $params){
        $themeEstate = ThemeEstate::selectRecord($params);
        if(!empty($themeEstate)){
            //根据当前信息所属的业态去不同的solr库获取数据
            foreach ($themeEstate['results'] as $key => $val){
                switch ($val['propertyTypeID']){
                    case ESTATE_PROPERTY_TYPE:
                        $fields = 'id,estateName,areaTxt,estateAddress,coverImageName,estateLongitude,estateLatitude,userWishlistCount,totalSeeHouseCount,
                        totalReviewCount,promotionInfo,tagList,areaTxt,propertyTypeName,majorHouseType,lastAveragePrice,lastRoomMinPrice,isJudge,
                        undetermined,collaborationType,totalSeeHouseReviewCount,minBuildArea,maxBuildArea,pageViewCount';
                        $info = SearchForm::suggest($companyCode,'',SOLR_ZHUZHAI, array('id'=>$val['estateID']), $fields);
                        $val['estateName'] = $info[0]['estateName'];
                        $val['areaTxt'] = $info[0]['areaTxt'];
                        $val['estateAddress'] = $info[0]['estateAddress'];
                        $val['coverImageName'] = IMG_DOMAIN . $info[0]['coverImageName'];
                        $val['estateLongitude'] = $info[0]['estateLongitude'];
                        $val['estateLatitude'] = $info[0]['estateLatitude'];
                        $val['userWishlistCount'] = $info[0]['userWishlistCount'];
                        $val['totalSeeHouseCount'] = $info[0]['totalSeeHouseCount'];
                        $val['totalReviewCount'] = $info[0]['totalReviewCount'];
                        $val['tagList'] = isset($info[0]['tagList']) ? $info[0]['tagList'] : '';
                        $val['lastAveragePrice'] = $info[0]['lastAveragePrice'];
                        $val['undetermined'] = $info[0]['undetermined'];
                        $val['lastRoomMinPrice'] = $info[0]['lastRoomMinPrice'];
                        $val['isJudge'] = $info[0]['isJudge'];
                        $val['propertyTypeName'] = str_replace('/', ' ', $info[0]['propertyTypeName']);
                        $val['collaborationType'] = $info[0]['collaborationType'];
                        $val['totalSeeHouseReviewCount'] = $info[0]['totalSeeHouseReviewCount'];
                        $val['promotionInfo'] = $info[0]['promotionInfo'];
                        $val['minBuildArea'] = $info[0]['minBuildArea'];
                        $val['maxBuildArea'] = $info[0]['maxBuildArea'];
                        $val['pageViewCount'] = $info[0]['pageViewCount'];
                        $val['commentNum'] = $info[0]['totalSeeHouseReviewCount'] + $info[0]['totalReviewCount'];
                        $majorHouseType = !empty($info[0]['majorHouseType']) ? HouseType::makeHouseArray($info[0]['majorHouseType']) : '';
                        $val['houseType'] = !empty($majorHouseType) ? HouseType::makeHousePattern($majorHouseType, 2) : '';
                        $themeEstate['results'][$key] = $val;
                        break;
                    case BUSINESS_PROERTY_TYPE:
                        $fields = 'id,estateName,areaTxt,estateAddress,coverImageName,estateLongitude,estateLatitude,userWishlistCount,totalSeeHouseCount,
                        totalReviewCount,promotionInfo,tagList,areaTxt,propertyTypeName,majorHouseType,lastAveragePrice,lastRoomMinPrice,isJudge,
                        undetermined,collaborationType,totalSeeHouseReviewCount,minBuildArea,maxBuildArea,pageViewCount,standardFloorArea';
                        $info = SearchForm::suggest($companyCode,'',SOLR_STORE, ['id'=>$val['estateID']], $fields);
                        $val['estateName'] = $info[0]['estateName'];
                        $val['areaTxt'] = $info[0]['areaTxt'];
                        $val['estateAddress'] = $info[0]['estateAddress'];
                        $val['coverImageName'] = IMG_DOMAIN . $info[0]['coverImageName'];
                        $val['estateLongitude'] = $info[0]['estateLongitude'];
                        $val['estateLatitude'] = $info[0]['estateLatitude'];
                        $val['userWishlistCount'] = $info[0]['userWishlistCount'];
                        $val['totalSeeHouseCount'] = $info[0]['totalSeeHouseCount'];
                        $val['totalReviewCount'] = $info[0]['totalReviewCount'];
                        $val['tagList'] = isset($info[0]['tagList']) ? $info[0]['tagList'] : '';
                        $val['lastAveragePrice'] = $info[0]['lastAveragePrice'];
                        $val['undetermined'] = $info[0]['undetermined'];
                        $val['lastRoomMinPrice'] = $info[0]['lastRoomMinPrice'];
                        $val['isJudge'] = $info[0]['isJudge'];
                        $val['propertyTypeName'] = str_replace('/', ' ', $info[0]['propertyTypeName']);
                        $val['collaborationType'] = $info[0]['collaborationType'];
                        $val['totalSeeHouseReviewCount'] = $info[0]['totalSeeHouseReviewCount'];
                        $val['promotionInfo'] = $info[0]['promotionInfo'];
                        $val['minBuildArea'] = $info[0]['minBuildArea'];
                        $val['maxBuildArea'] = $info[0]['maxBuildArea'];
                        $val['pageViewCount'] = $info[0]['pageViewCount'];
                        $val['standardFloorArea'] = $info[0]['standardFloorArea'];
                        $val['commentNum'] = $info[0]['totalSeeHouseReviewCount'] + $info[0]['totalReviewCount'];
                        //$majorHouseType = !empty($info[0]['majorHouseType']) ? HouseType::makeHouseArray($info[0]['majorHouseType']) : '';
                        $val['houseType'] = '';//!empty($majorHouseType) ? HouseType::makeHousePattern($majorHouseType, 2) : '';
                        $themeEstate['results'][$key] = $val;
                        break;
                    case OFFICE_PROERTY_TYPE:
                        $fields = 'id,estateName,areaTxt,estateAddress,coverImageName,estateLongitude,estateLatitude,userWishlistCount,totalSeeHouseCount,
                        totalReviewCount,promotionInfo,tagList,areaTxt,propertyTypeName,majorHouseType,lastAveragePrice,lastRoomMinPrice,isJudge,
                        undetermined,collaborationType,totalSeeHouseReviewCount,minBuildArea,maxBuildArea,pageViewCount,standardFloorArea';
                        $info = SearchForm::suggest($companyCode,'',SOLR_OFFICE, ['id'=>$val['estateID']], $fields);
                        $val['estateName'] = $info[0]['estateName'];
                        $val['areaTxt'] = $info[0]['areaTxt'];
                        $val['estateAddress'] = $info[0]['estateAddress'];
                        $val['coverImageName'] = IMG_DOMAIN . $info[0]['coverImageName'];
                        $val['estateLongitude'] = $info[0]['estateLongitude'];
                        $val['estateLatitude'] = $info[0]['estateLatitude'];
                        $val['userWishlistCount'] = $info[0]['userWishlistCount'];
                        $val['totalSeeHouseCount'] = $info[0]['totalSeeHouseCount'];
                        $val['totalReviewCount'] = $info[0]['totalReviewCount'];
                        $val['tagList'] = isset($info[0]['tagList']) ? $info[0]['tagList'] : '';
                        $val['lastAveragePrice'] = $info[0]['lastAveragePrice'];
                        $val['undetermined'] = $info[0]['undetermined'];
                        $val['lastRoomMinPrice'] = $info[0]['lastRoomMinPrice'];
                        $val['isJudge'] = $info[0]['isJudge'];
                        $val['propertyTypeName'] = str_replace('/', ' ', $info[0]['propertyTypeName']);
                        $val['collaborationType'] = $info[0]['collaborationType'];
                        $val['totalSeeHouseReviewCount'] = $info[0]['totalSeeHouseReviewCount'];
                        $val['promotionInfo'] = $info[0]['promotionInfo'];
                        $val['minBuildArea'] = $info[0]['minBuildArea'];
                        $val['maxBuildArea'] = $info[0]['maxBuildArea'];
                        $val['pageViewCount'] = $info[0]['pageViewCount'];
                        $val['standardFloorArea'] = $info[0]['standardFloorArea'];
                        $val['commentNum'] = $info[0]['totalSeeHouseReviewCount'] + $info[0]['totalReviewCount'];
                        //$majorHouseType = !empty($info[0]['majorHouseType']) ? HouseType::makeHouseArray($info[0]['majorHouseType']) : '';
                        $val['houseType'] = '';//!empty($majorHouseType) ? HouseType::makeHousePattern($majorHouseType, 2) : '';
                        $themeEstate['results'][$key] = $val;
                        break;
                }
            }
        }
        return $themeEstate;
    }
}