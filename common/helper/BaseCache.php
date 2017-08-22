<?php

/*
 * 基本类缓存
 * @author <liangpingzheng>
 * @date Mar 13, 2017 2:10:10 PM
 */

namespace common\helper;

use Yii;

class BaseCache {

    /**
     * 取缓存
     * @param $key
     */
    public static function get($key) {
        if (empty($key))
            return false;
        return Yii::$app->redis->get($key);
    }

    /**
     * 设置缓存
     */
    public static function set($key, $val, $expire = null) {

        return Yii::$app->redis->set($key, $val, $expire);
    }

    public static function delete($key) {

        return Yii::$app->redis->delete($key);
    }

    /**
     * BaseCache::hSet('h', 'key1', 'hello');
     * 向名称为h的hash中添加元素key1—>hello
     * @param string $key
     * @param string $field
     * @param string $value
     * @param int $expire
     * @return type
     */
    public static function hSet($key, $field, $value, $expire = null) {
        return Yii::$app->redis->hset($key, $field, $value, $expire);
    }

    /**
     * BaseCache::hGet('h', 'key1');
     * 返回名称为h的hash中key1对应的value（hello）
     * @param string $key
     * @param string $field
     * @return type
     * @author <liangpingzheng>
     */
    public static function hGet($key, $field) {
        return Yii::$app->redis->hget($key, $field);
    }

    /**
     * BaseCache::hMset('user:1', array('name' => 'Joe', 'salary' => 2000));
     * 向名称为key的hash中批量添加元素
     * @param type $key
     * @param array $arr
     * @param int $expire 缓存过期时间
     * @return type
     * @author <liangpingzheng>
     */
    public static function hMset($key, $arr, $expire = null) {
        return Yii::$app->redis->hmset($key, $arr, $expire);
    }

    /**
     * BaseCache::hmGet('h', array('field1', 'field2'));
     * 返回名称为h的hash中field1,field2对应的value
     * @param type $key 缓存Key
     * @param array $fieldArr hash缓存对应的字段数组
     * @return type
     * @author <liangpingzheng>
     */
    public static function hMget($key, $fieldArr) {
        return Yii::$app->redis->hmget($key, $fieldArr);
    }

    /**
     * BaseCache::hGetAll('h');
     * 返回名称为h的hash中所有的键（field）及其对应的value
     * @param string $key 缓存Key
     * @return array
     * @author <liangpingzheng>
     */
    public static function hGetAll($key) {
        return Yii::$app->redis->hgetall($key);
    }

    /**
     * BaseCache::setNx('h','value');
     * 返回key是否存在
     * @param string $key 缓存Key
     * @param string value 缓存值
     * @return boole
     * @author <liangpingzheng>
     */
    public static function setNx($key, $arr, $expire = null) {
        return Yii::$app->redis->setnx($key, $arr, $expire);
    }

    public static function flushDB() {
        return Yii::$app->redis->clear();
    }

    public static function keys($key) {
        return Yii::$app->redis->keys($key);
    }

    public static function delTree($tree) {
        $keys = self::keys($tree . '*');
        if ($keys) {
            if (is_array($keys)) {
                foreach ($keys as $key) {
                    self::delete(str_replace('fang_', '', $key));
                }
            } else {
                self::delete(str_replace('fang_', '', $key));
            }
        }
        return true;
    }

}
