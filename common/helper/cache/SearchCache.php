<?php
/**
 * 搜索条件缓存
 * @Author: <lixiaobin>
 * @Date: 17-3-22
 */

namespace common\helper\cache;


use common\logic\PropertyLogic;
use Yii;
use common\logic\BrtLogic;
use common\logic\TagLogic;
use common\logic\AreaLogic;
use common\logic\RingLogic;
use common\helper\BaseCache;
use common\logic\TrackLogic;
use common\logic\EstatePriceLogic;
use common\models\estate\config\Property;

class SearchCache{

    const REDIS_AREAS  = 'areas';
    const REDIS_TRACKS = 'tracks';
    const REDIS_TAGS   = 'tags';
    const REDIS_TYPES  = 'types';
    const REDIS_RINGS  = 'rings';
    const REDIS_BRTS   = 'brts';
    const REDIS_PRICES = 'prices';

    /**
     * 根据当前城市获取缓存中的区域及商圈信息 pc
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     * @Parans: int $companyCode 城市编码
     * @Return array
     */
    public static function getAreaCache($companyCode){
        if(is_numeric($companyCode)){
            //判断redis是否存在城市区域信息
            $key = self::REDIS_AREAS.':'.$companyCode;
            $areasArr = BaseCache::get($key);
            if($areasArr === false){
                $areasArr = AreaLogic::selectAreaLogic($companyCode,'estateAreaID,estateAreaName,parentID');
                if(empty($areasArr)) return false;
                BaseCache::set($key, $areasArr, 24*3600);
            }
            return $areasArr;
        }
        return false;
    }

    /**
     * 根据当前城市获取缓存中的地铁交通信息 pc
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     * @Parans: int $companyCode 城市编码
     * @Return Array
     */
    public static function getTrackCache($companyCode){
        if(is_numeric($companyCode)){
            //判断redis是否存在城市区域信息
            $key = self::REDIS_TRACKS.':'.$companyCode;
            $tracskArr = BaseCache::get($key);
            if($tracskArr === false){
                $tracskArr = TrackLogic::selectTrackLogic($companyCode, 'trafficID,trafficName,parentID');
                if(empty($tracskArr)) return false;
                BaseCache::set($key, $tracskArr, 24*3600);
            }
            return $tracskArr;
        }
        return false;
    }

    /**
     * 获取户型 配置文件中配置
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     * @Return Array
    */
    public static function getHouseTypeCache(){
        return Yii::$app->params['houseType'];
    }

    /**
     * 根据城市编码和业态查询价格并且缓存起来
     * @Params: Int $companyCode 城市编码
     * @Params: Int  $propertyTypeId 业态id
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-03
    */
    public static function getPriceCache($companyCode,$propertyTypeId){
        if(is_numeric($companyCode) && is_numeric($propertyTypeId)){
            $key = self::REDIS_PRICES .':'.$companyCode .'-'.$propertyTypeId;
            $pricesArray = BaseCache::get($key);
            if($pricesArray === false){
                $pricesArr = EstatePriceLogic::selectPriceLogic($companyCode,$propertyTypeId,'id,regionName,maxValue,minValue,propertyTypeID');
                if(empty($pricesArr)) return false;
                foreach ($pricesArr as $val){
                    $pricesArray[$val['id']] = $val;
                }
                BaseCache::set($key, $pricesArray);
            }
            return $pricesArray;
        }
        return false;
    }

    /**
     * 获取当前城市下缓存中的特色标签 pc
     * @Params: int $companyCord 城市编码
     * @Params: int $propertyTypeID 业态ID
     * @Return: Array
     * @Author: <lixiaobin>
     * @Data: 2017-03-22
    */
    public static function getTagInfoCache($companyCode,$type){
        $key = self::REDIS_TAGS.':'.$companyCode.'-'.$type;
        $tagsArray = BaseCache::get($key);
        if($tagsArray === false){
            $tagsArr = TagLogic::selectTagLogic($companyCode,$type, 'tagID,tagName');
            if(empty($tagsArr)) return false;
            foreach ($tagsArr as $val){
                $tagsArray[$val['tagID']] = $val;
            }
            BaseCache::set($key, $tagsArray);
        }
        return $tagsArray;
    }

