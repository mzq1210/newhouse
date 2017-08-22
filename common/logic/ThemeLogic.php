<?php
/**
 * 处理主题推荐楼盘的逻辑
 * @Author: mzq
 * @Date: 17-5-3
 */

namespace common\logic;

use common\models\webMgmt\Theme;

class ThemeLogic{

    /**
     * @Author: mzq
     * @Date: 17-5-3
     * @param $activityID
     * @return array|bool
     */
    public static function selectThemeLogic($activityID){
        $model = Theme::selectRecord($activityID);
        if(!empty($model)){
            return $model;
        }
        return false;
    }
}