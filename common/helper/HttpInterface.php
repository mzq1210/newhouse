<?php

namespace common\helper;

use common\components\Tools;
use common\helper\HttpRequest as re;

/**
 * 请求API接口类
 *
 * @author eboss
 */
class HttpInterface {

    //***************资讯模块***************************
    //获取资讯列表
    public static function getArticleList($params) {
        $keywords = HttpRequest::send('get', '/article', $params);
        return $keywords;
    }

    //获取楼盘相关资讯
    public static function getArticleEstate($params) {
        $keywords = HttpRequest::send('get', '/article/getEstate', $params);
        return $keywords;
    }

    //获取资讯详情
    public static function getArticleDetail($params) {
        $keywords = HttpRequest::send('get', '/article/getDetail', $params);
        return $keywords['results'];
    }

    //获取资讯类型
    public static function getArticleCategory() {
        $keywords = HttpRequest::send('get', '/article/getCategory');
        return $keywords['results'];
    }

    //资讯浏览记录接口
    public static function addArticlePageView($params) {
        $pageView = HttpRequest::send('get', '/article/pageView', $params);
        return $pageView['results'];
    }

    //***************经纪人模块***************************
    //获取热门经纪人

    public static function getHotBroker($params) {
        $keywords = HttpRequest::send('get', '/agency', $params);
        return $keywords['results'];
    }

    //获取经纪人在线状态

    public static function getBrokerStatus($params) {
        $keywords = HttpRequest::send('get', '/agency/status', $params);
        return $keywords['results'];
    }

    //获取明星经纪人
    public static function getStarBroker($params) {
        $keywords = HttpRequest::send('get', '/agency/super', $params);
        return $keywords['results'];
    }

    //经纪人详情
    public static function getBrokerInfo($params) {
        $keywords = HttpRequest::send('get', '/agency/detail', $params);
        return $keywords['results'];
    }

    //经纪人详情
    public static function getUserBrokerList($params) {
        $keywords = HttpRequest::send('get', '/userbrokerlist', $params);
        return $keywords['results'];
    }

    //经纪人详情
    public static function getUserMessageNum($params) {
        $keywords = HttpRequest::send('get', '/usermessagenum', $params);
        return $keywords['results'];
    }

    //***************首页模块***************************
    /**
     * 获取广告Banner图
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公司编码
     *          Int $params['clientType'] 客户端类型 0:Website;1:Mobilesite;2:App
     *          String $params['categoryKey'] 广告分类唯一码
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getBanner($params) {
        $banner = HttpRequest::send('get', '/banner', $params);
        return $banner['results'];
    }

    /**
     * 获取推荐主题楼盘分类 (pc使用)
     * @Params: Array $params
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getThemeEstate($params) {
        $theme = HttpRequest::send('get', '/theme', $params);
        return $theme['results'];
    }

    /**
     * 获取主题楼盘列表页Banner
     * @Params: Array $params
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getThemeBanner($params) {
        $themeBanner = HttpRequest::send('get', '/theme/banner', $params);
        return $themeBanner['results'];
    }

    /**
     * 获取主题楼盘列表
     * @Params: Array $params
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getThemeEstateList($params) {
        $estateList = HttpRequest::send('get', '/estate', $params);
        $results['results'] = $estateList['results'];
        $results['curPage'] = isset($estateList['curPage']) ? $estateList['curPage'] : 0;
        $results['pageCount'] = isset($estateList['pageCount']) ? $estateList['pageCount'] : 0;
        $results['count'] = isset($estateList['count']) ? $estateList['count'] : 0;
        return $results;
    }

    /**
     * 获取推荐热门资讯
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公司编码
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getHotArticle($params) {
        $hotArticle = HttpRequest::send('get', '/hotarticle', $params);
        return $hotArticle['results'];
    }

    /**
     * 获取推荐热门楼盘
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公司编码
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getHotEstate($params) {
        $hotEstate = HttpRequest::send('get', '/hotestate', $params);
        return $hotEstate['results'];
    }

    /**
     * 获取热门搜索关键词
     * @Params: Array $params
     *          Int $params['companyCode'] 公司编码
     *          Int $params['propertyTypeID'] 物业业态类型ID
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getHotSearch($params) {
        $keywords = HttpRequest::send('get', '/keywords', $params);
        return $keywords['results'];
    }

    /**
     * 获取区域交通等搜索标签
     * @Params: Array $params
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getLocation($params) {
        $location = HttpRequest::send('get', '/location', $params);
        return $location['results'];
    }

    /**
     * 获取市场行情
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公司编码
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getMarketInfo($params) {
        $market = HttpRequest::send('get', '/market', $params);
        return $market['results'];
    }

    /**
     * 获取服务模块配置(Wap)
     * @Params: Array $params
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getSiteConfig($params) {
        $siteConfig = HttpRequest::send('get', '/siteconfig', $params);
        return $siteConfig['results'];
    }

    /**
     * 获取所有开通的城市
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-25
     */
    public static function getCompanyAll() {
        $companyAll = HttpRequest::send('get', '/company');
        return $companyAll['results'];
    }

