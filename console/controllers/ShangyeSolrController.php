<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-27
 * Time: 下午1:48
 */

namespace console\controllers;


use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class ShangyeSolrController extends Controller
{
    protected $db;
    protected $tagColorArr;

    public function init() {
        $this->db = Yii::$app->db;
        $this->tagColorArr=[
            ['bg'=>'green','color'=>'white'],
            ['bg'=>'#34ddd','color'=>'pink'],
            ['bg'=>'#ccc','color'=>'#red'],
            ['bg'=>'#ccc','color'=>'#blue'],
        ];
        parent::init();
    }

    public function actionIndex() {
        $start = microtime(true);
        $s = 100;
        $countSql = "SELECT bp.estateID as estateID,bp.propertyTypeID as propertyTypeID FROM Estate_BasicInfoPropertyType as bp INNER JOIN Estate_BasicInfo as b on bp.estateID=b.estateID where bp.propertyTypeID in (5,6,7,8,9) and b.status=2";
        $count = $this->db->createCommand($countSql)->queryAll();
        $result = [];
        foreach ($count as $key=>$val){
            $result[$val['estateID']][] = $val['propertyTypeID'];
        }
        foreach($result as $key=>$value){
            $sql = "select b.estateID as id,b.estateName,b.estateAliasName,b.estateDeveloperName,b.estateAddress,b.coverImageName,b.estateLongitude,b.estateLatitude,b.estateAreaID,b.ringRoadID as ringRoad,
                e.userWishlistCount,e.totalSeeHouseCount,e.totalReviewCount,e.totalReviewScore,
                o.officeID,o.promotionInfo,o.lastRoomMinPrice,o.title,o.lastAveragePrice,o.salesPhoneNumber,concat(o.projectDescription,o.projectSupportingDescription) as projectEnvironmentDescription,o.propertyCompany,o.salesOfficeAddress,o.propertyPrice,
                o.projectProgress,o.propertyRightLimit,o.greenRate,o.totalBuildingArea,o.planedCarport,o.projectDescription,o.totalFloorArea,o.floorHeight,o.buildingFloorSummary,
                o.projectSupportingDescription,o.saleStatus,o.roomStatus,
                company.companyCode,company.companyName 
                from Estate_BasicInfo as b 
                left join Estate_ExtendInfo as e on b.estateID=e.estateID 
                left join Estate_Office as o on o.estateID=b.estateID 
                left join CP_CompanyInfo as company on company.companyCode=b.companyCode 
                where b.estateID={$key}";

            $data = $this->db->createCommand($sql)->queryOne();

            //获取城市
            $data['estateArea'] = $this->_getArea($data['estateAreaID']);
            //获取交通
            $data['estateTrack'] = $this->_getEstateTrack($data['id']);
            //获取户型（一室 二室）
            $data['estateHouseType'] = $this->_getHoustType($data['id']);
            //获取tag 特色
            $data['tag'] = $this->_getTga($data['id']);
            //获取类型（物业类型ID）
            $data['propertyType'] = $value;
            //在售、售罄、期房、现房、尾房
            $data['other'] = explode(',', $this->_getSaleStatus($data['saleStatus']) . ','. $this->_getRoomStatus($data['roomStatus']));
            //获取物业类型一级ID
            $data['propertyTypeParentID'] = $this->_getPropertyTypeParentId($data['propertyType']);
            //获取物业类型名称
            $data['propertyTypeName'] = $this->_getPropertyTypeName($data['propertyType']);
            //区域名称
            $data['areaTxt'] = $this->_getArea($data['estateAreaID'],2);
            //获取楼盘图片信息,8表示写字楼
            $data['estatePicture'] = $this->_getEstatePicture($data['id'],OFFICE_PROERTY_TYPE);
            //获取预售许可证list,8表示住宅
            $data['preSalePermitList'] = $this->_getpreSalePermitList($data['id'],OFFICE_PROERTY_TYPE);
            //获取预售证最近的一条
            $data['preSalePermit'] = $this->_getLastData($data['preSalePermitList'],'preSalePermitCode');
            //获取合作银行
            $data['bankName'] = $this->_getBank($data['officeID']);
            //获取明星经纪人
            $data['superAgency'] = $this->_getSuperAgency($data['id']);
            //获取写字楼特色标签,显示用的,拼接成json形式
            $data['tagList'] = $this->_getTagList($data['officeID']);
            //获取开盘时间列表
            $data['openingDateList'] = $this->_getOpenList($data['id'],OFFICE_PROERTY_TYPE);
            //获取最新开盘时间
            $data['openingDate'] = $this->_getLastData($data['openingDateList'],'openingDate');
            //获取入住时间列表
            $data['closingDateList'] = $this->_getCloseList($data['id'],OFFICE_PROERTY_TYPE);
            //获取最新入住时间
            $data['closingDate'] = $this->_getLastData($data['closingDateList'],'closingDate');
            //获取主力户型list的json
            $data['estateHouseTypeList'] = $this->_getHoustTypeList($data['id']);
            //获取商铺和写字楼的所有标签json
            $data['tagListAll'] = $this->_getTagListAll($data['tag']);
            //获取楼盘均价(查询用)
            $data['lastAveragePrice'] = $this->_getAveragePrice($data['id']);
            unset($data['estateAreaID'],$data['officeID'],$data['saleStatus'],$data['roomStatus']);

            $res[] = $data;

        };
        $c = count($res);
        $r = Yii::$app->solr->add($res,SOLR_SHANGYE);

        var_dump($r);
        $etime=microtime(true);//获取程序执行结束的时间

        $total = $etime - $start;
        echo "\n 共{$c}条数据:[页面执行时间：{$total} ]秒 \n";exit;

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

    //获取交通
    /*private function _getEstateTrack($estateID){
        $sql = " SELECT `trafficID` FROM Estate_TrafficRelation WHERE estateID = {$estateID} ";
        $trackData =  $this->db->createCommand($sql)->queryAll();
        $tracks = [];
        foreach ($trackData as $val){
            array_push($tracks, $val['trafficID']);
        }
        return array_values(array_unique($tracks));
    }*/
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
        $sql = " select `bedRoomQuantity` from Estate_HouseType WHERE estateID = {$estateID} AND auditStatus = 1 and propertyTypeID in (". BUSINESS_PROERTY_TYPE . "," . OFFICE_PROERTY_TYPE .")";
        $houstTypeData =  $this->db->createCommand($sql)->queryAll();
        $houstType = [];
        foreach ($houstTypeData as $val){
            array_push($houstType, $val['bedRoomQuantity']);
        }
        return array_values(array_unique($houstType));
    }
    //获取特色
    private function _getTga($estateID){
        $sql = "select ob.tagID from Estate_Office_BuildingTag as ob right join Estate_Office as o on o.officeID=ob.officeID where o.estateID = {$estateID}";
        $sql2 = "select ob.tagID from Estate_Store_BuildingTag as ob right join Estate_Store as s on s.storeID=ob.storeID where s.estateID = {$estateID}";
        $tagData =  $this->db->createCommand($sql)->queryAll();
        $tagData2 =  $this->db->createCommand($sql2)->queryAll();
        $tags = [];
        foreach ($tagData as $val){
            array_push($tags, $val['tagID']);
        }
        foreach ($tagData2 as $val){
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
    private function _getBank($officeID)
    {
        if(!$officeID){
            return "";
        }
        $sql = "select bankName from Estate_Office_Bank where officeID=".$officeID;
        $res = $this->db->createCommand($sql)->queryAll();
        if(empty($res)){
            return "";
        }
        return join('、',ArrayHelper::getColumn($res,'bankName'));
    }

    //获取特色标签列表
    private function _getTagList($residentialID)
    {
        if(!$residentialID){
            return "";
        }
        $sql = 'select t.tagName from Estate_Office_BuildingTag as rt left join Estate_BuildingTagInfo as t on rt.tagID=t.tagID where rt.officeID=' . $residentialID .' order by t.sortIndex desc';
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
    private function _getHoustTypeList($estateID)
    {
        $sql = "select bedRoomQuantity,livingRoomQuantity,restRoomQuantity,CookRoomQuantity,buildArea from Estate_HouseType where estateID=".$estateID." and isMajor=1 and auditStatus=1 and propertyTypeID in (". BUSINESS_PROERTY_TYPE . "," . OFFICE_PROERTY_TYPE .") order by sortIndex asc";
        $res = $this->db->createCommand($sql)->queryAll();
        if(empty($res)){
            return "";
        }
        return Json::encode($res);
    }
    //根据taglist数组获取对应名称和样式
    private function _getTagListAll($tagList)
    {

        $str = join(',',$tagList);
        if(empty($str)){
            return "";
        }
        $sql = "select tagName from Estate_BuildingTagInfo where tagID in (".trim($str,',').") order by sortIndex desc";
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

    //获取楼盘价格(商铺和写字楼)
    private function _getAveragePrice($estateID)
    {
        
    }
}