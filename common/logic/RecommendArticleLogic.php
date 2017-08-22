<?php
/**
 * 从热门文章推荐模型中获取数据
 * @Author: <lixiaobin>
 * @Date: 17-4-13
 */

namespace common\logic;

use common\models\webMgmt\Article;
use common\models\webMgmt\RecommendArticle;

class RecommendArticleLogic{

    /**
     * 根据当前客户端类型、城市编码热门文章推荐
     * @Params: Array $params
     *          int $params['companyCode'] 城市公司编码
     *          int $params['clientType'] 客户端类型 0:pc站 1:wap站 2：App *注意：暂时去掉按客户端类型分类查询
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-04-13
     */
    public static function selectArticLogic($params, $fields){
        $articlInfo = RecommendArticle::selectRecord($params, $fields);
        if(!empty($articlInfo)){
            foreach ($articlInfo as $val){
                $temp = Article::getOne($val['articleID']);
                $val['publishDate'] = date('Y-m-d H:i', strtotime($temp['publishDate']));
                $val['source'] = $temp['source'];
                $val['homeHotArticleImageName'] = IMG_DOMAIN . $val['homeHotArticleImageName'];
                $articlArr[] = $val;
            }
            return $articlArr;
        }
        return false;
    }
}