    //***************楼盘(Estate)***************************
    /**
     * 获取住宅列表信息
     * @Params: Array $params
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getHouseEstate($params) {
        $houseEstate = HttpRequest::send('get', '/loupan', $params);
        $results['results'] = $houseEstate['results'];
        $results['curPage'] = isset($houseEstate['curPage']) ? $houseEstate['curPage'] : 0;
        $results['pageCount'] = isset($houseEstate['pageCount']) ? $houseEstate['pageCount'] : 0;
        $results['count'] = isset($houseEstate['count']) ? $houseEstate['count'] : 0;
        return $results;
    }

    /**
     * 获取商铺列表信息
     * @Params: Array $params
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getStoreEstate($params) {
        $storeEstate = HttpRequest::send('get', '/store', $params);
        $results['results'] = $storeEstate['results'];
        $results['curPage'] = isset($storeEstate['curPage']) ? $storeEstate['curPage'] : 0;
        $results['pageCount'] = isset($storeEstate['pageCount']) ? $storeEstate['pageCount'] : 0;
        $results['count'] = isset($storeEstate['count']) ? $storeEstate['count'] : 0;
        return $results;
    }

    /**
     * 获取写字楼表信息
     * @Params: Array $params
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getOfficeEstate($params) {
        $officeEstate = HttpRequest::send('get', '/office', $params);
        $results['results'] = $officeEstate['results'];
        $results['curPage'] = isset($officeEstate['curPage']) ? $officeEstate['curPage'] : 0;
        $results['pageCount'] = isset($officeEstate['pageCount']) ? $officeEstate['pageCount'] : 0;
        $results['count'] = isset($officeEstate['count']) ? $officeEstate['count'] : 0;
        return $results;
    }

    /**
     * 获取楼盘搜索信息
     * @Params: Array $params
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getSearchEstate($params) {
        $searchEstate = HttpRequest::send('get', '/search', $params);
        return $searchEstate['results'];
    }

    /**
     * 获取楼盘简要信息
     * @Params: Array $params
     * @Return: Array
     * @Author: <wangluohua>
     * @Date: 2017-06-01
     */
    public static function getEstateBrief($params) {
        $searchEstate = HttpRequest::send('get', '/detail/simple', $params);
        return $searchEstate['results'];
    }

    //***************户型(Room)***************************
    /**
     * 获取户型价格
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getRoomPrice($params) {
        $keywords = HttpRequest::send('get', '/house/price', $params);
        return $keywords['results'];
    }

    /**
     * 获取户型样板间
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getRoomModel($params) {
        $keywords = HttpRequest::send('get', '/house/protoroom', $params);
        return $keywords['results'];
    }

    /**
     * 获取户型详情
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getRoomDetail($params) {
        $keywords = HttpRequest::send('get', '/house/detail', $params);
        return $keywords['results'];
    }

    /**
     * 获取楼盘户型
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getEstateRoom($params) {
        $keywords = HttpRequest::send('get', '/house', $params);
        return $keywords['results'];
    }

    //***************相册(Photo)***************************
    /**
     * 获取楼盘相册
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getEstatePhoto($params) {
        $keywords = HttpRequest::send('get', '/album', $params);
        return $keywords['results'];
    }

    /**
     * 获取相册轮拨图
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getPhotoList($params) {
        $keywords = HttpRequest::send('get', '/album/banner', $params);
        return $keywords['results'];
    }

    //***************楼盘详情(Detail)***************************
    /**
     * 获取同城楼盘推荐
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getTopEstate($params) {
        $keywords = HttpRequest::send('get', '/detail/recommend', $params);
        return $keywords['results'];
    }

    /**
     * 获取楼盘基础信息
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getEstateDetail($params) {
        $keywords = HttpRequest::send('get', '/detail/basic', $params);
        return $keywords['results'];
    }

    /**
     * 获取带看评价
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getEstateView($params) {
        $keywords = HttpRequest::send('get', '/detail/task', $params);
        return $keywords;
    }

    /**
     * 获取用户评价
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getEstateComment($params) {
        $keywords = HttpRequest::send('get', '/detail/review', $params);
        return $keywords;
    }

    /**
     * 新增评价
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function addEstateComment($params) {
        return $keywords = HttpRequest::send('post', '/review', $params);
    }

    /**
     * 获取购买意向
     * @Params: Array $params
     * @Return: Array
     * @Author: <wangluohua>
     * @Date: 2017-05-11
     */
    public static function getPurchaseIntention() {
        $keywords = HttpRequest::send('get', '/purchaseintention');
        return $keywords['results'];
    }

