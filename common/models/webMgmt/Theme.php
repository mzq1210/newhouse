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

class Theme extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_ThemeActivity';
    }

    public static function tableDesc(){
        return '主题类别表';
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
     * @return array|bool
     */
    public static function selectRecord($activityID){
        $field = [
            'themeActivityID',
            'themeActivityName',
            'beginDate',
            'endDate',
            'themeActivityImageName',
            'mobileThemeActivityImageName',
            'themeActivityBgColor',
            'mobileThemeActivityBgColor',
            'auditStatus'
        ];
        $companyField = [
            "themeActivityCompanyID",
            "themeActivityID",
            "companyCode",
            "companyName",
            "estateActivitySummary",
            "isEnablePhoneRegistration",
            "registrationPhoneNumber",
            "isEnable400Phone",
            "registrationBranchNumber",
            "isActive"
        ];
        $model = self::find()->select($field)->where(['themeActivityID'=> $activityID])->active()->asArray()->one();
        if(!empty($model)){
            $model['themeActivityImageName'] = empty($model['themeActivityImageName']) ? '' : IMG_DOMAIN . $model['themeActivityImageName'];
            $model['mobileThemeActivityImageName'] = empty($model['mobileThemeActivityImageName']) ? '' : IMG_DOMAIN . $model['mobileThemeActivityImageName'];
            $model['activityCompany'] = ThemeCompany::find()->select($companyField)->where(['themeActivityID'=> $activityID])->active()->asArray()->all();
            return ['results' => $model];
        }
        return false;
    }
}