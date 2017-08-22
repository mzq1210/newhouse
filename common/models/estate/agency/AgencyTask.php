<?php
/**
 * 外部经纪人的楼盘带看记录信息
 * <liangshimao>
 */

namespace common\models\estate\agency;

use Yii;
use yii\data\Pagination;
use yii\db\ActiveRecord;
use common\models\query\BaseQuery;

class AgencyTask extends ActiveRecord
{
    public static function tableName() {
        return 'External_AgencyTask';
    }

    public static function tableDesc() {
        return '外部经纪人的楼盘带看记录信息';
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
     * 获取经纪人带看信息
     * @Author: <liangshimao>
     * @Date: 2017-03-29
     * @Return: Array
     */
    public static function selectRecord($estateID,$pageSize,$page,$fields = '*'){
        $data = self::find()->select($fields)->where(['estateID' => $estateID])->orderBy(['taskDate'=>SORT_DESC]);
        $totalCount = $data->count();
        $pageCount = ceil($totalCount/$pageSize);
        if($page < 1){
            $page = 1;
        }
        $list = $data->offset(($page-1)*$pageSize)->limit($pageSize)->asArray()->all();
        return [
            'data'=>$list,
            'curPage' => $page,
            'pageCount' => $pageCount,
            'count' => $totalCount,
        ];
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