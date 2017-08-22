<?php
/**
 * 外部经纪人信息
 * User: luohua
 * Date: 17-4-14
 * Time: 下午3:45
 */
namespace common\logic;

use common\models\estate\agency\AgencyEstate;
use Yii;
use common\models\estate\agency\AgencyInfo;
use yii\helpers\ArrayHelper;
use common\components\HuanXinUsers;

class AgencyInfoLogic{

    /**
     * 获取经纪人详情信息
     * @param string $fields
     * @param array $condition
     * @return array|null|\yii\db\ActiveRecord
     * <wangluohua>
     */
    public static function getAgencyInfoOne($fields = '*', $condition = []){
        return AgencyInfo::selectOne($fields,$condition);
    }

    /**
     * 获取经纪人详细信息
     */
    public static function getAgencyInfo($agencyID)
    {
        return AgencyInfo::selectOne('agencyID,agencyCellphone,agencyName,avatarsImageName,chatUserID,agencyOrg',['agencyID'=>$agencyID]);
    }

    /**
     * 从数据库中取出热门经纪人信息
     * @param $estateID
     * <liangshimao>
     */
    public static function getAgency($estateID) {
        $res = [];
        $agencyList = AgencyEstate::selectRecord($estateID, 'agencyID,totalAgencyTaskCount,totalCustomerReviewScore');
        if (empty($agencyList)) {
            return false;
        }
        foreach ($agencyList as $key => $value) {
            //查询经纪人详情,并且只查询在职的.
            $info = AgencyInfo::getDetail($value['agencyID'], 'avatarsImageName,agencyName,agencyCellphone,chatUserID',true);
            if(empty($info)){
                continue;
            }
            //拼接经纪人带看情况和基本信息
            $res[] = ArrayHelper::merge($value, $info);

        }

        return $res;
    }

    /**
     * 获取明星经纪人信息
     *  <liangshimao>
     */
    public static function getSuperAgency($estateID) {

        $agency = AgencyEstate::selectSuperRecord($estateID, 'agencyID,totalAgencyTaskCount,totalCustomerReviewScore');
        if (empty($agency)) {
            return false;
        }
        $res = [];
        foreach ($agency as $key=>$value){
            $info = AgencyInfo::getDetail($value['agencyID'], 'avatarsImageName,agencyName,agencyCellphone,chatUserID',true);
            if(!empty($info)){
                //拼接经纪人带看情况和基本信息
                $res = ArrayHelper::merge($value, $info);
                break;
            }

        }

        return $res;
    }

    /**
     * 获取经纪人在线状态
     */
    public static function getAgencyStatus($user)
    {
        $huanXin = new HuanXinUsers();
        return $huanXin->actionIsonline($user);
    }
}