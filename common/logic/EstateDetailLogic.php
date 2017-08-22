<?php

/**
 * 楼盘详情logic
 * <liangshimao>
 */

namespace common\logic;

use common\components\Tools;
use common\models\estate\agency\AgencyEstate;
use common\models\estate\agency\AgencyInfo;
use common\models\estate\agency\AgencyTask;
use common\models\estate\basic\Extend;
use common\models\estate\config\Area;
use common\models\estate\config\Property;
use common\models\estate\config\Track;
use common\models\estate\extend\Album;
use common\models\estate\extend\AlbumImage;
use common\models\estate\extend\AreaPriceStatistics;
use common\models\estate\extend\Closing;
use common\models\estate\extend\HouseType;
use common\models\estate\extend\HouseTypeProComment;
use common\models\estate\extend\HouseTypeProtoRoom;
use common\models\estate\extend\Opening;
use common\models\estate\extend\PageViewLog;
use common\models\estate\extend\PreSalePermit;
use common\models\estate\extend\PriceStatistics;
use common\models\estate\basic\Basic;
use common\models\SearchForm;
use common\models\user\PurchaseIntention;
use common\models\user\Review;
use common\models\user\UserInfo;
use common\models\user\ViewHistory;
use console\common\House;
use Yii;
use yii\helpers\ArrayHelper;

class EstateDetailLogic {
    public static $basic = 'id,estateName,estateAliasName,estateDeveloperName,coverImageName,estateAddress,estateLongitude,estateLatitude,companyCode,collaborationType,minBuildArea,maxBuildArea,status,
    
    totalReviewCount,totalReviewScore,totalAreaReviewScore,totalPriceReviewScore,userWishlistCount,totalSeeHouseCount,weeklySeeHouseCount,monthlySeeHouseCount,
    totalSeeHouseReviewCount,pageViewCount,totalHouseTypeReviewScore,totalFacilityReviewScore,totalEnvironmentReviewScore,totalTrafficReviewScore,
    
    propertyTypeParentID,tagList,estateTrack,areaTxt,majorHouseType,
    openingDate,actualOpeningDate,openingDateList,preSalePermit,preSalePermitList,closingDate,actualClosingDate,closingDateList,
    BuildingType,propertyTypeName,bankName,estateAreaId,albumCount,propertyTypeChildName,
    
    totalFloorArea,decorationSummary,propertyCompanyDescription,lastPriceDescription,
    floorHeight,planedCarport,buildingFloorSummary,roomStatus,saleStatus,startDate,completeDate,
    promotionInfo,title,lastRoomMinPrice,isJudge,undetermined,salesPhoneNumber,
    projectEnvironmentDescription,propertyCompany,salesOfficeAddress,propertyPrice,
    loanType,projectProgress,propertyRightLimit,totalBuildingArea,lastAveragePrice,
    projectDescription,downPaymentDescription';

    public static $zhuzhaiBasicField = 'greenRate,volumeRate,planedCommunityDescription,propertyAdvantage,planedHousehold,liftFamilyDescription,';

    public static $storeBasicField = 'officeLevel,innerFacilityDescription,standardFloorArea,singleFloorArea,hasFlue,totalSet,planedPropertyType,enterMerchant,';

    public static $officeBasicField = 'greenRate,projectSupportingDescription,standardFloorArea,officeLevel,enterCompany,liftSupportingDescription,innerFacilityDescription,';

    //public static $simpleFiled = 'id,estateName,tagList,propertyTypeParentID,salesPhoneNumber';

    /**
     * 从solr中取出基本数据
     * @param $estateID
     * @param string $fields
     * @return bool
     * <liangshimao>
     */
    public static function getDataFromSolr($estateID, $propertyTypeID, $fields = '*') {
        $core = self::getCoreName($propertyTypeID);
        $data = Yii::$app->solr->findByPk($estateID, $core, $fields);
        if (empty($data) || !is_array($data)) {
            return false;
        }
        return $data;
    }