    public static function setViewLog($params) {
        $keywords = HttpRequest::send('get', '/detail/viewlog', $params);
        return $keywords['results'];
    }

    //***************价格走势(Price)***************************
    /**
     * 获取关注信息
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getEstatePrice($params) {
        $keywords = HttpRequest::send('get', '/price', $params);
        return $keywords['results'];
    }

    //***************关注(Follow)***************************
    /**
     * 获取关注信息
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function getFollowList($params) {
        $keywords = HttpRequest::send('get', '/follow', $params);
        if (empty($keywords['results'])) {
            return 0;
        }
        return 1;
    }

    /**
     * 关注
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function addFollow($params) {
        $keywords = HttpRequest::send('get', '/follow/add', $params);
        if (empty($keywords['results'])) {
            return 0;
        }
        return 1;
    }

    /**
     * 取消关注
     * @Params: Array $params
     * @Return: Array
     * @Author: <liangshimao>
     * @Date: 2017-05-11
     */
    public static function deleteFollow($params) {
        $keywords = HttpRequest::send('get', '/follow/delete', $params);
        if (empty($keywords['results'])) {
            return 0;
        }
        return 1;
    }

    //***************地图找房(Map)***************************
    /**
     * @desc: 住宅
     * @Params: Array $params
     * @Return: Array
     * @Author: <mzq>
     * @Date: 2017-05-11
     */
    public static function getMapHouse($params) {
        return HttpRequest::send('get', '/map/gethouse', $params);
    }

    /**
     * @desc: 商铺
     * @Params: Array $params
     * @Return: Array
     * @Author: <mzq>
     * @Date: 2017-05-11
     */
    public static function getMapStore($params) {
        return HttpRequest::send('get', '/map/getstore', $params);
    }

    /**
     * @desc: 写字楼
     * @Params: Array $params
     * @Return: Array
     * @Author: <mzq>
     * @Date: 2017-05-11
     */
    public static function getMapOffice($params) {
        return HttpRequest::send('get', '/map/getoffice', $params);
    }

    //***************新房活动周(Market)***************************
    /**
     * @desc: 报名
     * @Params: Array $params
     * @Return: Array
     * @Author: <mzq>
     * @Date: 2017-05-11
     */
    public static function addMarket($params) {
        $keywords = HttpRequest::send('get', '/activity/registration', $params);
        return $keywords['results'];
    }

    /**
     * @desc: 活动楼盘
     * @Params: Array $params
     * @Return: Array
     * @Author: <mzq>
     * @Date: 2017-05-11
     */
    public static function getMarketEstate($params) {
        $keywords = HttpRequest::send('get', '/activity/getactestate', $params);
        return $keywords['results'];
    }

    /**
     * @desc: 活动楼盘详情
     * @Params: Array $params
     * @Return: Array
     * @Author: <mzq>
     * @Date: 2017-05-11
     */
    public static function getMarketEstateDetail($params) {
        $keywords = HttpRequest::send('get', '/activity/activityestatedetail', $params);
        return $keywords;
    }

    /**
     * @desc: 检测活动楼盘是否报名
     * @Params: Array $params
     * @Return: Array
     * @Author: <mzq>
     * @Date: 2017-05-11
     */
    public static function getMarketEstateRegistration($params) {
        $keywords = HttpRequest::send('get', '/activity/registrationinfo', $params);
        return $keywords;
    }

