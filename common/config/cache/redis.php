<?php

/*
 * @desc   
 * @author <liangpingzheng>
 * @date Mar 10, 2017 5:16:57 PM
 */

//return [
//    'class' => '\yii\redis\Session',
//    'keyPrefix' => '',
//    'flashParam' => '',
//    'redis' => [
//        'class' => '\yii\redisha\Connection',
//        'masterName' => 'mymaster',
//        'sentinels' => [
//            '127.0.0.1'
//        ]
//    ]
//];
//return [
//    'class' => 'yii\redis\Connection',
//    'hostname' => 'localhost',
//    'port' => 6379,
//    'database' => 0,
//];




/*return [
    'class' => 'yii\redisd\RedisdClient',
    'options' => [
        'host' => '127.0.0.1',//至少两台服务器 可以是一主一从 主的挂了后 自动换从服务器为主
        'slave' => '127.0.0.1',//至少一台服务器
        'port' => 6379,
        'database' => 1,
        'password' => '',
        'timeout' => 0.1,
        'expire' => 60,
        'pconnect' => false,
        'prefix' => 'fang_',
        'serialize' => 1,
    ],
];*/
return [
    'class' => 'yii\redisd\RedisClusters',
    'options' => [
        'host' => '127.0.0.1',
        'port' => 19868,
        'timeout' => 10,
        'expire' => 60,
        'prefix' => 'fang_',
        'serialize' => 1,
    ],
];

