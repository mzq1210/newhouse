<?php
/**
 * 主题类别推荐楼盘表
 * @Author: <mzq>
 * @Date: 17-4-27
 */

namespace common\models\webMgmt;

use yii\db\ActiveRecord;

class ThemeRegistration extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_ThemeActivityRegistration';
    }

    public static function tableDesc(){
        return '主题活动报名表';
    }

    /**
     * @Author: <mzq>
     * @Date: 17-5-3
     * @param array $data
     * @return bool|mixed
     */
    public static function insertRecord($data = []){
        if (empty($data))
            return false;
        $model = new self();
        $model->setAttributes($data, false);
        if ($model->save())
            return $model->primaryKey;
        return false;
    }

    /**
     * @Author: <mzq>
     * @Date: 17-5-9
     * @param $activityEstateID
     * @return bool|mixed
     */
    public static function selectRecord($activityEstateID){
        return self::find()->where(['themeActivityEstateID' => $activityEstateID])->asArray()->one();
    }

    /**
     * @Author: <mzq>
     * @Date: 17-5-9
     * @param $params
     * @return bool|mixed
     */
    public static function selectDetailRecord($params){
        return self::find()->where(['themeActivityID' => $params['activityID'],'propertyTypeID' =>$params['propertyTypeID'], 'themeActivityEstateID' => $params['activityEstateID'], 'registrationCellphone' => $params['phone']])->asArray()->one();
    }
}