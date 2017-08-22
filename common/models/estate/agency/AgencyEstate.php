<?php
/**
 * 外部经纪人与楼盘关系信息
 * <liangshimao>
 */

namespace common\models\estate\agency;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class AgencyEstate extends ActiveRecord
{
    public static function tableName() {
        return 'External_AgencyEstateRelation';
    }

    public static function tableDesc() {
        return '外部经纪人与楼盘关系信息';
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
     * 获取推荐经纪人信息
     * @Author: <liangshimao>
     * @Date: 2017-03-29
     * @Return: Array
     */
    public static function selectRecord($estateID, $fields = '*'){
        if(!is_numeric($estateID)) return false;
        return self::find()->select($fields)->where(['estateID' => $estateID,'isSuperStar' => 0])->orderBy(['sortIndex'=>SORT_ASC])->asArray()->all();
    }

    /**
     * 获取明星经纪人信息
     * <liangshimao>
     */
    public static function selectSuperRecord($estateID,$fields = '*')
    {
        if(!is_numeric($estateID)) return false;
        return self::find()->select($fields)->where(['estateID' => $estateID,'isSuperStar' => 1])->asArray()->all();
    }

    /**
     * 关联经纪人表
     * <liangshimao>
     */
    public function getAgency()
    {
        return $this->hasOne(AgencyInfo::className(),['agencyID' => 'agencyID']);
    }

}