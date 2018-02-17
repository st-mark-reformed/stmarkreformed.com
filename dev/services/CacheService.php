<?php

namespace dev\services;

use Craft;
use \craft\helpers\FileHelper;
use \craft\utilities\ClearCaches;

/**
 * Class CacheService
 */
class CacheService
{
    /**
     * Clears template caches
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
                    Craft::warning(
                        "Could not clear the directory {$action}: " .
                            $e->getMessage(),
                        __METHOD__
                    );
                }
                continue;
            }

            if (isset($cacheOption['params'])) {
                \call_user_func_array($action, $cacheOption['params']);
                continue;
            }

            $action();
        }
    }
}
