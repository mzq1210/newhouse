<?php

/*
 * 百度统计key获取
 * @autho <liangpingzheng>
 */

namespace common\helper;

use Yii;

class analyHelper {

    public static function getKey($domain, $wabSite = 'pc') {
        $keyConfig = require(Yii::getAlias('@app') . '/../common/config/analy.php');

        if (is_array($keyConfig) && isset($keyConfig[$wabSite][$domain])) {
            return $keyConfig[$wabSite][$domain];
        }
        return 'none';
    }

}
