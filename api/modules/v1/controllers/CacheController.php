<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace api\modules\v1\controllers;

use app\components\ActiveController;
use common\helper\BaseCache;

class CacheController extends ActiveController {

    public function actionDel() {
        $data = [
            'ST','areas', 'prices', 'estate_simpleHouseType', 'tracks', 'estate_houseType', 'company_code', 'estate_estateData',
            'company_all', 'hot_search_keyword', 'types', 'estate_superAgency','banner_info','home_theme_estate_category',
            'home_market','home_recommended_article','home_recommended_estate','estate_agency','estate_price','estate_album',
            'tags','rings'
        ];
        foreach ($data as $val) {
            BaseCache::delTree($val);
        }
//        BaseCache::flushDB();
        return ['成功', 200];
    }

}
