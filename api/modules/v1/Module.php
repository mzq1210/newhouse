<?php
/**
 * Created by PhpStorm.
 * User: leexb
 * Date: 17-4-23
 * Time: 上午10:46
 */


namespace api\modules\v1;

/**
 * v1 module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'api\modules\v1\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}