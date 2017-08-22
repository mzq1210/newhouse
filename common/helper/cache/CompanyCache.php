<?php
/**
 * 获取城市公司
 * @Author: <lixiaobin>
 * @Date: 17-3-22
 */

namespace common\helper\cache;

use common\helper\BaseCache;
use common\logic\CompanyLogic;

class CompanyCache
{

    const REDIS_COMPANY_CODE = 'company_code';
    const REDIS_COMPANY_OPEN = 'company_open';
    const REDIS_COMPANY_ALL = 'company_all';

    /**
     * 获取城市公司code和名称缓存
     * @Params: string $domain 城市公司简拼
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-21
     */
    public static function getCompanyCodeCache($subDomain)
    {
        $key = self::REDIS_COMPANY_CODE . ":" . $subDomain;
        $domainInfoCode = BaseCache::get($key);
        if ($domainInfoCode === false) {
            $domainInfo = CompanyLogic::selectCompanyCodeLogic($subDomain, 'companyCode,companyName,switchConfig');
            if (empty($domainInfo)) return false;
            $domainInfoCode = $domainInfo;
            BaseCache::set($key, $domainInfoCode);
        }
        return $domainInfoCode;
    }

    /**
     * 存储公司信息
     * @Params: string $domain 城市公司简拼
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-10
     */
    public static function getOpenCompanyCache($subDomain)
    {
        $key = self::REDIS_COMPANY_OPEN . ":" . $subDomain;
        $companyInfo = BaseCache::get($key);
        if ($companyInfo === false) {
            unset($companyInfo['switchConfig']);
            $companyInfo = CompanyLogic::selectCompanyLogic($subDomain, 'companyCode,companyName,switchConfig,secondDomainName');
            if (empty($companyInfo)) return false;
            BaseCache::set($key, $companyInfo);
        }
        return $companyInfo;
    }

    /**
     * 获取所有正常的城市并且按照城市首字母排序
     * @Return: json
     * @Auhtor: <lixiaobin>
     * @Date: 2017-05-25
     */
    public static function getCompanyAllCache()
    {
        $key = self::REDIS_COMPANY_ALL;
        $companyAll = BaseCache::get($key);
        if ($companyAll === false) {
            $companyAll = CompanyLogic::selectCompanyAllLogic('companyName,secondDomainName,switchConfig');
            if (empty($companyAll)) return false;
            BaseCache::set($key, $companyAll);
        }
        return $companyAll;

    }
}