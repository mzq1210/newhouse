<?php
/**
 * 主题类别推荐楼盘表
 * @Author: <mzq>
 * @Date: 17-4-27
 */

namespace common\models\webMgmt;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class ThemeCompany extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_ThemeActivityCompany';
    }

    public static function tableDesc(){
        return '主题活动上线城市表';
    }

    /**
     * @inheritdoc
     * @return BaseQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find(){
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * @Author: <mzq>
     * @Date: 17-4-27
     * @param $activityID
     * @param $companyID
     * @return array|bool
     */
    public static function selectRecord($activityID, $companyID){
        $model =  self::find()->where(['themeActivityID' => $activityID, 'companyCode' =>$companyID])->active()->asArray()->one();
        if(!empty($model)){
            return $model;
        }
        return false;
    }
}