<?php

namespace dev\services;

use Craft;

/**
 * Class ConfigService
 */
class ConfigService
{
    /**
     * Get config
     * @param string $file
     * @return mixed
     */
    public function getCustomConfig(string $file)
    {
        $fileParts = explode('.', $file);
        $file = $fileParts[0];
        unset($fileParts[0]);
        $item = implode('.', $fileParts);

        $path = rtrim(Craft::$app->getConfig()->configDir, '/');
        $path .= "/{$file}.php";

        if (! file_exists($path)) {
            return null;
        }

        $config = include $path;

        if ($item) {
            $config = $this->getArrayDot($config, $item);
        }

        return $config;
    }

    /**
     * Gets an array item from dot syntax
     * @param array $arr
     * @param string $path
     * @return mixed
     */
    private function getArrayDot(array $arr, string $path)
    {
        $val = $arr;
        foreach (explode('.', $path) as $step) {
            if (! isset($val[$step])) {
                return null;
            }
            $val = $val[$step];
        }
        return $val;
    }
}
