<?php
/**
 * 文章浏览记录 model 类
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */

namespace common\models\webMgmt;

use yii\db\ActiveRecord;

class ArticlePageViewLog extends ActiveRecord{
    public static function tableName(){
        return 'WebMgmt_ArticlePageViewLog';
    }

    public static function tableDesc(){
        return '文章浏览记录';
    }

    /**
     * 插入浏览记录，存在更新，不存在插入
     * @Params: Array
     *          int articleID 文章ID
     *          int clientType 浏览量来源
     *          int companyCode 公司编码
     * @Return: true OR false
     * @Author: <lixiaobin>
     * @Date: 2017-06-16
    */
    public static function insertRecord($params){
        $datetime = date('Y-m-d H:i:s');
        $params['inDate'] = $datetime;
        $params['lastEditDate'] = $datetime;
        $model = new self;
        $model->setAttributes($params, false);
        if($model->save()){
            return true;
        }else{
            return false;
        }
    }
}