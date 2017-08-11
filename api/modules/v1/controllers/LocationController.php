<?php
/**
 * 获取物业业态、区域、户型、交通、均价、特色、类型、环线、其他 信息接口
 * @Author: <lixiaobin>
 * @Date: 17-4-24
 */

namespace api\modules\v1\controllers;

use common\helper\cache\SearchCache;
use app\components\ActiveController;

class LocationController extends ActiveController
{

    /**
     * 获取物业业态、区域、户型等信息
     * @Author: <lixiaobin>
     * @Date: 2017-04-24
     * @Params: int companyCode 城市公司编码
     * @Parmas: int area 1:需要查询区域 0或者无:不需要查询
     * @Params: int traffic 1:需要查询交通 0或者无:不需要查询
     * @Params: int houseType 1:需要查询户型 0或者无:不需要查询
     * @Params: int price 1:需要查询(需要物业类型配合查询)均价  0或者无:不需要查询 注：查询价格需要与property(物业类型)并且的关系才可以查询到价格
     * @Params: int tag 1:需要查询(需要物业类型配合查询)特色标签  0或者无:不需要查询 注：查询特色标签需要与property(物业类型)并且的关系才可以查询到特色标签
     * @Params: int propertyTypeID 1:住宅 5:商铺 8:写字楼 0或者无:不需要查询
     * @Params: int ring 1:需要查询环线 0或者无:不需要查询
     * @Params: int other 1:需要查询其他 0或者无:不需要查询
     * @Params: int propertyTypeParentID 1:需要查询物业业态的父类 0或者无:不需要查询 注：查询的物业业态的父类
     * @Return: Json
     */
    public function actionIndex()
    {
        try {
            $locationArr = [];
            $companyCode = $this->request->get('companyCode', '');
            if (is_numeric($companyCode)) {
                //获取area的值 用来判断是否获取 区域 数据
                $area = $this->request->get('area', '');
                //获取traffic的值 用来判断是否获取 交通 数据
                $traffic = $this->request->get('traffic', '');
                //获取houseType的值 用来判断是否获取 户型 数据
                $houseType = $this->request->get('houseType', '');
                //获取price的值 用来判断是否获取 均价 数据
                $price = $this->request->get('price', '');
                //获取tag的值 用来判断是否获取 特色标签 数据
                $tag = $this->request->get('tag', '');
                //获取property的值 用来判断是否获取 物业类型 数据 和获取价格共用 获取价格时需要companyCode 和 property（物业业态id）
                $propertyTypeID = $this->request->get('propertyTypeID', '');
                //获取tag的值 用来判断是否获取 环线 数据
                $ring = $this->request->get('ring', '');
                //获取tag的值 用来判断是否获取 其他 数据
                $other = $this->request->get('other', '');
                //获取tag的值 用来判断是否获取 其他 数据
                $order = $this->request->get('order', '');
                //获取propertyTop的值 用来判断是否获取 物业类型（顶级信息） 数据
                $propertyTypeParentID = $this->request->get('propertyTypeParentID', '');

                if ($area) {
                    $locationArr['area'] = SearchCache::getAreaCache($companyCode);
                }
                if ($traffic) {
                    $locationArr['traffic'] = SearchCache::getTrackCache($companyCode);
                }
                if ($houseType) {
                    $locationArr['houseType'] = SearchCache::getHouseTypeCache();
                }
                if ($propertyTypeID) {
                    $locationArr['propertyType'] = SearchCache::getPropertyCache($propertyTypeID);
                }
                if ($price && $propertyTypeID) {
                    $locationArr['price'] = SearchCache::getPriceCache($companyCode, $propertyTypeID);
                }
                if ($tag && $propertyTypeID) {
                    $locationArr['tag'] = SearchCache::getTagInfoCache($companyCode, $propertyTypeID);
                }
                if ($ring) {
                    $locationArr['ring'] = SearchCache::getRingCache($companyCode);
                }
                if ($other) {
                    $locationArr['other'] = SearchCache::getOtherCache();
                }
                if($order){
                    $locationArr['order'] = SearchCache::getOrderCache();
                }
                if ($propertyTypeParentID) {
                    $locationArr['propertyTypeParentName'] = SearchCache::getPropertyParentCache(0, $companyCode);
                }
                $results = [
                    'results' => $locationArr
                ];

                return ['成功', 200, $results];

            } else {
                return ['城市编码格式不正确', 201];
            }
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

    }

}