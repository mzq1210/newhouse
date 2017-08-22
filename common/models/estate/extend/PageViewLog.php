<?php
/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-3-20
 * Time: 下午1:33
 */

namespace common\models\estate\extend;
use yii\db\ActiveRecord;

class PageViewLog extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_PageViewLog';
    }

    public static function tableDesc() {
        return '楼盘浏览量';
    }

    public static function insertRecord($estateID,$clientType,$companyCode)
    {
        $model = new self();
        $model->setAttributes([
            'estateID' => $estateID,
            'clientType' => $clientType,
            'companyCode' => $companyCode,
            'inDate' => date('Y-m-d H:i:s'),
            'lastEditDate' => date('Y-m-d H:i:s'),
        ],false);
        if($model->save()){
            return true;
        }
        return false;
    }
}