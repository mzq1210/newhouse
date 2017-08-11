<?php

/**
 * Created by PhpStorm.
 * User: leexb
 * Date: 17-4-24
 * Time: 上午11:00
 */
return [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'enableStrictParsing' => true,
    'rules' => [
        ['class' => 'yii\rest\UrlRule', 'controller' => ['banner' => 'v1/banner'], 'except' => ['get', 'post'], 'pluralize' => false],//获取广告图
        ['class' => 'yii\rest\UrlRule', 'controller' => ['location' => 'v1/location'], 'except' => ['get', 'post'], 'pluralize' => false],//区域、交通等信息
        ['class' => 'yii\rest\UrlRule', 'controller' => ['detail' => 'v1/detail'], 'except' => ['get', 'post'], 'pluralize' => false],
        ['class' => 'yii\rest\UrlRule', 'controller' => ['loupan' => 'v1/loupan'], 'except' => ['get', 'post'], 'pluralize' => false],//普通住宅楼盘
        ['class' => 'yii\rest\UrlRule', 'controller' => ['keywords' => 'v1/keywords'], 'except' => ['get', 'post'], 'pluralize' => false], //热门关键词
        ['class' => 'yii\rest\UrlRule', 'controller' => ['theme' => 'v1/theme'], 'except' => ['get', 'post'], 'pluralize' => false], //主题楼盘
        ['class' => 'yii\rest\UrlRule', 'controller' => ['hotestate' => 'v1/hotestate'], 'except' => ['get', 'post'], 'pluralize' => false], //热门推荐的楼盘
        ['class' => 'yii\rest\UrlRule', 'controller' => ['estate' => 'v1/estate'], 'except' => ['get', 'post'], 'pluralize' => false], //主题楼盘列表
        ['class' => 'yii\rest\UrlRule', 'controller' => ['hotarticle' => 'v1/hotarticle'], 'except' => ['get', 'post'], 'pluralize' => false], //热门推荐的文章
        ['class' => 'yii\rest\UrlRule', 'controller' => ['market' => 'v1/market'], 'except' => ['get', 'post'], 'pluralize' => false], //市场行情
        ['class' => 'yii\rest\UrlRule', 'controller' => ['store' => 'v1/store'], 'except' => ['get', 'post'], 'pluralize' => false], //商铺列表
        ['class' => 'yii\rest\UrlRule', 'controller' => ['office' => 'v1/office'], 'except' => ['get', 'post'], 'pluralize' => false], //写字楼列表
        ['class' => 'yii\rest\UrlRule', 'controller' => ['search' => 'v1/search'], 'except' => ['get', 'post'], 'pluralize' => false], //住宅、商铺、写字楼统一搜索
        ['class' => 'yii\rest\UrlRule', 'controller' => ['map' => 'v1/map'], 'except' => ['get', 'post'], 'pluralize' => false],
        ['class' => 'yii\rest\UrlRule', 'controller' => ['article' => 'v1/article'], 'except' => ['get', 'post'], 'pluralize' => false],//资讯相关
        ['class' => 'yii\rest\UrlRule', 'controller' => ['im' => 'v1/im'], 'except' => ['get', 'post'], 'pluralize' => false],//环信聊天记录接口
        ['class' => 'yii\rest\UrlRule', 'controller' => ['siteconfig' => 'v1/siteconfig'], 'except' => ['get', 'post'], 'pluralize' => false],//首页服务模块配置
        ['class' => 'yii\rest\UrlRule', 'controller' => ['activity' => 'v1/activity'], 'except' => ['get', 'post'], 'pluralize' => false],//新房活动周模块配置
        ['class' => 'yii\rest\UrlRule', 'controller' => ['official' => 'v1/official'], 'except' => ['get', 'post'], 'pluralize' => false],//集团官网获取接口
        ['class' => 'yii\rest\UrlRule', 'controller' => ['company' => 'v1/company'], 'except' => ['get', 'post'], 'pluralize' => false],//集团官网获取接口

        //获取广告
        'GET,HEAD banner' => 'v1/banner/index',
        //调取区域、交通等信息
        'GET,HEAD location' => 'v1/location/index',
        //调取普通住宅
        'GET,HEAD loupan' => 'v1/loupan/index',
        //搜索
        'GET,HEAD loupan/suggest' => 'v1/loupan/suggest',
        //热门关键词
        'GET,HEAD keywords' => 'v1/keywords/index',
        //主题楼盘分类
        'GET,HEAD theme' => 'v1/theme/index',
        //获取主题楼盘列表页banner
        'GET,HEAD theme/banner' => 'v1/theme/banner',
        //获取热门推荐的楼盘
        'GET,HEAD hotestate' => 'v1/hotestate/index',
        //获取推荐热门文章
        'GET,HEAD hotarticle' => 'v1/hotarticle/index',
        //获取市场行情
        'GET,HEAD market' => 'v1/market/index',
        //首页服务模块配置
        'GET,HEAD siteconfig' => 'v1/siteconfig/index',
        //主题楼盘列表
        'GET,HEAD estate' => 'v1/estate/index',
        //商铺列表
        'GET,HEAD store' => 'v1/store/index',
        //写字楼列表
        'GET,HEAD office' => 'v1/office/index',
        //住宅、商铺、写字楼统一搜索
        'GET,HEAD search' => 'v1/search/index',
        //集团官网调取楼盘接口
        'GET,HEAD getEstate' => 'v1/official/estate',
        //集团官网调取搜索接口
        'GET,HEAD headerSearch' => 'v1/official/search',
        //集团官网调取城市接口
        'GET,HEAD getCity' => 'v1/official/city',
        //获取所有开通的城市
        'GET,HEAD company' => 'v1/company/index',
        //获取当前城市详情
        'GET,HEAD company/detail' => 'v1/company/detail',

        //获取楼盘详细信息相关接口
        'GET,HEAD detail/basic' => 'v1/detail/basic',//获取住宅楼盘solr中的基本信息
        'GET,HEAD detail/simple' => 'v1/detail/simple',//获取住宅楼盘solr中的最基本信息
        'GET,HEAD detail/task' => 'v1/detail/task',//获取住宅楼盘带看评价
        'GET,HEAD detail/review' => 'v1/detail/review',//获取住宅楼盘用户评价
        'GET,HEAD detail/recommend' => 'v1/detail/recommend',//获取住宅楼盘同城楼盘推荐
        'GET,HEAD detail/viewlog' => 'v1/detail/viewlog',//获取住宅楼盘同城楼盘推荐


        //地图
        //调取区域、交通等信息
        'GET,HEAD map/index' => 'v1/map/index',
        'GET,HEAD map/gethouse' => 'v1/map/gethouse',
        'GET,HEAD map/getstore' => 'v1/map/getstore',
        'GET,HEAD map/getoffice' => 'v1/map/getoffice',

        //新房活动周
        'GET,HEAD activity/index' => 'v1/activity/index',
        'GET,HEAD activity/getactestate' => 'v1/activity/getactestate',
        'GET,HEAD activity/registration' => 'v1/activity/registration',
        'GET,HEAD activity/activityestatedetail' => 'v1/activity/activityestatedetail',
        'GET,HEAD activity/registrationinfo' => 'v1/activity/registrationinfo',
        'GET,HEAD activity/estateactivity' => 'v1/activity/estateactivity',
        'GET,HEAD activity/estateproperty' => 'v1/activity/estateproperty',


        //资讯相关
        'GET,HEAD article' => 'v1/article/index',//获取资讯列表(分城市)
        'GET,HEAD article/getEstate' => 'v1/article/estate',//获取资讯列表(根据楼盘和业态)
        'GET,HEAD article/getDetail' => 'v1/article/detail',//获取资讯列表(根据楼盘和业态)
        'GET,HEAD article/getCategory' => 'v1/article/category',//获取资讯列表(根据楼盘和业态)
        'GET,HEAD article/pageView' => 'v1/article/pageview',//记录浏览记录


        //经纪人相关
        'GET,HEAD agency' => 'v1/agency/index',//获取推荐经纪人(根据楼盘)
        'GET,HEAD agency/super' => 'v1/agency/super',//获取明星经纪人(根据楼盘)
        'GET,HEAD agency/detail' => 'v1/agency/detail',//获取明星经纪人(根据楼盘)
        'GET,HEAD agency/status' => 'v1/agency/status',//获取经纪人在线状态

        //相册相关
        'GET,HEAD album' => 'v1/album/index',//获取楼盘相册(根据楼盘和业态)
        'GET,HEAD album/banner' => 'v1/album/banner',//获取首页展示图片集(根据楼盘和业态)

        //户型相关
        'GET,HEAD house' => 'v1/house/index',//获取户型列表(根据楼盘和业态)
        'GET,HEAD house/detail' => 'v1/house/detail',//获取户型详情(根据户型id)
        'GET,HEAD house/protoroom' => 'v1/house/protoroom',//获取户型样板间信息(根据户型id)
        'GET,HEAD house/price' => 'v1/house/price',//获取户型价格信息
        'GET,HEAD house/estate' => 'v1/house/estate',//获取楼盘id(根据户型id)

        //价格走势相关
        'GET,HEAD price' => 'v1/price/index',//获取价格走势(根据楼盘和业态)
        //关注楼盘
        'GET,HEAD follow' => 'v1/follow/index',//获取价格走势(根据楼盘和业态)
        'GET,HEAD follow/add' => 'v1/follow/add',//获取价格走势(根据楼盘和业态)
        'GET,HEAD follow/delete' => 'v1/follow/del',//获取价格走势(根据楼盘和业态)

        //im聊天
        'POST messages' => 'v1/im/inmsg',
        //用户评论
        'POST review'  => 'v1/detail/inreview',
        //用户购买意向
        'GET purchaseintention'  => 'v1/detail/purchaseintention',
        //聊天历史记录
        'GET messages' => 'v1/im/messageinfolist',
        //获取用户
        'GET userbrokerlist' => 'v1/im/userbrokerlist',
        //获取用户与经纪人聊天记录数量
        'GET usermessagenum' => 'v1/im/usermessagenum',
        //获取用户与经纪人聊天记录数量
        'GET userinfo' => 'v1/user/index',
        //获取用户未读聊天记录数量
        'GET messageunreadnum' => 'v1/im/messageunreadnum',
        'GET cache/del' => 'v1/cache/del'
    ],
];
