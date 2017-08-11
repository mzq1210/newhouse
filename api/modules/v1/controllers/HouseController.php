<?php
/**
 * 户型接口 对外提供户型相关信息
 * User: liangshimao
 * Date: 17-4-28
 * Time: 下午4:02
 */
namespace api\modules\v1\controllers;

use common\helper\cache\EstateDetailCache;
use common\logic\EstateDetailLogic;
use app\components\ActiveController;

class HouseController extends ActiveController
{
    /**
     * 获取户型
     * @param int $estateID 楼盘id
     * @param int $propertyTypeID 业态id
     * @author <liangshimao>
     */
    public function actionIndex()
    {
        $estateID = $this->request->get('estateID');
        $propertyTypeID = $this->request->get('propertyTypeID',ESTATE_PROPERTY_TYPE);
        $type = $this->request->get('type',0);
        if (empty($estateID) || !is_numeric($estateID)){
            return ['楼盘id为空或不合法', 201];
        }
        try {
            if($type == 0){
                $data = EstateDetailCache::getHouseType($estateID,$propertyTypeID);
            }else{
                $data = EstateDetailCache::getSimpleHouseType($estateID,$propertyTypeID);
            }
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }

    /**
     * 获取楼盘户型详细信息
     * @param int $houseTypeID 户型id(必填)
     * @author <liangshimao>
     */
    public function actionDetail()
    {
        $houseTypeID = $this->request->get('houseTypeID');
        if (empty($houseTypeID) || !is_numeric($houseTypeID)) {
            return ['楼盘户型id为空', 201];
        }
        try {
            $data = EstateDetailLogic::getHouseTypeDetail($houseTypeID);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }

    /**
     * 获取户型样板间信息
     * @param int $houseTypeID 户型id(必填)
     * @author <liangshimao>
     */
    public function actionProtoroom()
    {
        $houseTypeID = $this->request->get('houseTypeID');
        if (empty($houseTypeID) || !is_numeric($houseTypeID)) {
            return ['楼盘户型id为空', 201];
        }
        try {
            $data = EstateDetailLogic::getHouseTypeProtoRoom($houseTypeID);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }
    
    public function actionPrice()
    {
        $houseTypeID = $this->request->get('houseTypeID',0);
        $estateID = $this->request->get('estateID',0);
        $propertyTypeID = $this->request->get('propertyTypeID',ESTATE_PROPERTY_TYPE);
        try {
            if(!empty($houseTypeID)){
                $detail = EstateDetailLogic::getHouseTypeDetail($houseTypeID);
                $estateID = empty($detail['estateID']) ? 0 : $detail['estateID'];
                $propertyTypeID = empty($detail['propertyTypeID']) ? 0 : $detail['propertyTypeID'];
            }
            $data = EstateDetailLogic::getDataFromSolr($estateID,$propertyTypeID,'lastRoomMinPrice,undetermined,isJudge,lastAveragePrice,loanType');
            if(!empty($data['loanType'])){
                $res = [];
                $loanArr = explode(',', $data['loanType']);
                foreach ($loanArr as $l){
                    $id = 0;
                    if(strpos($l,'商业') !== false){
                        $id = 1;
                    }elseif(strpos($l,'公积金') !== false){
                        $id = 2;
                    }elseif(strpos($l,'组合') !== false){
                        continue;
                    }
                    $res[]= ['id'=>$id,'type'=>$l];
                    $data['loan'] = $res;
                }
            }else{
                $data['loan'] = [];
            }
            unset($data['loanType']);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }
}