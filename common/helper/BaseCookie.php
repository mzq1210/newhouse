<?php
/**
 * cookie缓存类
 * @Author: <lixiaobin>
 * @Date: 17-4-17
 */

namespace common\helper;

use Yii;
use yii\helpers\Json;

class BaseCookie{
    
    /**
     * 设置cookie
     * @Author: <lixiaobin>
     * @date: 2017-04-18
     * @Params: string $key Cookie的键名
     * @Params: string $value Cookie的键值
     * @Params: int $expire Cookie生命周期
     * @Params： string $domain Cookie存储域名
    */
    public static function set($key, $value,$expire = COOKIE_TIMEOUT, $domain = SUBDOMAIN,$type = false){
        if($expire > 0){
            $expire = time() + $expire;
        }elseif($expire < 0){
            $expire = time() - 1;
        }else{
            $expire = 0;
        }
        if($domain == SUBDOMAIN){
            return setcookie($key,$value,$expire,'/', SUBDOMAIN);
        }elseif($domain == WAPDOMAIN){
            if(empty($type)){
                return setcookie($key, $value, $expire, '/', '', $secure = false, $httponly = true);
            }else{
                return setcookie($key, $value, $expire, '/', WAPDOMAIN);
            }
        }else{
            return setcookie($key,$value,$expire,'/', $domain);
        }
        
    }

    public static function get($key){
        if(isset($_COOKIE[$key])){
            $info = $_COOKIE[$key];
            if(preg_match('/{.*}/', $info)){
                return Json::decode($info);
            }
            return $info;
        }else{
            return false;
        }
    }

    /**
     * 删除Cookie
    */
    public static function remove($key,$domain = DOMAIN){
        return self::set($key, '', '-1', $domain);
    }

    /**
     * 获取cookie
     */
    public static function getCookie($key) {
        
        $cookies = Yii::$app->request->cookies;
        if(($cookie = $cookies->get($key)) !== null) {
            return $cookie->value;
        }
        return null;
    }

    /**
     * 设置cookie
     */
    public static function setCookie($key, $value = '', $time = COOKIE_TIMEOUT, $domain = '') {
        if($time > 0){
            $time = time()+$time;
        }elseif($time < 0){
            $time = time()-1;
        }else{
            $time = 0;
        }
        $domain = !empty($domain) ? DOMAIN : '';
        $cookies = Yii::$app->response->cookies;
        $cookies->add(new \yii\web\Cookie([
            'name' => $key,
            'value' => $value,
            'expire' => $time,
            'domain' => $domain
        ]));
        return true;
    }

    /**
     *
    */
    public static function removeCookie($key = ''){
        if(!empty($key)){
            return Yii::$app->response->getCookies()->remove($key);
        }

    }


}