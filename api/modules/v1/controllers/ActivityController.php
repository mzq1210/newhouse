<?php
/**
 * 主题楼盘接口
 * @Author: <mzq>
 * @Date: 17-4-27
 */

namespace api\modules\v1\controllers;

use common\logic\ThemeLogic;
use app\components\ActiveController;
use common\models\webMgmt\ThemeCompany;
use common\models\webMgmt\ActivityEstate;
use common\models\webMgmt\ThemeRegistration;
use common\logic\EstateDetailLogic;
use common\helper\cache\EstateDetailCache;

class ActivityController extends ActiveController{

    /**
     * 获取楼盘主题
     * @Author: <mzq>
     * @Date: 2017-04-27
     * @Return: Json
    */
    public function actionIndex(){
        try {
            $activityID = $this->request->get('activityID', '');
            $results = ThemeLogic::selectThemeLogic($activityID);
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

    /**
     * @Desc 获取主题活动城市对应楼盘接口
     * @Author: <mzq>
     * @Date: 2017-5-3
     * @return array
     */
    public function actionGetactestate(){
        try {
            $temp = $info = array();
            $activityID = $this->request->get('activityID', '');
            $companyID = $this->request->get('companyID', '');
            //判断如果没有城市id
            if (!is_numeric($companyID)) {
                return ['城市编码格式不正确', 201];
            }
            $themeCompany = ThemeCompany::selectRecord($activityID, $companyID);
            $results = ActivityEstate::selectRecord($themeCompany['themeActivityCompanyID']);

            foreach ($results['results'] as $key => $val){
                $temp[$val['estateID']][] = $val;
            }

            //多个业态只展示一个楼盘，展示规则按住宅、商铺、写字楼优先展示的顺序展示
            foreach ($temp as $key => $value){
                $info[$key]['themeActivityEstateID'] = $value[0]['themeActivityEstateID'];
                $info[$key]['themeActivityCompanyID'] = $value[0]['themeActivityCompanyID'];
                $info[$key]['themeActivityID'] = $value[0]['themeActivityID'];
                $info[$key]['estateID'] = $value[0]['estateID'];
                $info[$key]['defaultPropertyTypeID'] = $value[0]['propertyTypeID'];
                $info[$key]['beginDate'] = $value[0]['beginDate'];
                $info[$key]['endDate'] = $value[0]['endDate'];
                $info[$key]['estateImageName'] = empty($value[0]['estateImageName'])? '': IMG_DOMAIN . $value[0]['estateImageName'];
                $info[$key]['themeActivityName'] = $value[0]['themeActivityName'];
                $info[$key]['isEnableTag'] = $value[0]['isEnableTag'];
                $info[$key]['tag'] = $value[0]['tag'];
                $info[$key]['recommandedReason'] = $value[0]['recommandedReason'];
                $info[$key]['summary'] = $value[0]['summary'];
                $info[$key]['propertyTypeID'] = '';
                foreach ($value as $k => $v) {
                    $info[$key]['propertyTypeID'][]= $v['propertyTypeID'];
                }
            }

            foreach ($info as $key => $val){
                $info[$key]['basic'] = EstateDetailLogic::getBasic($key, $val['defaultPropertyTypeID']);
            }
            foreach ($results['results'] as $key => $val){
                $results['results'][$key]['basic'] = EstateDetailLogic::getBasic($val['estateID'], $val['propertyTypeID']);
            }

            $result['oldData'] = $results['results'];
            $result['newData'] = $info;
            $result['company'] = $themeCompany;
            $data['results'] = $result;
            return ['成功', 200, $data];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

    /**
     * @Desc 主题报名接口
     * @Author: <mzq>
     * @Date: 2017-5-3
     * @return array
     */
    public function actionRegistration(){
        try {
            $activityEstateID = $this->request->get('activityEstateID', '');
            $companyID = $this->request->get('activityCompanyID', '');
            $activityID = $this->request->get('activityID', '');
            $estateID = $this->request->get('estateID', '');
            $propertyTypeID = $this->request->get('propertyTypeID', '');
            $userID = $this->request->get('userID', '');
            $username = $this->request->get('username', '');
            $phone = $this->request->get('phone', '');
            $data = [
                'themeActivityEstateID' => $activityEstateID,
                'themeActivityCompanyID' => $companyID,
                'themeActivityID' => $activityID,
                'estateID' => $estateID,
                'propertyTypeID' => $propertyTypeID,
                'userID' => $userID,
                'registrationUserName' => $username,
                'registrationCellphone' => $phone,
                'inDate' => date('Y-m-d H:i:s')
            ];
            $results['results'] = ThemeRegistration::insertRecord($data);
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

    /**
     * @Desc 获取单个主题楼盘信息接口
     * @Author: <mzq>
     * @Date: 2017-5-3
     * @return array
     */
    public function actionActivityestatedetail(){
        try {
            $activityEstateID = $this->request->get('activityEstateID', '');
            $activityID = $this->request->get('activityID', '');
            $params = [
                'activityEstateID' => $activityEstateID,
                'activityID' => $activityID
            ];
            $results['results'] = ActivityEstate::selectDetailRecord($params);
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

    /**
     * @Desc 查询用户是否报名接口
     * @Author: <mzq>
     * @Date: 2017-5-3
     * @return array
     */
    public function actionRegistrationinfo(){
        try {
            $activityEstateID = $this->request->get('activityEstateID', '');
            $propertyTypeID = $this->request->get('propertyTypeID', '');
            $activityID = $this->request->get('activityID', '');
            $phone = $this->request->get('phone', '');
            $params = [
                'activityEstateID' => $activityEstateID,
                'activityID' => $activityID,
                'phone'      => $phone,
                'propertyTypeID' => $propertyTypeID
            ];
            $results = ThemeRegistration::selectDetailRecord($params);

            if($results){
                $data['results'] = 200;
            }else{
                $data['results'] = 202;
            }
            return ['成功', 200, $data];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

    /**
     * 获取楼盘主题
     * @Author: <mzq>
     * @Date: 2017-04-27
     * @Return: Json
     */
    public function actionEstateactivity(){
        try {
            $estateID = $this->request->get('estateID', '');
            $propertyTypeID = $this->request->get('propertyTypeID', '');
            $params = [
                'estateID' => $estateID,
                'propertyTypeID' => $propertyTypeID
            ];
            $results['results'] = ActivityEstate::selectInfoRecord($params);
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

    /**
     * 获取楼盘主题
     * @Author: <mzq>
     * @Date: 2017-04-27
     * @Return: Json
     */
    public function actionEstateproperty(){
        try {
            $estateID = $this->request->get('estateID', '');
            $params = [
                'estateID' => $estateID
            ];
            $results['results'] = ActivityEstate::selectAllRecord($params);
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

}