    /**
     * 获取solr中的所有楼盘信息,楼盘详情首页用
     * @param $estateID
     * @param string $core
     * @param bool $preview 是否预览
     * @return bool
     * <liangshimao>
     */
    public static function getBasic($estateID, $propertyTypeID) {
        $field = self::getFieldByProperty($propertyTypeID);
        $data = self::getDataFromSolr($estateID, $propertyTypeID, $field);
        if (empty($data))
            return false;
        //楼盘封面图片返回完整路径
        //$data['banner'] = self::getPicture($estateID, $propertyTypeID);
        $data['coverImageName'] = empty($data['coverImageName']) ? '' : IMG_DOMAIN . $data['coverImageName'];
        //楼盘价格去掉.00的字样
        $data['lastAveragePrice'] = floatval($data['lastAveragePrice']);
        $data['lastRoomMinPrice'] = floatval($data['lastRoomMinPrice']);
        //楼盘业态列表转换为文字列表
        $data['propertyTypeList'] = [];
        if (isset($data['propertyTypeParentID']) && is_array($data['propertyTypeParentID'])) {
            $data['propertyTypeList'] = Property::makepropertyArray($data['propertyTypeParentID']);
        }
        //拼接交通路况信息
        $data['trafficInfo'] = '';
        if (!empty($data['estateTrack'])) {
            $data['trafficInfo'] = Track::makeString(Track::makeTrafficArray($data['estateTrack']));
        }
        //拼接主力户型信息
        $data['majorHouse'] = [];
        if (!empty($data['majorHouseType'])) {
            $data['majorHouse'] = HouseType::makeHouseArray($data['majorHouseType']);
        }
        //拼接楼盘标签字符串
        $data['tagString'] = '';
        if (!empty($data['tagList'])) {
            $data['tagString'] = join('、', $data['tagList']);
        }
        //拼接合作银行字符串
        $data['bankString'] = '';
        if (!empty($data['bankName'])) {
            $data['bankString'] = join('、', $data['bankName']);
        }
        //拼接工程进度字符串
        $data['projectProgressString'] = '';
        $data['projectProgressDate'] = '';
        if ($data['projectProgress'] == '0') {
            $data['projectProgressString'] = '未开工';
            $data['projectProgressDate'] = $data['startDate'];
        }else if($data['projectProgress'] == '1'){
            $data['projectProgressString'] = '在建中';
            $data['projectProgressDate'] = $data['completeDate'];
        }else if($data['projectProgress'] == '2'){
            $data['projectProgressString'] = '已竣工';
        }
        //拼接建筑类型字符串
        $data['BuildingTypeString'] = '';
        if (!empty($data['BuildingType'])) {
            $data['BuildingTypeString'] = join('、', $data['BuildingType']);
        }
        //预售许可证转换内容
        $data['preSalePermitArray'] = [];
        if (!empty($data['preSalePermitList'])) {
            $data['preSalePermitArray'] = PreSalePermit::makePermitArray($data['preSalePermitList']);
        }
        //开盘信息转换内容
        $data['openingDateListArray'] = [];
        if (!empty($data['openingDateList'])) {
            $data['openingDateListArray'] = Opening::makeOpeningArray($data['openingDateList']);
        }
        //交房信息转换内容
        $data['closingDateArray'] = [];
        if (!empty($data['closingDateList'])) {
            $data['closingDateArray'] = Closing::makeClosingArray($data['closingDateList']);
        }
        //楼盘所在区域和地址
        $data['areaName'] = !empty($data['areaTxt'])?join('-',$data['areaTxt']):'暂无信息';

        //物业费信息
        $data['propertyPrice'] = $data['propertyPrice']=='0.00'?'待定':($data['propertyPrice']==''?'暂无信息':$data['propertyPrice'].'元/平米/月');
        //如果solr中不包含这些字段，则手动添加上去！
        if (empty($data['preSalePermit'])) {
            $data['preSalePermit'] = '';
        }
        if (empty($data['openingDate'])) {
            $data['openingDate'] = empty($data['actualOpeningDate'])?'':date('Y-m-d',strtotime($data['actualOpeningDate']));
        }
        if (empty($data['closingDate'])) {
            $data['closingDate'] = empty($data['actualClosingDate'])?'':date('Y-m-d',strtotime($data['actualClosingDate']));
        }
        if (empty($data['propertyTypeName'])) {
            $data['propertyTypeName'] = '';
        }
        if (empty($data['tagList'])) {
            $data['tagList'] = [];
        }
        if (empty($data['bankName'])) {
            $data['bankName'] = '';
        }
        if (empty($data['BuildingType'])) {
            $data['BuildingType'] = '';
        }

        unset($data['propertyType'], $data['estateTrack'], $data['majorHouseType'], $data['propertyTypeParentID'], $data['openingDateList'], $data['closingDateList'], $data['BuildingType'], $data['preSalePermitList'],$data['bankName'],$data['projectProgress'],$data['actualOpeningDate'],$data['actualClosingDate'],$data['startDate'],$data['completeDate']);
        foreach ($data as $key => $val) {
            if ($val === '') {
                $data[$key] = '暂无信息';
            }
        }

        return $data;
    }

