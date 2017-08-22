<?php

/**
 * 楼盘详情简要信息缓存
 * <liangshimao>
 */

namespace common\helper\cache;

use common\helper\BaseCache;
use common\logic\AgencyInfoLogic;
use common\logic\ArticleLogic;
use common\logic\CompanyLogic;
use common\logic\EstateDetailLogic;
use common\models\estate\basic\Basic;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

class EstateDetailCache {

    const REDIS_ESTATE_CODE = 'estate';

    /**
     * 获取户型
     * <liangshimao>
     */
    public static function getHouseType($estateID, $propertyTypeID) {
        $key = self::makeKey('houseType',$estateID, $propertyTypeID);
        $res = BaseCache::get($key);
        if ($res === false) {
            $houseType = EstateDetailLogic::getHouseType($estateID, $propertyTypeID);
            if (empty($houseType))
                return false;
            $houseTypeJson = Json::encode($houseType);
            BaseCache::set($key,$houseTypeJson);
            return $houseType;
        }
        return Json::decode($res);
    }

    /**
     * 获取简单户型信息（户型详情页面导航用）
     * @param $estateID
     * @param $propertyTypeID
     * @return array|bool|mixed
     */
    public static function getSimpleHouseType($estateID, $propertyTypeID) {
        $key = self::makeKey('simpleHouseType',$estateID, $propertyTypeID);
        $res = BaseCache::get($key);
        if ($res === false) {
            $houseType = EstateDetailLogic::getSimpleHouseType($estateID, $propertyTypeID);
            if (empty($houseType))
                return false;
            $houseTypeJson = Json::encode($houseType);
            BaseCache::set($key,$houseTypeJson);
            return $houseType;
        }
        return Json::decode($res);
    }

    //获取相册
    public static function getAlbum($estateID, $propertyTypeID) {
        $key = self::makeKey('album',$estateID, $propertyTypeID);
        $res = BaseCache::get($key);
        if ($res === false) {
            $album = EstateDetailLogic::getAlbum($estateID, $propertyTypeID);
            if (empty($album))
                return false;
            $albumJson = Json::encode($album);
            BaseCache::set($key,$albumJson);
            return $album;
        }
        return Json::decode($res);
    }

    //获取价格趋势
    public static function getPrice($estateID, $propertyTypeID, $pageSize = 0) {
        $key = self::makeKey('price',$estateID, $propertyTypeID);
        $res = BaseCache::get($key);
        if ($res === false) {
            if ($pageSize == 0) {
                $pageSize = CHART_PRICE_TREND_MONTH_NUM;
            }
            $params = [
                'estateID' => $estateID,
                'propertyTypeID' => $propertyTypeID,
                'pageSize' => $pageSize,
            ];

            $price = EstateDetailLogic::getPriceStatistics($params);
            if (empty($price))
                return false;
            $priceJson = Json::encode($price);
            BaseCache::set($key,$priceJson);
            return $price;
        }
        return Json::decode($res);
    }

    //获取明星经纪人信息
    public static function getSuperAgency($estateID, $propertyTypeID = '*') {
        $key = self::makeKey('superAgency',$estateID, $propertyTypeID);
        $res = BaseCache::get($key);
        if ($res === false) {
            $album = AgencyInfoLogic::getSuperAgency($estateID);
            if (empty($album))
                return false;
            $albumJson = Json::encode($album);
            BaseCache::set($key,$albumJson);
            return $album;
        }
        return Json::decode($res);
    }

    //获取楼盘图片信息
    public static function getPicture($estateID, $propertyTypeID) {
        $key = self::makeKey('picture',$estateID, $propertyTypeID);
        $res = BaseCache::get($key);
        if ($res === false) {
            $album = EstateDetailLogic::getPicture($estateID, $propertyTypeID);
            if (empty($album))
                return false;
            $albumJson = Json::encode($album);
            BaseCache::set($key,$albumJson);
            return $album;
        }
        return Json::decode($res);
    }

    //获取推荐经纪人信息
    public static function getAgency($estateID, $propertyTypeID = '*') {
        $key = self::makeKey('agency',$estateID, $propertyTypeID);
        $res = BaseCache::get($key);
        if ($res === false) {
            $album = AgencyInfoLogic::getAgency($estateID);
            if (empty($album))
                return false;
            $albumJson = Json::encode($album);
            BaseCache::set($key,$albumJson);
            return $album;
        }
        return Json::decode($res);
    }

    //获取楼盘各个模块是否有数据的数据
    public static function getEstateData($estateID,$propertyTypeID)
    {
        $key = self::makeKey('estateData',$estateID, $propertyTypeID);
        $res = BaseCache::get($key);
        if ($res === false) {
            $article = ArticleLogic::getArticleByEstateID(['estateID'=>$estateID,'propertyTypeID'=>$propertyTypeID,'typeID'=>0,'pageSize'=>1,'page'=>1]);
            $album = self::getAlbum($estateID, $propertyTypeID);
            $house = self::getHouseType($estateID, $propertyTypeID);
            $task = EstateDetailLogic::getAgencyTask($estateID, 1,1);
            //$review = EstateDetailLogic::getUserReview($estateID,1,1);
            //$price = self::getPrice($estateID,$propertyTypeID);
            $data = [
                'article' => empty($article['results'])?'0':'1',
                'album' => empty($album)?'0':'1',
                'house' => empty($house)?'0':'1',
                'task' => empty($task)?'0':'1',
                //'review' => empty($review)?'0':'1',
                //'price' => empty($price['data'])?'0':'1',
            ];
            $dataJson = Json::encode($data);
            BaseCache::set($key,$dataJson,1800);
            return $data;
        }
        return Json::decode($res);
    }

    public static function makeKey($name,$estateID, $propertyTypeID = '*') {
        return self::REDIS_ESTATE_CODE . '_' . $name . ':' . $propertyTypeID . ':' . $estateID;
    }

}
