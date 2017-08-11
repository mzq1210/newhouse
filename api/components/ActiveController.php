<?php

/*
 * restful api 基类(删除系统原有的方法)
 * 
 * @author liangpingzheng
 */


namespace app\components;

use Yii;

class ActiveController extends \yii\rest\ActiveController
{
    public $result = ['errcode' => 404, 'errmsg' => '操作错误'];
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
    //如果不使用字段的控制器这个modelClass可以随意定义 暂时先随意定义一个
    public $modelClass = 'common\models\BannerInfo';

    public $request;

    public function init(){
        $this->request = Yii::$app->request;
    }


    public function actions()
    {
        $actions = parent::actions();
        // 注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        return $actions;
    }
    public function actionError()
    {
        return $this->result;
    }
}