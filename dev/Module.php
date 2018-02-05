<?php

namespace dev;

use Craft;
use \craft\helpers\FileHelper;
use \craft\utilities\ClearCaches;
use yii\base\Module as ModuleBase;
use dev\twigextensions\DevTwigExtensions;

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

        Craft::$app->view->registerTwigExtension(
            new DevTwigExtensions()
        );

        if (getenv('CLEAR_TEMPLATE_CACHE_ON_LOAD') === 'true') {
            $this->clearTemplateCache();
        }
    }

    /**
     * Gets the template cache service
     */
    public function clearTemplateCache()
    {
        $actOn = [
            'compiled-templates',
            'template-caches'
        ];

        foreach (ClearCaches::cacheOptions() as $cacheOption) {
            if (! isset($cacheOption['key'], $cacheOption['action']) ||
                ! \in_array($cacheOption['key'], $actOn, true)
            ) {
                continue;
            }

            $action = $cacheOption['action'];

            if (\is_string($action)) {
                try {
                    FileHelper::clearDirectory($action);
                } catch (\Throwable $e) {
                    Craft::warning("Could not clear the directory {$action}: ".$e->getMessage(), __METHOD__);
                }
            } elseif (isset($cacheOption['params'])) {
                \call_user_func_array($action, $cacheOption['params']);
            } else {
                $action();
            }
        }
    }
}
