<?php
/**
 * 楼盘相册接口
 * User: liangshimao
 * Date: 17-4-28
 * Time: 下午4:23
 */

namespace api\modules\v1\controllers;

use app\components\ActiveController;
use common\helper\cache\EstateDetailCache;

class AlbumController extends ActiveController
{
    /**
     * 获取楼盘相册
     * @param int @estateID 楼盘id（必填）
     * @author <liangshimao>
     */
    public function actionIndex()
    {
        $estateID = $this->request->get('estateID');
        $propertyTypeID = $this->request->get('propertyTypeID',ESTATE_PROPERTY_TYPE);
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = EstateDetailCache::getAlbum($estateID, $propertyTypeID);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }

    /**
     * 获取首页楼盘图片集合
     * @param int $estateID 楼盘id（必填）
     * @author <liangshimao>
     */
    public function actionBanner()
    {
        $estateID = $this->request->get('estateID');
        $propertyTypeID = $this->request->get('propertyTypeID',ESTATE_PROPERTY_TYPE);
        if (empty($estateID) || !is_numeric($estateID)) {
            return ['楼盘id为空或不合法', 201];
        }
        try {
            $data = EstateDetailCache::getPicture($estateID, $propertyTypeID);
        } catch (\Exception $e) {
            return [$e->getMessage(), 500];
        }

        if(empty($data)){
            return ['成功', 200];
        }

        return ['成功', 200, ['results'=>$data]];
    }

}