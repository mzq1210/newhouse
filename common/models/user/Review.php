<?php
/**
 * 用户评价表model类
 * User: <liangshimao>
 * Date: 17-4-5
 * Time: 下午5:26
 */

namespace common\models\user;
use common\models\query\BaseQuery;
use yii\data\Pagination;
use yii\db\ActiveRecord;

class Review extends ActiveRecord
{
    public static function tableName() {
        return 'User_Review';
    }

    public static function tableDesc() {
        return '注册用户点评表';
    }

    public static function find()
    {
        return \Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    public static function selectRecord($estateID,$pageSize,$page,$fields='*')
    {
        $data = self::find()->select($fields)->where(['estateID' => $estateID])->active()->audit()->orderBy(['inDate'=>SORT_DESC]);
        $totalCount = intval($data->count());
        $pageCount = ceil($totalCount/$pageSize);
        if($page < 1){
            $page = 1;
        }
        $list = $data->offset(($page-1)*$pageSize)->limit($pageSize)->asArray()->all();
        return [
            'data'=> $list,
            'curPage' => $page,
            'pageCount' => $pageCount,
            'count' => $totalCount,
        ];
    }

    public static function insertRecord($data)
    {
        $model = new self();
        $model->setAttributes($data,false);
        if($model->save()){
            return true;
        }
        return false;
    }
}