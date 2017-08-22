<?php

/*
 * @desc   
 * @author <liangpingzheng>
 * @date Mar 22, 2017 3:38:02 PM
 */

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class SolrController extends Controller {

    protected $db;
    protected $tagColorArr;

    public function init() {
        $this->db = Yii::$app->db;
        $this->tagColorArr=[
            ['bg'=>'#ccc','color'=>'#ff0000'],
            ['bg'=>'#34ddd','color'=>'#00ff00'],
            ['bg'=>'#ccc','color'=>'#ff0000'],
            ['bg'=>'#ccc','color'=>'#ff0000'],
        ];
        parent::init();
    }

    public function actionIndex() {
        $zstime = microtime(true);
        $countSql = " SELECT COUNT(1) as num FROM Estate_ResidentialBuilding r,Estate_BasicInfo b WHERE r.estateID = b.estateID AND b.status = 2 ";

        $count = $this->db->createCommand($countSql)->queryOne();
        $pageSize = 500;
        for( $i= 0; $i< $count['num']/$pageSize ;$i++){
            $stime=microtime(true);
            $start = $i * $pageSize;

            $sqltest = "select r.residentialBuildingID,b.estateID as id,b.estateName,b.estateAliasName,b.estateDeveloperName,b.estateAddress,b.coverImageName,b.estateLongitude,b.estateLatitude,b.estateAreaID,b.ringRoadID as ringRoad,
                e.userWishlistCount,e.totalSeeHouseCount,e.totalReviewCount,e.totalReviewScore,
                r.promotionInfo,r.lastRoomMinPrice,r.title,r.lastAveragePrice,r.salesPhoneNumber,concat(r.projectDescription,r.planedCommunityDescription) as projectEnvironmentDescription,r.propertyCompany,r.salesOfficeAddress,r.propertyPrice,r.loanType,
                r.projectProgress,r.propertyRightLimit,r.greenRate,r.volumeRate,r.totalBuildingArea,r.planedCarport,r.projectDescription,r.totalFloorArea,r.floorHeight,r.decorationSummary,r.planedHousehold,r.buildingFloorSummary,r.liftFamilyDescription,
                r.propertyAdvantage,r.propertyCompanyDescription,r.planedCommunityDescription,r.saleStatus,r.roomStatus,
                company.companyCode,company.companyName 
                from Estate_BasicInfo as b 
                left join Estate_ExtendInfo as e on b.estateID=e.estateID 
                right join Estate_ResidentialBuilding as r  on b.estateID=r.estateID 
                left join CP_CompanyInfo as company on company.companyCode=b.companyCode 
                where b.status=2 limit {$start},{$pageSize}";

            $data = $this->db->createCommand($sqltest)->queryAll();
            foreach ($data as $key=>$val){
                //获取城市
                $data[$key]['estateArea'] = $this->_getArea($val['estateAreaID']);
                //获取区域id
                $data[$key]['estateAreaId'] = $this->_getAreaParentID($val['estateAreaID']);
                //获取佳通
                $data[$key]['estateTrack'] = $this->_getEstateTrack($val['id']);
                //获取户型（一室 二室）
                $data[$key]['estateHouseType'] = $this->_getHoustType($val['id']);
                //获取tag 特色
                $data[$key]['tag'] = $this->_getTga($val['residentialBuildingID']);
                //获取类型（物业类型ID）
                $data[$key]['propertyType'] = $this->_getPropertyType($val['id']);
                //在售、售罄、期房、现房、尾房
                $data[$key]['other'] = explode(',', $this->_getSaleStatus($val['saleStatus']) . ','. $this->_getRoomStatus($val['roomStatus']));
                //获取物业类型一级ID
                $data[$key]['propertyTypeParentID'] = $this->_getPropertyTypeParentId($data[$key]['propertyType']);
                //获取物业类型名称
                $data[$key]['propertyTypeName'] = $this->_getPropertyTypeName($data[$key]['propertyType']);
                //区域名称
                $data[$key]['areaTxt'] = $this->_getArea($val['estateAreaID'],2);
                //获取楼盘图片信息
                $data[$key]['estatePicture'] = $this->_getEstatePicture($val['id'],ESTATE_PROPERTY_TYPE);
                //获取预售许可证list,1表示住宅
                $data[$key]['preSalePermitList'] = $this->_getpreSalePermitList($val['id'],ESTATE_PROPERTY_TYPE);
                //获取预售证最近的一条
                $data[$key]['preSalePermit'] = $this->_getLastData($data[$key]['preSalePermitList'],'preSalePermitCode');
                //获取合作银行
                $data[$key]['bankName'] = $this->_getBank($val['id']);
                //获取建筑类型
                $data[$key]['BuildingType'] = $this->_getBuildingType($val['residentialBuildingID']);
                //获取明星经纪人
                $data[$key]['superAgency'] = $this->_getSuperAgency($val['id']);
                //获取特色标签,显示用的,拼接成json形式
                $data[$key]['tagList'] = $this->_getTagList($val['id']);
                //获取开盘时间列表
                $data[$key]['openingDateList'] = $this->_getOpenList($val['id'],ESTATE_PROPERTY_TYPE);
                //获取最新开盘时间
                $data[$key]['openingDate'] = $this->_getLastData($data[$key]['openingDateList'],'openingDate');
                //获取入住时间列表
                $data[$key]['closingDateList'] = $this->_getCloseList($val['id'],ESTATE_PROPERTY_TYPE);
                //获取最新入住时间
                $data[$key]['closingDate'] = $this->_getLastData($data[$key]['closingDateList'],'closingDate');
                //获取主力户型list的json
                $data[$key]['estateHouseTypeList'] = $this->_getHoustTypeList($val['id'],ESTATE_PROPERTY_TYPE);

                unset($data[$key]['estateAreaID'],$data[$key]['residentialBuildingID'],$data[$key]['saleStatus'],$data[$key]['roomStatus']);

            };
            Yii::$app->solr->add($data,SOLR_ZHUZHAI);
            $etime=microtime(true);//获取程序执行结束的时间
            $total=$etime-$stime;   //计算差值

            //echo "\n {$i}:[页面执行时间：{$total} ]秒 \n";
        }

        $zetime=microtime(true);//获取程序执行结束的时间
        $ztotal=$zetime-$zstime;   //计算差值
        echo "\n :[总运行时长：{$ztotal} ]秒 \n";

    }

    //获取区县商圈的名称
    private function _getArea($estateArea,$type = 1){
        $sql = " SELECT `estateAreaID`,`estateAreaName`,`parentID` FROM Estate_AreaInfo WHERE estateAreaID = {$estateArea}";
        $areaData = $this->db->createCommand($sql)->queryOne();
        if($type == 1){
            $prenteData = '';
            $arr = [];
            if(!empty($areaData['parentID'])){
                $prenteSql = " SELECT `estateAreaID`,`estateAreaName`,`parentID` FROM Estate_AreaInfo WHERE estateAreaID = {$areaData['parentID']}";
                $prenteData = $this->db->createCommand($prenteSql)->queryOne();
            }
            if(!empty($prenteData)){
                array_push($arr, $prenteData['estateAreaID']);
            }
            array_push($arr, $areaData['estateAreaID']);
            return $arr;
        }else{
            $prenteData = '';
            $arr = [];
            if(!empty($areaData['parentID'])){
                $prenteSql = " SELECT `estateAreaID`,`estateAreaName`,`parentID` FROM Estate_AreaInfo WHERE estateAreaID = {$areaData['parentID']}";
                $prenteData = $this->db->createCommand($prenteSql)->queryOne();
            }
            if(!empty($prenteData)){
                array_push($arr, $prenteData['estateAreaName']);
            }
            array_push($arr, $areaData['estateAreaName']);
            return $arr;
        }
    }
    //获取父类estateAreaID
    private function _getAreaParentID($estateArea){
        $sql = " SELECT `estateAreaID`,`parentID` FROM Estate_AreaInfo WHERE estateAreaID = {$estateArea}";
        $areaData = $this->db->createCommand($sql)->queryOne();
        if($areaData['parentID'] == 0){
            return $areaData['estateAreaID'];
        }else{
            $sql = " SELECT `estateAreaID`,`parentID` FROM Estate_AreaInfo WHERE estateAreaID = {$areaData['parentID']}";
            $parentData = $this->db->createCommand($sql)->queryOne();
            return $parentData['estateAreaID'];
        }
    }

    //获取交通
    private function _getEstateTrack($estateID){
        $sql = " SELECT `trafficID` FROM Estate_TrafficRelation WHERE estateID = {$estateID} ";
        $trackData =  Yii::$app->db->createCommand($sql)->queryAll();
        if(empty($trackData)) return '';
        $tracks = [];
        foreach ($trackData as $val){
            //获取交通父类ＩＤ
            $tracks[] = $this->_getTrackParentId($val['trafficID']);
            //array_push($tracks, $val['trafficID']);
        };
        foreach($tracks as $key => $val) {
            foreach($val as $value) {
                $new_arr[] = $value;
            }
        }
        return $new_arr;
    }

    private function _getTrackParentId($trafficID){
        $sql = " SELECT `trafficID`,`parentID`,`trafficLevelPath` FROM Estate_TrafficInfo WHERE `trafficID` = {$trafficID}";
        $trackParent = Yii::$app->db->createCommand($sql)->queryOne();
        if(empty($trackParent)) return '';
        $parentArr = [];
        if($trackParent['parentID'] != 0){
            $parentArr = explode('/', trim($trackParent['trafficLevelPath'],'/'));
        }else{
            array_push($parentArr, $trackParent['trafficID']);
        }
        return $parentArr;
    }

    //获取户型
    private function _getHoustType($estateID){
        $sql = " select `bedRoomQuantity` from Estate_HouseType WHERE `estateID` = {$estateID} AND `propertyTypeID` = ".ESTATE_PROPERTY_TYPE." AND auditStatus = 1";
        $houstTypeData =  $this->db->createCommand($sql)->queryAll();
        $houstType = [];
        foreach ($houstTypeData as $val){
            array_push($houstType, $val['bedRoomQuantity']);
        }
        return array_values(array_unique($houstType));

    }
    //获取特色
    private function _getTga($residentialBuildingID){
        $sql = "select `tagID` from Estate_ResidentialBuilding_BuildingTag where residentialBuildingID = {$residentialBuildingID}";
        $tagData =  $this->db->createCommand($sql)->queryAll();
        $tags = [];
        foreach ($tagData as $val){
            array_push($tags, $val['tagID']);
        }
        return array_values(array_unique($tags));
    }
    //获取类型
    private function _getPropertyType($estateID){
        $sql = "select `propertyTypeID` from Estate_BasicInfoPropertyType where estateID = {$estateID}";
        $propertyData =  $this->db->createCommand($sql)->queryAll();
        $propertys = [];
        foreach ($propertyData as $val){
            array_push($propertys, $val['propertyTypeID']);
        }
        return array_values(array_unique($propertys));
    }

    //获取其他（销售状态 0-待售 1-在售 2-售罄）
    private function _getSaleStatus($saleStatus){
        $other = Yii::$app->params['other'];
        switch ($saleStatus){
            case 0:
                $saleStatus = 3;
                break;
            case 1:
                $saleStatus = 4;
                break;
            case 2:
                $saleStatus = 5;
                break;
        }
        return $other[$saleStatus - 1]['id'];
    }

    //获取其他 （房源状态 0-期房 1-现房 2-尾房）
    private function _getRoomStatus($roomStatus){
        $other = Yii::$app->params['other'];
        switch ($roomStatus){
            case 0:
                $roomStatus = 6;
                break;
            case 1:
                $roomStatus = 7;
                break;
            case 2:
                $roomStatus = 8;
                break;
        }
        return $other[$roomStatus - 1]['id'];
    }

    //组合楼盘相册
    private function _getEstatePicture($estateID,$propertyTypeID)
    {
        $sql = "select albumName,albumID from Estate_Album where estateID=".$estateID." and propertyTypeID=".$propertyTypeID." order by sortIndex asc";
        $res = $this->db->createCommand($sql)->queryAll();
        if(empty($res)){
            return "";
        }
        foreach ($res as $key => $value){
            $s = "select imageName from Estate_AlbumImage where albumID=".$value['albumID']." order by sortIndex asc";
            $r = $this->db->createCommand($s)->queryOne();
            $Arr[] = ['albumName' => $value['albumName'],'imageName' => $r['imageName']];
        }

        return Json::encode($Arr);
    }

    //获取预售许可证最近信息
    private function _getLastData($json,$key)
    {
        $list = Json::decode($json);
        if(count($list) > 0){
            $one = $list[0];
            return $one[$key];
        }else{
            return '暂无信息';
        }

    }

    //获取预售许可证所有信息
    private function _getpreSalePermitList($estateID,$propertyTypeID)
    {
        $sql = "select preSalePermitCode,DATE_FORMAT(PreSaleDate,'%Y-%m-%d') as PreSaleDate,preSaleScope from Estate_PreSalePermit where estateID=".$estateID." and propertyTypeID=".$propertyTypeID." order by PreSaleDate desc";
        $res = $this->db->createCommand($sql)->queryAll();
        if(empty($res)){
            return "";
        }
        return Json::encode($res);
    }

    //获取推荐经纪人信息
    private function _getSuperAgency($estateID)
    {
        $sql = "select a.agencyName,a.avatarsImageName,a.agencyCellphone from External_AgencyInfo as a right join External_AgencyEstateRelation as ae on ae.agencyID=a.agencyID where ae.estateID=".$estateID." and ae.isSuperStar=1";
        $res = $this->db->createCommand($sql)->queryOne();
        if(empty($res)){
            return "";
        }
        return Json::encode($res);
    }

    //合作银行
    private function _getBank($residentialID)
    {
        $sql = "select bankName from Estate_ResidentialBuilding_Bank where residentialBuildingID=".$residentialID;
        $res = $this->db->createCommand($sql)->queryAll();
        if(empty($res)){
            return "";
        }
        return join('、',ArrayHelper::getColumn($res,'bankName'));
    }
    //建筑类型
    private function _getBuildingType($residentialID)
    {
        $sql = "select buildingTypeName from Estate_ResidentialBuilding_BuildingType as rt right join Estate_BuildingTypeInfo as t on t.buildingTypeID=rt.buildingTypeID where rt.residentialBuildingID=".$residentialID ." order by t.sortIndex desc";
        $res = $this->db->createCommand($sql)->queryAll();
        if(empty($res)){
            return "";
        }
        return join('、',ArrayHelper::getColumn($res,'buildingTypeName'));
    }

    //获取特色标签列表
    private function _getTagList($residentialID)
    {
        $sql = 'select t.tagName from Estate_ResidentialBuilding_BuildingTag as rt left join Estate_BuildingTagInfo as t on rt.tagID=t.tagID where rt.residentialBuildingID=' . $residentialID .' order by t.sortIndex desc';
        $tag = $this->db->createCommand($sql)->queryAll();
        if(empty($tag)){
            return "";
        }
        //为标签添加上背景颜色和字体颜色
        foreach ($tag as $k=>$v){
            if($k <=3){
                $tag[$k]['backColor'] = $this->tagColorArr[$k]['bg'];
                $tag[$k]['fontColor'] = $this->tagColorArr[$k]['color'];
            }else{
                $tag[$k]['backColor'] = $this->tagColorArr[3]['bg'];
                $tag[$k]['fontColor'] = $this->tagColorArr[3]['color'];
            }

        }
        return Json::encode($tag);
    }

    //获取开盘信息列表
    private function _getOpenList($estateID,$propertyTypeID)
    {
        $sql = "select openingDate,openingDetail from Estate_Opening where estateID=".$estateID." and propertyTypeID=".$propertyTypeID." order by openingDate asc";
        $res = $this->db->createCommand($sql)->queryAll();
        if(empty($res)){
            return "";
        }
        return Json::encode($res);
    }

    //获取交房信息列表
    private function _getCloseList($estateID,$propertyTypeID)
    {
        $sql = "select closingDate,closingDetail from Estate_Closing where estateID=".$estateID." and propertyTypeID=".$propertyTypeID." order by closingDate asc";
        $res = $this->db->createCommand($sql)->queryAll();
        if(empty($res)){
            return "";
        }
        return Json::encode($res);
    }

    /**
     * 通过当前楼盘的物业类型获取一级类 用于详情页切换（住宅，商业，写字楼）
     * @Author: <lixiaobin>
     * @date: 2017-03-27
     * @Params: array $propertyTypeIDarray 当钱楼盘所属的物业类型ID
     * @Return Array
    */
    private function _getPropertyTypeParentId($propertyTypeIDarray = []){
        if(!empty($propertyTypeIDarray) && is_array($propertyTypeIDarray)){
            $str = implode(',', $propertyTypeIDarray);
            $sql = " SELECT `propertyTypeID`,`propertyTypeName`,`parentID` FROM Estate_PropertyType WHERE `propertyTypeID` IN (".$str.") AND `isActive` = 1";
            $info = Yii::$app->db->createCommand($sql)->queryAll();
            if(empty($info) && !is_array($info)) return '';
            $parentIdArr = [];
            foreach($info as $key => $val){
                if($val['parentID'] != 0){
                    $prentSql = " SELECT `propertyTypeID` FROM Estate_PropertyType WHERE `propertyTypeID` = {$val['parentID']} AND `isActive` = 1";
                    $prenteInfo = Yii::$app->db->createCommand($prentSql)->queryOne();
                    array_push($parentIdArr,$prenteInfo['propertyTypeID']);
                }else{
                    array_push($parentIdArr,$val['propertyTypeID']);
                }
            }
            return !empty($parentIdArr) ? (array_unique($parentIdArr)) : '';
        }

        return '';
    }

    /**
     * 通过当前楼盘的物业类型获取物业名称
     * @Author: <lixiaobin>
     * @date: 2017-03-27
     * @Params: array $propertyTypeIDarray 当钱楼盘所属的物业类型ID
     * @Return String
     */
    private function _getPropertyTypeName($array = []){
        if(!empty($array) && is_array($array)){
            $str = implode(',', $array);
            $sql = " SELECT `propertyTypeID`,`propertyTypeName` FROM Estate_PropertyType WHERE `propertyTypeID` IN (".$str.") AND `isActive` = 1";
            $info = Yii::$app->db->createCommand($sql)->queryAll();
            if(empty($info) && !is_array($info)) return '';
            $PropertyTypeName = '';
            foreach($info as $val){
                $PropertyTypeName .=  $val['propertyTypeName'] .'/';
            }
            return trim($PropertyTypeName, '/');
        }
        return '';
    }

    //获取主力户型list
    private function _getHoustTypeList($estateID,$propertyTypeID)
    {
        $sql = "select bedRoomQuantity,livingRoomQuantity,restRoomQuantity,CookRoomQuantity,buildArea from Estate_HouseType where estateID=".$estateID." and isMajor=1 and auditStatus=1 and propertyTypeID=".$propertyTypeID." order by sortIndex asc";
        $res = $this->db->createCommand($sql)->queryAll();
        if(empty($res)){
            return "";
        }
        return Json::encode($res);
    }

}
