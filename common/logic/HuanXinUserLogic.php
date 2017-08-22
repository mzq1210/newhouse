<?php
/**
 * 环信用户分配操作逻辑层
 * User: luohua
 * Date: 17-4-6
 * Time: 下午2:14
 */
namespace common\logic;
use common\models\im\HuanXinUser;
use common\components\HuanXinUsers;

class HuanXinUserLogic
{
    public static function IsAssigned($condition = []){
        $result = self::selectOne($condition);
        if (empty($result))
            return true;
        return false;
    }

    public static function updateRecord($condition = []){
        
    }
    
    public static function assigned($condition){
        $user = HuanXinUser::findOne(['isAssigned' =>0]);
        if (empty($user)){
            $huanXinUser = new HuanXinUsers();
            $huanXinUser->actionBatchRegister();
            $user = HuanXinUser::findOne(['isAssigned' =>0]);
        }
        $user->setAttributes($condition, false);
        if ($user->save());
            return $user;
        return false;
    }
    
    public static function selectOne($condition = []){
        $result = HuanXinUser::selectOne($condition);
        return $result;
    }
    
}