    /**
     * 获取楼盘简要信息
     * <liangshimao>
     */
    public static function getSimple($estateID, $propertyTypeID)
    {

        $data = self::getDataFromSolr($estateID, $propertyTypeID, 'id,estateName,coverImageName,externalEstateID,areaTxt');
        if (empty($data))
            return false;
        $data['coverImageName'] = empty($data['coverImageName']) ? '' : IMG_DOMAIN . $data['coverImageName'];
        $data['areaName'] = !empty($data['areaTxt'])?join('-',$data['areaTxt']):'暂无信息';
        unset($data['areaTxt']);
        return $data;
    }

    /**
     * 获取经纪人带看信息和评价 (多种业态公用)
     * page 为0 表示第1页
     */
    public static function getAgencyTask($estateID, $pageSize, $page) {
        $res = [];
        $task = AgencyTask::selectRecord($estateID, $pageSize, $page, 'agencyID,taskDate,customerName,customerReviewSummary,customerReviewScore,customerReviewTag');
        if (empty($task['data'])) {
            return false;
        }
        foreach ($task['data'] as $key => $value) {
            $info = AgencyInfo::getDetail($value['agencyID'], 'avatarsImageName,agencyName,agencyCellphone,isActive');
            if (empty($info)) {
                $info = ['avatarsImageName' => '', 'agencyName' => '', 'agencyCellphone' => ''];
            }
            //带看时间格式统一化(如:2017-02-08)
            $value['taskDate'] = date('Y-m-d', strtotime($value['taskDate']));
            $value['customerReviewScore'] = number_format(round($value['customerReviewScore'],1),1);
            $value['customerReviewTag'] = empty($value['customerReviewTag'])?[]:explode(',',$value['customerReviewTag']);
            $value['customerName'] = substr_replace($value['customerName'],'****',3,4);
            //拼接经纪人带看情况和基本信息
            $res[] = ArrayHelper::merge($value, $info);
        }
        return [
            'results' => $res,
            'curPage' => $task['curPage'],
            'pageCount' => $task['pageCount'],
            'count' => $task['count'],
        ];
    }

    /**
     * 获取楼盘户型
     * <liangshimao>
     */
    public static function getHouseType($estateID, $propertyTypeID) {
        $res = [
            ['label' => '主推户型', 'data' => []],
            ['label' => '一居户型', 'data' => []],
            ['label' => '二居户型', 'data' => []],
            ['label' => '三居户型', 'data' => []],
            ['label' => '四居户型', 'data' => []],
            ['label' => '五居及以上', 'data' => []],
            ['label' => '其他户型', 'data' => []],
        ];
        $list = HouseType::selectRecord($estateID, $propertyTypeID, 'houseTypeID,houseTypeName,salesStatus,isMajor,bedRoomQuantity,livingRoomQuantity,restRoomQuantity,cookRoomQuantity,buildArea,houseTypeImageName,houseTypeDescription');
        if (empty($list))
            return false;
        foreach ($list as $key => $value) {
            $value['houseTypeImageName'] = IMG_DOMAIN . $value['houseTypeImageName'];
            $value['buildArea'] = floatval($value['buildArea']);
            $houseLabel = HouseType::makeTypeName($value['bedRoomQuantity'], $value['livingRoomQuantity'], $value['restRoomQuantity'], $value['cookRoomQuantity']);

            $value['houseTypeLabel'] = !empty($houseLabel)?$houseLabel:$value['houseTypeDescription'];
            
            switch ($value['bedRoomQuantity']) {
                case 0:
                    $res[6]['data'][] = $value;
                    break;
                case 1:
                    $res[1]['data'][] = $value;
                    break;
                case 2:
                    $res[2]['data'][] = $value;
                    break;
                case 3:
                    $res[3]['data'][] = $value;
                    break;
                case 4:
                    $res[4]['data'][] = $value;
                    break;
                default:
                    $res[5]['data'][] = $value;
                    break;
            }

            if ($value['isMajor'] == '1') {
                $res[0]['data'][] = $value;
            }
        }

        foreach ($res as $k => $v) {
            $res[$k]['count'] = count($v['data']);
            if ($res[$k]['count'] == 0) {
                unset($res[$k]);
            } else {
                ArrayHelper::multisort($res[$k]['data'], 'isMajor', SORT_DESC);
            }
        }

        return array_values($res);
    }

