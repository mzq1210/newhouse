<?php
/**
 * 城市区域类
 * @Author: <lixiaobin>
 * @Date: 17-3-22
 */

namespace common\logic;

use Yii;
use common\helper\pinyin\YPinYin;
use common\models\estate\config\Track;

class TrackLogic{

    /**
     * 获取当前城市下的地铁信息 pc
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     * @Params: int $companyCord 城市编码
     * @Params: string $fields 查询的字段
     * @Return: Array
    */
    public static function selectTrackLogic($companyCode, $fields='*'){
        if(is_numeric($companyCode)){
            $trackArr = Track::selectRecord($companyCode, $fields);
            if(empty($trackArr)) return false;
            $tracksArr = self::recombineTrackArr($trackArr);
            //将children重新组合数组 键名为商圈名称首个汉字首字母
            foreach ($tracksArr as $val){
                if(isset($val['children']) && $val['children']){
                    foreach ($val['children'] as $v){
                        $vKey = YPinYin::getFirstCharter($v['trafficName']);
                        $val['childrens'][$vKey][] = $v;
                    }
                    unset($val['children']);
                    ksort($val['childrens'],SORT_ASC);
                }else{
                    $val['childrens'] = [];
                }
                $tracksArrs[] = $val;
            }
            return $tracksArrs;
        }
        return false;
    }


    /**
     * 将查询城市区域数据 递归为一个多为数组 pc
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     * @params array $areaArr 城市区域数据
     * @Return array
     */
    private static function recombineTrackArr($trackArr, $parentID = 0){
        $tree = array();
        //每次都声明一个新数组用来放子元素
        foreach($trackArr as $v){
            //匹配子记录
            if($v['parentID'] == $parentID){
                //递归获取子记录
                $v['children'] = self::recombineTrackArr($trackArr,$v['trafficID']);
                //将记录存入新数组
                $tree[] = $v;
            }
        }
        return $tree;
    }

    /**
     * 根据城市编码 和 地铁线路id 或者 地铁站ID 获取地铁线路和地铁站点名称
     * @Author: <lixiaobin>
     * @Date: 2017-03-27
     * @Params: int $companyCode 当前城市编码
     * @Params: int $trafficID 当前线路或者当前站ID
     * @Params: String $fields 需要查询的字符串
     * @Return: String 如:海淀区-中关村
     */
    public static function selectTrackNameLogic($companyCode, $trafficID, $fields='*'){
        if(is_numeric($companyCode) && is_numeric($trafficID)){
            $info = Track::selectTrafficNameRecord($companyCode, $trafficID, $fields);
            if(empty($info)) return false;
            $parentInfo = ['trafficID' => '', 'trafficName' => ''];
            if(!empty($info['parentID'])){
                $parentInfo = Track::selectTrafficNameRecord($companyCode, $info['parentID'], $fields);
                $list['parentTraffic'] = $parentInfo;
                $list['childrenTraffic'] = $info;
            }else{
                $list['parentTraffic'] = $info;
                $list['childrenTraffic'] = $parentInfo;
            }
            return $list;
        }
        return false;
    }
}