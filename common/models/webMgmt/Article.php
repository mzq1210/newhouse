<?php

/**
 * 文章 model 类
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */

namespace common\models\webMgmt;

use common\models\query\BaseQuery;
use yii\db\ActiveRecord;

class Article extends ActiveRecord {

    public static function tableName() {
        return 'WebMgmt_Article';
    }

    public static function tableDesc() {
        return '文章表(咨询|动态|导购)';
    }

    public static function find() {
        return \Yii::createObject(BaseQuery::className(), [get_called_class()]);
    }

    public static function selectRecord($params) {
        $field = "articleID,title,summary,thumbnailImageName,publishDate,source,content";
        $query = self::find()->select($field)->where(['companyCode' => $params['companyCode']])->audit()->active();
        if ($params['typeID']) {
            $query->andWhere('articleCategoryID=' . $params['typeID']);
        }
        //查询总数
        $countQuery = clone $query;
        $count = $countQuery->count();
        $query->orderBy(['publishDate' => SORT_DESC]);
        $offset = $params['page'] > 0 ? ($params['page']) - 1 : 0;
        $list = $query->offset($offset * $params['pageSize'])->limit($params['pageSize'])->asArray()->all();
        return ['count' => $count, 'results' => $list];
    }

    public static function getOne($articleID,$preview = 0) {
        $field = "articleID,title,author,source,summary,thumbnailImageName,publishDate,content,articleCategoryID";
        $info = self::find()->select($field)->where(['articleID' => $articleID]);
        if(!$preview){
            $info->audit()->active();
        }
        return $info->asArray()->one();
    }

}