    /**
     * 获取简单户型信息（户型详情导航栏）
     * <liangshimao>
     */
    public static function getSimpleHouseType($estateID, $propertyTypeID) {
        $res = [
            ['label' => '全部户型', 'data' => []],
            ['label' => '一居户型', 'data' => []],
            ['label' => '二居户型', 'data' => []],
            ['label' => '三居户型', 'data' => []],
            ['label' => '四居户型', 'data' => []],
            ['label' => '五居户型及以上', 'data' => []],
            ['label' => '其他户型', 'data' => []],
        ];
        $list = HouseType::selectRecord($estateID, $propertyTypeID, 'houseTypeID,isMajor,bedRoomQuantity,livingRoomQuantity,restRoomQuantity,cookRoomQuantity,buildArea,houseTypeDescription');
        if (empty($list))
            return false;
        foreach ($list as $key => $value) {
            $houseLabel = HouseType::makeTypeName($value['bedRoomQuantity'], $value['livingRoomQuantity'], $value['restRoomQuantity'], $value['cookRoomQuantity']);
            $value['houseTypeLabel'] = !empty($houseLabel)?$houseLabel:$value['houseTypeDescription'];
            $value['buildArea'] = floatval($value['buildArea']);
            switch ($value['bedRoomQuantity']) {
                case 0:
                    $res[6]['data'][] = $value;
                    break;
                case 1:
                    $res[1]['data'][] = $value;
                    break;
                case 2:
                    $res[2]['data'][] = $value;
                    break;
                case 3:
                    $res[3]['data'][] = $value;
                    break;
                case 4:
                    $res[4]['data'][] = $value;
                    break;
                default:
                    $res[5]['data'][] = $value;
                    break;
            }
            $res[0]['data'][] = $value;
        }

        foreach ($res as $k => $v) {
            $res[$k]['count'] = count($v['data']);
            if ($res[$k]['count'] == 0) {
                unset($res[$k]);
            } else {
                ArrayHelper::multisort($res[$k]['data'], 'isMajor', SORT_DESC);
            }
        }

        return array_values($res);
    }

    /**
     * 获取户型详情
     * <liangshimao>
     */
    public static function getHouseTypeDetail($houseTypeID) {
        $info = HouseType::getDetail($houseTypeID, 'houseTypeID,estateID,propertyTypeID,houseTypeName,isMajor,salesStatus,bedRoomQuantity,livingRoomQuantity,restRoomQuantity,cookRoomQuantity,buildArea,sharedArea,insideArea,noChargeArea,houseTypeImageName,tags,aspect,houseTypeDescription');
        if (empty($info)) {
            return false;
        }
        //户型图片修改为绝对路径
        $info['houseTypeImageName'] = IMG_DOMAIN . $info['houseTypeImageName'];
        $info['aspect'] = empty($info['aspect'])?'':$info['aspect'];
        $info['buildArea'] = empty(intval($info['buildArea']))?'暂无信息':floatval($info['buildArea']).'㎡';
        $info['sharedArea'] = empty(intval($info['sharedArea']))?'暂无信息':floatval($info['sharedArea']).'㎡';
        $info['insideArea'] = empty(intval($info['insideArea']))?'暂无信息':floatval($info['insideArea']).'㎡';
        $info['noChargeArea'] = empty(intval($info['noChargeArea']))?'暂无信息':floatval($info['noChargeArea']).'㎡';
        
        $comment = HouseTypeProComment::selectRecord($houseTypeID, 'tagName,content');
        $protoRoom = self::getHouseTypeProtoRoom($houseTypeID);
        $info['comment'] = $comment;
        $info['photo'] = $protoRoom;
        $houseLabel = HouseType::makeTypeName($info['bedRoomQuantity'], $info['livingRoomQuantity'], $info['restRoomQuantity'], $info['cookRoomQuantity']);
        $info['houseLabel'] = !empty($houseLabel)?$houseLabel:$info['houseTypeDescription'];
        unset($info['houseTypeDescription']);
        return $info;
    }

