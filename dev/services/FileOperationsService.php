<?php

namespace dev\services;

use Craft;

/**
 * Class FileOperationsService
 */
class FileOperationsService
{
    /**
     * Gets a file's modification time
     * @param string $filePath
     * @return string
     */
    public function getFileTime(string $filePath = '') : string
    {
        if (file_exists($filePath)) {
            return (string) filemtime($filePath);
        }

        $basePath = rtrim(Craft::$app->getConfig()->general->basePath, '/');
        $filePath = ltrim($filePath, '/');
        $newPath = "{$basePath}/{$filePath}";

        if (file_exists($newPath)) {
            return (string) filemtime($newPath);
        }

        return (string) uniqid('', false);
    }
}
