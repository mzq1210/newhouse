<?php
/**
 * 主题推荐楼盘列表
 * @Author: <lixiaobin>
 * @Date: 17-5-2
 */

namespace api\modules\v1\controllers;

use app\components\ActiveController;
use common\helper\BaseCache;
use common\logic\ThemeEstateLogic;
use common\models\webMgmt\ThemeEstate;

class EstateController extends ActiveController{

    /**
     * 获取推荐主题楼盘列表
     * @Params: Int companyCode 公司编码 必填
     * @Params: Int categoryID 推荐主题分类ID 必填
     * @Params: Int page 当前页 默认1
     * @Params: Int pageSize 每页显示的条数 默认15
     * @Return: Json
     * @Author: <lixiaobin>
     * @Date: 2017-04-24
     *
    */
    public function actionIndex(){
        try{
            $companyCode = $this->request->get('companyCode', '');
            $params['categoryID'] = $this->request->get('categoryID',0);
            $params['page'] = (int)$this->request->get('page',1);
            $params['pageSize'] = (int)$this->request->get('pageSize',PAGESIZE);
            
            if (!is_numeric($companyCode)) {
                return ['公司编码格式不正确', 201];
            }
            if (!is_numeric($params['categoryID'])) {
                return ['楼盘主题格式错误', 201];
            }
            $estate = ThemeEstateLogic::selectEstateLogic($companyCode, $params);
            $results = [];
            if (!empty($estate['results'])) {
                $results['results'] = $estate['results'];
                $results['count'] = $estate['count'];
                $results['curPage'] = $params['page'];
                $results['pageCount'] = ceil($estate['count'] / $params['pageSize']);
            }
            return ['成功', 200, $results];
        }catch (\Exception $e){
            return [$e->getMessage(), 500];
        }
    }

}