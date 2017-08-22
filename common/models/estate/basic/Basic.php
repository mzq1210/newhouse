<?php
/**
 *楼盘基本信息表
 * <liangshimao>
 */

namespace common\models\estate\basic;

use common\models\estate\config\Tag;
use common\models\estate\extend\PreSalePermit;
use Yii;
use common\models\estate\extend\Closing;
use common\models\estate\extend\Opening;
use common\models\query\BaseQuery;
use yii\db\ActiveRecord;

class Basic extends ActiveRecord
{
    public static function tableName() {
        return 'Estate_BasicInfo';
    }

    public static function tableDesc() {
        return '楼盘基本信息';
    }

    /**
     * 为model添加筛选方法,对应baseQuery中的各个方法,必须重写find()方法.
     * <liangshimao>
     */
    public static function find()
    {
        return Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    /**
     * 关联开盘信息表
     * <liangshimao>
     */
    public function getOpening()
    {
        return $this->hasOne(Opening::className(),['estateID' => 'estateID']);
    }

    /**
     * 关联交房时间信息表
     * <liangshimao>
     */
    public function getClosing()
    {
        return $this->hasOne(Closing::className(),['estateID' => 'estateID']);
    }

    /**
     * 关联住宅表
     * <liangshimao>
     */
    public function getResidential()
    {
        return $this->hasOne(Residential::className(),['estateID' => 'estateID']);
    }

    /**
     * 关联商铺表
     * <liangshimao>
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(),['estateID' => 'estateID']);
    }

    /**
     * 关联写字楼表
     * <liangshimao>
     */
    public function getOffice()
    {
        return $this->hasOne(Office::className(),['estateID' => 'estateID']);
    }

    /**
     * 关联住宅楼楼盘特色标签
     */
    public function getResidentialTag()
    {
        return $this->hasMany(ResidentialTag::className(),['residentialBuildingID' => 'residentialBuildingID'])->viaTable('Estate_ResidentialBuilding',['estateID' => 'estateID'] )->select('tagID')->asArray();

        //return $this->hasMany(Tag::className(),['tagID' => 'tagID'])->viaTable('Estate_ResidentialBuilding_BuildingTag',['residentialBuildingID' => 'estateID'])->select('tagName')->asArray();
    }

    /**
     * 关联住宅楼楼盘建筑类型
     * <liangshimao>
     */
    public function getResidentialType()
    {
        return $this->hasMany(ResidentialType::className(),['residentialBuildingID' => 'residentialBuildingID'])->viaTable('Estate_ResidentialBuilding',['estateID' => 'estateID'] )->select('buildingTypeID')->asArray();
    }
    /**
     * 关联住宅楼楼盘合作银行
     * <liangshimao>
     */
    public function getResidentialBank()
    {
        return $this->hasMany(ResidentialBank::className(),['residentialBuildingID' => 'residentialBuildingID'])->viaTable('Estate_ResidentialBuilding',['estateID' => 'estateID'] )->select('bankName')->asArray();
    }


    /**
     * 关联商铺楼盘特色标签
     * <liangshimao>
     */
    public function getStoreTag()
    {
        return $this->hasMany(StoreTag::className(),['storeID' => 'storeID'])->viaTable('Estate_Store',['estateID' => 'estateID'] )->select('tagId')->active()->asArray();
    }


    /**
     * 关联商铺楼楼盘合作银行
     * <liangshimao>
     */
    public function getStoreBank()
    {
        return $this->hasMany(StoreBank::className(),['storeID' => 'storeID'])->viaTable('Estate_Store',['estateID' => 'estateID'] )->select('bankName')->asArray();
    }


    /**
     * 关联写字楼楼楼盘特色标签
     * <liangshimao>
     */
    public function getOfficeTag()
    {
        return $this->hasMany(OfficeTag::className(),['officeID' => 'officeID'])->viaTable('Estate_Office',['estateID' => 'estateID'] )->select('tagId')->active()->asArray();
    }

    /**
     * 关联写字楼楼楼盘合作银行
     * <liangshimao>
     */
    public function getOfficeBank()
    {
        return $this->hasMany(OfficeBank::className(),['officeID' => 'officeID'])->viaTable('Estate_Office',['estateID' => 'estateID'] )->select('bankName')->asArray();
    }

    /**
     * 获取楼盘预售许可证
     * <liangshimao>
     */
    public function getPermit()
    {
        return $this->hasMany(PreSalePermit::className(),['estateID'=>'estateID'])->orderBy(['PreSaleDate ' => SORT_DESC])->asArray();
    }

}