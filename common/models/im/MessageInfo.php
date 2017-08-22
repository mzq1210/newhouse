<?php
/**
 * 客服系统记录用户聊天信息.
 * User: luohua
 * Date: 17-4-10
 * Time: 下午12:45
 */
namespace common\models\im;
use Yii;
use yii\db\ActiveRecord;

class MessageInfo extends ActiveRecord
{
    public static function tableName()
    {
        return 'IM_MessageInfo';
    }

    public static function tableDesc()
    {
        return '客户IM聊天消息表';
    }
    
    public static function insertRecord($data = []){
        if (empty($data))
            return false;
        $model = new self();
        $model->setAttributes($data, false);
        if ($model->save())
           return $model->primaryKey;
        return false;
    }

    public  static function deleteRecord($data =[]){
        if (empty($data))
            return false;
        $result = self::deleteAll($data);
        return $result;
    }


}