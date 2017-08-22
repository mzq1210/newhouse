<?php
/**
 * 环信用户.
 * User: luohua
 * Date: 17-3-31
 * Time: 下午12:45
 */
namespace common\models\im;
use Yii;
use yii\db\ActiveRecord;

class HuanXinUser extends ActiveRecord
{
    public static function tableName()
    {
        return 'IM_HuanXinUser';
    }

    public static function tableDesc()
    {
        return '环信用户表';
    }

    public static function insertRecord($field=[], $data=[]){
        $result = Yii::$app->db->createCommand()->batchInsert(self::tableName(), $field, $data)->execute();
        return $result;
    }

    public static function countNum($condition = []){
        $num = self::find()->where($condition)->count();
        return $num;
    }
    
    public static function selectOne($condition = []){
        $result = self::findOne($condition);
        return $result;
    }
    
    public static function selectAll($condition = []){
        $result = self::find()->where($condition)->asArray()->all();
        return $result;
    }
    
}