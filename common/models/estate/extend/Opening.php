<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-20
 * Time: 下午1:29
 */

namespace common\models\estate\extend;
use yii\db\ActiveRecord;

class Opening extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_Opening';
    }

    public static function tableDesc() {
        return '楼盘开盘信息表';
    }

    /**
     * 把solr中的开盘信息转换为数组
     * <liangshimao>
     */
    public static function makeOpeningArray($array)
    {
        if(empty($array) || !is_array($array)){
            return false;
        }
        $res = [];
        foreach ($array as $val){
            $value = explode('^|^',$val);
            if(count($value) != 3) break;
            $res[] = [
                'openingDate' => !empty($value[0])?$value[0]:date('Y-m-d',strtotime($value[1])),
                'openingDetail' => $value[2],
            ];
        }
        return $res;
    }
}