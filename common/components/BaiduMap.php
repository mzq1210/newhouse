<?php
/**
 * Baidu Map Api for PHP Class
 *
 * @author Instrye <instrye@gmail.com>
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link web api desc ,http://lbsyun.baidu.com/index.php?title=webapi
 * @since 0.1
 */
namespace common\components;

use Behat\Gherkin\Exception;

class BaiduMap {

    private  $ak = 'QIYfhmChNF1rAg2GbuG4CrZ0';	//服务核心,替换成自己的AK http://lbsyun.baidu.com/apiconsole/key?application=key
    private  $method = 'GET';
    public   $output = 'json'; //json or xml
    private  $coord = 'bd09ll';//为空是百度墨卡托坐标,bd09ll 是百度经纬度坐标
    private  $url = 'https://api.map.baidu.com/';

    /**
     * 请确保正确安装cURL扩展
     */
    public function __construct(){
        if (!function_exists('curl_init')) {
            throw new Exception('OpenAPI needs the cURL PHP extension.');
        }
    }

    /**
     * POI数据
     * @param  integer $type       [0:默认通过城市检索][1:通过坐标圆点&半径检索][2:通过矩形框检索]
     * @param  string  $query      [关键词][多个关键词用'$'隔开]
     * @param  string  $region     城市
     * @param  string  $location   [lat,lng][38.76623,116.43213]
     * @param  integer $radius     半径
     * @param  string  $bounds    [lat,lng(左下角坐标),lat,lng(右上角坐标)] [38.76623,116.43213,39.54321,116.46773]
     * @param  integer $scope      [1:基本][2:详细]
     * @param  integer $page_size  [每页展示条数][max=20*count($query)]
     * @param  integer $page_num   [页数]
     * @param  string  $tag        [标签项][用','分割] http://lbsyun.baidu.com/index.php?title=lbscloud/poitags
     * @param  string  $filter     过滤条件[http://lbsyun.baidu.com/index.php?title=webapi/guide/webservice-placeapi]
     * @param  integer $coord_type [1:wgs84ll即GPS经纬度][2:gcj02ll即国测局经纬度坐标][3:bd09ll即百度经纬度坐标][4:bd09mc即百度米制坐标]
     * @link http://lbsyun.baidu.com/index.php?title=webapi/guide/webservice-placeapi
     * @return mixed
     */
    public function place($type = 0,$query = '',$region = '',$location = '',$radius = 0,$bounds = '',$scope = 2,$page_size = 10,$page_num = 0,$tag = '',$filter = '',$coord_type = 3){
        switch ($type) {
            case 0:
                $params['region'] = $region;
                break;
            case 1:
                $params['location'] = $location;
                $params['radius'] = $radius;
                break;
            case 2:
                $params['bounds'] = $bounds;
                break;
            default:
                $params['region'] = $region;
        }
        $params['query'] = $query;
        $params['scope'] = $scope;
        $params['page_size'] = $page_size;
        $params['page_num'] = $page_num;
        $params['tag'] = $tag;
        $params['filter'] = $filter;
        $params['coord_type'] = $coord_type;
        $params['output'] = $this->output;
        return $this->_sendHttp('place/v2/search',$params);
    }

    /**
     * 普通IP定位
     * @param  string $ip ip
     * @link http://lbsyun.baidu.com/index.php?title=webapi/ip-api
     * @return mixed
     */
    public function locationip($ip = '127.0.0.1'){
        $params['ip'] = $ip;
        $params['coor'] = $this->coord;
        $params['ak'] = $this->ak;
        return $this->_sendHttp('location/ip',$params);
    }

    /**
     * 高精度IP定位
     * @param  string  $ip         ip
     * @param  string  $qterm      pc|mb
     * @param  integer $extensions 1|2|3
     * @link http://lbsyun.baidu.com/index.php?title=webapi/high-acc-ip
     * @return mixed
     */
    public function highacciploc($ip = '127.0.0.1',$qterm = 'pc',$extensions = 3){
        $params['qcip'] = $ip;
        $params['qterm'] = $qterm;
        $params['extensions'] = $extensions;
        $params['coord'] = $this->coord;
        return $this->_sendHttp('highacciploc/v1',$params);
    }

    /**
     * 生成URL
     * @param  string $uri
     * @param  array $params
     * @return mixed
     */
    private function _sendHttp($uri,$params){
        if($this->method === 'GET'){
            $url = $this->url . $uri . '?ak=' . $this->ak;
            unset($params['ak']);
            foreach ($params as $key => $v) {
                $url .="&{$key}=" . urlencode($v);
            }
            $data = $this->_curl($url);
        } else {
            $url = urlencode($this->url . $uri);
            $data = $this->_curl($url,$params);
        }
        return $data;
    }

    /**
     * 生成发送HTTP请求
     * @param  string $url
     * @param  array $postData
     * @return mixed
     */
    private function _curl($url,$postData = NULL){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //https请求
        if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        if(is_array($postData) && 0 < count($postData)){
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}
