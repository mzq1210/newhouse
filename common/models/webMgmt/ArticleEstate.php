<?php

/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-4-6
 * Time: 上午9:50
 */

namespace common\models\webMgmt;

use yii\db\ActiveRecord;
use yii\data\Pagination;

class ArticleEstate extends ActiveRecord {

    public static function tableName() {
        return 'WebMgmt_ArticleEstateRelation';
    }

    public static function tableDesc() {
        return '文章和楼盘关联表';
    }

    /**
     * 根据条件查询楼盘相关的资讯(包含业态)
     * @param array $params 参数数组
     * @return array
     * @author <liangpingzheng>
     */
    public static function selectRecord($params) {

        $query = self::find()->select('a.articleID,a.title,a.summary,a.thumbnailImageName,a.content,a.publishDate,a.author,a.source');
        $query->from(['ar' => 'WebMgmt_ArticleEstateRelation']);
        $query->innerJoin(['a' => 'WebMgmt_Article'], '{{ar}}.articleID={{a}}.articleID');
        $query->andWhere('ar.estateID=' . $params['estateID'] . ' and a.auditStatus=1 and a.isActive=1');
        $query->andWhere('ar.propertyTypeID=' . $params['propertyTypeID']);
        if ($params['typeID']) {
            $query->andWhere('a.articleCategoryID=' . $params['typeID']);
        }

        //查询总页数
        $countQuery = clone $query;
        $count = $countQuery->count();
        $query->orderBy(['a.publishDate' => SORT_DESC]);
        $offset = $params['page'] > 0 ? ($params['page']) - 1 : 0;
        $list = $query->offset($offset * $params['pageSize'])->limit($params['pageSize'])->asArray()->all();
        return ['count' => $count, 'results' => $list];
    }

    /**
     * 根据资讯ID 查找楼盘ID
     * $param int $articleID
     * @return int estateID 楼盘ID
     * @author <liangpingzheng>
     */
    public static function getEstateID($articleID) {
        $info = self::find()->select('estateID')->where(['articleID' => $articleID])->asArray()->one();
        if (empty($info)) {
            return false;
        }
        return $info['estateID'];
    }

}
