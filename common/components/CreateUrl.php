<?php
/**
 * 生成和解析URL类
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */

namespace common\components;

use Yii;

class CreateUrl{

    //真实字段对应别名(参数别名)
    static public $paramsAlias = array(
        'estateArea'      => 'a', //区域--所代表的字母
        'estateTrack'     => 'k', //地铁--所代表的字母
        'estateHouseType' => 'h', //户型--所代表的字母
        'lastAveragePrice'=> 'p', //楼盘均价--所代表的字母
        'tag'            => 't', //特色--所代表的字母
        'propertyType'   => 'y', //类型--所代表的字母
        'ringRoad'       => 'r', //环线--所代表的字母
        'other'          => 'o',//其他
        'page'           => 'n',//分页--所代表的字母
        'customPriceTop' => 'g',//自定义价格[高]
        'customPriceLow' => 'd',//自定义价格[低]
        'order' => 's'//排序--所代表的字母
    );


    /**
     * 搜索区域URL生成
     * @param string $controller 控制器名称
     * @param object $model 数据model
     * @param array $params 参数
     * @return string $url 返回url
     */
    public static function createSearchUrl($model, $params = array())
    {
       $url = Yii::$app->request->getHostInfo().'/'.Yii::$app->controller->id.'/';
        $paramsStr = '';
        if($model)
        {
            //生成url时将page重新设置
            $model['page'] = 0;
            $paramData = array_merge($model, $params);
            unset($paramData['companyCode']);
            if(isset($params['lastAveragePrice']) && $params['lastAveragePrice'] > 0)//均价和自定义价格只能有一个
            {
                unset($paramData['customPriceTop'], $paramData['customPriceLow']);
            }
            foreach($paramData as $key => $val)
            {
                if($val > 0 && $key != 'search') $paramsStr .= self::$paramsAlias[$key].$val;
            }
        }

        if(!empty($paramsStr) && !empty($url)){
            $url = $url . $paramsStr;
        }
        
        $url = trim($url, '/');
        isset($paramData['search']) && $paramData['search'] != '' && $url .= '/_'.trim($paramData['search']);
        return  trim($url, '/');
    }

    public static function parseSearchUrl($url = '')
    {
        $pathInfo = Yii::$app->getRequest()->getPathInfo();
        $controller = Yii::$app->controller->id;
        $pathInfo = str_replace($controller. "/", "", $pathInfo);
        return self::params($pathInfo);

    }

    public static function params($pathInfo){
        $result = array('keywords' => '', 'condition' => '');
        $path = explode('/', $pathInfo);
        foreach ($path as $value)
        {
            if (preg_match("/^_/", $value))
            {
                $result['keywords'] = substr($value, 1);
                continue;
            }
            if (preg_match("/^[abcdefghijklmnopqrstuvwxyz][0-9]+/", $value))
            {
                $result['condition'] = $value;
                continue;
            }
        }
        if(isset($result['condition']))
        {
            $parnt = array();
            foreach(array_flip(self::$paramsAlias) as $alias => $rep)
            {
                $parnt[$alias] = ','.$rep.'=>';
            }
            $tempStr = strtr($result['condition'], $parnt);
            $tempArray = explode(',', $tempStr);
            $paramCloum = array();
            foreach($tempArray as $val)
            {
                if(! $val)  continue;
                list($param, $value) = explode('=>', $val);
                $paramCloum[$param] = (int)$value;
            }
            $result['condition'] = $paramCloum;
        }

        return $result;
    }


}