<?php
/**
 * 集团官网请求接口
 * @Author: <lxiiaobin>
 * @Date: 17-5-10
 */

namespace api\modules\v1\controllers;

use yii\helpers\Url;
use common\models\SearchForm;
use common\helper\cache\SiteCache;
use app\components\ActiveController;
use common\helper\cache\CompanyCache;

class OfficialController extends ActiveController
{

    /**
     * 根据domain查看城市是否开通
     * @Params: string $subDomain 城市简拼 必填
     * @Rreturn: Json
     * @Author: <lixiaobin>
     * @Date: 2017-05-10
     */
    public function actionCity()
    {
        try {
            $subDomain = $this->request->get('domain', '');
            if (empty($subDomain)) {
                return ['缺少domain参数', 201];
            }
            $results = [];
            $companyArr = CompanyCache::getOpenCompanyCache($subDomain);
            if (!empty($companyArr)) {
                unset($companyArr['isEnableCommercial'],$companyArr['switchConfig']);
                $results['results'] = $companyArr;
            }
            return ['成功', 200, $results];

        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

    /**
     * 获取住宅楼盘三条信息
     * @Params: string $domain 城市简拼 如：cd (成都) 必填
     * @Params: int $clienType 客户端类型 1:pc 2:wap
     * @Params: int $page 当前页数  非必填
     * @Params: int $pageSize 每页显示条数  非必填
     * @Return: Json
     * @Author: <lixiaobin>
     * @Date: 2017-05-08
     */
    public function actionEstate()
    {
        try {
            //获取条件
            $domain = $this->request->get('domain', '');
            if (!empty($domain)) {
                $companyInfo = CompanyCache::getCompanyCodeCache($domain);
                if (empty($companyInfo)) {
                    return ['当前城市没有开通', 201];
                }
                $companyCode = $companyInfo['companyCode'];
            }
            $clientType = $this->request->get('clientType', 1);
            $pageSize = $this->request->get('pageSize', 3);
            //实例化搜索模型类
            $model = new SearchForm();
            $whereArr = [
                'companyCode' => $companyCode,
            ];
            //将条件赋给SearchFrom类中的属性
            $model->setAttributes($whereArr, false);
            $fields = 'id,estateName,coverImageName,lastAveragePrice,lastRoomMinPrice,isJudge,undetermined,areaTxt,minBuildArea,maxBuildArea,promotionInfo,propertyTypeName,tagList';
            $info = $model->searchs(1, $pageSize, $model->companyCode, SOLR_ZHUZHAI, $fields);
            //根据客户端类型获取不同的 hostUrl
            if ($clientType == 1) {
                $hostUrl = 'http://' . $domain . DOMAIN . CATALOG.'/';
            } elseif ($clienType = 2) {
                $hostUrl = WAPDOMAIN . '/' . $domain . '/';
            }
            $results = [];
            if ($info['numFound'] > 0 && !empty($info['docs'])) {
                //需要unset掉的字段
                $unsetArr = ['id','areaTxt', 'lastAveragePrice', 'isJudge', 'undetermined', 'lastRoomMinPrice'];
                $results['results'] = $this->_searchEstateInfo($info['docs'], $hostUrl, $unsetArr);
            }
            $results['moreUrl'] = $hostUrl.'loupan';
            return ['成功', 200, $results,];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

    /**
     * 搜索获取住宅楼盘信息
     * @Params: string $domain 城市简拼 如：cd (成都) 必填
     * @Params: int $clienType 客户端类型 1:pc 2:wap 必填
     * @Params: String 关键词 非必填
     * @Return: Json
     * @Author: <lixiaobin>
     * @Date: 2017-05-08
     */
    public function actionSearch()
    {
       // try {
            //获取条件
            $domain = $this->request->get('domain', '');
            if (!empty($domain)) {
                $companyInfo = CompanyCache::getCompanyCodeCache($domain);
                if (empty($companyInfo)) {
                    return ['当前城市没有开通', 201];
                }
                $companyCode = $companyInfo['companyCode'];
            }
            $clientType = $this->request->get('clientType', 1);
            $keywords = $this->request->get('keywords', '');
            $results = [];
            //根据客户端类型获取hostUrl
            if ($clientType == 1) {
                $hostUrl = 'http://' . $domain . '.' . SUBDOMAIN . '/';
            } elseif ($clientType == 2) {
                $hostUrl = WAPDOMAIN . '/' . $domain . '/';
            }

            //如果关键词不存在获取热门推荐的楼盘信息
            if (empty($keywords)) {
                $params['companyCode'] = $companyCode;
                $hotEstate = SiteCache::getRecommendeEstateCache($params);
                if (!empty($hotEstate)) {
                    $results['results'] = $this->_hotEstateInfo($hotEstate, $hostUrl);
                }
            } else {
                //实例化搜索模型类
                $model = new SearchForm();
                $whereArr = [
                    'companyCode' => $companyCode,
                ];
                //将条件赋给SearchFrom类中的属性
                $model->setAttributes($whereArr, false);
                $estateInfo = SearchForm::suggest($companyCode, $keywords, SOLR_ZHUZHAI);
                if (!empty($estateInfo)) {
                    //需要unset掉的字段
                    $unsetArr = ['id', 'lastAveragePrice', 'undetermined', 'isJudge', 'lastRoomMinPrice', 'areaTxt','propertyTypeName','price'];
                    $results['results'] = $this->_searchEstateInfo($estateInfo, $hostUrl, $unsetArr);
                }
            }
            return ['成功', 200, $results];
        /*} catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }*/
    }

    /**
     * 热门楼盘整合并删除无用的字段
     * @Params: Array $hotEstate 热门楼盘信息
     * @Params: String $hostUrl 主域名地址
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-10
     */
    private function _hotEstateInfo($hotEstate, $hostUrl)
    {
        foreach ($hotEstate as $val) {
            //根据楼盘业态生成不同的详情页地址
            if ($val['propertyTypeID'] == ESTATE_PROPERTY_TYPE) {
                $val['url'] = $hostUrl . 'loupan/detail_' . $val['id'];
            } elseif ($val['propertyTypeID'] == BUSINESS_PROERTY_TYPE) {
                $val['url'] = $hostUrl . 'store/detail_' . $val['id'];
            } elseif ($val['propertyTypeID'] == OFFICE_PROERTY_TYPE) {
                $val['url'] = $hostUrl . 'office/detail_' . $val['id'];
            }
            if ($val['isJudge'] == 1) {
                $val['price'] = $val['undetermined'];
            } elseif ($val['isJudge'] == 2) {
                $val['price'] = '总价:' . floatval($val['lastRoomMinPrice']) . '万元起';
            } else {
                $val['price'] = $val['lastAveragePrice'] . '元/平米';
            }
            //清除掉不需要的字段
            unset($val['estateID'], $val['propertyTypeID'], $val['totalReviewCount'], $val['minBuildArea'],
                $val['estateAddress'], $val['id'], $val['maxBuildArea'], $val['userWishlistCount'],
                $val['totalSeeHouseCount'], $val['promotionInfo'], $val['lastAveragePrice'], $val['coverImageName'],
                $val['undetermined'], $val['isJudge'], $val['lastRoomMinPrice'], $val['areaTxt'], $val['tagList'],$val['collaborationType'],
                $val['propertyTypeName'],$val['price'],$val['totalSeeHouseReviewCount']
            );
            $results[] = $val;
        }
        return $results;
    }

    /**
     * 搜索楼盘整合并删除无用的字段
     * @Params: Array $hotEstate 热门楼盘信息
     * @Params: String $hostUrl 主域名地址
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-10
     */
    private function _searchEstateInfo($estateInfo, $hostUrl, $unsetArr = [])
    {
        foreach ($estateInfo as $val) {
            if ($val['isJudge'] == 1) {
                $val['price'] = '售价 '. $val['undetermined'];
            } elseif ($val['isJudge'] == 2) {
                $val['price'] = '总价:' . floatval($val['lastRoomMinPrice']) . '万元起';
            } else {
                $val['price'] = $val['lastAveragePrice'] . '元/平';
            }
            //$val['propertyTypeName'] = $val['propertyTypeName'];
            $val['url'] = $hostUrl . 'loupan/detail_' . $val['id'];
            //判断楼盘是否有封面图面
            if(isset($val['coverImageName'])){
                $val['coverImageName'] = IMG_DOMAIN . $val['coverImageName'];
            }
            //清除掉不需要的字段
            if (!empty($unsetArr)) {
                foreach ($unsetArr as $uv) {
                    unset($val[$uv]);
                }
            }
            $results[] = $val;
        }
        return $results;
    }
}