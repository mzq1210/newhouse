<?php
/**
 * 获取特色标签
 * @Author: <lixiaobin>
 * @Date: 17-3-22
 */

namespace common\logic;

use common\models\estate\config\Tag;

class TagLogic{

    /**
     * 查询楼盘标签信息 pc
     * @Atuhro： <lixiaobin>
     * @Date: 2017-03-22
     * @Params: int $companyCord 城市编码
     * @Params: string $fields 查询的字段
     * @Return: Array
    */
    public static function selectTagLogic($companyCord,$propertyTypeID = ESTATE_PROPERTY_TYPE, $fields = '*'){
        //判断$companyCord 是否为数组或者数字字符串
        if(is_numeric($companyCord)){
            return Tag::selectRecord($companyCord,$propertyTypeID, $fields);
        }
        return false;
    }


}