<?php
/**
 * 楼盘搜索模型嘞
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\Pagination;
use common\components\CreateUrl;
use common\helper\cache\SearchCache;
use common\helper\pinyin\YPinYin;

class SearchForm extends Model
{

    public $companyCode = 0,//城市ID
        $search = '',//搜索关键字
        $estateArea = 0, //区域
        $estateTrack = 0, //地铁
        $estateHouseType = 0, //户型
        $lastAveragePrice = 0, //楼盘均价
        $tag = 0, //特色
        $propertyType = 0, //类型
        $ringRoad = 0, //环线
        $other = 0, //其他
        $customPriceLow,//自定义价格［高］
        $customPriceTop,//自定义价格[低]
        $order,//排序
        //add by mzq 2017-5-27 经纬度范围搜索
        $swLng,
        $swLat,
        $neLng,
        $neLat;

    //前台搜索排序类型
    const ORDER_DEFAULT = 0; //默认排序
    const ORDER_OPENTIME_DESC = 1;//开盘时间倒序
    const ORDER_OPENTIME_ASC = 2;//开盘时间顺序
    const ORDER_PRICE_DESC = 3;//价格从高到低
    const ORDER_PRICE_ASC = 4;//价格从低到高

    /**
     * 查询ｓｏｌｒ中的数据
     * @Author: <lixiaobin>
     * @Date: 2017-03-28
     * @Params: int $page 当前页数
     * @Params: int $pageSize 每页显示的条数
     * @Params: int $companyCode 城市编码
     * @Params: string $solrDb 当前需要查询的ｓｏｌｒ库名
     * @Params: string $fl 需要查询的字段
     * @Params: Return
     */
    /*public function search($page = 1, $pagesize = PAGESIZE,$companyCode = '1000',$solrDb='zhuzhai',$fl = '') {

        $list = array();
        try {            
            
            $data = array();
            $data['q'] = $this->_getSolrSearchQ($companyCode,$solrDb);
            $data['sort'] = $this->_getSolrSearchSort();
            if(!empty($fl)){
                $data['fl'] = $fl;
            }
            $data['page'] = $page;
            $data['pageSize'] = $pagesize;
            $list = Yii::$app->solr->select($data, $solrDb);
            
            if($list['success'] == 1)
            {
                $data = $list['data'];
                $pages = new Pagination(['totalCount' =>$data['numFound']]);
                $pages->pageSize = $pagesize;
                $data['pages'] = $pages;
                return $data;

            }
        } catch (Exception $ex) {
            return false;
        }

        return array();
    }*/

    /**
     * 查询ｓｏｌｒ中的数据
     * @Params: int $page 当前页数
     * @Params: int $pageSize 每页显示的条数
     * @Params: int $companyCode 城市编码
     * @Params: string $solrDb 当前需要查询的ｓｏｌｒ库名
     * @Params: string $fl 需要查询的字段
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-03-28
     */
    public function searchs($page = 1, $pagesize = PAGESIZE, $companyCode, $solrDb = 'zhuzhai', $fl = '')
    {
        try {
            $data = array();
            $data['q'] = $this->_getSolrSearchQ($companyCode, $solrDb);
            $data['sort'] = $this->_getSolrSearchSort();
            if (!empty($fl)) {
                $data['fl'] = $fl;
            }
            $data['page'] = $page;
            $data['pageSize'] = $pagesize;
            $list = Yii::$app->solr->select($data, $solrDb);
            if ($list['success'] == 1) {
                $data = $list['data'];
                return $data;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    private function _getSolrSearchQ($companyCode, $solrDb)
    {
        //判断属于哪个物业类型的价格
        $propertyType = $solrDb == SOLR_ZHUZHAI ? ESTATE_PROPERTY_TYPE : ($solrDb == SOLR_STORE ? BUSINESS_PROERTY_TYPE : ($solrDb == SOLR_OFFICE ? OFFICE_PROERTY_TYPE : ESTATE_PROPERTY_TYPE));
        $conditionQ = "companyCode:{$companyCode} AND status:2 ";//查询处于上架状态的楼盘，并按城市区分
        $params = $this->attributes;
        //排除的字段
        $fields = ['lastAveragePrice', 'companyCode', 'customPriceLow', 'customPriceTop', 'order', 'swLng', 'swLat', 'neLng', 'neLat'];
        foreach ($params as $key => $value) {
            if (!in_array($key, $fields) && ($params['other'] != 1 && $params['other'] != 2)) {
                $value > 0 && $conditionQ .= " AND {$key}:{$value} ";
            }
        }
        if ($params['search'])//关键字搜索
        {
            $search = strtolower(trim($params['search']));
            $search = strtr($search, array('·' => ' ', '.' => ' ', '-' => ' ', '－' => ' ', '_' => ''));
            $searchArr = preg_split("/[\s| ]+/", $search);
            $sCount = count($searchArr);
            if ($sCount == 1) {
                $searchAllPinYin = '';
                if (preg_match("/[\x7f-\xff]/", $search)) {
                    $searchAllPinYin = YPinYin::encode($search, 'all');
                }
                $searchAllPinYin = !empty($searchAllPinYin) ? ' OR search:' . $searchAllPinYin : '';
                $conditionQ .= " AND ( search:$search" . $searchAllPinYin . ")";
            } else {
                $search = '';
                foreach ($searchArr as $keyword) {
                    if ($keyword != '') {
                        if (preg_match("/[\x7f-\xff]/", $keyword)) {
                            $searchAllPinYin = YPinYin::encode($keyword, 'all');
                            $search .= $searchAllPinYin . ' OR ';
                        }
                        $search .= $keyword . ' OR ';
                    }
                }
                $search = rtrim($search, ' OR ');
                $conditionQ .= " AND search:({$search}) ";
            }
        }
        //价格区间
        if ($params['lastAveragePrice'] > 0) {//类型搜索
            $price = SearchCache::getOnePrice($companyCode, $propertyType, $params['lastAveragePrice']);
            if (preg_match('/^(\d+)-(\d+)$/', $price['regionName'])) {
                $conditionQ .= " AND lastAveragePrice:[{$price['minValue']} TO {$price['maxValue']}] ";
            } else if (strpos($price['regionName'], '以下')) {
                $regionName = (int)$price['minValue'] - 1;
                $conditionQ .= " AND lastAveragePrice:[* TO {$regionName}] ";
            } else if (strpos($price['regionName'], '以上')) {
                $regionName = (int)$price['minValue'] + 1;
                $conditionQ .= " AND lastAveragePrice:[{$regionName} TO *] ";
            }
        } else {
            if ($params['customPriceLow'] > 0 && $params['customPriceTop'] > 0) {
                $conditionQ .= " AND lastAveragePrice:[{$params['customPriceLow']} TO {$params['customPriceTop']}] ";
            } else if ($params['customPriceLow'] > 0) {
                $price = (float)$params['customPriceLow'];
                $conditionQ .= " AND lastAveragePrice:[{$price} TO *] ";
            } else if ($params['customPriceTop'] > 0) {
                $price = (float)$params['customPriceTop'];
                $conditionQ .= " AND lastAveragePrice:[* TO {$price}] ";
            }
        }
        switch ($params['other']) {
            case 1:
                $endDate = date('Y-m-d', time() - (30 * 24 * 3600));
                $conditionQ .= " AND actualOpeningDate:[" . $endDate . " TO " . date('Y-m-d') . "] ";
                break;
            case 2:
                $endDate = date('Y-m-d', time() - (90 * 24 * 3600));
                $conditionQ .= " AND actualOpeningDate:[" . $endDate . " TO " . date('Y-m-d') . "] ";
                break;
        }
        if ($params['swLng'] > 0) {//范围搜索
            $conditionQ .= " AND estateLongitude:[{$params['swLng']} TO {$params['neLng']}] ";
            $conditionQ .= " AND estateLatitude:[{$params['swLat']} TO {$params['neLat']}] ";
        }
        return $conditionQ;

    }


    /**
     * 获取搜索排序规则
     * @return string
     */
    private function _getSolrSearchSort()
    {
        $conditonSort = 'isJudge ASC';//默认按照计算出的数字大小 和点击次数
        switch ($this->order) {
            case self::ORDER_OPENTIME_DESC:
                $conditonSort .= ',actualOpeningDate DESC';//开盘时间倒序
                break;
            case self::ORDER_OPENTIME_ASC:
                $conditonSort .= ',actualOpeningDate ASC';//开盘时间顺序
                break;
            case self::ORDER_PRICE_DESC:
                $conditonSort .= ',lastAveragePrice DESC';//均价降序
                break;
            case self::ORDER_PRICE_ASC:
                $conditonSort .= ',lastAveragePrice ASC';//均价升序
                break;
        }
        return $conditonSort . ',sortScore DESC,pageViewCount DESC';
    }

    /**
     * 搜索下拉框数据
     * @Author: <lixiaobin>
     * @date: 2017-03-26
     * @params: int $companyCode
     * @return \CPagination
     * @throws CHttpException
     */
    public static function suggest($companyCode, $search = '', $solrDb = SOLR_ZHUZHAI, $listWhere = [], $fields = '')
    {
        try {
            if(empty($companyCode)) return false;
            $where = '';
            if (!empty($listWhere)) {
                foreach ($listWhere as $key => $val) {
                    $where .= ' AND ' . $key . ':' . $val;
                }
            }
            if (!empty($search)) {
                $search = strtolower(trim($search));
                $search = strtr($search, array('·' => ' ', '.' => ' ', '-' => ' ', '－' => ' ', '_' => ''));
                $searchArr = preg_split("/[\s| ]+/", $search);
                $sCount = count($searchArr);
                if ($sCount == 1) {
                    $searchAllPinYin = '';
                    if (preg_match("/[\x7f-\xff]/", $search)) {
                        $searchAllPinYin = YPinYin::encode($search, 'all');
                    }
                    $searchAllPinYin = !empty($searchAllPinYin) ? ' OR search:' . $searchAllPinYin : '';
                    $search = " AND ( search:$search" . $searchAllPinYin . ")";
                } else {
                    $search = '';
                    foreach ($searchArr as $keyword) {
                        if ($keyword != '') {
                            if (preg_match("/[\x7f-\xff]/", $keyword)) {
                                $searchAllPinYin = YPinYin::encode($keyword, 'all');
                                $search .= $searchAllPinYin . ' OR ';
                            }
                            $search .= $keyword . ' OR ';
                        }
                    }
                    $search = rtrim($search, ' OR ');
                    $search = " AND search:({$search}) ";
                }
            }
            $params = array();
            $params['q'] = "companyCode:{$companyCode} AND status:2 " . $search . $where;
            $params['fl'] = !empty($fields) ? $fields : 'id,estateName,areaTxt,propertyTypeName,lastAveragePrice,lastRoomMinPrice,isJudge,undetermined,propertyTypeChildName';
            //$params['fl'] = '*';
            //$params['hl.fl'] = 'estateName';
            $data['sort'] = 'sortScore DESC,pageViewCount DESC';
            $params['pageSize'] = 10;
            $list = Yii::$app->solr->select($params, $solrDb);
            if ($list['success'] == 1) {
                return $list['data']['docs'];
            }
        } catch (Exception $e) {
           return false;
        }
    }

    //搜索主题楼盘
    public static function selectSolrEstate($companyCode, $estateID, $solrDb = SOLR_ZHUZHAI)
    {
        if (empty($companyCode) && empty($estateID) == '') return false;
        try {
            $params = array();
            $params['q'] = "companyCode:{$companyCode} AND id:{$estateID} AND status:2";
            $params['fl'] = 'id,estateName,areaTxt,lastAveragePrice,lastRoomMinPrice,isJudge,undetermined,estateAddress,propertyTypeName,promotionInfo,maxBuildArea,minBuildArea,tagList,userWishlistCount,totalSeeHouseCount,totalReviewCount,coverImageName,collaborationType,totalSeeHouseReviewCount,pageViewCount,propertyTypeChildName';
            $data['sort'] = 'sortScore DESC,pageViewCount DESC';
            $params['pageSize'] = 7;
            $list = Yii::$app->solr->select($params, $solrDb);
            if ($list['success'] == 1) {
                return $list['data']['docs'];
            }
        } catch (Exception $e) {
            return false;
        }
    }

    //如果首页楼盘木有推荐 则获取四个合作楼盘
    public static function getFourEstateInfo($whereArr = [],$solrDb = SOLR_ZHUZHAI){
        if(!empty($whereArr)){
            $where = '';
            foreach($whereArr as $key => $val){
                $where .= ' AND ' . $key . ':' . $val;
            }

            $params['q'] = "status:2" .$where;
            $params['fl'] = 'id,estateName,areaTxt,lastAveragePrice,lastRoomMinPrice,isJudge,undetermined,estateAddress,propertyTypeName,promotionInfo,maxBuildArea,minBuildArea,tagList,userWishlistCount,totalSeeHouseCount,totalReviewCount,coverImageName,collaborationType,totalSeeHouseReviewCount,pageViewCount';
            $data['sort'] = 'sortScore DESC,pageViewCount DESC';
            $params['pageSize'] = 4;
            $list = Yii::$app->solr->select($params, $solrDb);
            if ($list['success'] == 1) {
                return $list['data']['docs'];
            }
        }
        return false;
    }


    /**
     * 直接通过URL获取搜索的关键字
     */
    public static function getSearchKeyword()
    {
        $arr = CreateUrl::parseSearchUrl();
        if (isset($arr['keywords'])) {
            return urldecode($arr['keywords']);
        }

        return '';
    }
}
