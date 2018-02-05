<?php

namespace dev;

use Craft;
use yii\base\Module as ModuleBase;

/**
 * Custom module class for this project.
 *
 * This class will be available throughout the system via:
 * `Craft::$app->getModule('dev')`.
 *
 * If you want the module to get loaded on every request, uncomment this line
 * in config/app.php:
 *
 *     'bootstrap' => ['dev']
 *
 * Learn more about Yii module development in Yii's documentation:
 * http://www.yiiframework.com/doc-2.0/guide-structure-modules.html
 */
class Module extends ModuleBase
{
    /**
     * Initializes the module.
     * @throws \Exception
     */
    public function init()
    {
        Craft::setAlias('@dev', __DIR__);
        parent::init();

        // Custom initialization code goes here...
    }
}
