<?php
/**
 * 住宅、商铺、写字楼统一搜索接口
 * @Author: <lixiaobin>
 * @Date: 17-5-3
 */

namespace api\modules\v1\controllers;

use common\models\SearchForm;
use app\components\ActiveController;

class SearchController extends ActiveController{

    /**
     * 根据城市编码、楼盘类型、关键词搜索
     * @Author: <lixiaobin>
     * @Date: 2017-04-26
     * @Params: int $companyCode 城市公司编码 必填
     * @Params: int $propertyID 楼盘业态ID 1：住宅 5：商铺 8：写字楼 必填
     * @Params: int $search 非必填
     * @Return: Json
     */
    public function actionIndex()
    {
        try {
            //判断如果没有城市公司code
            $companyCode = $this->request->get('companyCode', '');
            $propertyTypeID = $this->request->get('propertyTypeID', '');
            $search = $this->request->get('keywords', '');
            $areaID = $this->request->get('areaID', '');
            $houseTypeID = $this->request->get('houseTypeID', '');
            if (!is_numeric($companyCode)) {
                return ['公司编码格式不正确', 201];
            }
            //根据不同的楼盘类型走不同的ｓｏｌｒ库获取数据
            switch ($propertyTypeID){
                case ESTATE_PROPERTY_TYPE:
                    $solrDb = SOLR_ZHUZHAI;
                    break;
                case BUSINESS_PROERTY_TYPE:
                    $solrDb = SOLR_STORE;
                    break;
                case OFFICE_PROERTY_TYPE:
                    $solrDb = SOLR_OFFICE;
                    break;
                default:
                    return ['业态不存在', 201];
                    break;
            }
            $where = [];
            if(!empty($areaID)){
               $where['estateArea'] = $areaID;
            }
            if(!empty($houseTypeID)){
                $where['estateHouseType'] = $houseTypeID;
            }
            $solrInfo = SearchForm::suggest($companyCode, $search, $solrDb, $where);

            $results = [];
            if(!empty($solrInfo)){
                $results = ['results' => $solrInfo];
            }
            return ['成功', 200, $results];

        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }
}