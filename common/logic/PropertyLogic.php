<?php
/**
 * 楼盘类型
 * @Author: <lixiaobin>
 * @Date: 17-3-22
 */

namespace common\logic;

use yii\helpers\Json;
use common\models\cp\Company;
use common\models\estate\config\Property;

class PropertyLogic{

    /**
     * 查询楼盘标签信息 pc
     * @Atuhro： <lixiaobin>
     * @Date: 2017-03-22
     * @Params: int $parentID 父类id来判定属于住宅还是商业
     * @Params: string $fields 查询的字段
     * @Return: Array
     */
    public static function selectProperLogic($parentID = 1, $fields = '*'){
        return Property::selectRecord($parentID, $fields);
    }

    /**
     * 根据当前城市是否启用商业导航 查询业态
     * @Params: int $parentID 父类id来判定属于住宅还是商业
     * @Params: int $companyCode 公司编码
     * @Params: string $fields 查询的字段
     * @Return: Array
     * @Atuhro： <lixiaobin>
     * @Date: 2017-03-22
     */
    public static function selectProperParentLogic($parentID = 0, $companyCode, $fields='*'){
        //获取当前城市下信息，根据isEnableCommercial判断是否开启商业导航
        $switchConfig = Company::selectRecordNav($companyCode, 'switchConfig');
        if(!empty($switchConfig)){
            //获取一级业态
            $parentParent = Property::selectRecord($parentID, $fields);
            $switchConfigArr = Json::decode($switchConfig['switchConfig']);
            $isEnableCommercial = $switchConfigArr['isEnableCommercial'];
            $results = '';
            if(!$isEnableCommercial){
                //循环剔除商铺和写字楼的业态
                foreach ($parentParent as $val){
                    if($val['propertyTypeID'] == ESTATE_PROPERTY_TYPE){
                        $results[] = $val;
                    }
                }
            }
            return $results ? $results : $parentParent;
        }
        return false;
    }

}