    /**
     * 获取普通住宅类型标签 pc
     * @Parents: $parendID 住宅楼盘 或者是 商业楼盘父类ID
     * @Return:  Array
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
    */
    public static function getPropertyCache($parentID){
        $key = self::REDIS_TYPES.':'.$parentID;
        $typesArray = BaseCache::get($key);
        if($typesArray === false){
            $typesArr = PropertyLogic::selectProperLogic($parentID, 'propertyTypeID,propertyTypeName');
            if(empty($typesArr)) return false;
            foreach($typesArr as $val){
                $typesArray[$val['propertyTypeID']] = $val;
            }
            BaseCache::set($key, $typesArray);
        }
        return $typesArray;
    }

    /**
     * 获取开通城市商业导航的业态
     * @Parents: $parendID 住宅楼盘 或者是 商业楼盘父类ID
     * @Params: $companyCode 公司编码
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-22
     */
    public static function getPropertyParentCache($parentID = 0, $companyCode){
        $key = self::REDIS_TYPES.':'.$companyCode .':'.$parentID;
        $propertyParentArray = BaseCache::get($key);
        if($propertyParentArray === false){
            $propertyParentArray = PropertyLogic::selectProperParentLogic($parentID, $companyCode, 'propertyTypeID,propertyTypeName');
            if(empty($propertyParentArray)) return false;
            BaseCache::set($key, $propertyParentArray);
        }
        return $propertyParentArray;
    }

    /**
     * 获取当前城市下缓存中的环线 pc
     * @Params: int $companyCord 城市编码
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     */
    public static function getRingCache($companyCord){
        $key = self::REDIS_RINGS.':'.$companyCord;
        $ringsArray = BaseCache::get($key);
        if($ringsArray === false){
            $ringsArr = RingLogic::selectRingLogic($companyCord,'ringRoadID,ringRoadName');
            if(empty($ringsArr)) return false;
            foreach($ringsArr as $val){
                $ringsArray[$val['ringRoadID']] = $val;
            }
            BaseCache::set($key, $ringsArray);
        }
        return $ringsArray;
    }

    /**
     * 获取其他条件 配置文件中配置
     * @Return Array
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     */
    public static function getOtherCache(){
        return Yii::$app->params['other'];
    }

    /**
     * 获取排序条件 配置文件中配置
     * @Return Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-16
     */
    public static function getOrderCache(){
        return Yii::$app->params['order'];
    }


    /**
     * 获取当前城市下的单个价格，返回给搜索使用
     * @Author: <lixiaobin>
     * @date: 2017-03-25
     * @params: int $companyCode 公司编码
     * @params: int $type
     * @params: int $index 价格ID
     * @Return: string
    */
    public static function getOnePrice($companyCode, $propertyType, $index){
        $key = self::REDIS_PRICES .':'.$companyCode .'-'.$propertyType;
        $pricesArr = BaseCache::get($key);
        if($pricesArr === false){
            $pricesArr = EstatePriceLogic::selectPriceLogic($companyCode,$propertyType,'id,regionName,maxValue,minValue,propertyTypeID');
            if(empty($pricesArr)) return false;
            BaseCache::set($key, $pricesArr);
        }
        foreach ($pricesArr as $val){
            if($val['id'] == $index){
                $regionName['regionName'] = $val['regionName'];
                $regionName['minValue'] = $val['minValue'];
                $regionName['maxValue'] = $val['maxValue'];
                break;
            }
        }
        return $regionName;

    }

    /**
     * 根据城市编码和区域商圈id 获取区域及商圈名称
     * @Params: int $companyCode 城市编码
     * @Params: int $estateAreaId 区域或商圈id
     * @Return: String 如：海淀区-中关村
     * @Author: <lixiaobin>
     * @Date: 2017-03-27
    */
    public static function getAreaName($companyCode, $estateAreaId){
        return AreaLogic::selectAreaNameLogic($companyCode, $estateAreaId,'estateAreaID,estateAreaName,parentID');
    }

    /**
     * 根据城市编码和地铁线路（快速公交）id或站点id获取线路及站点名称
     * @Params: int $companyCode 城市编码
     * @Params: int $trafficId 交通id
     * @Return: String 如：1号线-国贸站
     * @Author: <lixiaobin>
     * @Date: 2017-03-27
     */
    public static function getTrackName($companyCode, $trafficId){
        return TrackLogic::selectTrackNameLogic($companyCode, $trafficId,'trafficID,trafficName,parentID');
    }

    /**
     * 获取业态名称
     * @Params: int $propretyId 业态ID
     * @Return: String
     * @Author: <lixiaobin>
     * @Date: 2017-03-27
     */
    public static function getPropertyName($propretyId){
        return Property::getPropertyTypeName($propretyId);
    }

}