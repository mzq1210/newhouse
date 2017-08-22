<?php
/**
 * 地图搜索类
 * @Author: <mzq>
 * @Date: 17-3-27
 */

namespace common\logic;

use Yii;
use common\helper\pinyin\YPinYin;
use common\models\estate\config\Area;

class AreaLogic{

    /**
     * 查询城市区域信息 pc
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     * @params int $comporyCode 城市编码
     * @Return array
     */
    public static function selectAreaLogic($companyCode, $fields = '*'){
        if(is_numeric($companyCode)){
            $areaArr = Area::selectRecord($companyCode,$fields);
            if(empty($areaArr)) return false;
            $areasArr = self::recombineAreaArr($areaArr);
            //将children重新组合数组 键名为商圈名称首个汉字首字母
            foreach ($areasArr as $val){
                if(isset($val['children']) && $val['children']){
                    foreach ($val['children'] as $v){
                        $vKey = YPinYin::getFirstCharter($v['estateAreaName']);
                        $val['childrens'][$vKey][] = $v;
                    }
                    unset($val['children']);
                    ksort($val['childrens'],SORT_ASC);
                }else{
                    $val['childrens'] = '';
                }
                $areasArrs[] = $val;
            }
            return $areasArrs;
        }
        return false;
    }

    /**
     * 将查询城市区域数据 递归为一个多为数组 pc
     * @Author: <lixiaobin>
     * @Date: 2017-03-22
     * @Params array $areaArr 城市区域数据
     * @Return array
     */
    private static function recombineAreaArr($areaArr, $parentID = 0){
        $tree = array();
        //每次都声明一个新数组用来放子元素
        foreach($areaArr as $v){
            //匹配子记录
            if($v['parentID'] == $parentID){
                //递归获取子记录
                $v['children'] = self::recombineAreaArr($areaArr,$v['estateAreaID']);
                //将记录存入新数组
                $tree[] = $v;
            }
        }
        return $tree;
    }

    /**
     * 根据城市编码 和 区域或商圈ID 获取区域和商圈名称
     * @Author: <lixiaobin>
     * @Date: 2017-03-27
     * @Params: int $companyCode 当前城市编码
     * @Params: int $estateAreaId 当前线路或者当前站ID
     * @Params: String $fields 需要查询的字符串
     * @Return: Array
     * @EditDate: 2017-05-16 将返回字符串改为数组
     */
    public static function selectAreaNameLogic($companyCode, $estateAreaID, $fields='*'){
        if(is_numeric($companyCode) && is_numeric($estateAreaID)){
            $info = Area::selectEstateNameRecord($companyCode, $estateAreaID, $fields);
            if(empty($info)) return false;
            $parentInfo = ['estateAreaID' => '', 'estateAreaName' => ''];
            if(!empty($info['parentID'])){
                $parentInfo = Area::selectEstateNameRecord($companyCode, $info['parentID'], $fields);
                $list['parentArea'] = $parentInfo;
                $list['childrenArea'] = $info;
            }else{
                $list['parentArea'] = $info;
                $list['childrenArea'] = $parentInfo;
            }
            return $list;
        }
        return false;
    }
    
    /**
     * 根据楼盘区域id查询区域信息
     * @Author: <wangluoha>
     * @Params: int $estateAreaId 
     */
    
    public static function selectAreaInfoLogic($estateAreaId){
        if (empty($estateAreaId))
            return false;
        return Area::selectAreaInfo($estateAreaId);
    }

}