<?php
/**
 * 主题类别推荐楼盘表
 * @Author: <lixiaobin>
 * @Date: 17-4-17
 */

namespace common\models\webMgmt;

use yii\db\ActiveRecord;
use yii\data\Pagination;

class ThemeEstate extends ActiveRecord{

    public static function tableName(){
        return 'WebMgmt_HomeThemeEstate';
    }

    public static function tableDesc(){
        return '主题类别推荐楼盘表';
    }

    /**
     * 根据推荐主题分类获取当前主题下的楼盘列表
     * @Params: Array $params
     *          Int $params['categoryID'] 推荐主题分类ID 必填
     *          Int $params['page'] 当前页 必填
     *          Int $params['pageSize'] 每页显示的条数 必填
     * @Return: Array
     * @Author: <lixiaobin>
     * @Date: 2017-05-11
     */
    public static function selectRecord($params){
        $query = self::find();
        $query->orderBy(['id' => SORT_DESC]);
        $query->andWhere(['themeEstateCategoryID' => $params['categoryID']]);
        $countQuery = clone $query;
        $count = $countQuery->count();
        $offset = $params['page'] > 0 ? ($params['page']) - 1 : 0;
        $list = $query->offset($offset * $params['pageSize'])->limit($params['pageSize'])->asArray()->all();
        if(!empty($list)){
            return ['count' => $count, 'results' => $list];
        }
        return false;
    }
}