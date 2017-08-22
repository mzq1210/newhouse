<?php
/**
 * 首页逻辑业务层
 * @Author: <lixiaobin>
 * @Date: 17-3-21
 */

namespace app\common\logic;

use common\models\estate\config\Area;

class SiteLogic{
    
    private $redis;
    
    public function init(){
        $this->redis = Yii::$app->redis;
    }
    
    public function selectAreaRecord($comporyCode){
        if(is_numeric($comporyCode)){
            $areaArr = Area::selectRecord($comporyCode);
        }
        return flase;
    }
}