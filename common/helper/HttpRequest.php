<?php

/*
 * http请求接口专用类  示例 HttpRequest::send('get','/getCity',['id=>28]);
 * @author <liangpingzheng>
 * @date Apr 25, 2017 1:46:00 PM
 */

namespace common\helper;

use common\helper\Curl;

class HttpRequest {

    public static function send($method = 'get', $url, $params=[]) {
        $curl = Curl::getInstance();
        switch ($method) {
            case 'get':
                $ret = $curl->get($url, $params);
                break;
            case 'post':
                $ret = $curl->post($url, $params);
                break;
            case 'delete':
                $ret = $curl->delete($url, $params);
                break;
            default :
                $ret = $curl->get($url, $params);
        }
        return $ret;
    }
}