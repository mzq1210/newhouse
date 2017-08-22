<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-20
 * Time: 下午1:30
 */

namespace common\models\estate\extend;
use common\models\query\BaseQuery;
use yii\db\ActiveRecord;

class HouseType extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_HouseType';
    }

    public static function tableDesc() {
        return '楼盘户型表';
    }

    /**
     * @inheritdoc
     * @return BaseQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return \Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 获取户型信息
     * @Author: <liangshimao>
     * @Date: 2017-03-24
     * @Return: Array
     */
    public static function selectRecord($estateID,$propertyTypeID, $fields = '*',$isMajor = false){
        if(!is_numeric($estateID)) return false;
        $query = self::find()->select($fields)->where(['estateID' => $estateID])->property($propertyTypeID)->audit()->active();
        if($isMajor){
            $query->andWhere(['isMajor'=> 1]);
        }
        return $query->orderBy(['sortIndex'=> SORT_ASC])->asArray()->all();
    }

    /**
     * 获取户型详细信息
     * <liangshimao>
     */
    public static function getDetail($houseTypeID,$fields = '*')
    {
        return self::find()->select($fields)->where(['houseTypeID'=>$houseTypeID])->asArray()->one();
    }

    /**
     * 根据信息组合命令 格式如:3室2厅1卫
     */
    public static function makeTypeName($bed,$living,$rest,$cook,$houseTypeDescription = '')
    {
        $str = '';
        if(!empty($bed)){
            $str .= $bed.'室';
        }
        if(!empty($living)){
            $str .= $living.'厅';
        }
        if(!empty($rest)){
            $str .= $rest.'卫';
        }
        if(!empty($cook)){
            $str .= $cook.'厨';
        }
        if(!empty($str)){
            return $str;
        }else{
            return $houseTypeDescription;
        }
    }

    /**
     * 根据主力户型数组获取对应格式的字符串
     * @param $array
     * @param int $type 三种类型的格式
     * @param int $count 表示截取的主力户型的个数
     * 格式一:3居-90平
     * 格式二:3居室(120平米)
     * 格式三:3室2厅2卧室(约100平米)
     */
    public static function makeHousePattern($array,$type = 1,$count = false)
    {
        if(empty($array) || !is_array($array)){
            return false;
        }

        $res = [];
        //以下是处理从solr中取出的数据
        foreach ($array as $key=>$val){
            if($count && $key >= $count) break;
            switch ($type){
                case 1:
                    $res[] = !empty($val['bedRoomQuantity'])?($val['bedRoomQuantity'].'居室'):(!empty($val['livingRoomQuantity'])?($val['livingRoomQuantity'].'厅'):$val['houseTypeDescription']).'-'.ceil($val['buildArea']).'平';
                    break;
                case 2:
                    $res[] = !empty($val['bedRoomQuantity'])?($val['bedRoomQuantity'].'居室'):(!empty($val['livingRoomQuantity'])?($val['livingRoomQuantity'].'厅'):$val['houseTypeDescription']).'('.ceil($val['buildArea']).'平米)';
                    break;
                case 3:
                    $res[] = self::makeTypeName($val['bedRoomQuantity'],$val['livingRoomQuantity'],$val['restRoomQuantity'],$val['cookRoomQuantity'],$val['houseTypeDescription']) . '(约'. round($val['buildArea']) .'㎡)';
                    break;
            }
        }
        return $res;
    }

    /**
     * 从solr中取出的户型信息进行解析成正常的数组
     * @param $array
     * @return array|bool
     */
    public static function makeHouseArray($array)
    {
        if(empty($array) || !is_array($array)){
            return false;
        }

        $res = [];
        //以下是处理从solr中取出的数据
        foreach ($array as $key=>$val){
            $value = explode('/',$val);
            if(count($value) != 6) break;
            $res[] = [
                'bedRoomQuantity' => $value[0],
                'livingRoomQuantity' => $value[1],
                'restRoomQuantity' => $value[2],
                'cookRoomQuantity' => $value[3],
                'buildArea' => $value[4],
                'houseTypeDescription' => $value[5],
            ];
        }
        return $res;
    }
}