<?php
/**
 * 文章类型 model
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */
namespace common\models\webMgmt;

use yii\db\ActiveRecord;

class ArticleCategory extends ActiveRecord{
    public static function tableName(){
        return 'WebMgmt_ArticleCategory';
    }

    public static function tableDesc(){
        return '文章类型表';
    }

    public static function getCategory()
    {
        return self::find()->select('articleCategoryID,articleCategoryName')->orderBy(['sortIndex'=>SORT_ASC])->asArray()->all();
    }
}