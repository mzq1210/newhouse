<?php

/**
 * 资讯业务逻辑
 *
 * @author <liangpingzheng>
 */

namespace common\logic;

use common\models\webMgmt\Article;
use common\models\webMgmt\ArticleCategory;
use common\models\webMgmt\ArticleEstate;

class ArticleLogic {

    public static function getArticle($params) {
        $info = Article::selectRecord($params);
        $data = $info['results'];
        $newData = [];
        foreach ($data as $val) {
            $temp = $val;
            $temp['publishDate'] = date('Y-m-d H:i', strtotime($val['publishDate']));
            $temp['thumbnailImageName'] = IMG_DOMAIN . $val['thumbnailImageName'];
            $newData[] = $temp;
        }
        $info['results'] = $newData;
        return $info;
    }

    public static function getArticleByEstateID($params) {
        $info = ArticleEstate::selectRecord($params);
        $data = $info['results'];
        $newData = [];
        foreach ($data as $val) {
            $temp = $val;
            $temp['publishDate'] = date('Y-m-d H:i', strtotime($val['publishDate']));
            $temp['thumbnailImageName'] = IMG_DOMAIN . $val['thumbnailImageName'];
            $newData[] = $temp;
        }
        $info['results'] = $newData;
        return $info;
    }

    public static function getDetail($articleID,$preview) {
        //查询楼盘基本信息
        $info = Article::getOne($articleID,$preview);
        //$estateID = ArticleEstate::getEstateID($articleID);

        if ($info) {
            $info['publishDate'] = date('Y-m-d H:i', strtotime($info['publishDate']));
            $info['thumbnailImageName'] = IMG_DOMAIN . $info['thumbnailImageName'];
            //$info['estateID'] = $estateID;
        }
        return ['results' => $info];
    }

    /**
     * 获取文章类型
     * @return array
     */
    public static function getArticleCategory()
    {
        $data = ArticleCategory::getCategory();
        return ['results' => $data];
    }

}
