<?php
/**
 * 公共组件
 * @Author: <lixiaobin>
 * Date: 17-5-25
 */

namespace common\components;

use yii\helpers\Url;
use yii\base\Component;
use common\helper\BaseCookie;
use common\helper\HttpInterface;

class Common extends Component{

    /**
     * 获取所有开通的城市
     * @Return: Json
     * @Auhthor: <lixiaobin>
     * @Date: 2017-05-25
    */
    public function getCompany(){
        $companyAll = HttpInterface::getCompanyAll();
        return $companyAll;
    }

    /**
     *
    */
    public function getPropertyParent($params){
        $propertyTypeParent = HttpInterface::getLocation($params);
        return $propertyTypeParent;
    }

    /**
     * 获取热门关键词组件
     * @params： array $params
     *           companyCode 公司编码
     *           propertyTypeID 业态ID
     * @Return: Json
     * @Author: <lixiaobin>
     * @Date:2017-06-05
    */
    public function getHotKeywords($params){
        $hotKeywrids = HttpInterface::getHotSearch($params);
        return $hotKeywrids;
    }

    /**
     * 生成公共im经纪人列表Url
     * @Return: String
     * @Auhthor: <lixiaobin>
     * @Date: 2017-06-21
     */
    public function getImUrl(){
        //获取cookie中的domain
        $subDomain = BaseCookie::getCookie('domain');
        return Url::toRoute(['im/broker','City' => $subDomain]);
    }
    
    /**
     * 调取友情链接热词
     * @Return array
     * @Author: <lixiaobin>
     * @Date: 2017-08-13
    */
    public function getOfficialLink($params){
        $res = HttpInterface::getOfficialLink($params);
        return $res;
    }



}