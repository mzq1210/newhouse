<?php
/**
 * 这里是统一的筛选方法.
 * 用法:Model::find()->where(['id'=>1])->active()->all();
 * <liangshimao>
 */

namespace common\models\query;


use yii\db\ActiveQuery;

class BaseQuery extends ActiveQuery
{
    /**
     * 反回正常数据,未被删除的
     */
    public function active()
    {
        return $this->andWhere(['isActive' => 1]);
    }

    /**
     * 返回审核通过的信息
     */
    public function audit()
    {
        return $this->andWhere(['auditStatus' => 1]);
    }

    /**
     * 获取上架的楼盘信息
     */
    public function published()
    {
        return $this->andWhere(['status' => 2]);
    }

    /**
     * 筛选物业类型
     */
    public function property($propertyTypeID)
    {
        return $this->andWhere(['propertyTypeID' => $propertyTypeID]);
    }

    /**
     * 筛选物业类型
     */
    public function parent()
    {
        return $this->andWhere(['parentID' => 0]);
    }
}