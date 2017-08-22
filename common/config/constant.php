<?php

/**
 * 将所用的到配置参数（常量）配置在该文件中
 * @Author: 李效宾
 * @Date: 2017-03-22
 */

//配置主域名
defined('DOMAIN') OR define('DOMAIN', '.5i5j.com.cn');
defined('CATALOG') OR define('CATALOG', '/fang');
defined('SUBDOMAIN') OR define('SUBDOMAIN', 'http://f'.DOMAIN);
//defined('SUBDOMAINS') OR define('SUBDOMAINS', 'fang'.DOMAIN);
defined('APIDOMAIN') OR define('APIDOMAIN', 'http://apifang'.DOMAIN);
defined('WAPDOMAIN') OR define('WAPDOMAIN', 'fang'.DOMAIN);



//配置存储cookie名称
//wap站
defined('WAP_HISTORY') OR define('WAP_HISTORY', 'fang_wap_search_history');
//pc站
defined('PC_HISTORY') OR define('PC_HISTORY', 'fang_pc_search_history');

//图片域名配置
defined('IMG_DOMAIN') OR define('IMG_DOMAIN', 'http://huijinhang-image-hz.oss-cn-hangzhou.aliyuncs.com/');

//配置每页显示的条数
defined('PAGESIZE') OR define('PAGESIZE', 10);



//普通住宅户型
defined('ESTATE_PROPERTY_TYPE') OR define('ESTATE_PROPERTY_TYPE', 1);
//商业楼盘
defined('BUSINESS_PROERTY_TYPE') OR define('BUSINESS_PROERTY_TYPE', 5);
//写字楼
defined('OFFICE_PROERTY_TYPE') OR define('OFFICE_PROERTY_TYPE', 8);

//定义客户端类型
//0-Website
defined('WEBSITE') OR define('WEBSITE', 0);
//1-Mobilesite
defined('MOBILESITE') OR define('MOBILESITE', 1);
//2-App
defined('APPSITE') OR define('APPSITE', 2);

//solr 搜索引擎配置库
defined('SOLR_ZHUZHAI') OR define('SOLR_ZHUZHAI', 'zhuzhai');
//solr 商铺模块
defined('SOLR_STORE') OR define('SOLR_STORE', 'store');
//solr 写字楼模块
defined('SOLR_OFFICE') OR define('SOLR_OFFICE', 'office');

//楼盘价格趋势图默认查询月数
defined('CHART_PRICE_TREND_MONTH_NUM') OR define('CHART_PRICE_TREND_MONTH_NUM', 12);

/******* 环信仿真环境 start ********/
//环信客户端id
//defined('HUANXIN_CLIENT_ID') OR define('HUANXIN_CLIENT_ID', 'YXA68dQB4HMGEeaWZi8JidawwQ');
//环信客户端密码
//defined('HUANXIN_CLIENT_SECRET') OR define('HUANXIN_CLIENT_SECRET', 'YXA6w6LevVes8zpQHya8Xcv5SWHvO2A');
//环信应用标识
//defined('HUANXIN_ORG_NAME') OR define('HUANXIN_ORG_NAME', 'moshou2016');
//defined('HUANXIN_APP_NAME') OR define('HUANXIN_APP_NAME', '51moshouapp');
/******* 环信仿真环境 end ********/

//环信客户端id
defined('HUANXIN_CLIENT_ID') OR define('HUANXIN_CLIENT_ID', 'YXA6k9eZ8C-hEeeYg8katOz8RA');
//环信客户端密码
defined('HUANXIN_CLIENT_SECRET') OR define('HUANXIN_CLIENT_SECRET', 'YXA6ED_rNXlwZM6KoPu65Qz0CPGimrg');
//环信应用标识
defined('HUANXIN_ORG_NAME') OR define('HUANXIN_ORG_NAME', '343479866');
defined('HUANXIN_APP_NAME') OR define('HUANXIN_APP_NAME', '5i5jbeta');


//环信每次批量注册数量
defined('HUANXIN_REGISTER_NUM') OR define('HUANXIN_REGISTER_NUM', 50);
//注册环信用户名前缀
defined('HUANXIN_USER_PREFIX') OR define('HUANXIN_USER_PREFIX', 'xinfang_user_');
//环信限制每次批量导入用户的数量
defined('HUANXIN_USER_IMPORT_NUM') OR define('HUANXIN_USER_IMPORT_NUM', 20);
//可用环信数量为多少时出发的再次批量添加
defined('HUANXIN_RETISTER_TRIGGER_NUM') OR define('HUANXIN_RETISTER_TRIGGER_NUM', 100);


/****************日志相关******************/
defined('LOG_RECORD') OR define('LOG_RECORD', 1);//是否开启日志
defined('LOG_SAVE_MODE') OR define('LOG_SAVE_MODE', 0);//是否socket存储 1 sockey  0 本机文件存储
defined('LOG_HOST') OR define('LOG_HOST', "http://127.0.0.1:9502");//socket地址
defined('LOG_SAVE_PATH') OR define('LOG_SAVE_PATH', '/tmp');
defined('LOG_FILE_SIZE') OR define('LOG_FILE_SIZE', 20);//单位 M  单个文件大小 超过设定的大小后重新按当天日期生成一个文件  文件名以当天日期

/****************地图相关******************/
defined('MAP_ZOOM') OR define('MAP_ZOOM', 14);//地图等级默认分界点



define('CAS_HOST','passport.5i5j.com'); //CAS服务器地址
define('CAS_CONTEXT','/passport');//CAS服务器路径
define('CAS_PORT',80);//CAS服务器端口
define('CAS_SERVER_CA_CERT_PATH','/path/to/cachain.pem');///CAS服务器证书地址
define('CAS_LOGOUT_URL', 'http://'.CAS_HOST.'/passport/v1/logout');//服务器退出接口

defined('USER_INFO') OR define('USER_INFO', 'user_info');


/****session和cookie存活周期***/
defined('SESSION_TIMEOUT') OR define('SESSION_TIMEOUT', 3600);
defined('COOKIE_TIMEOUT') OR define('COOKIE_TIMEOUT', 3600);

//加密信息秘钥
defined('SECRET_KEY') OR define('SECRET_KEY', '74d738020dca22a731e30058ac7242ee');

//hms后台接口地址
defined('HMS_HOST') OR define('HMS_HOST', 'http://apibackend.fangodata.com:81');

//友情链接接口
defined('OFFICIAL_API') OR define('OFFICIAL_API', 'http://api'.DOMAIN);