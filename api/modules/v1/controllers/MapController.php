<?php
/**
 * Created by PhpStorm.
 * User: mzq
 * Date: 17-4-23
 * Time: 上午10:49
 */

namespace api\modules\v1\controllers;

use Yii;
use common\logic\MapLogic;
use app\components\ActiveController;

class MapController extends ActiveController
{

    /**
     * 禁用基类的方法
    */
    public function actions() {
        $actions = parent::actions();
        // 禁用""index,delete" 和 "create" 操作
        unset($actions['delete'], $actions['create'],$actions['view']);
        return $actions;
    }

    /**
     * @Desc 获取区域住宅数据
     * @Author: <mzq>
     * @Date: 2017-04-24
     * @return array
     */
    public function actionGethouse(){
        try {
            $companyCode = $this->request->get('companyCode', '');
            //判断如果没有城市公司code
            if (!is_numeric($companyCode)) {
                return ['城市编码格式不正确', 201];
            }
            $params = $this->request->get();
            $results = [];
            $results['results'] = MapLogic::getMapData($params, SOLR_ZHUZHAI);
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }
    
    /**
     * @Desc 获取区域商铺数据
     * @Author: <mzq>
     * @Date: 2017-04-24
     * @return array
     */
    public function actionGetstore(){
        try {
            $companyCode = $this->request->get('companyCode', '');
            //判断如果没有城市公司code
            if (!is_numeric($companyCode)) {
                return ['城市编码格式不正确', 201];
            }
            $params = $this->request->get();
            $results = [];
            $results['results'] = MapLogic::getMapData($params, SOLR_STORE);
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }

    /**
     * @Desc 获取区域写字楼数据
     * @Author: <mzq>
     * @Date: 2017-04-24
     * @return array
     */
    public function actionGetoffice(){
        try {
            $companyCode = $this->request->get('companyCode', '');
            //判断如果没有城市公司code
            if (!is_numeric($companyCode)) {
                return ['城市编码格式不正确', 201];
            }
            $params = $this->request->get();
            $results = [];
            $results['results'] = MapLogic::getMapData($params, SOLR_OFFICE);
            return ['成功', 200, $results];
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
    }


}