<?php

/**
 * Created by PhpStorm.
 * User: smile
 * Date: 16-9-18
 * Time: 下午3:38
 */

namespace common\components;

use yii\base\Component;
use yii\helpers\Json;
use common\helper\BaseCookie;

class Tools extends Component {

    /**
     * 字符串加密、解密函数
     * @param	string	$txt		字符串
     * @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
     * @param	string	$key		密钥：数字、字母、下划线
     * @param	string	$expiry		过期时间
     * @return	string
     */
    static function sysAuth($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
        $ckey_length = 4;
        $key = md5($key != '' ? $key : 'hugain');
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(strtr(substr($string, $ckey_length), '-_', '+/')) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . rtrim(strtr(base64_encode($result), '+/', '-_'), '=');
        }
    }

    /*
     * 中文截取字符串
     * 第三个参数是false，不加...
     */

    public static function cutUtf8($string, $length, $etc = '...') {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strlen) {
            $result .= $etc;
        }
        return $result;
    }

    /**
     * 时间转为为几天前,几分钟前等数据
     * @param $the_time
     * @return string
     */
    public static function time_tran($the_time) {
        $now_time = date("Y-m-d H:i:s", time());
        //echo $now_time;
        $now_time = strtotime($now_time);
        $show_time = strtotime($the_time);
        $dur = $now_time - $show_time;
        if ($dur < 0) {
            return date('Y-m-d H:i', strtotime($the_time));
        } else {
            if ($dur < 60) {
                return $dur . '秒前';
            } else {
                if ($dur < 3600) {
                    return floor($dur / 60) . '分钟前';
                } else {
                    if ($dur < 86400) {
                        return floor($dur / 3600) . '小时前';
                    } else {
                        if ($dur < 259200) {//3天内
                            return floor($dur / 86400) . '天前';
                        } else {
                            return date('Y-m-d', strtotime($the_time));
                        }
                    }
                }
            }
        }
    }

    public static function time_today($the_time) {
        $today = strtotime(date("Y-m-d"));
        $now_time = strtotime($the_time);
        $dur_time = $now_time - $today;
        if ($dur_time > 0 && $dur_time < 60 * 60 * 24) {
            return date('H:i', strtotime($the_time));
        } else if($dur_time < 0 && $dur_time > -60*60*24){
            return '1天前';
        } else if($dur_time < -60*60*24 && $dur_time > -60*60*48){
            return '2天前';
        }else{
            return date('Y-m-d', strtotime($the_time));
        }
    }

    /**
     * 存储历史记录到cookie
     * @Author: <lixiaobin>
     * @Date: 2017-05-03
     * @Params: array $params 数组参数：key 存储cookie存储的键名，id、name、propertyID
     * @Return: boole
     */
    public static function historyCookie($params,$type = '') {

        //获取wap网站缓存
        if (!empty($params)) {
            $key = $params['key'];
            //删除key键，方便下面数组追加
            unset($params['key']);
            $history = BaseCookie::get($key);
            $history = !empty($history) ? $history : [];
            $true = '';
            if(!empty($history)){
                //如果有名称重复的则不添加
                foreach ($history as $val) {
                    if ($val['name'] == $params['name']) {
                        $true = true;
                    }
                }
            }

            if (empty($true)) {
                if (count($history) >= 10) {
                    array_pop($history);
                }
                array_unshift($history, $params);
                BaseCookie::set($key, Json::encode($history), COOKIE_TIMEOUT * 10, $params['domain'],$type);
                return true;
            }
        }
        return false;
    }

    /**
     * 根据尺寸返回图片的缩略图
     * @param string $url 原图片网络访问地址
     * @param int $w 缩略图宽度
     * @param int $h 缩略图高度
     * @author <liangpingzheng>
     * @date 2017-05-10
     * @return string 缩略图片地址
     * http://image-demo.img-cn-hangzhou.aliyuncs.com/example.jpg?x-oss-process=image/resize,w_400/watermark,image_cGFuZGEucG5nP3gtb3NzLXByb2Nlc3M9aW1hZ2UvcmVzaXplLFBfMzA,t_90,g_se,x_10,y_10

     */
    public static function thumb($url, $w = 120, $h = 120, $watermark = FALSE) {
        $url = $url . "?x-oss-process=image/resize,m_fill," . 'h_' . $h . ',w_' . $w;
        $watermark_url = base64_encode("water_5i5j.png");
        if ($watermark) {
            $url = $url . "/watermark,image_" . $watermark_url . ",t_100,g_center,x_10,y_10";
        }
        return $url;
    }

    public static function getClientIP() {
        $ip = '';
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "Unknow";
        return $ip;
    }

    /**
     * CURL Post 发送josn数据方法
     * @Params: $url
     * @Params: $params json格式数据
     * @Return: json
     * @Author: <lixiaobin>
     * @Date: 2017-08-15
    */
    public static function curlPostJson($url,$params,$type = false){
        //$type 为true 需要将参数添加到header中
        if(!empty($type)){
            $header = ['token:'. $params['token']];
            $content = ['service' => $params['service']];
            if(isset($params['ST'])){
                $content = array_merge($content,['ST' => $params['ST']]);
            }
            $ch = curl_init();
            if(substr($url,0,5)=='https'){
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
            $response = curl_exec($ch);
            if($error=curl_error($ch)){
                die($error);
            }
            curl_close($ch);
            return $response;
        }else{
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($params))
            );
            return curl_exec($ch);
        }

    }

}
