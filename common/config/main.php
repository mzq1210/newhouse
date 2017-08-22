<?php

include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constant.php');

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'runtimePath' => '@root/runtime',
    'timeZone' => 'Asia/Chongqing',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => require(__DIR__ . '/db/db.php'),
        'solr' => require(__DIR__ . '/cache/solr.php'),
        'redis' => require(__DIR__ . '/cache/redis.php'),
        'deviceDetect' => [
            'class' => 'common\service\DeviceDetect'
        ],
        'common' => [
            'class' => 'common\components\Common',
        ],
        'session' => [
            'class' => 'yii\redisd\Session',
            'redis' => 'redis',
            'keyPrefix' => "fang_"
        ],
    ],
];
