<?php

/*
 * @desc   
 * @author <liangpingzheng>
 * @date Mar 10, 2017 2:19:05 PM
 */

return [
    'class' => 'yii\solr\SolrClient',
    'core' => 'newhouse',
    'master' => ['host' => '10.10.115.169', 'port' => 8085, 'path' => '/solr'],
    'slave' => [
        ['host' => '10.10.115.169', 'port' => 8085, 'path' => '/solr', 'weight' => 5],
        ['host' => '10.10.115.169', 'port' => 8085, 'path' => '/solr', 'weight' => 5],
    ],
];


//return [
//    'class' => 'yii\solr\SolrClient',
//    'core' => 'newhouse',
//    'master' => ['host' => '127.0.0.1', 'port' => 8085, 'path' => '/solr'],
//    'slave' => [
//        ['host' => '127.0.0.1', 'port' => 8085, 'path' => '/solr', 'weight' => 5],
//        ['host' => '127.0.0.1', 'port' => 8085, 'path' => '/solr', 'weight' => 5],
//    ],
//];
