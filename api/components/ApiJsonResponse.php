<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use \yii\web\JsonResponseFormatter;
use common\helper\Log;
use yii\helpers\Json;

/**
 * Description of ApiJsonResponse
 *
 * @author eboss
 */
class ApiJsonResponse extends JsonResponseFormatter {

    public function formatJson($response) {
        $response->getHeaders()->set('Content-Type', 'application/json; charset=UTF-8');
        \Yii::$app->response->format = 'json';
        $data = $response->data;
        $ret = [
            'msg' => $data[0],
            'code' => isset($data[1]) ? $data[1] : 0,
            'results'=>[]
        ];
        if($ret['code'] == 500) {
            Log::error($ret['msg']);
            $ret['msg'] = "服务器异常";
        } else {
            if (isset($data[2]) && !empty($data[2])) {
                $retData = $data[2];
                if (isset($retData['results']) && !empty($retData['results'])) {
                    $ret['results'] = $retData['results'];
                    unset($retData['results']);
                    $ret = array_merge($ret, $retData);
                } else {
                    $ret = [
                        'msg' => '暂无数据',
                        'code' => 204,
                        'results' => []
                    ];
                }
            } elseif ($ret['code'] == 200) {
                $ret = [
                    'msg' => '暂无数据',
                    'code' => 204,
                    'results' => []
                ];
            }
        }
        $ret = Json::encode($ret);
        $response->content = $ret;
    }

}
