<?php

/**
 * Created by PhpStorm.
 * User: smile
 * Date: 17-4-28
 * Time: 下午4:28
 */

namespace api\modules\v1\controllers;

use common\helper\cache\EstateDetailCache;
use app\components\ActiveController;

class PriceController extends ActiveController {

    /**
     * 获取价格走势信息
     * @param int $estateID 楼盘id（必填）
     * @author <liangshimao>
     */
    public function actionIndex() {
        $estateID = $this->request->get('estateID');
        $propertyTypeID = $this->request->get('propertyTypeID', ESTATE_PROPERTY_TYPE);
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = EstateDetailCache::getPrice($estateID, $propertyTypeID);
        } catch (\Exception $e) {
            return[$e->getMessage(), 500];
        }

        return ['成功', 200, ['results' => $data]];
    }

}
