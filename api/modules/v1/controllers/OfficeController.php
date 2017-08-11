<?php
/**
 * 写字楼列表也接口
 * @Author: <lixiaobin>
 * @Date: 17-5-3
 */

namespace api\modules\v1\controllers;

use common\models\SearchForm;
use app\components\ActiveController;

class OfficeController extends ActiveController{

    /**
     * 查询写字楼列表信息
     * @Author: <lixiaobin>
     * @Date: 2017-04-26
     * @Params: int $companyCode 城市公司编码 必填
     * @Params: string $keywords 关键词 非必填
     * @Params: int  $estateArea 区域ID 非必填
     * @Params: int  $estateTrack 交通ID 非必填
     * @Params: int  $estateHouseType 户型ID 非必填
     * @Params: int  $lastAveragePrice 均价ID 非必填
     * @Params: int  $customPriceLow 自定义价格 非必填
     * @Params: int  $customPriceTop 自定义价格 非必填
     * @Params: int  $tag 特色标签ID 非必填
     * @Params: int  $ringRoad 环线ID 非必填
     * @Params: int  $other 其他ID 非必填
     * @Params: int  $order 排序ID 非必填
     * @Params: int  $page 当前页数 非必填 默认1
     * @Params: int  $pageSize 每页显示条数 非必填 默认15
     * @Return: Json
     */
    public function actionIndex(){
        try {
            //获取条件
            $companyCode = $this->request->get('companyCode', '');
            //判断如果没有城市公司code
            if (!is_numeric($companyCode)) {
                return ['公司编码格式错误', 201];
            }
            $keywords = $this->request->get('search', '');
            $estateArea = $this->request->get('estateArea', '');
            $estateTrack = $this->request->get('estateTrack', '');
            $propertyType = $this->request->get('propertyType', '');
            //$estateHouseType = $this->request->get('estateHouseType', '');
            $lastAveragePrice = $this->request->get('lastAveragePrice', '');
            $customPriceLow = $this->request->get('customPriceLow', '');
            $customPriceTop = $this->request->get('customPriceTop', '');
            $tag = $this->request->get('tag', '');
            $ringRoad = $this->request->get('ringRoad', '');
            $other = $this->request->get('other', '');
            $order = $this->request->get('order', '');
            $page = $this->request->get('page', 1);
            $pageSize = $this->request->get('pageSize', PAGESIZE);
            //add by mzq 2017-5-27 经纬度范围搜索
            $swLng = $this->request->get('swLng', '');
            $swLat = $this->request->get('swLat', '');
            $neLng = $this->request->get('neLng', '');
            $neLat = $this->request->get('neLat', '');
            //重组数组付给SearchFrom类的属性
            $whereArr = [
                'companyCode' => $companyCode,
                'estateArea' => $estateArea,
                'estateTrack' => $estateTrack,
                'propertyType' => $propertyType,
                //'estateHouseType' => $estateHouseType,
                'lastAveragePrice' => $lastAveragePrice,
                'customPriceLow' => $customPriceLow,
                'customPriceTop' => $customPriceTop,
                'tag' => $tag,
                'ringRoad' => $ringRoad,
                'other' => $other,
                'order' => $order,
                //add by mzq 2017-5-27 经纬度范围搜索
                'swLng' => $swLng,
                'swLat' => $swLat,
                'neLng' => $neLng,
                'neLat' => $neLat
            ];
            //实例化搜索模型类
            $model = new SearchForm();
            //将条件赋给SearchFrom类中的属性
            $model->setAttributes($whereArr, false);
            $model->search = urldecode($keywords);
            //从solr中获取数据
            $fields = 'id,estateName,areaTxt,estateAddress,promotionInfo,coverImageName,estateLongitude,estateLatitude,userWishlistCount,totalSeeHouseCount,totalReviewCount,estateAreaId,
            lastAveragePrice,lastRoomMinPrice,isJudge,undetermined,minBuildArea,maxBuildArea,tagList,propertyTypeName,propertyTypeChildName,collaborationType,totalSeeHouseReviewCount,standardFloorArea,pageViewCount';
            $info = $model->searchs($page, $pageSize, $model->companyCode, SOLR_OFFICE, $fields);
            $results = [];
            if ($info['numFound'] > 0 && !empty($info['docs'])) {
                foreach ($info['docs'] as $val){

                    $val['commentNum'] = 0;
                    if($val['collaborationType'] == 1){
                        $val['commentNum'] = $val['totalSeeHouseReviewCount'] + $val['totalReviewCount'];
                    }else{
                        $val['commentNum'] = $val['totalReviewCount'];
                    }

                    $val['coverImageName'] = IMG_DOMAIN . $val['coverImageName'];
                    $docs[] = $val;
                }
                $results['curPage'] = $page;
                $results['pageCount'] = ceil($info['numFound'] / $pageSize);
                $results['count'] = $info['numFound'];
                $results['results'] = $docs;
            }
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

}