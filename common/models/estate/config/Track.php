<?php
/**
 * 轨道交通信息model类
 * <liangshimao>
 */

namespace common\models\estate\config;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class Track extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_TrafficInfo';
    }

    public static function tableDesc() {
        return '轨道交通';
    }
    /**
     * @inheritdoc
     * @return BaseQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 获取当前城市的地铁信息
     * @Author <lixiaobin>
     * @Date 2017-03-21
     * @Params int $companyCode 城市编码
     * @Return array
     */
    public static function selectRecord($companyCode, $fields = '*'){
        if(is_numeric($companyCode)){
            return self::find()->select($fields)->where(['companyCode' => $companyCode])->active()->orderBy('sortIndex ASC, trafficID ASC' )->asArray()->all();
        }
        return false;
    }

    /**
     * 获取当前城市的地名称
     * @Author <lixiaobin>
     * @Date 2017-03-26
     * @Params int $companyCode 城市编码
     * @Return array
     */
    public static function selectTrafficNameRecord($companyCode, $trafficId, $fields = '*'){
        if(is_numeric($companyCode)){
            return self::find()->select($fields)->where(['companyCode' => $companyCode,'trafficID' => $trafficId])->active()->orderBy('sortIndex ASC' )->asArray()->one();
        }
        return false;
    }

    /**
     * 根据solr中存储的trafficID拼接成固定格式:如K1-东湖公园站,
     * 两种格式分别为类型1:K1-东湖公园站 类型2:K1(东湖公园站)
     * <liangshimao>
     */
    public static function makeString($array = [] ,$type = 1)
    {

        if(empty($array)){
            return '';
        }
        $res = [];
        foreach ($array as $value){
            if($type == '1'){
                $res[] = $value['tail'].'-'.$value['station'];
            }else{
                $res[] = $value['tail'].'('.$value['station'].')';
            }
        }
        return join('  ',$res);
    }

    /**
     * 根据solr中存储的trafficID拼接成固定格式的数组
     * @param $array
     * @return string
     */
    public static function makeTrafficArray($array)
    {
        $info = self::find()->select('trafficID,trafficName,parentID')->where(['in','trafficID',$array])->asArray()->all();
        if(empty($info)){
            return [];
        }
        $res = [];
        foreach ($info as $value){
            if($value['parentID'] == '0'){
                foreach ($info as $val){
                    if($val['parentID'] == $value['trafficID']){
                        $res[] = [
                            'tail' => $value['trafficName'],
                            'station' => $val['trafficName']
                        ];
                    }
                }
            }
        }
        return $res;
    }
}