    /**
     * 获取户型样板间
     */
    public static function getHouseTypeProtoRoom($houseTypeID) {
        $info = HouseTypeProtoRoom::selectRecord($houseTypeID, 'protoRoomImageName,protoRoomTitle,id');
        $detail = HouseType::getDetail($houseTypeID, 'houseTypeImageName');
        $detail['houseTypeImageName'] = IMG_DOMAIN . $detail['houseTypeImageName'];
        $houseImageArray = [
            'protoRoomImageName'=> $detail['houseTypeImageName'],
            'protoRoomTitle' => '户型图',
            'id' => 0,
        ];
        if (empty($info)) {
            return [$houseImageArray];
        }
        foreach ($info as $key => $value) {
            $info[$key]['protoRoomImageName'] = IMG_DOMAIN . $value['protoRoomImageName'];
        }
        array_unshift($info,$houseImageArray);

        return $info;
    }

    /**
     * 价格走势
     * <liangshimao>
     */
    public static function getPriceStatistics($params) {
        $estateID = $params['estateID'];
        $propertyTypeID = $params['propertyTypeID'];
        $pageSize = $params['pageSize'];
        if (empty($estateID))
            return false;
        if (empty($propertyTypeID))
            return false;

        $m = date("m");
        $y = date("Y");
        $montha = [];
        $months = [];
        for ($i = 0; $i<12; $i++){
            $nowMouth = $m ;
            if ($nowMouth < '10' and $i > 0){
                $nowMouth = "0".$nowMouth;
            }
            $montha[] = "$y".'.'.$nowMouth;
            $m--;
            if ($m < '1'){
                $m = '12';
                $y = $y - 1;
            }
        }

        $condition = [
            'estateID' => $estateID,
            'propertyTypeID' => $propertyTypeID,
            'pageSize' => $pageSize
        ];
        $field = 'averagePrice,statisticsTime';
        $estatePriceTrend = PriceStatistics::findPrice($field, $condition);
        $estateResult = [];

        //查询楼盘基本信息
        $estateBasicInfo = Basic::findOne(["estateID" => $estateID]);
        //查询楼盘业态类型
        $propertyTypeName = Property::getPropertyTypeName($propertyTypeID);

        $areaCondition = [
            'estateAreaID' => $estateBasicInfo->estateAreaID,
            'propertyTypeID' => $propertyTypeID,
            'pageSize' => $pageSize
        ];

        $areaPriceTrend = AreaPriceStatistics::findPrice($field, $areaCondition);
        $areaResult = [];

        //查询楼盘区域信息
        $areaInfo = AreaLogic::selectAreaInfoLogic($estateBasicInfo->estateAreaID);

        sort($montha);
        $monthb = $montha;
        /*foreach ($montha as $values){
            $months[] = substr($values, 5);
        }*/
        //根据合并的日期 统计楼盘所有日期的价格
        foreach ($montha as $val) {
            foreach ($estatePriceTrend as $k => $v) {
                $key = date("Y.m", strtotime($v['statisticsTime']));
                if ($val == $key) {
                    $estateResult[$val] = [$val, intval($v['averagePrice'])];
                }
            }
            if (!isset($estateResult[$val])) {
                $estateResult[$val] = [$val, null];
            }
        }

        //根据合并的日期 统计地区所有日期的价格
        foreach ($montha as $val) {
            foreach ($areaPriceTrend as $k => $v) {
                $key = date("Y.m", strtotime($v['statisticsTime']));
                if ($val == $key) {
                    $areaResult[$val] = [$val, intval($v['averagePrice'])];
                }
            }
            if (!isset($areaResult[$val])) {
                $areaResult[$val] = [$val, null];
            }
        }

        foreach ($montha as $key=>$value){
            if (is_null($areaResult[$value][1]) && $key > 0){
                $areaResult[$value][1] = isset($areaResult[$montha[$key-1]][1]) ? $areaResult[$montha[$key-1]][1] : null;
            }
            if (is_null($estateResult[$value][1]) && $key > 0){
                $estateResult[$value][1] = isset($estateResult[$montha[$key-1]][1]) ? $estateResult[$montha[$key-1]][1] :  null;
            }
        }

        foreach ($montha as $key=>$value){
            if (is_null($areaResult[$value][1]) && is_null($estateResult[$value][1])){
                unset($areaResult[$value]);
                unset($estateResult[$value]);
                unset($monthb[$key]);
            }
        }

        $monthb = array_values($monthb);
        foreach ($monthb as $values){
            $months[] = substr($values, 5);
        }

        $estateResult = array_values($estateResult);
        $estate['name'] = $estateBasicInfo['estateName']."(".$propertyTypeName.")";
        $estate['data'] = $estateResult;


        $areaResult = array_values($areaResult);
        $area['name'] = $areaInfo->estateAreaName."(".$propertyTypeName.")";
        $area['data'] = $areaResult;

        //title标题
        $title = "房价走势图(元/㎡)";
        $monthNum = count($monthb);
        if ($monthNum > 1) {
            $title = $monthb [0] . "-" . $monthb [$monthNum - 1] . $title;
        } elseif ($monthNum == 1) {
            $title = $monthb[0] . $title;
        }

        //获得本月均价
        $estatePrice = isset($estateResult[$monthNum-1]) ? $estateResult[$monthNum-1][1] : null;


        //楼盘价格环比上月增长
        $monthNum = count($estateResult);
        if ($monthNum > 1) {
            $thisMonth = $estateResult[$monthNum - 1][1] ? intval($estateResult[$monthNum - 1][1]) : 0; //本月价格
            $lastMonth = $estateResult[$monthNum - 2][1] ? intval($estateResult[$monthNum - 2][1]) : 0; //上月价格
            if ($thisMonth == 0 or $lastMonth == 0) {
                $rate = null;
            } else {
                $rateResult = (($thisMonth - $lastMonth) / $lastMonth) * 100;
                $rate = sprintf("%.2f", $rateResult);
            }
        } else {
            $rate = null;
        }
        
        return [
            'title' => $title,
            'series' => [$estate, $area],
            'month' => $monthb,
            'month_s' => $months,
            'rate' => $rate,
            'estatePrice' => $estatePrice,
        ];
    }

