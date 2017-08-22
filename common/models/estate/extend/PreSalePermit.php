<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-20
 * Time: 下午1:30
 */

namespace common\models\estate\extend;
use yii\db\ActiveRecord;

class PreSalePermit extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_PreSalePermit';
    }

    public static function tableDesc() {
        return '楼盘预售许可证表';
    }
    
    /**
     * 获取预售许可证详情
     * <liangshimao>
     */
    public static function getDetail($id,$fields = '*')
    {
        return self::find()->select($fields)->where(['id'=>$id])->asArray()->one();
    }

    /**
     * 把solr中的预售许可证列表转换为数组
     * <liangshimao>
     */
    public static function makePermitArray($array)
    {
        if(empty($array) || !is_array($array)){
            return false;
        }
        $res = [];
        foreach ($array as $val){
            $value = explode('^|^',$val);
            if(count($value) != 3) break;
            $res[] = [
                'preSalePermitCode' => $value[0],
                'preSaleDate' => date('Y-m-d',strtotime($value[1])),
                'preSaleScope' => $value[2],
            ];
        }
        return $res;
    }
}