    /**
     * @desc: 活动主题
     * @Params: Array $params
     * @Return: Array
     * @Author: <mzq>
     * @Date: 2017-05-11
     */
    public static function getMarketTheme($params) {
        $keywords = HttpRequest::send('get', '/activity/index', $params);
        return $keywords['results'];
    }

    /**
     * @desc: 楼盘活动业态详情
     * @Params: Array $params
     * @Return: Array
     * @Author: <mzq>
     * @Date: 2017-05-11
     */
    public static function getMarketEstateTheme($params) {
        $keywords = HttpRequest::send('get', '/activity/estateactivity', $params);
        return $keywords['results'];
    }

    /**
     * @desc: 楼盘活动所有业态
     * @Params: Array $params
     * @Return: Array
     * @Author: <mzq>
     * @Date: 2017-05-11
     */
    public static function getMarketEstateProperty($params) {
        $keywords = HttpRequest::send('get', '/activity/estateproperty', $params);
        return $keywords['results'];
    }

    //***************IM 聊天模块***************************
    /**
     * 新建聊天信息
     * @Params: Array $params
     * @Return: Array
     * @Author: <wangluohua>
     * @Date: 2017-05-22
     */
    public static function addChat($params) {
        $keywords = HttpRequest::send('post', '/messages', $params);
        return $keywords;
    }

    /**
     * 获取聊天历史记录
     * @Params: Array $params
     * @Return: Array
     * @Author: <wangluohua>
     * @Date: 2017-05-22
     */
    public static function getChatList($params) {
        $keywords = HttpRequest::send('get', '/messages', $params);
        return $keywords['results'];
    }

    /**
     * 获取用户基本信息
     * @Params: Array $params
     * @Return: Array
     * @Author: <wangluohua>
     * @Date: 2017-05-22
     */
    public static function getUserInfo($params) {
        $keywords = HttpRequest::send('get', '/userinfo', $params);
        return $keywords['results'];
    }

    /**
     * 获取当前城市信息
     * @Params: Array $params
     *          Int $params['companyCode'] 公司编码
     *          Int $params['propertyTypeID'] 物业业态类型ID
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function getCompanyInfo($params) {
        return $keywords = HttpRequest::send('get', '/company/detail', $params);
    }

    //获得用户未读消息数量模块
    public static function getMessageUnRead($params) {
        return $keywords = HttpRequest::send('get', '/messageunreadnum', $params);
    }

    /**
     * cas统一退出接口
     * @param type $params
     * return 成功返回200,失败返回相应的错http码
     */
    public static function casLogout($params) {
        HttpRequest::send('get', CAS_LOGOUT_URL, $params);
    }

    /**
     * 用户数据回传后台接口
     * @Params: $params Json数据
     * @Return : Array
     * @Author: <lixiaobin>
     * @Date: 2017-08-13
     */
    public static function sendHmsUserDate($params) {
        return Tools::curlPostJson(HMS_HOST . '/api/v1/usermgmt/user/sync', $params);
    }

    /**
     * 用户切换站点数数回传HMS后台接口
     * @Params: $params Json数据
     * @Return : Array
     * @Author: <lixiaobin>
     * @Date: 2017-08-13
     */
    public static function sendHmsUserSite($params) {
        return Tools::curlPostJson(HMS_HOST . '/api/v1/usermgmt/user/siterelation/sync', $params);
    }

    /**
     * 调取集团官网不同城市的友链和热词
     */
    public static function getOfficialLink($params) {
        if (empty($params))
            return false;
        //定义对应城市
        $cityArr = [
            '1000' => 1,
            '1006' => 1
        ];
        $params['city_id'] = $cityArr[$params['companyCode']];
        $params['area_id'] = 0;
        $params['prot'] = 'pc';
        unset($params['companyCode']);
        return HttpRequest::send('get', OFFICIAL_API . '/v1/adminfriendlink/gethotwordsarr', $params);
    }

    //app集成wap登陆
    public static function appLoginWap($params) {
        return Tools::curlPostJson('http://10.2.1.92:8088/appapi/index/v1/st', $params, true);
    }

    //获取用户信息
    public static function getStUserInfo($params) {
        return Tools::curlPostJson('http://10.2.1.92:8088/appapi/index/v1/STuser', $params, true);
    }

    public static function getNavLink() {
        $params['city_id'] = 1;
        return HttpRequest::send('get', OFFICIAL_API . '/v1/navigation', $params);
    }

}
