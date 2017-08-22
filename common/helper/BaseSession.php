<?php
/**
 * session存储公共类
 * @Author: <lixiaobin>
 * @Date: 17-7-7
 */

namespace common\helper;
use Yii;

class BaseSession{

    /**
     * 生成用户唯一KEY
    */
    public static function getSessionUserId() {
        $userIP = self::getClientIP();
        $userIP = str_replace('.', '_', $userIP);
        $time = strtotime(date('Y-m-d H', time()).':00:00');
        $key = $userIP . '_'. $time;
        return $key;
    }

    /**
     * 获取客户端IP
     */
    public static function getClientIP() {
        if(isset($_SERVER['HTTP_CDN_SRC_IP'])) {
            return $_SERVER['HTTP_CDN_SRC_IP'];
        } else {
            return Yii::$app->request->userIP;
        }
    }

    /**
     * 获取session
     */
    public static function getLocalSession($key) {
        return isset($_SESSION[$key])?$_SESSION[$key]:[];
    }

    /**
     * 设置session
     */
    public static function setLocalSession($key, $value) {
        $_SESSION[$key] = $value;
        return true;
    }


    /**
     * 获取session Yii2 自带
     */
    public static function getSession($key) {
        $sessions = Yii::$app->session;
        return $sessions->get($key);
    }

    /**
     * 设置session Yii2 自带
     */
    public static function setSession($key, $value, $time = SESSION_TIMEOUT) {
        $sessions = Yii::$app->session;
        $sessions->timeout = $time;
        $sessions->set($key, $value);
        return true;
    }


    /**
     * 销毁session Yii2 自带
     */
    public static function removeSession($key = '') {
        $sessions = Yii::$app->session;
        if(!$key) return $sessions->removeAll();
        $sessions->remove($key);
        return true;
    }

}
