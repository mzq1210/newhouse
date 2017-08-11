<?php
/**
 * 获取所有开通的城市公司
 * @Author: <lixiaobin>
 * Date: 17-5-25
 */

namespace api\modules\v1\controllers;

use app\components\ActiveController;
use common\helper\cache\CompanyCache;
use common\logic\CompanyLogic;

class CompanyController extends ActiveController{

    /**
     * 获取所有正常的城市并且按照城市首字母排序
     * @Return: json
     * @Auhtor: <lixiaobin>
     * @Date: 2017-05-25
    */
    public function actionIndex(){
        try{
            $companyAll = CompanyCache::getCompanyAllCache();
            $results = [];
            if (!empty($companyAll)) {
                $results = ['results' => $companyAll];
            }
            return ['成功', 200, $results];
        }catch (\Exception $e){
            return [$e->getMessage(), 500];
        }

    }

    /**
     * 获取当前城市信息
     * @Return: json
     * @Auhtor: <mzq>
     * @Date: 2017-05-25
     */
    public function actionDetail(){
        try{
            $domain = $this->request->get("domain", '');
            $fields = 'longitude,latitude';
            $info = CompanyLogic::selectCompanyDetailLogic($domain, $fields);
            $results = [];
            if (!empty($info)) {
                $results = ['results' => $info];
            }
            return ['成功', 200, $results];
        }catch (\Exception $e){
            return [$e->getMessage(), 500];
        }

    }



}