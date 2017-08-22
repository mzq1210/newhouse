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

class ActivityEstate extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_ThemeActivityEstate';
    }

    public static function tableDesc(){
        return '主题活动楼盘表';
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
     * @param $themeActivityCompanyID
     * @return array|bool
     */
    public static function selectRecord($themeActivityCompanyID){
        $dateTime = date('Y-m-d H:i:s');
        $field = 'themeActivityEstateID,themeActivityCompanyID, themeActivityID,estateID,propertyTypeID,beginDate,endDate,estateImageName,themeActivityName,
        isEnableTag,tag,recommandedReason,summary,';
        $model =  self::find()->select($field)->where(['themeActivityCompanyID' =>$themeActivityCompanyID])
            ->andWhere(['<','beginDate',$dateTime])
            ->andWhere(['>','endDate',$dateTime])
            ->active()->orderBy('sortIndex ASC')->asArray()->all();
        if(!empty($model)){
            return ['results' => $model];
        }
        return false;
    }


    /**
     * @Author: <mzq>
     * @Date: 17-5-9
     * @param $params
     * @return bool|mixed
     */
    public static function selectDetailRecord($params){
        return self::find()->where(['themeActivityID' => $params['activityID'], 'themeActivityEstateID' => $params['activityEstateID']])->asArray()->one();
    }

    /**
     * @desc 获取活动楼盘某业态详情
     * @Author: <mzq>
     * @Date: 17-4-27
     * @param $params
     * @return array|bool
     */
    public static function selectInfoRecord($params){
        $dateTime = date('Y-m-d H:i:s');
        $info = self::find()
            ->where(['estateID' => $params['estateID'], 'propertyTypeID' => $params['propertyTypeID']])
            ->andWhere(['<','beginDate',$dateTime])
            ->andWhere(['>','endDate',$dateTime])
            ->active()
            ->asArray()
            ->one();
        if(!empty($info)){
            return ['results' => $info];
        }
        return false;
    }

    /**
     * @desc 获取活动楼盘下有哪些类型
     * @Author: <mzq>
     * @Date: 17-4-27
     * @param $params
     * @return array|bool
     */
    public static function selectAllRecord($params){
        $dateTime = date('Y-m-d H:i:s');
        $info = self::find()
            ->where(['estateID' => $params['estateID']])
            ->andWhere(['<','beginDate',$dateTime])
            ->andWhere(['>','endDate',$dateTime])
            ->active()
            ->asArray()
            ->all();
        if(!empty($info)){
            return ['results' => $info];
        }
        return false;
    }

}