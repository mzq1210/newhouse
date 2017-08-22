<?php
/**
 * 获取环线信息
 * @Author: <lixiaobin>
 * @Date: 17-3-22
 */

namespace common\logic;

use common\models\estate\config\Ring;

class RingLogic{

    /**
     * 查询楼盘环线信息 pc
     * @Atuhro： <lixiaobin>
     * @Date: 2017-03-22
     * @Params: int $companyCord 城市编码
     * @Params: string $fields 查询的字段
     * @Return: Array
     */
    public static function selectRingLogic($companyCode, $fields = '*'){
        if(is_numeric($companyCode)){
            return Ring::selectRecord($companyCode, $fields);
        }
        return false;
    }
    
}