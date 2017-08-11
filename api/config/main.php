<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'), require(__DIR__ . '/params.php')
);

return [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
    ],
    'components' => [

        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
//        'response' => [
//            'class' => 'yii\web\Response',
//            'on beforeSend' => function ($event) {
//                $response = $event->sender;
//                if ($response->data !== null) {
//                    $response->data = [
//                        'success' => $response->isSuccessful,
//                        'data' => $response->data,
//                    ];
//                    $response->statusCode = 200;
//                }
//            },
//            ],
//            'response' => [
//                'class' => 'yii\web\Response',
//                'on beforeSend' => function($event) {
//                    $response = $event->sender;
//                    if (
//                        $response->format != \yii\web\Response::FORMAT_JSON //没设定format为JSON
//                        && is_array($response->data) //数组
//                    ) {
//                        $data = $response->data;
//                        $response->data = [
//                            'msg' => $data[0],
//                            'code' => isset($data[1]) ? $data[1] : 0,
//                            'data' => isset($data[2]) ? $data[2] : '',
//                        ];
//                        $response->format = \yii\web\Response::FORMAT_JSON;
//                    }
//                }
//                ],
        'response' => [
            'format' => 'json',
            'formatters' => [
                'json' => 'app\components\ApiJsonResponse',
            ],
        ],
        'request' => [
            'csrfParam' => '_csrf-api',
            'cookieValidationKey' => '02mxcX6kfifYzfTGQQr9-rLOjCnXQtQw',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'session' => [
            'name' => 'api-newhouse-5i5j',
        ],
        'urlManager' => require(__DIR__ . '/urlManager.php'),
    ],
    'params' => $params,
];
