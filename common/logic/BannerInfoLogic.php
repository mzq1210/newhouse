<?php
/**
 * 从model类中获取banner信息
 * @Author: <lixiaobin>
 * @Date: 17-4-13
 */

namespace common\logic;

use common\models\webMgmt\BannerCategory;
use common\models\webMgmt\BannerInfo;
use Yii;

class BannerInfoLogic{
    
    /**
     * 根据站点类型、banner分类id、城市编码获取banner信息
     * @Params: Array $params
     *          Int $params['companyCode'] 城市公式编码
     *          Int $params['clientType'] 客户端类型0:Website 1:Mobilesite 2:App
     *          String $params['categoryKey'] 广告分类唯一码
     * @Params: string $fields 需要查询的字段
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-13
    */
    public static function selectBannerLogic($params, $fields = '*'){
        //获取广告分类ID
        $bannerCategoryInfo = BannerCategory::selectRecord($params['clientType'],$params['categoryKey'],'categoryID,width,height');
        if(!empty($bannerCategoryInfo)){
            //将广告分类ID作为调价 压入 $params数组中
            $params['categoryID'] = $bannerCategoryInfo['categoryID'];
            //unset不需要的条件
            unset($params['clientType'],$params['categoryKey']);
            $bannerInfo = BannerInfo::selectRecord($params,$fields);
            if(!empty($bannerInfo)){
                //设置广告图片的尺寸
                foreach ($bannerInfo as $val){
                    $val['width'] = $bannerCategoryInfo['width'];
                    $val['height'] = $bannerCategoryInfo['height'];
                    $val['bannerImageName'] = IMG_DOMAIN . $val['bannerImageName'];
                    $bannser[] = $val;
                }
                return $bannser;
            }
        }
        return false;
    }
    
}