    /**
     * 获取用户评论
     * <liangshimao>
     */
    public static function getUserReview($estateID, $pageSize, $page) {
        $list = Review::selectRecord($estateID, $pageSize, $page, 'userID,totalReviewScore,comments,purchaseIntentionID,inDate,userName,isAnonymous');
        if (empty($list['data'])) {
            return false;
        }
        $res = [];
        foreach ($list['data'] as $r) {
            $info = UserInfo::selectOneRecord($r['userID'], 'avatarImageName');
            if (empty($info)) {
                $info = ['avatarImageName' => ''];
            }
            //用户头像添加主域名
            $info['avatarImageName'] = empty($info['avatarImageName'])?SUBDOMAIN.'/img/pic_head.png':IMG_DOMAIN . $info['avatarImageName'];
            //获取用户购买意向文字
            $p['purchaseIntention'] = PurchaseIntention::getName($r['purchaseIntentionID']);
            //评价时间封装为“2分钟前,2天前” 的格式
            $r['inDate'] = Tools::time_today($r['inDate']);
            $r['totalReviewScore'] = number_format(round($r['totalReviewScore'],1),1);
            if($r['isAnonymous'] == '0'){
                if(preg_match('/^1[0-9]{10}$/',$r['userName'])){
                    $r['nickName'] = substr_replace($r['userName'],'****',3,4);
                }else{
                    $r['nickName'] = $r['userName'];
                }
            }else{
                $r['nickName'] = '匿名用户';
            }
            //如果是游客或匿名用户就给个默认头像
            if($r['nickName']=='游客' || $r['nickName']=='匿名用户'){
                $info['avatarImageName'] = SUBDOMAIN.'/img/pic_head.png';
            }
            unset($r['purchaseIntentionID'],$r['isAnonymous'],$r['userName']);
            //拼接用户点评信息和用户基本信息
            $res[] = ArrayHelper::merge($p, ArrayHelper::merge($r, $info));
        }
        return [
            'results' => $res,
            'curPage' => $list['curPage'],
            'pageCount' => $list['pageCount'],
            'count' => $list['count'],
        ];
    }

    /**
     * 插入用户评论
     * <wangluohua>
     */
    public static function inUserReview($data) {
        $data['userName'] = $data['nickname'];
        $data['totalReviewScore'] = ($data['areaReviewScore'] + $data['trafficReviewScore'] + $data['houseTypeReviewScore'] + $data['facilityReviewScore'] + $data['environmentReviewScore'])/5;
        $data['isActive'] =1;
        $data['inDate'] = date("Y-m-d H:i:s");
        return Review::insertRecord($data);
    }

