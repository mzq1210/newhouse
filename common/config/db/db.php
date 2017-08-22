<?php
/**
 * 一主多从链接数据库
 * Author: leexb
 * Date: 17-3-13
 */

return [
    'class' => 'yii\db\Connection',

    // 配置主服务器
    'dsn' => 'mysql:host=127.0.0.1;dbname=hms;port=3306',
    'username' => 'hmsdbo',
    'password' => '123456',
    'charset' => 'utf8',

    // 配置从服务器
    'slaveConfig' => [
        'username' => 'hmsdbo',
        'password' => '123456',
        'attributes' => [
            // use a smaller connection timeout
            PDO::ATTR_TIMEOUT => 10,
        ],
        'charset' => 'utf8',
    ],

    // 配置从服务器组
    'slaves' => [
        ['dsn' => 'mysql:host=127.0.0.1;dbname=hms;port=3306'],
    ],
];

/**
 * 多主多从链接数据库
*/

/*return [
    'class' => 'yii\db\Connection',
    'charset' => 'utf8',

    'masterConfig' => [
        'username' => 'root',
        'passowrd' => '',
        'attributes' => [
            // use a smaller connection timeout
            PDO::ATTR_TIMEOUT => 10,
        ],
    ],

    'masters' => [
        ['dsn' => 'mysql:host=127.0.0.1;dbname=testdb1;port=3306'],
        ['dsn' => 'mysql:host=127.0.0.1;dbname=testdb;port=3306'],
    ],

    // 配置从服务器
    'slaveConfig' => [
        'username' => 'root',
        'password' => '111111',
        'attributes' => [
            // use a smaller connection timeout
            PDO::ATTR_TIMEOUT => 10,
        ],
    ],

    // 配置从服务器组
    'slaves' => [
        ['dsn' => 'mysql:host=127.0.0.1;dbname=testdb1;port=3306']
    ],
];*/
