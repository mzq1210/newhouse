<?php

/*
 * 资讯接口 对外提供有关资讯的所有数据
 * @data 2017-4-26
 * @author liangpingzheng
 */

namespace api\modules\v1\controllers;

use app\components\ActiveController;
use common\logic\ArticleLogic;
use common\models\webMgmt\ArticlePageViewLog;

class ArticleController extends ActiveController {

    /**
     * 获取资讯列表
     * @param int $typeID 分类ID 可以为空  为空时全部资讯
     * @param int page  当前页
     * @pageSize int 每页显示条数
     * @author <liangpingzheng>
     */
    public function actionIndex() {
        $params = [];
        $params['typeID'] = intval($this->request->get("typeID", 0));
        $params['page'] = intval($this->request->get("page", 1));
        $params['pageSize'] = intval($this->request->get("pageSize", PAGESIZE));
        $params['companyCode'] = intval($this->request->get("companyCode", 1000));
        try {
            $info = ArticleLogic::getArticle($params);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
        return ['成功', 200, $info];
    }

    /**
     * 根据资讯ID 获取详情
     * @param int $articleID 资讯ID
     * @author <liangpingzheng>
     */
    public function actionDetail() {
        $articleID = intval($this->request->get("articleID", 2332));
        $preview = $this->request->get("preview", 0);
        try {
            $info = ArticleLogic::getDetail($articleID,$preview);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }
        return ['成功', 200, $info];
    }

    /**
     * alia getByEstateID
     * 根据楼盘ID 获取相关资讯
     * @param int estateID 楼盘ID
     * @param int $name 业态
     * @param int $pageSize 获取多少条
     * @author <liangpingzheng>
     */
    public function actionEstate() {
        $params = [];
        $params['typeID'] = intval($this->request->get("typeID", 0));
        $params['page'] = intval($this->request->get("page", 1));
        $params['pageSize'] = intval($this->request->get("pageSize", PAGESIZE));
        $params['estateID'] = intval($this->request->get("estateID", 175));
        $params['propertyTypeID'] = intval($this->request->get("propertyTypeID", 8));
        try{
        $info = ArticleLogic::getArticleByEstateID($params);
        } catch(\Exception $e){
            return [$e->getMessage(), 500];
        }
        return ['成功', 200, $info];
    }

    /**
     * 获取资讯类型
     * @return array
     * @author <liangshimao>
     */
    public function actionCategory()
    {
        try{
            $info = ArticleLogic::getArticleCategory();
        } catch(\Exception $e){
            return [$e->getMessage(), 500];
        }
        return ['成功', 200, $info];
    }

    /**
     * 记录文章浏览记录接口
     * @Params: int articleID 文章ID
     * @Params: int clientType 浏览量来源
     * @Params: int companyCode 公司编码
     * @Return: true OR false
     * @Author: <lixiaobin>
     * @Date: 2017-06-16
     */
    public function actionPageview(){
        try{
            $params = [];
            $params['articleID'] = intval($this->request->get("articleID", 0));
            $params['clientType'] = intval($this->request->get("clientType", 0));
            $params['companyCode'] = intval($this->request->get("companyCode", 0));
            if(!is_numeric($params['articleID'])){
                return ['文章ID错误', 201];
            }
            if(!is_numeric($params['articleID']) && $params['articleID'] < 3 ){
                return ['客户端ID错误', 201];
            }
            if(!is_numeric($params['companyCode'])){
                return ['城市公司编码错误', 201];
            }
            $results['results'] = ArticlePageViewLog::insertRecord($params);
            if(!empty($results)){
                return ['插入成功', 200, $results];
            }else{
                return ['插入失败', 201];
            }
        }catch (\Exception $e){
            return [$e->getMessage(), 500];
        }
    }

}