    /**
     * 获取楼盘相册
     * <liangshimao>
     */
    public static function getAlbum($estateID, $propertyTypeID) {
        $list = Album::selectRecord($estateID, $propertyTypeID, 'albumName,albumID');
        if (empty($list)) {
            return false;
        }
        foreach ($list as $key => $value) {
            $photo = AlbumImage::selectRecord($value['albumID'], 'imageName,id');

            if (count($photo) > 0) {
                //$photoList = ArrayHelper::getColumn($photo, 'imageName');
                //给楼盘相册添加到完整地址
                $photoList = [];
                foreach ($photo as $k => $v) {
                    $photoList[$k]['imageName'] = IMG_DOMAIN . $v['imageName'];
                    $photoList[$k]['id'] = $value['albumID'].'-'.$v['id']; //如果用id,可能会存在同一张图片存在不同相册中的问题,故修改为如此.
                }

                $list[$key]['photo'] = $photoList;
                $list[$key]['count'] = count($photoList);
            } else {
                unset($list[$key]);
            }
        }

        return array_values($list);
    }

    /**
     * 获取楼盘首页图片数组 从每个相册中取一张图片
     * <liangshimao>
     */
    public static function getPicture($estateID, $propertyTypeID) {
        $album = Album::selectRecord($estateID, $propertyTypeID, 'albumName,albumID');
        if (empty($album)) {
            return false;
        }
        $arr = [];
        foreach ($album as $key => $value) {
            $r = AlbumImage::selectOneRecord($value['albumID'], 'imageName,id');
            if (!empty($r)) {
                $arr[] = ['albumName' => $value['albumName'], 'imageName' => IMG_DOMAIN . $r['imageName'],'id'=>$value['albumID'].'-'.$r['id']];
            }
        }
        return $arr;
    }

    /**
     * 获取同区域楼盘推荐
     * <liangshimao>
     */
    public static function getRecommend($estateID, $estateAreaId, $companyCode, $propertyTypeID, $pageSize) {

        $core = self::getCoreName($propertyTypeID);
        $searchModel = new SearchForm();
        $searchModel->estateArea = $estateAreaId;
        $list = $searchModel->searchs(1, $pageSize + 1, $companyCode, $core, 'coverImageName,estateName,id,propertyTypeChildName,lastAveragePrice,totalReviewScore,isJudge,lastRoomMinPrice,undetermined,areaTxt');
        $res = [];
        if (!empty($list['docs'])) {
            //避免推荐楼盘中出现自己，需要筛选一下
            foreach ($list['docs'] as $key => $value) {
                if ($value['id'] != $estateID) {
                    $value['coverImageName'] = IMG_DOMAIN . $value['coverImageName'];
                    $res[] = $value;
                }
                if (count($res) >= $pageSize) {
                    break;
                }
            }
            return $res;
        }

        return false;
    }

    /**
     * 根据业态获取获取solr的名称
     * @param $propertyTypeID
     * @return string
     */
    public static function getCoreName($propertyTypeID) {
        if ($propertyTypeID == ESTATE_PROPERTY_TYPE) {
            return SOLR_ZHUZHAI;
        } elseif ($propertyTypeID == BUSINESS_PROERTY_TYPE) {
            return SOLR_STORE;
        } else {
            return SOLR_OFFICE;
        }
    }

    public static function getFieldByProperty($propertyTypeID) {
        if ($propertyTypeID == ESTATE_PROPERTY_TYPE) {
            return self::$zhuzhaiBasicField . self::$basic;
        } elseif ($propertyTypeID == BUSINESS_PROERTY_TYPE) {
            return self::$storeBasicField . self::$basic;
        } else {
            return self::$officeBasicField . self::$basic;
        }
    }

    /**
     * 获取用户购买意向
     * @author <wangluohua>
     */
    public static function getPurchaseIntention()
    {
        $fields = 'purchaseIntentionID,purchaseIntentionDescription';
        $condition = [];
        return PurchaseIntention::getSelectRecord($fields, $condition);
    }

    /**
     * 设置用户浏览记录
     */
    public static function setViewLog($estateID,$propertyTypeID,$userID,$clientType,$companyCode)
    {
        PageViewLog::insertRecord($estateID,$clientType,$companyCode);
        if(!empty($userID)){
            ViewHistory::insertRecord($userID, $estateID, $propertyTypeID);
        }
        return true;
    }

}
